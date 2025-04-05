<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    public function index(string $id){
        $category = Category::find($id);
        return view('products', ['category' => $category->name,
            'products' => $category->products]);
    }
}
