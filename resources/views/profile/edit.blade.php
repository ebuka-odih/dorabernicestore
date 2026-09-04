<x-storefront-layout :title="'Profile Settings'">

    <section class="bg-ink-900 text-cream py-16 text-center">
        <p class="eyebrow text-gold-400">Your Account</p>
        <h1 class="font-serif text-4xl mt-2">Profile Settings</h1>
    </section>

    <section class="max-w-3xl mx-auto px-6 py-16">
        <div class="flex gap-8 mb-10 text-sm">
            <a href="{{ route('account.orders') }}" class="text-ink-500 hover:text-gold-600 pb-2">Orders</a>
            <a href="{{ route('profile.edit') }}" class="text-gold-600 border-b-2 border-gold-500 pb-2">Profile Settings</a>
        </div>

        <div class="space-y-10">
            <div class="bg-white border border-ink-100 p-8">
                @include('profile.partials.update-profile-information-form')
            </div>

            <div class="bg-white border border-ink-100 p-8">
                @include('profile.partials.update-password-form')
            </div>

            <div class="bg-white border border-ink-100 p-8">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </section>

</x-storefront-layout>
