<?php

namespace App\Support;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Database\QueryException;
use Illuminate\Support\Str;

class StripeOrders
{
    /**
     * Turn a succeeded PaymentIntent into an Order, idempotently.
     *
     * @param  array<int, array{product_id: ?int, name: string, price: float, quantity: int}>  $lineItems
     */
    public static function finalize(object $intent, array $lineItems): Order
    {
        if ($existing = Order::where('stripe_payment_intent_id', $intent->id)->first()) {
            return $existing;
        }

        $meta = (array) $intent->metadata;
        $subtotal = (float) ($meta['order_subtotal'] ?? 0);
        $shippingCost = (float) ($meta['order_shipping_cost'] ?? 0);

        try {
            $order = Order::create([
                'user_id' => $meta['user_id'] ?? null,
                'order_number' => 'DB-'.strtoupper(Str::random(8)),
                'status' => 'processing',
                'payment_status' => 'paid',
                'stripe_payment_intent_id' => $intent->id,
                'customer_name' => $meta['customer_name'] ?? '',
                'customer_email' => $meta['customer_email'] ?? '',
                'customer_phone' => $meta['customer_phone'] ?? null,
                'shipping_address' => $meta['shipping_address'] ?? '',
                'shipping_city' => $meta['shipping_city'] ?? '',
                'shipping_postcode' => $meta['shipping_postcode'] ?? '',
                'shipping_country' => $meta['shipping_country'] ?? '',
                'notes' => $meta['notes'] ?? null,
                'subtotal' => $subtotal,
                'shipping_cost' => $shippingCost,
                'total' => $subtotal + $shippingCost,
            ]);
        } catch (QueryException $e) {
            // Lost the race to a concurrent webhook/return hit — the unique
            // constraint on stripe_payment_intent_id caught it. Use theirs.
            return Order::where('stripe_payment_intent_id', $intent->id)->firstOrFail();
        }

        foreach ($lineItems as $line) {
            $order->items()->create([
                'product_id' => $line['product_id'],
                'product_name' => $line['name'],
                'price' => $line['price'],
                'quantity' => $line['quantity'],
            ]);

            if ($line['product_id'] && $product = Product::find($line['product_id'])) {
                $product->decrement('stock', min($line['quantity'], $product->stock));
            }
        }

        return $order;
    }

    /**
     * Reconstruct line items from the compact cart snapshot stored in
     * PaymentIntent metadata — used when there's no live cart session
     * available (e.g. a webhook firing after the browser has gone).
     *
     * @return array<int, array{product_id: ?int, name: string, price: float, quantity: int}>
     */
    public static function lineItemsFromSnapshot(object $intent): array
    {
        $snapshot = json_decode(((array) $intent->metadata)['cart_snapshot'] ?? '[]', true) ?: [];

        return collect($snapshot)->map(fn ($line) => [
            'product_id' => $line['id'] ?? null,
            'name' => $line['name'] ?? 'Item',
            'price' => (float) ($line['p'] ?? 0),
            'quantity' => (int) ($line['q'] ?? 1),
        ])->all();
    }
}
