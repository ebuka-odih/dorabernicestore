<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Support\ProductImages;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::query()
            ->with(['category', 'images'])
            ->when($request->filled('q'), fn ($q) => $q->where('name', 'like', '%'.$request->string('q').'%'))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();

        return view('admin.products.form', ['product' => new Product, 'categories' => $categories]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['slug'] = Str::slug($data['name']).'-'.Str::lower(Str::random(4));

        $product = Product::create($data);

        ProductImages::storeUploaded($product, $this->uploadedImages($request));

        return redirect()->route('admin.products.index')->with('status', 'Product created.');
    }

    public function edit(Product $product)
    {
        $categories = Category::orderBy('name')->get();
        $product->load('images');

        return view('admin.products.form', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $product->load('images');

        $data = $this->validated($request);

        $removeIds = collect($request->input('remove_images', []))
            ->map(fn ($id) => (int) $id)
            ->intersect($product->images->pluck('id')->all())
            ->values()
            ->all();

        $remaining = $product->images->count() - count($removeIds);
        $incoming = count($this->uploadedImages($request));

        if ($remaining + $incoming > ProductImages::MAX_IMAGES) {
            return back()
                ->withInput()
                ->withErrors(['images' => 'A product can have at most '.ProductImages::MAX_IMAGES.' images. Remove an existing image to add a new one.']);
        }

        $product->update($data);

        $primaryId = $request->input('primary_image_id') !== null
            ? (int) $request->input('primary_image_id')
            : null;

        ProductImages::sync($product, $this->uploadedImages($request), $removeIds, $primaryId);

        return redirect()->route('admin.products.index')->with('status', 'Product updated.');
    }

    public function destroy(Product $product)
    {
        $product->load('images');

        ProductImages::deleteAll($product);
        $product->delete();

        return back()->with('status', 'Product deleted.');
    }

    /** Uploaded files, filtered to valid uploads (empty file inputs yield nulls). */
    protected function uploadedImages(Request $request): array
    {
        return array_values(array_filter(
            (array) $request->file('images', []),
            fn ($file) => $file && $file->isValid()
        ));
    }

    protected function validated(Request $request): array
    {
        $product = $request->route('product');

        $request->validate([
            'images' => ['nullable', 'array', 'max:'.ProductImages::MAX_IMAGES],
            'images.*' => ['image', 'mimes:jpg,jpeg,png,webp,gif', 'max:5120'],
            'remove_images' => ['nullable', 'array'],
            'remove_images.*' => ['integer'],
            'primary_image_id' => ['nullable', 'integer'],
        ]);

        $data = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'sku' => ['required', 'string', 'max:100', 'unique:products,sku,'.($product?->id ?? 'NULL')],
            'description' => ['nullable', 'string', 'max:2000'],
            'material' => ['nullable', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'sale_price' => ['nullable', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'icon' => ['nullable', 'string', 'max:50'],
            'is_featured' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['is_featured'] = $request->boolean('is_featured');
        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}
