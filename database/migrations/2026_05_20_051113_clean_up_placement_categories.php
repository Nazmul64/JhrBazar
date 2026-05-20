<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use App\Models\Category;
use App\Models\Product;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Get placement options from config
        $placementSections = config('placement.frontend_sections');

        if (!$placementSections) {
            // Fallback list if config is not loaded yet in command line environment
            $placementSections = [
                'Accessories', 'Automotive', 'Baby & Kids', 'Baby Products', 'Bags', 'Bags & Travel',
                'Beauty', 'Books & Magazines', 'Books & Stationery', 'Clothing', 'Cosmetics',
                'Cosmetics & Beauty', 'Digital Products', 'Dining', 'Electrical Products', 'Electronics',
                'Fashion', 'Fashion & Clothing', 'Fitness', 'Food', 'Food & Grocery', 'Footwear',
                'Furniture', 'Gadgets & Accessories', 'Grocery', 'Grocery & Food', 'Health',
                'Health & Personal Care', 'Home', 'Home & Furniture', 'Home Decor', 'Hot Deals',
                'Innerwear', 'Jewelry', 'Jewelry & Accessories', 'Kitchen', 'Kitchen & Dining',
                'Panjabi', 'Personal Care', 'Pet Products', 'Service', 'Service Categories', 'Shirt',
                'Shoes', 'Shoes & Footwear', 'Sports', 'Sports & Fitness', 'T-Shirt', 'Test Category',
                "Toy's", 'Toys', 'Toys & Baby Products', 'Travel', "Women's Shopping",
                'কাপড় ড্রেস', 'কুর্তি ও গাউন', 'ঘড়ি', 'চশমা', 'জুতা', 'টুপি', 'থ্রি-পিস',
                'প্যান্ট', 'ফার্নিচার', 'বাচ্চাদের জামা', 'বুটিকস থ্রিপিস', 'বেডশীট', 'বোরকা'
            ];
        }

        // 1. Create a "General" category if it does not exist, to hold products belonging to deleted categories
        $generalCategory = Category::firstOrCreate(
            ['name' => 'General'],
            ['slug' => 'general', 'is_active' => true]
        );

        // 2. Find categories to delete
        $categoriesToDelete = Category::whereIn('name', $placementSections)->get();

        foreach ($categoriesToDelete as $category) {
            if ($category->id === $generalCategory->id) {
                continue;
            }

            // Move products to the "General" category
            Product::where('category_id', $category->id)->update([
                'category_id' => $generalCategory->id
            ]);

            \DB::table('seller_products')->where('category_id', $category->id)->update([
                'category_id' => $generalCategory->id
            ]);

            // Delete the category
            $category->delete();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No reverse operation as we are cleaning up seeded/obsolete DB data
    }
};
