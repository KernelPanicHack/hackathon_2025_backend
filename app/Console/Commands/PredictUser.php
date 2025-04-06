<?php

namespace App\Console\Commands;

use App\Models\Operation;
use App\Models\PredictExpenses;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class PredictUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:predict-user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $url = env('PYTHON_API_PREDICT');
        $endpoint = '/forecast';

        $users = User::query()->get();
        foreach ($users as $user) {
            $data = [];
            $operations = Operation::query()->where('user_id', $user->id)->get();
            foreach ($operations as $operation) {
                $data[] = [
                    'date' => $operation->date,
                    'cost' => $operation->cost
                ];
                if (count($data) > 0) {
                    $response = Http::post($url . $endpoint, ['data' => $data]);
                    $json = $response->json();
                    $forecast = $json['forecast'];
                    foreach ($forecast as $date => $money) {
                        $predictExpense = PredictExpenses::query()->where('date', $date)->where('user_id', $user->id)->first();
                        if (!$predictExpense) {
                            $predictExpense = new PredictExpenses();
                            $predictExpense->date = $date;
                            $predictExpense->user_id = $user->id;
                        }
                        $predictExpense->money = $money;
                        $predictExpense->save();
                    }
                    $user->cushion = $json['cushion'];
                    $user->save();
                }
            }
        }
    }
}
