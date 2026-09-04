<?php

namespace App\Http\Controllers;

use App\Support\StripeOrders;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook;

class StripeWebhookController extends Controller
{
    /**
     * Safety net behind the /checkout/return redirect: if a customer pays
     * but never makes it back to our site (closed tab, network drop,
     * abandoned 3D Secure), this is what actually records the order.
     */
    public function handle(Request $request)
    {
        $secret = config('services.stripe.webhook_secret');

        if (! $secret) {
            Log::warning('Stripe webhook received but STRIPE_WEBHOOK_SECRET is not configured — refusing to trust an unsigned event.');

            return response('Webhook not configured', 501);
        }

        try {
            $event = Webhook::constructEvent($request->getContent(), $request->header('Stripe-Signature', ''), $secret);
        } catch (SignatureVerificationException|\UnexpectedValueException $e) {
            Log::warning('Stripe webhook signature verification failed', ['error' => $e->getMessage()]);

            return response('Invalid signature', 400);
        }

        if (($event->type ?? null) === 'payment_intent.succeeded') {
            $intent = $event->data->object;
            StripeOrders::finalize($intent, StripeOrders::lineItemsFromSnapshot($intent));
        }

        return response()->json(['received' => true]);
    }
}
