<?php

namespace App\Jobs;

use App\Extensions\Amqp\jobs\JobInterface;
use App\Extensions\Centrifuge\Centrifuge;
use App\Models\Category;
use App\Models\Dto\CategoryDto;
use App\Models\Operation;
use App\WSMessages\ReloadProducts;
use Illuminate\Support\Facades\Http;

class CategorizeJob implements JobInterface
{

    public function __construct(
        public Operation   $operation,
        public CategoryDto $dto
    )
    {
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $url = env('PYTHON_API');
        $endpoint = '/api/categorize-transaction';

        $url = $url . $endpoint;
        $validatedData = [
            'Date' => $this->dto->date,
            'WithDrawal' => $this->dto->cost,
            'Balance' => $this->dto->remainingBalance,
        ];
        $response = Http::post($url, $validatedData);
        if ($response->successful()) {
            /** @var Centrifuge $client */
            $client = app(Centrifuge::class);
//            $client->publishMessage('reset-race', new ReloadProducts());
            $categoryName = ($response->json())['category'];

            $category = Category::query()
                ->where('name', $categoryName)
                ->first();
            $this->operation->category_id = $category->id;
            $this->operation->updated_at = now();
            $this->operation->save();
        } else {
            $this->log("Ошибка при обращении к апи");
        }
    }

    /**
     * @inheritDoc
     */
    public function log($msg)
    {
        // TODO: дописать логи
    }
}
