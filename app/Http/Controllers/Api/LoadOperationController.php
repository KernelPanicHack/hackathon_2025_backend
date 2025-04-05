<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoadOperationRequest;
use App\Models\Category;
use App\Models\Operation;
use App\Models\Type;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;

/**
 * Создание операции.
 *
 * @return JsonResponse
 */
class LoadOperationController extends Controller
{
    public function loadOperation(LoadOperationRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $type = Type::where('name', $validated['type'])->firstOrFail();
//        $category = Category::where('name', $validated['category'])->firstOrFail();

//        $operation = new Operation();
//        $operation->type = $type->id;
//        $operation->cost = $validated['cost'];
//        $operation->remaining_balance = $validated['remaining_balance'];
//        $operation->ref_no = $validated['ref_no'];
        $validatedData = [
            'Date' => $validated['date'],
            'WithDrawal' => $validated['cost'],
            'Balance' => $validated['remaining_balance'],
        ];
        $url = 'http://192.168.10.224:8000/api/categorize-transaction';
        $response = Http::post($url, $validatedData);

        // Проверка успешности ответа
        if ($response->successful()) {
//            $operation->save();
            // Возвращаем полученные данные из внешнего API
            return response()->json($response->json(), $response->status());
        } else {
            // Обработка ошибки и возврат соответствующего ответа
            return response()->json([
                'error' => 'Ошибка при вызове API',
                'details' => $response->body(),
            ], $response->status());
        }

    }
}
