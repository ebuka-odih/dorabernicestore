<x-storefront-layout :title="'Contact'">

    <section class="bg-ink-900 text-cream py-16 text-center">
        <p class="eyebrow text-gold-400">We're Here to Help</p>
        <h1 class="font-serif text-4xl mt-2">Contact Us</h1>
    </section>

    <section class="max-w-5xl mx-auto px-6 py-20 grid md:grid-cols-2 gap-16">
        <div>
            <h2 class="font-serif text-2xl text-ink-900 mb-6">Get In Touch</h2>
            <form action="{{ route('contact.submit') }}" method="POST" class="space-y-5">
                @csrf
                <div>
                    <x-input-label for="name" value="Name" />
                    <x-text-input id="name" name="name" value="{{ old('name') }}" required class="mt-1" />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="email" value="Email" />
                    <x-text-input type="email" id="email" name="email" value="{{ old('email') }}" required class="mt-1" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="message" value="Message" />
                    <textarea id="message" name="message" rows="5" required class="w-full border-ink-200 focus:border-gold-500 focus:ring-gold-400 mt-1">{{ old('message') }}</textarea>
                    <x-input-error :messages="$errors->get('message')" class="mt-2" />
                </div>
                <button type="submit" class="btn-gold">Send Message</button>
            </form>
        </div>

        <div class="space-y-8">
            <div>
                <h3 class="eyebrow text-ink-500 mb-2">Boutique</h3>
                <p class="text-ink-700">14 Ardglass Lane<br>Belfast, BT1 2AB<br>United Kingdom</p>
            </div>
            <div>
                <h3 class="eyebrow text-ink-500 mb-2">Hours</h3>
                <p class="text-ink-700">Monday – Saturday, 10am – 6pm</p>
            </div>
            <div>
                <h3 class="eyebrow text-ink-500 mb-2">Email</h3>
                <p class="text-ink-700">hello@dorabernicestore.com</p>
            </div>
        </div>
    </section>

</x-storefront-layout>
