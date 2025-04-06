<?php

namespace App\Http\Controllers;

use App\Models\Operation;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function index()
    {
        $currentMonth = Carbon::now()->format('Y-m');
        $data = $this->getMonthData($currentMonth);

        return view('profile.index', [
            'currentMonth' => $currentMonth,
            'data'         => $data
        ]);
    }

    public function monthData(Request $request)
    {
        $month = $request->input('month', Carbon::now()->format('Y-m'));

        // Определяем самый ранний месяц операций типа "Withdrawal" (type_id = 1)
        $firstOperation = Operation::where('type_id', '1')->orderBy('date', 'asc')->first();
        if ($firstOperation) {
            $firstMonth = Carbon::parse($firstOperation->date)->format('Y-m');
            // Если запрошенный месяц раньше первого месяца операций – возвращаем уведомление
            if (Carbon::parse($month . '-01')->lt(Carbon::parse($firstMonth . '-01'))) {
                return response()->json([
                    'error'   => true,
                    'message' => 'Данных за предыдущие месяца нет'
                ], 200);
            }
        }

        $data = $this->getMonthData($month);

        return response()->json($data);
    }

    /**
     * Получает данные по операциям (расходам) за указанный месяц.
     *
     * @param string $month в формате "Y-m"
     * @return array
     */
    protected function getMonthData(string $month): array
    {
        // Определяем начало и конец месяца
        $startDate = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        $endDate   = Carbon::createFromFormat('Y-m', $month)->endOfMonth();

        // Жадная загрузка связанных категорий
        $operations = Operation::with('category')
            ->where('type_id', '1')
            ->whereBetween('date', [$startDate, $endDate])
            ->get();

        // Группируем операции по category_id
        $grouped = $operations->groupBy('category_id');

        // Данные для диаграммы: подписи – имя категории, данные – сумма расходов
        $chartLabels = $grouped->map(function ($group) {
            return optional($group->first()->category)->name ?? 'Без категории';
        })->values()->toArray();

        $chartData = $grouped->map(function ($group) {
            return $group->sum('cost');
        })->values()->toArray();

        // История – последние 10 операций (новейшие первыми)
        $history = $operations->sortByDesc('date')
            ->take(10)
            ->map(function ($operation) {
                return [
                    'date'     => Carbon::parse($operation->date)->format('d.m.Y'),
                    'category' => optional($operation->category)->name ?? 'Без категории',
                    'amount'   => $operation->cost,
                ];
            })->values()->toArray();

        // Прогноз расходов по неделям (предполагаем, что месяц условно делится на 4 недели)
        $forecast = [];
        for ($i = 0; $i < 4; $i++) {
            $weekStart = $startDate->copy()->addDays($i * 7);
            $weekEnd   = $i < 3 ? $weekStart->copy()->addDays(6) : $endDate;
            $weekExpense = $operations->filter(function ($operation) use ($weekStart, $weekEnd) {
                return Carbon::parse($operation->date)->between($weekStart, $weekEnd);
            })->sum('cost');
            $forecast[] = $weekExpense;
        }

        return [
            'month'       => $month,
            'chartLabels' => $chartLabels,
            'chartData'   => $chartData,
            'history'     => $history,
            'forecast'    => $forecast,
        ];
    }
}
