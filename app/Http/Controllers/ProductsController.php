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

        $operationType = $operations->first()->type ?? ''; // Предполагаем, что все операции одной категории имеют одинаковый тип

        // Если это депозит, исключаем категории, связанные с депозитами
        if ($operationType === 'deposit') {
            $otherCategories = Category::where('id', '!=', $id)
                ->where('type', 'withdrawal') // Фильтруем только категории расходов
                ->get();
        } else {
            // Если это не депозит, просто получаем все другие категории с типом 'expense'
            $otherCategories = Category::where('id', '!=', $id)
                ->where('type', 'withdrawal') // Фильтруем только категории расходов
                ->get();
        }

        return view('products', [
            'category' => $category,
            'items' => $operations,
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
            'hidden'      => true,
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

        $otherCategories = Category::where('id', '!=', $categoryId)->get();

        return view('category.show', [
            'category' => $category,
            'items' => $operations,
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
