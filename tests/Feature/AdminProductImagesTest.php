<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminProductImagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    public function test_admin_can_create_product_with_up_to_four_cropped_square_images(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $category = Category::create(['name' => 'Rings', 'slug' => 'rings']);

        $response = $this->actingAs($admin)->post(route('admin.products.store'), [
            'category_id' => $category->id,
            'name' => 'Gold Band',
            'sku' => 'GB-001',
            'price' => 199,
            'stock' => 5,
            'images' => [
                UploadedFile::fake()->image('one.jpg', 1600, 900),
                UploadedFile::fake()->image('two.png', 800, 1200),
            ],
        ]);

        $response->assertRedirect(route('admin.products.index'));

        $product = Product::where('sku', 'GB-001')->firstOrFail();
        $this->assertCount(2, $product->images);

        foreach ($product->images as $image) {
            Storage::disk('public')->assertExists($image->path);
            [$width, $height] = getimagesize(Storage::disk('public')->path($image->path));
            $this->assertSame($width, $height, 'Stored rendition must be square-cropped.');
            $this->assertLessThanOrEqual(1200, $width);
        }
    }

    public function test_more_than_four_images_are_rejected(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $category = Category::create(['name' => 'Rings', 'slug' => 'rings']);

        $files = array_map(
            fn ($i) => UploadedFile::fake()->image("img{$i}.jpg", 600, 600),
            range(1, 5)
        );

        $this->actingAs($admin)->post(route('admin.products.store'), [
            'category_id' => $category->id,
            'name' => 'Gold Band',
            'sku' => 'GB-001',
            'price' => 199,
            'stock' => 5,
            'images' => $files,
        ])->assertSessionHasErrors('images');

        $this->assertDatabaseMissing('products', ['sku' => 'GB-001']);
    }

    public function test_admin_can_remove_an_image_add_another_and_pick_the_main_image(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $product = $this->productWithImages(2);
        $doomed = $product->images->first();
        $keeper = $product->images->last();

        $this->actingAs($admin)->put(route('admin.products.update', $product), [
            'category_id' => $product->category_id,
            'name' => $product->name,
            'sku' => $product->sku,
            'price' => 199,
            'stock' => 5,
            'remove_images' => [$doomed->id],
            'primary_image_id' => $keeper->id,
            'images' => [UploadedFile::fake()->image('new.jpg', 700, 700)],
        ])->assertRedirect(route('admin.products.index'));

        $product->refresh();
        $this->assertCount(2, $product->images);
        $this->assertSame($keeper->id, $product->images->first()->id);
        $this->assertDatabaseMissing('product_images', ['id' => $doomed->id]);
        Storage::disk('public')->assertMissing($doomed->path);
    }

    public function test_update_beyond_four_images_is_rejected(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $product = $this->productWithImages(4);

        $this->actingAs($admin)->put(route('admin.products.update', $product), [
            'category_id' => $product->category_id,
            'name' => $product->name,
            'sku' => $product->sku,
            'price' => 199,
            'stock' => 5,
            'images' => [UploadedFile::fake()->image('extra.jpg', 600, 600)],
        ])->assertSessionHasErrors('images');

        $this->assertCount(4, $product->fresh()->images);
    }

    public function test_product_page_shows_gallery_and_viewer_when_images_exist(): void
    {
        $product = $this->productWithImages(3);

        $response = $this->get(route('product.show', $product));

        $response->assertOk();
        foreach ($product->images as $image) {
            $response->assertSee($image->url, false);
        }
        $response->assertSee('Product image viewer', false);
    }

    public function test_product_page_falls_back_to_icon_without_images(): void
    {
        $category = Category::create(['name' => 'Rings', 'slug' => 'rings']);
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Plain Band',
            'slug' => 'plain-band',
            'sku' => 'PB-001',
            'price' => 99,
            'stock' => 3,
            'icon' => 'ring',
            'is_active' => true,
        ]);

        $this->get(route('product.show', $product))
            ->assertOk()
            ->assertDontSee('Product image viewer', false);
    }

    public function test_admin_form_shows_image_upload_field_and_existing_images(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $product = $this->productWithImages(2);

        $this->actingAs($admin)->get(route('admin.products.create'))
            ->assertOk()
            ->assertSee('name="images[]"', false);

        $this->actingAs($admin)->get(route('admin.products.edit', $product))
            ->assertOk()
            ->assertSee('name="images[]"', false)
            ->assertSee('name="remove_images[]"', false)
            ->assertSee('name="primary_image_id"', false)
            ->assertSee($product->images->first()->url, false);
    }

    public function test_deleting_product_removes_its_image_files(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $product = $this->productWithImages(2);
        $paths = $product->images->pluck('path')->all();

        $this->actingAs($admin)->delete(route('admin.products.destroy', $product))
            ->assertRedirect();

        foreach ($paths as $path) {
            Storage::disk('public')->assertMissing($path);
        }
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

    protected function productWithImages(int $count): Product
    {
        $category = Category::create(['name' => 'Rings', 'slug' => 'rings-'.$count.random_int(1000, 9999)]);
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Imaged Ring',
            'slug' => 'imaged-ring-'.$category->id,
            'sku' => 'IR-'.$category->id,
            'price' => 199,
            'stock' => 5,
            'icon' => 'ring',
            'is_active' => true,
        ]);

        $admin = User::factory()->create(['is_admin' => true]);

        // Upload through the real HTTP stack so files are cropped/stored for real.
        $files = array_map(
            fn ($i) => UploadedFile::fake()->image("seed{$i}.jpg", 900, 700),
            range(1, $count)
        );

        $this->actingAs($admin)->put(route('admin.products.update', $product), [
            'category_id' => $category->id,
            'name' => $product->name,
            'sku' => $product->sku,
            'price' => 199,
            'stock' => 5,
            'is_active' => 1,
            'images' => $files,
        ])->assertRedirect(route('admin.products.index'));

        return $product->fresh()->load('images');
    }
}
