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
                'order_number' => 'JD-'.strtoupper(Str::random(8)),
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
        $snapshot = json_decode(static::snapshotJson((array) $intent->metadata), true) ?: [];

        return collect($snapshot)->map(fn ($line) => [
            'product_id' => $line['id'] ?? null,
            'name' => $line['name'] ?? 'Item',
            'price' => (float) ($line['p'] ?? 0),
            'quantity' => (int) ($line['q'] ?? 1),
        ])->all();
    }

    /**
     * Build the compact cart snapshot attached to the PaymentIntent, e.g.
     * `[['id' => 3, 'name' => 'Gold Locket', 'q' => 2, 'p' => 120.0]]`.
     *
     * @param  iterable<object{product: object, quantity: int}>  $items  (Cart::items())
     */
    public static function cartSnapshot(iterable $items): array
    {
        return collect($items)->map(fn ($item) => [
            'id' => $item->product->id,
            'name' => $item->product->name,
            'q' => $item->quantity,
            'p' => (float) $item->product->currentPrice(),
        ])->values()->all();
    }

    /**
     * Human-readable purchase summary for the PaymentIntent description,
     * so the products show in the Stripe Dashboard and on receipts,
     * e.g. "2 × Gold Locket, 1 × Pearl Ring".
     */
    public static function paymentDescription(array $snapshot): string
    {
        $summary = collect($snapshot)
            ->map(fn ($line) => ((int) ($line['q'] ?? 1)).' × '.($line['name'] ?? 'Item'))
            ->join(', ');

        return Str::limit($summary, 450, '…');
    }

    /**
     * Metadata carrying the purchase: totals plus the cart snapshot split
     * across `cart_snapshot_1..N` keys, since Stripe caps each metadata
     * value at 500 characters. A Stripe metadata update replaces the whole
     * map, so writers must always send the complete set from this method.
     */
    public static function purchaseMetadata(array $snapshot, float $subtotal, float $shippingCost): array
    {
        return [
            'order_subtotal' => (string) $subtotal,
            'order_shipping_cost' => (string) $shippingCost,
            'item_count' => (string) array_sum(array_map(fn ($line) => (int) ($line['q'] ?? 1), $snapshot)),
            ...static::snapshotMetadata($snapshot),
        ];
    }

    /**
     * @return array<string, string> `cart_snapshot_1..N` chunks (450 chars each).
     */
    public static function snapshotMetadata(array $snapshot): array
    {
        $json = json_encode(array_values($snapshot)) ?: '[]';
        $chunks = [];
        $length = mb_strlen($json);
        $part = 1;

        do {
            $chunks['cart_snapshot_'.$part] = mb_substr($json, ($part - 1) * 450, 450);
            $part++;
        } while (($part - 1) * 450 < $length);

        return $chunks;
    }

    /**
     * Reassemble the snapshot JSON, supporting the chunked
     * `cart_snapshot_1..N` keys and the legacy single `cart_snapshot` key.
     */
    public static function snapshotJson(array $metadata): string
    {
        $chunks = [];

        foreach ($metadata as $key => $value) {
            if (preg_match('/^cart_snapshot_(\d+)$/', (string) $key, $matches)) {
                $chunks[(int) $matches[1]] = (string) $value;
            }
        }

        if ($chunks !== []) {
            ksort($chunks);

            return implode('', $chunks);
        }

        return (string) ($metadata['cart_snapshot'] ?? '[]');
    }
}
