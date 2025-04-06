<?php

namespace App\Http\Controllers\Api;

use App\Extensions\Amqp\helpers\MessageSerializer;
use App\Extensions\Amqp\helpers\QueueHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoadOperationRequest;
use App\Jobs\CategorizeJob;
use App\Models\Dto\CategoryDto;
use App\Models\Item;
use App\Models\Operation;
use App\Models\Type;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

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
        $item = Item::query()
            ->where('name', $request->post('item'))
            ->first();
        if (!$item) {
            $item = new Item();
            $item->name = $request->post('item');
            $item->slug = Str::slug($request->post('item'));
            $item->save();
        }

        $user = Auth::user();
        $operation = new Operation();
        $operation->type_id = $type->id;
        $operation->user_id = $user->id;
        $operation->item_id = $item->id;
        $operation->cost = $validated['cost'];
        $operation->date = $validated['date'];
        $operation->remaining_balance = $validated['remaining_balance'];
        $operation->ref_no = $validated['ref_no'];

        $operation->save();


        $data = new CategoryDto();
        $data->date = $validated['date'];
        $data->cost = $validated['cost'];
        $data->remainingBalance = $validated['remaining_balance'];

        QueueHelper::addToSlowQueue(
            MessageSerializer::serializeMessage(new CategorizeJob($operation, $data)),
            'slow.categorize'
        );

        return response()->json('ok', 201);
    }
}
