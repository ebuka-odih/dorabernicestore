@php $icons = ['ring', 'necklace', 'earrings', 'bracelet', 'pendant', 'watch', 'gift', 'diamond']; @endphp

<x-admin-layout :title="$product->exists ? 'Edit Product' : 'Add Product'">

    <form action="{{ $product->exists ? route('admin.products.update', $product) : route('admin.products.store') }}" method="POST" class="max-w-3xl bg-white border border-ink-100 p-8 space-y-6">
        @csrf
        @if ($product->exists) @method('PUT') @endif

        <div class="grid sm:grid-cols-2 gap-6">
            <div>
                <x-input-label for="name" value="Name" />
                <x-text-input id="name" name="name" value="{{ old('name', $product->name) }}" required class="mt-1" />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>
            <div>
                <x-input-label for="category_id" value="Category" />
                <select id="category_id" name="category_id" required class="w-full border-ink-200 focus:border-gold-500 focus:ring-gold-400 mt-1">
                    <option value="">Select a category</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected(old('category_id', $product->category_id) == $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
            </div>
            <div>
                <x-input-label for="sku" value="SKU" />
                <x-text-input id="sku" name="sku" value="{{ old('sku', $product->sku) }}" required class="mt-1" />
                <x-input-error :messages="$errors->get('sku')" class="mt-2" />
            </div>
            <div>
                <x-input-label for="material" value="Material" />
                <x-text-input id="material" name="material" value="{{ old('material', $product->material) }}" placeholder="18k Gold, Diamond..." class="mt-1" />
            </div>
            <div>
                <x-input-label for="price" value="Price" />
                <x-text-input type="number" step="0.01" id="price" name="price" value="{{ old('price', $product->price) }}" required class="mt-1" />
                <x-input-error :messages="$errors->get('price')" class="mt-2" />
            </div>
            <div>
                <x-input-label for="sale_price" value="Sale Price (optional)" />
                <x-text-input type="number" step="0.01" id="sale_price" name="sale_price" value="{{ old('sale_price', $product->sale_price) }}" class="mt-1" />
            </div>
            <div>
                <x-input-label for="stock" value="Stock" />
                <x-text-input type="number" id="stock" name="stock" value="{{ old('stock', $product->stock ?? 0) }}" required class="mt-1" />
            </div>
        </div>

        <div>
            <x-input-label for="description" value="Description" />
            <textarea id="description" name="description" rows="4" class="w-full border-ink-200 focus:border-gold-500 focus:ring-gold-400 mt-1">{{ old('description', $product->description) }}</textarea>
        </div>

        <div>
            <x-input-label value="Icon" />
            <div class="grid grid-cols-4 sm:grid-cols-8 gap-3 mt-2">
                @foreach ($icons as $icon)
                    <label class="flex flex-col items-center gap-2 border border-ink-200 py-4 cursor-pointer has-[:checked]:border-gold-500 has-[:checked]:bg-gold-50">
                        <input type="radio" name="icon" value="{{ $icon }}" class="sr-only" @checked(old('icon', $product->icon) === $icon)>
                        <x-jewel-icon :icon="$icon" class="w-8 h-8 text-ink-700" />
                    </label>
                @endforeach
            </div>
            <x-input-error :messages="$errors->get('icon')" class="mt-2" />
        </div>

        <div class="flex gap-8">
            <label class="flex items-center gap-2 text-sm text-ink-600">
                <input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $product->is_featured)) class="border-ink-300 text-gold-600 focus:ring-gold-400">
                Featured on homepage
            </label>
            <label class="flex items-center gap-2 text-sm text-ink-600">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $product->exists ? $product->is_active : true)) class="border-ink-300 text-gold-600 focus:ring-gold-400">
                Active / visible in shop
            </label>
        </div>

        <div class="flex gap-4 pt-4">
            <button type="submit" class="btn-gold">{{ $product->exists ? 'Update Product' : 'Create Product' }}</button>
            <a href="{{ route('admin.products.index') }}" class="btn-gold-outline">Cancel</a>
        </div>
    </form>

</x-admin-layout>
