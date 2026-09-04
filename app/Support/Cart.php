<?php

namespace App\Support;

use App\Models\Product;
use Illuminate\Support\Collection;

class Cart
{
    protected const SESSION_KEY = 'cart';

    public static function contents(): array
    {
        return session(self::SESSION_KEY, []);
    }

    public static function add(int $productId, int $quantity = 1): void
    {
        $cart = self::contents();
        $cart[$productId] = ($cart[$productId] ?? 0) + $quantity;
        session([self::SESSION_KEY => $cart]);
    }

    public static function update(int $productId, int $quantity): void
    {
        $cart = self::contents();

        if ($quantity < 1) {
            unset($cart[$productId]);
        } else {
            $cart[$productId] = $quantity;
        }

        session([self::SESSION_KEY => $cart]);
    }

    public static function remove(int $productId): void
    {
        $cart = self::contents();
        unset($cart[$productId]);
        session([self::SESSION_KEY => $cart]);
    }

    public static function clear(): void
    {
        session()->forget(self::SESSION_KEY);
    }

    public static function count(): int
    {
        return array_sum(self::contents());
    }

    public static function items(): Collection
    {
        $cart = self::contents();

        if (empty($cart)) {
            return collect();
        }

        return Product::query()
            ->whereIn('id', array_keys($cart))
            ->get()
            ->map(function (Product $product) use ($cart) {
                $quantity = $cart[$product->id];

                return (object) [
                    'product' => $product,
                    'quantity' => $quantity,
                    'lineTotal' => (float) $product->currentPrice() * $quantity,
                ];
            });
    }

    public static function subtotal(): float
    {
        return self::items()->sum('lineTotal');
    }
}
