<?php

namespace App\Http\Controllers;

use App\Models\Operation;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function index(){
        $currentMonth = Carbon::now()->format('Y-m');
        $data = $this->getMonthData($currentMonth);;

        return view('profile.index', [
            'currentMonth' => $currentMonth,
            'data' => $data
        ]);
    }

    public function monthData(Request $request)
    {
        $month = $request->input('month', Carbon::now()->format('Y-m'));

        // Определяем самый ранний месяц операций типа "Withdrawal"
        $firstOperation = Operation::where('type', 'Withdrawal')->orderBy('date', 'asc')->first();
        if ($firstOperation) {
            $firstMonth = Carbon::parse($firstOperation->date)->format('Y-m');
            // Если запрошенный месяц раньше первого месяца операций – возвращаем уведомление
            if (Carbon::parse($month . '-01')->lt(Carbon::parse($firstMonth . '-01'))) {
                return response()->json(['error' => true, 'message' => 'Данных за предыдущие месяца нет'], 200);
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

        // Получаем операции только с типом "expense" за указанный период
        $operations = Operation::where('type', 'Withdrawal')
            ->whereBetween('date', [$startDate, $endDate])
            ->get();

        // Группируем операции по категориям для построения диаграммы
        $grouped = $operations->groupBy('category');
        $chartLabels = $grouped->keys()->toArray();
        $chartData = $grouped->map(function ($group) {
            return $group->sum('cost');
        })->values()->toArray();

        // Формируем историю – последние 10 операций, отсортированных по дате (новейшие первыми)
        $history = $operations->sortByDesc('date')
            ->take(10)
            ->map(function ($operation) {
                return [
                    'date'     => Carbon::parse($operation->date)->format('d.m.Y'),
                    'category' => $operation->category,
                    'amount'   => $operation->cost,
                ];
            })->values()->toArray();

        // Рассчитываем прогноз – сумма расходов по неделям
        // Предполагаем, что в месяце 4 недели
        $forecast = [];
        for ($i = 0; $i < 4; $i++) {
            $weekStart = $startDate->copy()->addDays($i * 7);
            // Для первых трёх недель берём интервал в 7 дней, а для последней – до конца месяца
            $weekEnd = $i < 3 ? $weekStart->copy()->addDays(6) : $endDate;
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
