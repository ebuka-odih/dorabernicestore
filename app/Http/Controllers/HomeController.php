<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;

class HomeController extends Controller
{
    public function index()
    {
        $categories = Category::query()->orderBy('sort_order')->get();

        $featured = Product::query()
            ->with('category')
            ->where('is_active', true)
            ->where('is_featured', true)
            ->latest()
            ->take(6)
            ->get();

        $newArrivals = Product::query()
            ->with('category')
            ->where('is_active', true)
            ->latest()
            ->take(3)
            ->get();

        return view('home', compact('categories', 'featured', 'newArrivals'));
    }
}
