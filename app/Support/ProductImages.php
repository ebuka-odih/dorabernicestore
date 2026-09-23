<?php

namespace App\Support;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Handles product image uploads: validates the 4-image cap,
 * center-crops every upload to a square display rendition
 * (GD-based, no extra dependencies) and persists ProductImage rows.
 */
class ProductImages
{
    public const MAX_IMAGES = 4;

    /** Longest edge of the stored square rendition in pixels. */
    public const DISPLAY_SIZE = 1200;

    public static function disk(): \Illuminate\Filesystem\FilesystemAdapter
    {
        return Storage::disk('public');
    }

    /**
     * Append newly uploaded files to the product, respecting the cap.
     *
     * @param  array<int, UploadedFile>  $files
     */
    public static function storeUploaded(Product $product, array $files): void
    {
        $nextOrder = (int) ($product->images()->max('sort_order') ?? -1) + 1;

        foreach ($files as $file) {
            if ($product->images()->count() >= self::MAX_IMAGES) {
                break;
            }

            $product->images()->create([
                'path' => self::processAndStore($file, $product->id),
                'sort_order' => $nextOrder++,
            ]);
        }
    }

    /**
     * Apply removals, primary selection and new uploads from the admin form.
     *
     * @param  array<int, UploadedFile>  $newFiles
     * @param  array<int, int>  $removeIds
     */
    public static function sync(Product $product, array $newFiles, array $removeIds, ?int $primaryId): void
    {
        if (! empty($removeIds)) {
            $doomed = $product->images()->whereIn('id', $removeIds)->get();
            foreach ($doomed as $image) {
                self::disk()->delete($image->path);
                $image->delete();
            }
        }

        if ($primaryId) {
            $primary = $product->images()->whereKey($primaryId)->first();
            if ($primary) {
                $primary->update(['sort_order' => -1]);
                $rest = $product->images()->whereKeyNot($primaryId)->orderBy('sort_order')->get();
                $order = 0;
                $primary->update(['sort_order' => $order++]);
                foreach ($rest as $image) {
                    if ($image->sort_order !== $order) {
                        $image->update(['sort_order' => $order]);
                    }
                    $order++;
                }
            }
        }

        if (! empty($newFiles)) {
            $product->refresh();
            self::storeUploaded($product, $newFiles);
        }
    }

    /** Delete every stored file and row for the product. */
    public static function deleteAll(Product $product): void
    {
        foreach ($product->images as $image) {
            self::disk()->delete($image->path);
        }
        $product->images()->delete();
    }

    /**
     * Center-crop the upload to a square, downscale to DISPLAY_SIZE
     * and store it as a JPEG on the public disk. Returns the path.
     */
    public static function processAndStore(UploadedFile $file, int $productId): string
    {
        $data = file_get_contents($file->getRealPath());
        $source = imagecreatefromstring($data);

        if ($source === false) {
            // Not decodable by GD (e.g. an exotic variant) — store as-is.
            return $file->store("products/{$productId}", 'public');
        }

        $width = imagesx($source);
        $height = imagesy($source);
        $side = min($width, $height);
        $srcX = (int) (($width - $side) / 2);
        $srcY = (int) (($height - $side) / 2);

        $targetSide = min($side, self::DISPLAY_SIZE);
        $canvas = imagecreatetruecolor($targetSide, $targetSide);

        // White base so transparent PNG/WebP sources don't go black as JPEG.
        $white = imagecolorallocate($canvas, 255, 255, 255);
        imagefill($canvas, 0, 0, $white);

        imagecopyresampled($canvas, $source, 0, 0, $srcX, $srcY, $targetSide, $targetSide, $side, $side);

        ob_start();
        imagejpeg($canvas, null, 85);
        $jpeg = ob_get_clean();

        imagedestroy($source);
        imagedestroy($canvas);

        $path = "products/{$productId}/".Str::uuid()->toString().'.jpg';
        self::disk()->put($path, $jpeg);

        return $path;
    }
}
