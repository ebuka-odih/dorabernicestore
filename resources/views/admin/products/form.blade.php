@php $icons = ['ring', 'necklace', 'earrings', 'bracelet', 'pendant', 'watch', 'gift', 'diamond']; @endphp

<x-admin-layout :title="$product->exists ? 'Edit Product' : 'Add Product'">

    <form action="{{ $product->exists ? route('admin.products.update', $product) : route('admin.products.store') }}" method="POST" enctype="multipart/form-data" class="max-w-3xl bg-white border border-ink-100 p-8 space-y-6">
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
            <x-input-label value="Product Images (up to 4)" />
            <p class="text-xs text-ink-500 mt-1">Upload square-friendly photos. Each image is center-cropped to a square on save so it displays consistently on product pages.</p>

            @if ($product->exists && $product->images->isNotEmpty())
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-3" id="existing-images">
                    @foreach ($product->images as $image)
                        <div class="border border-ink-200 p-2" data-image-id="{{ $image->id }}">
                            <img src="{{ $image->url }}" alt="Product image {{ $loop->iteration }}" class="w-full aspect-square object-cover bg-ink-50" />
                            <label class="mt-2 flex items-center gap-2 text-xs text-ink-600">
                                <input type="radio" name="primary_image_id" value="{{ $image->id }}" @checked($loop->first) class="border-ink-300 text-gold-600 focus:ring-gold-400">
                                Main image
                            </label>
                            <label class="mt-1 flex items-center gap-2 text-xs text-rose-600">
                                <input type="checkbox" name="remove_images[]" value="{{ $image->id }}" class="border-ink-300 text-rose-600 focus:ring-rose-400 remove-image">
                                Remove
                            </label>
                        </div>
                    @endforeach
                </div>
            @endif

            <input
                id="images"
                type="file"
                name="images[]"
                multiple
                accept="image/jpeg,image/png,image/webp,image/gif"
                data-max-total="4"
                data-existing="{{ $product->exists ? $product->images->count() : 0 }}"
                class="mt-3 block w-full text-sm text-ink-600 file:mr-4 file:py-2 file:px-4 file:border file:border-ink-200 file:text-xs file:uppercase file:tracking-widest2 file:bg-white file:text-ink-700 hover:file:border-gold-500"
            />
            <p class="text-xs text-ink-500 mt-1" id="images-hint">You can add up to {{ 4 - ($product->exists ? $product->images->count() : 0) }} more image(s).</p>
            <div id="image-previews" class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-3"></div>
            <x-input-error :messages="$errors->get('images')" class="mt-2" />
            <x-input-error :messages="$errors->get('images.*')" class="mt-2" />
        </div>

        <script>
            (function () {
                var input = document.getElementById('images');
                var previews = document.getElementById('image-previews');
                var hint = document.getElementById('images-hint');
                if (!input) return;

                var maxTotal = parseInt(input.dataset.maxTotal, 10) || 4;

                function removedCount() {
                    return document.querySelectorAll('.remove-image:checked').length;
                }

                function slotsLeft() {
                    var existing = (parseInt(input.dataset.existing, 10) || 0) - removedCount();
                    return Math.max(0, maxTotal - existing);
                }

                function refreshHint() {
                    if (hint) hint.textContent = 'You can add up to ' + slotsLeft() + ' more image(s).';
                }

                document.querySelectorAll('.remove-image').forEach(function (box) {
                    box.addEventListener('change', refreshHint);
                });

                input.addEventListener('change', function () {
                    previews.innerHTML = '';
                    var files = Array.prototype.slice.call(input.files || []);

                    if (files.length > slotsLeft()) {
                        alert('A product can have at most ' + maxTotal + ' images. You can add ' + slotsLeft() + ' more.');
                        input.value = '';
                        return;
                    }

                    files.forEach(function (file, i) {
                        var url = URL.createObjectURL(file);
                        var wrap = document.createElement('div');
                        wrap.className = 'border border-ink-200 p-2';
                        wrap.innerHTML =
                            '<div class="w-full aspect-square overflow-hidden bg-ink-50">' +
                            '<img src="' + url + '" alt="New upload ' + (i + 1) + '" class="w-full h-full object-cover" />' +
                            '</div>' +
                            '<p class="mt-1 text-xs text-ink-500 truncate">' + file.name + '</p>';
                        previews.appendChild(wrap);
                    });
                    refreshHint();
                });

                refreshHint();
            })();
        </script>

        <div>
            <x-input-label value="Icon (fallback when no images)" />
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
