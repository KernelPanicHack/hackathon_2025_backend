<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Item;
use App\Models\Operation;
use App\Models\Type;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ItemsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $filePath = storage_path('../database/data/items.csv');
        if (($handle = fopen($filePath, 'r')) !== false) {
            // Пропускаем заголовок
            fgetcsv($handle);

            // Читаем данные построчно
            while (($data = fgetcsv($handle)) !== false) {
                Item::create([
                    'id' => $data[0],
                    'name' => $data[1],
                    'slug' => $data[2],
                ]);
            }

            fclose($handle);
        }
    }
}
