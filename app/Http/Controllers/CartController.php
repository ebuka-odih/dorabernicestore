<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Support\Cart;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $items = Cart::items();
        $subtotal = Cart::subtotal();

        return view('cart', compact('items', 'subtotal'));
    }

    public function store(Request $request, Product $product)
    {
        $data = $request->validate([
            'quantity' => ['nullable', 'integer', 'min:1', 'max:20'],
        ]);

        Cart::add($product->id, $data['quantity'] ?? 1);

        return back()->with('status', "{$product->name} added to your bag.");
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'quantity' => ['required', 'integer', 'min:0', 'max:20'],
        ]);

        Cart::update($product->id, $data['quantity']);

        return back()->with('status', 'Bag updated.');
    }

    public function destroy(Product $product)
    {
        Cart::remove($product->id);

        return back()->with('status', 'Item removed.');
    }
}
