@php $icons = ['ring', 'necklace', 'earrings', 'bracelet', 'pendant', 'watch', 'gift', 'diamond']; @endphp

<x-admin-layout :title="$category->exists ? 'Edit Category' : 'Add Category'">

    <form action="{{ $category->exists ? route('admin.categories.update', $category) : route('admin.categories.store') }}" method="POST" class="max-w-xl bg-white border border-ink-100 p-8 space-y-6">
        @csrf
        @if ($category->exists) @method('PUT') @endif

        <div>
            <x-input-label for="name" value="Name" />
            <x-text-input id="name" name="name" value="{{ old('name', $category->name) }}" required class="mt-1" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="description" value="Description" />
            <textarea id="description" name="description" rows="3" class="w-full border-ink-200 focus:border-gold-500 focus:ring-gold-400 mt-1">{{ old('description', $category->description) }}</textarea>
        </div>

        <div>
            <x-input-label value="Icon" />
            <div class="grid grid-cols-4 gap-3 mt-2">
                @foreach ($icons as $icon)
                    <label class="flex flex-col items-center gap-2 border border-ink-200 py-4 cursor-pointer has-[:checked]:border-gold-500 has-[:checked]:bg-gold-50">
                        <input type="radio" name="icon" value="{{ $icon }}" class="sr-only" @checked(old('icon', $category->icon) === $icon)>
                        <x-jewel-icon :icon="$icon" class="w-8 h-8 text-ink-700" />
                        <span class="text-xs uppercase text-ink-500">{{ $icon }}</span>
                    </label>
                @endforeach
            </div>
            <x-input-error :messages="$errors->get('icon')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="sort_order" value="Sort Order" />
            <x-text-input type="number" id="sort_order" name="sort_order" value="{{ old('sort_order', $category->sort_order ?? 0) }}" class="mt-1 w-32" />
        </div>

        <div class="flex gap-4 pt-4">
            <button type="submit" class="btn-gold">{{ $category->exists ? 'Update Category' : 'Create Category' }}</button>
            <a href="{{ route('admin.categories.index') }}" class="btn-gold-outline">Cancel</a>
        </div>
    </form>

</x-admin-layout>
