<x-admin-layout :title="'Products'">

    <div class="flex items-center justify-between mb-6 gap-4">
        <form class="flex-1 max-w-sm">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Search products..." class="w-full border-ink-200 focus:border-gold-500 focus:ring-gold-400 text-sm" />
        </form>
        <a href="{{ route('admin.products.create') }}" class="btn-gold shrink-0">Add Product</a>
    </div>

    <div class="bg-white border border-ink-100">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-xs uppercase tracking-widest2 text-ink-400 border-b border-ink-100">
                    <th class="px-6 py-3"></th>
                    <th class="px-6 py-3">Name</th>
                    <th class="px-6 py-3">Category</th>
                    <th class="px-6 py-3">Price</th>
                    <th class="px-6 py-3">Stock</th>
                    <th class="px-6 py-3">Status</th>
                    <th class="px-6 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-ink-100">
                @forelse ($products as $product)
                    <tr class="hover:bg-ink-50">
                        <td class="px-6 py-4"><x-jewel-icon :icon="$product->icon" class="w-6 h-6 text-gold-600" /></td>
                        <td class="px-6 py-4 text-ink-800">{{ $product->name }}</td>
                        <td class="px-6 py-4 text-ink-500">{{ $product->category->name }}</td>
                        <td class="px-6 py-4 text-ink-700">
                            ${{ number_format($product->price, 2) }}
                            @if ($product->onSale())<span class="text-gold-600"> / ${{ number_format($product->sale_price, 2) }}</span>@endif
                        </td>
                        <td class="px-6 py-4 {{ $product->stock === 0 ? 'text-rose-600' : 'text-ink-500' }}">{{ $product->stock }}</td>
                        <td class="px-6 py-4">
                            @if ($product->is_active)
                                <span class="text-xs uppercase px-2 py-1 bg-green-50 text-green-700">Active</span>
                            @else
                                <span class="text-xs uppercase px-2 py-1 bg-ink-100 text-ink-500">Hidden</span>
                            @endif
                            @if ($product->is_featured)
                                <span class="text-xs uppercase px-2 py-1 bg-gold-50 text-gold-700 ml-1">Featured</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right space-x-4">
                            <a href="{{ route('admin.products.edit', $product) }}" class="text-gold-600 hover:underline">Edit</a>
                            <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="inline" onsubmit="return confirm('Delete this product?')">
                                @csrf
                                @method('DELETE')
                                <button class="text-rose-600 hover:underline">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-6 py-8 text-center text-ink-400">No products yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $products->links() }}</div>

</x-admin-layout>
