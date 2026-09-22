<x-admin-layout :title="'Change Password'">

    <div class="max-w-xl">
        <div class="bg-white border border-ink-100 p-8">
            <h2 class="font-serif text-xl text-ink-900">Update Your Password</h2>
            <p class="text-sm text-ink-500 mt-1">Use a long, random password to keep the store secure.</p>

            <form method="POST" action="{{ route('admin.password.update') }}" class="mt-6 space-y-6">
                @csrf
                @method('PUT')

                <div>
                    <x-input-label for="current_password" value="Current Password" />
                    <x-text-input id="current_password" name="current_password" type="password" class="mt-1" autocomplete="current-password" />
                    <x-input-error :messages="$errors->get('current_password')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="password" value="New Password" />
                    <x-text-input id="password" name="password" type="password" class="mt-1" autocomplete="new-password" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="password_confirmation" value="Confirm New Password" />
                    <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1" autocomplete="new-password" />
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                </div>

                <x-primary-button>Update Password</x-primary-button>
            </form>
        </div>
    </div>

</x-admin-layout>
