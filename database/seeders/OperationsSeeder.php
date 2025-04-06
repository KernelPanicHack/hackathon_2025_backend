<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Item;
use App\Models\Operation;
use App\Models\Type;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OperationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $filePath = storage_path('../database/data/operations.csv');
        if (($handle = fopen($filePath, 'r')) !== false) {
            // Пропускаем заголовок
            fgetcsv($handle);

            // Читаем данные построчно
            while (($data = fgetcsv($handle)) !== false) {
                Operation::create([
                    'type_id' => Type::where('name', $data[1])->first()->id,
                    'cost' => $data[2],
                    'remaining_balance' => $data[3],
                    'category_id' => Category::where('name', $data[4])->first()->id,
                    'date' => $data[5],
                    'ref_no' => $data[6],
                    'item_id' => Item::where('name', $data[7])->first()->id,
                    'user_id' => 1,
                ]);
            }

            fclose($handle);
        }
    }
}
