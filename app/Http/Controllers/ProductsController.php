<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Item;
use App\Models\Operation;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    public function index(string $id)
    {
        $category = Category::findOrFail($id);

        $operations = Operation::with('item')
            ->whereHas('item', function ($query) use ($id) {
                $query->where('category_id', $id);
            })
            ->get();

        // Получаем первую операцию, чтобы определить тип
        $firstOperation = $operations->first();
        // Если тип операции deposit (type_id = 2), то фильтруем категории, исключая те,
        // которые используются в операциях с type_id = 2 (например, salary)
        if ($firstOperation && $firstOperation->type_id == 2) {
            $otherCategories = Category::where('id', '!=', $id)
                ->whereNotIn('id', function ($query) {
                    $query->select('category_id')
                        ->from('operations')
                        ->where('type_id', 2);
                })
                ->get();
        } else {
            // Если это не deposit, можно оставить только те категории,
            // которые используются в операциях с type_id отличным от 2
            $otherCategories = Category::where('id', '!=', $id)
                ->whereIn('id', function ($query) {
                    $query->select('category_id')
                        ->from('operations')
                        ->where('type_id', '<>', 2);
                })
                ->get();
        }

        return view('products', [
            'category'   => $category,
            'items'      => $operations,
            'categories' => $otherCategories,
        ]);
    }

    public function changeCategory(Request $request, $operationId)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
        ]);

        $operation = Operation::findOrFail($operationId);

        $operation->update([
            'category_id' => $request->category_id,
        ]);

        return response()->json(['success' => true]);
    }

    public function showCategory($categoryId)
    {
        $category = Category::findOrFail($categoryId);

        $operations = Operation::with('item')
            ->whereHas('item', function ($query) use ($categoryId) {
                $query->where('category_id', $categoryId);
            })
            ->get();

        $firstOperation = $operations->first();
        if ($firstOperation && $firstOperation->type_id == 2) {
            $otherCategories = Category::where('id', '!=', $categoryId)
                ->whereNotIn('id', function ($query) {
                    $query->select('category_id')
                        ->from('operations')
                        ->where('type_id', 2);
                })
                ->get();
        } else {
            $otherCategories = Category::where('id', '!=', $categoryId)
                ->whereIn('id', function ($query) {
                    $query->select('category_id')
                        ->from('operations')
                        ->where('type_id', '<>', 2);
                })
                ->get();
        }

        return view('category.show', [
            'category'   => $category,
            'items'      => $operations,
            'categories' => $otherCategories,
        ]);
    }

    public function updateItemCategory(Request $request, $itemId)
    {

        $newCategoryId = $request->input('category_id');

        $operation = Operation::where('item_id', $itemId)->first();

        $operation->item->update(['category_id' => $newCategoryId]);

        return redirect()->route('category.show', $operation->item->category_id);
    }
}
