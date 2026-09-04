<x-admin-layout :title="'Categories'">

    <div class="flex justify-end mb-6">
        <a href="{{ route('admin.categories.create') }}" class="btn-gold">Add Category</a>
    </div>

    <div class="bg-white border border-ink-100">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-xs uppercase tracking-widest2 text-ink-400 border-b border-ink-100">
                    <th class="px-6 py-3">Icon</th>
                    <th class="px-6 py-3">Name</th>
                    <th class="px-6 py-3">Products</th>
                    <th class="px-6 py-3">Sort</th>
                    <th class="px-6 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-ink-100">
                @forelse ($categories as $category)
                    <tr class="hover:bg-ink-50">
                        <td class="px-6 py-4"><x-jewel-icon :icon="$category->icon" class="w-6 h-6 text-gold-600" /></td>
                        <td class="px-6 py-4 text-ink-800">{{ $category->name }}</td>
                        <td class="px-6 py-4 text-ink-500">{{ $category->products_count }}</td>
                        <td class="px-6 py-4 text-ink-500">{{ $category->sort_order }}</td>
                        <td class="px-6 py-4 text-right space-x-4">
                            <a href="{{ route('admin.categories.edit', $category) }}" class="text-gold-600 hover:underline">Edit</a>
                            <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="inline" onsubmit="return confirm('Delete this category?')">
                                @csrf
                                @method('DELETE')
                                <button class="text-rose-600 hover:underline">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-6 py-8 text-center text-ink-400">No categories yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

</x-admin-layout>
