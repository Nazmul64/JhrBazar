<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->foreignId('shop_id')->nullable()->after('product_type')->constrained('shops')->onDelete('cascade');
        });

        // Populate existing reviews
        $reviews = DB::table('reviews')->get();
        foreach ($reviews as $review) {
            $shopId = null;
            if ($review->product_type === 'product' || $review->product_type === 'admin' || $review->product_type === 'digital_admin') {
                $productTable = ($review->product_type === 'digital_admin') ? 'digital_products' : 'products';
                $product = DB::table($productTable)->where('id', $review->product_id)->first();
                $shopId = $product->shop_id ?? null;
            } elseif ($review->product_type === 'seller' || $review->product_type === 'seller_product' || $review->product_type === 'digital_seller') {
                $productTable = ($review->product_type === 'digital_seller') ? 'seller_digital_products' : 'seller_products';
                $product = DB::table($productTable)->where('id', $review->product_id)->first();
                if ($product) {
                    $shop = DB::table('shops')->where('user_id', $product->seller_id)->first();
                    $shopId = $shop->id ?? null;
                }
            }

            if ($shopId) {
                DB::table('reviews')->where('id', $review->id)->update(['shop_id' => $shopId]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropForeign(['shop_id']);
            $table->dropColumn('shop_id');
        });
    }
};
