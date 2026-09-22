<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_empty_cart_redirects_to_cart(): void
    {
        $this->get(route('checkout.create'))
            ->assertRedirect(route('cart.index'));
    }

    public function test_checkout_without_stripe_keys_shows_setup_notice_and_no_payment_script(): void
    {
        config(['services.stripe.key' => null, 'services.stripe.secret' => null]);

        $this->addProductToCart();

        $this->get(route('checkout.create'))
            ->assertOk()
            ->assertSee('Payment processing is still being set up')
            ->assertDontSee('js.stripe.com', false)
            ->assertDontSee('payment-element', false);
    }

    protected function addProductToCart(): Product
    {
        $category = Category::create(['name' => 'Rings', 'slug' => 'rings']);
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Gold Locket',
            'slug' => 'gold-locket',
            'sku' => 'GL-001',
            'price' => 120,
            'stock' => 10,
            'is_active' => true,
        ]);

        $this->post(route('cart.store', $product), ['quantity' => 1]);

        return $product;
    }
}
