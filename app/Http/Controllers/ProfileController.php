<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Operation;
use App\Models\PredictExpenses;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function index()
    {
        $currentMonth = Carbon::now()->format('Y-m');
        $data = $this->getMonthData($currentMonth);

        return view('profile.index', [
            'currentMonth' => $currentMonth,
            'data' => $data
        ]);
    }

    public function monthData(Request $request)
    {
        $month = $request->input('month', Carbon::now()->format('Y-m'));

        $firstOperation = Operation::where('type_id', '1')->orderBy('date', 'asc')->first();
        if ($firstOperation) {
            $firstMonth = Carbon::parse($firstOperation->date)->format('Y-m');
            if (Carbon::parse($month . '-01')->lt(Carbon::parse($firstMonth . '-01'))) {
                return response()->json([
                    'error' => true,
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
        $startDate = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        $endDate = Carbon::createFromFormat('Y-m', $month)->endOfMonth();

        $operations = Operation::with('category')
            ->where('type_id', '1')
            ->whereBetween('date', [$startDate, $endDate])
            ->get();

        $grouped = $operations->groupBy('category_id');

        $chartLabels = $grouped->map(function ($group) {
            return optional($group->first()->category)->name ?? 'Без категории';
        })->values()->toArray();

        $chartData = $grouped->map(function ($group) {
            return $group->sum('cost');
        })->values()->toArray();

        $history = $operations->sortByDesc('date')
            ->take(10)
            ->map(function ($operation) {
                return [
                    'date' => Carbon::parse($operation->date)->format('d.m.Y'),
                    'category' => optional($operation->category)->name ?? 'Без категории',
                    'amount' => $operation->cost,
                ];
            })->values()->toArray();

        $forecast = [];
        $months = [];
        $user = Auth::user();
        $predictExpenses = PredictExpenses::query()->where('user_id', $user->id)->orderBy('date')->get();
        foreach ($predictExpenses as $predictExpense) {
            $firstMonth = Carbon::parse($predictExpense->date)->format('M');
            $forecast[] = $predictExpense->money;
            $months[] = $firstMonth;
        }

        $links = [];
        foreach ($chartLabels as $chartLabel) {
            $category = Category::query()->where('name', $chartLabel)->first();
            $links[] = route('products.index', ['id' => $category->id]);
        }


        return [
            'month' => $month,
            'months' => $months,
            'chartLabels' => $chartLabels,
            'chartData' => $chartData,
            'categoryLinks' => $links,
            'history' => $history,
            'forecast' => $forecast,
        ];
    }
}
