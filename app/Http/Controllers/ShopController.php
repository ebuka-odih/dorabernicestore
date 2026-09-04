<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::query()->orderBy('sort_order')->get();

        $query = Product::query()->with('category')->where('is_active', true);

        if ($request->filled('category')) {
            $query->whereHas('category', fn ($q) => $q->where('slug', $request->string('category')));
        }

        match ($request->string('sort')->toString()) {
            'price-asc' => $query->orderByRaw('COALESCE(sale_price, price) asc'),
            'price-desc' => $query->orderByRaw('COALESCE(sale_price, price) desc'),
            'name' => $query->orderBy('name'),
            default => $query->latest(),
        };

        $products = $query->paginate(9)->withQueryString();

        return view('shop', compact('products', 'categories'));
    }
}
