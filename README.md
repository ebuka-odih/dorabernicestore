# Dora Bernice

Fine jewelry e-commerce — a Laravel storefront and admin panel, with Stripe handling checkout payments.

## Stack

- Laravel 12, SQLite, Breeze (Blade + Alpine + Tailwind)
- Stripe (embedded Payment Element) for checkout
- A custom line-art SVG icon set stands in for product photography

## Getting started

```bash
composer install
npm install && npm run build
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
```

Add Stripe test keys to `.env` (`STRIPE_KEY`, `STRIPE_SECRET`, `STRIPE_WEBHOOK_SECRET`) to enable checkout — see the comments above those lines in `.env.example`. For local webhook testing, run `stripe listen --forward-to <your-app-url>/stripe/webhook`.

## Seeded accounts

- Admin: `admin@dorabernicestore.com` / `password`
- Customer: `customer@example.com` / `password`

## Structure

- `/admin` — product, category, and order management (gated by `is_admin` on the `users` table)
- `/shop`, `/cart`, `/checkout` — the public storefront
- `App\Support\Cart` — session-based cart
- `App\Support\StripeOrders` — turns a succeeded PaymentIntent into an `Order`, used by both the checkout return handler and the Stripe webhook
