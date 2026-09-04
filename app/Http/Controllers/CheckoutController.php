<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Support\Cart;
use App\Support\StripeOrders;
use Illuminate\Http\Request;
use Stripe\PaymentIntent;
use Stripe\StripeClient;

class CheckoutController extends Controller
{
    public function create(Request $request)
    {
        $items = Cart::items();

        if ($items->isEmpty()) {
            return redirect()->route('cart.index')->with('status', 'Your bag is empty.');
        }

        $subtotal = Cart::subtotal();
        $shippingCost = $subtotal >= 250 ? 0 : 15;
        $total = $subtotal + $shippingCost;

        $stripeConfigured = filled(config('services.stripe.key')) && filled(config('services.stripe.secret'));
        $clientSecret = null;

        if ($stripeConfigured) {
            try {
                $clientSecret = $this->resolvePaymentIntent($request, $total)->client_secret;
            } catch (\Exception $e) {
                $stripeConfigured = false;
            }
        }

        return view('checkout', compact('items', 'subtotal', 'shippingCost', 'total', 'stripeConfigured', 'clientSecret'));
    }

    /**
     * Create (or reuse) the PaymentIntent backing this checkout session,
     * keeping its amount in sync with the current cart total.
     */
    protected function resolvePaymentIntent(Request $request, float $total): PaymentIntent
    {
        $amount = (int) round($total * 100);
        $stripe = $this->stripe();
        $intentId = $request->session()->get('checkout_intent_id');

        if ($intentId) {
            try {
                $intent = $stripe->paymentIntents->retrieve($intentId);

                if (in_array($intent->status, ['requires_payment_method', 'requires_confirmation', 'requires_action'])) {
                    return $intent->amount === $amount
                        ? $intent
                        : $stripe->paymentIntents->update($intentId, ['amount' => $amount]);
                }
            } catch (\Exception $e) {
                // Intent is gone or invalid — fall through and start a fresh one.
            }
        }

        $intent = $stripe->paymentIntents->create([
            'amount' => $amount,
            'currency' => 'usd',
            'automatic_payment_methods' => ['enabled' => true],
        ]);

        $request->session()->put('checkout_intent_id', $intent->id);

        return $intent;
    }

    /**
     * AJAX endpoint: stash the shipping/contact form against the
     * PaymentIntent (session for the fast path, Stripe metadata as a
     * durable fallback for the webhook) before confirmPayment() runs.
     */
    public function saveDetails(Request $request)
    {
        $data = $request->validate([
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_email' => ['required', 'email', 'max:255'],
            'customer_phone' => ['nullable', 'string', 'max:50'],
            'shipping_address' => ['required', 'string', 'max:500'],
            'shipping_city' => ['required', 'string', 'max:255'],
            'shipping_postcode' => ['required', 'string', 'max:50'],
            'shipping_country' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $intentId = $request->session()->get('checkout_intent_id');

        abort_unless($intentId, 422, 'Your checkout session has expired. Please refresh and try again.');

        $request->session()->put('checkout_details', $data);

        $subtotal = Cart::subtotal();
        $shippingCost = $subtotal >= 250 ? 0 : 15;

        $snapshot = Cart::items()->map(fn ($item) => [
            'id' => $item->product->id,
            'name' => $item->product->name,
            'q' => $item->quantity,
            'p' => (float) $item->product->currentPrice(),
        ])->values()->all();

        $metadata = array_filter([
            ...$data,
            'user_id' => (string) auth()->id(),
            'order_subtotal' => (string) $subtotal,
            'order_shipping_cost' => (string) $shippingCost,
            'cart_snapshot' => substr(json_encode($snapshot), 0, 500),
        ], fn ($value) => $value !== null && $value !== '');

        $this->stripe()->paymentIntents->update($intentId, [
            'receipt_email' => $data['customer_email'],
            'metadata' => $metadata,
        ]);

        return response()->json(['ok' => true]);
    }

    /**
     * Where Stripe sends the browser back after confirmPayment() —
     * whether that happened inline or via a 3D Secure redirect.
     */
    public function return(Request $request)
    {
        $intentId = $request->session()->get('checkout_intent_id') ?? $request->query('payment_intent');

        if (! $intentId) {
            return redirect()->route('checkout.create')->with('status', 'We could not find your payment session — please try again.');
        }

        try {
            $intent = $this->stripe()->paymentIntents->retrieve($intentId);
        } catch (\Exception $e) {
            return redirect()->route('checkout.create')->with('status', 'We could not confirm your payment — please try again.');
        }

        if ($intent->status !== 'succeeded') {
            return redirect()->route('checkout.create')->with('status', 'Your payment was not completed. Please try again.');
        }

        $lineItems = Cart::items()->isNotEmpty()
            ? Cart::items()->map(fn ($item) => [
                'product_id' => $item->product->id,
                'name' => $item->product->name,
                'price' => (float) $item->product->currentPrice(),
                'quantity' => $item->quantity,
            ])->all()
            : StripeOrders::lineItemsFromSnapshot($intent);

        $order = StripeOrders::finalize($intent, $lineItems);

        $request->session()->forget(['checkout_intent_id', 'checkout_details']);
        Cart::clear();
        session(['last_order' => $order->order_number]);

        return redirect()->route('checkout.confirmation', $order->order_number);
    }

    public function confirmation(Order $order)
    {
        abort_unless($order->user_id === auth()->id() || session('last_order') === $order->order_number, 404);

        $order->load('items');

        return view('checkout-confirmation', compact('order'));
    }

    protected function stripe(): StripeClient
    {
        return new StripeClient(config('services.stripe.secret'));
    }
}
