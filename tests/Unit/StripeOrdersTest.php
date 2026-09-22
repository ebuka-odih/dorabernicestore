<?php

namespace Tests\Unit;

use App\Support\StripeOrders;
use PHPUnit\Framework\TestCase;

class StripeOrdersTest extends TestCase
{
    public function test_payment_description_lists_products_with_quantities(): void
    {
        $snapshot = [
            ['id' => 1, 'name' => 'Gold Locket', 'q' => 2, 'p' => 120.0],
            ['id' => 2, 'name' => 'Pearl Ring', 'q' => 1, 'p' => 85.0],
        ];

        $this->assertSame('2 × Gold Locket, 1 × Pearl Ring', StripeOrders::paymentDescription($snapshot));
    }

    public function test_payment_description_is_truncated(): void
    {
        $snapshot = array_map(fn ($i) => ['id' => $i, 'name' => str_repeat('A', 60), 'q' => 1, 'p' => 1.0], range(1, 20));

        $this->assertLessThanOrEqual(451, mb_strlen(StripeOrders::paymentDescription($snapshot)));
    }

    public function test_snapshot_chunks_reassemble_to_the_original_cart(): void
    {
        // A cart large enough that its JSON exceeds one 500-char metadata value.
        $snapshot = array_map(
            fn ($i) => ['id' => $i, 'name' => 'Product Number '.$i.' — 14k gold', 'q' => 2, 'p' => 99.99],
            range(1, 25)
        );

        $metadata = StripeOrders::purchaseMetadata($snapshot, 100.0, 15.0);

        foreach ($metadata as $value) {
            if (is_string($value)) {
                $this->assertLessThanOrEqual(500, strlen($value));
            }
        }

        $this->assertSame('100', $metadata['order_subtotal']);
        $this->assertSame('15', $metadata['order_shipping_cost']);
        $this->assertSame('50', $metadata['item_count']);

        $intent = (object) ['metadata' => $metadata];
        $lines = StripeOrders::lineItemsFromSnapshot($intent);

        $this->assertCount(25, $lines);
        $this->assertSame(3, $lines[2]['product_id']);
        $this->assertSame('Product Number 3 — 14k gold', $lines[2]['name']);
        $this->assertSame(99.99, $lines[2]['price']);
        $this->assertSame(2, $lines[2]['quantity']);
    }

    public function test_line_items_still_read_the_legacy_single_key_snapshot(): void
    {
        $intent = (object) ['metadata' => [
            'cart_snapshot' => json_encode([['id' => 7, 'name' => 'Old Item', 'q' => 1, 'p' => 10.5]]),
        ]];

        $lines = StripeOrders::lineItemsFromSnapshot($intent);

        $this->assertSame([[
            'product_id' => 7,
            'name' => 'Old Item',
            'price' => 10.5,
            'quantity' => 1,
        ]], $lines);
    }

    public function test_line_items_default_to_empty_when_no_snapshot_exists(): void
    {
        $this->assertSame([], StripeOrders::lineItemsFromSnapshot((object) ['metadata' => []]));
    }
}
