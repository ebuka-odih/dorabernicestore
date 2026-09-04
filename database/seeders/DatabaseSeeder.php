<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Dora Bernice',
            'email' => 'admin@dorabernicestore.com',
            'password' => bcrypt('password'),
            'is_admin' => true,
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Jane Customer',
            'email' => 'customer@example.com',
            'password' => bcrypt('password'),
            'is_admin' => false,
            'email_verified_at' => now(),
        ]);

        $categories = [
            ['name' => 'Rings', 'icon' => 'ring', 'sort_order' => 1, 'description' => 'Engagement, wedding, and statement rings finished by hand.'],
            ['name' => 'Necklaces', 'icon' => 'necklace', 'sort_order' => 2, 'description' => 'Pendants and chains in gold, silver, and platinum.'],
            ['name' => 'Earrings', 'icon' => 'earrings', 'sort_order' => 3, 'description' => 'Studs, hoops, and drops for every occasion.'],
            ['name' => 'Bracelets', 'icon' => 'bracelet', 'sort_order' => 4, 'description' => 'Bangles and tennis bracelets, stacked or solo.'],
        ];

        foreach ($categories as $data) {
            $category = Category::create([...$data, 'slug' => \Illuminate\Support\Str::slug($data['name'])]);
            $this->products($category);
        }
    }

    protected function products(Category $category): void
    {
        $catalog = match ($category->slug) {
            'rings' => [
                ['Solitaire Diamond Ring', 'ring', 1450, null, '18k White Gold', 'A classic round brilliant solitaire set in a hand-polished band.', true],
                ['Eternity Diamond Band', 'ring', 980, 850, '14k Yellow Gold', 'A full circle of pavé diamonds for everyday radiance.', false],
                ['Vintage Halo Ring', 'ring', 1120, null, 'Platinum', 'A center stone framed by a delicate halo of accent diamonds.', true],
                ['Twist Band Ring', 'ring', 420, null, '14k Rose Gold', 'A simple, sculptural twist — beautiful stacked or alone.', false],
            ],
            'necklaces' => [
                ['Pavé Disc Pendant', 'pendant', 690, 590, '14k White Gold', 'A circle of pavé diamonds suspended from a fine chain.', true],
                ['Layered Chain Necklace', 'necklace', 340, null, 'Sterling Silver', 'Three delicate chains layered at graduated lengths.', false],
                ['Solitaire Pendant', 'pendant', 560, null, '18k Yellow Gold', 'A single brilliant-cut diamond on a whisper-thin chain.', true],
                ['Bar Necklace', 'necklace', 260, null, '14k Gold Vermeil', 'A minimal polished bar for everyday layering.', false],
            ],
            'earrings' => [
                ['Diamond Stud Earrings', 'earrings', 780, null, '18k White Gold', 'Timeless round brilliant studs in a classic four-prong setting.', true],
                ['Butterfly Huggie Hoops', 'earrings', 250, 200, 'Sterling Silver', 'Playful pavé butterflies on a secure huggie hoop.', false],
                ['Drop Chandelier Earrings', 'earrings', 640, null, '14k Yellow Gold', 'Movement-rich drops for evening wear.', false],
                ['Classic Hoop Earrings', 'earrings', 310, null, '14k Gold', 'Polished hoops sized for everyday wear.', true],
            ],
            'bracelets' => [
                ['Diamond Tennis Bracelet', 'bracelet', 1890, 1650, '14k White Gold', 'A continuous line of matched round brilliants.', true],
                ['Chain Link Bracelet', 'bracelet', 410, null, '18k Gold Vermeil', 'A bold curb chain for stacking or wearing solo.', false],
                ['Bangle Set of Three', 'bracelet', 320, null, 'Sterling Silver', 'Three slender bangles finished with a soft polish.', false],
                ['Charm Bracelet', 'bracelet', 380, null, '14k Rose Gold', 'A delicate chain ready for your own charms.', false],
            ],
        };

        foreach ($catalog as $i => [$name, $icon, $price, $sale, $material, $description, $featured]) {
            Product::create([
                'category_id' => $category->id,
                'name' => $name,
                'slug' => \Illuminate\Support\Str::slug($name),
                'sku' => strtoupper(substr($category->slug, 0, 3)).'-'.(1000 + $i),
                'description' => $description,
                'material' => $material,
                'price' => $price,
                'sale_price' => $sale,
                'stock' => rand(4, 20),
                'icon' => $icon,
                'is_featured' => $featured,
                'is_active' => true,
            ]);
        }
    }
}
