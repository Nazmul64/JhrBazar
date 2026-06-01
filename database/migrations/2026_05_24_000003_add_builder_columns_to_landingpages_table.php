<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('landingpages', function (Blueprint $table) {
            if (!Schema::hasColumn('landingpages', 'additional_product_ids')) {
                $table->json('additional_product_ids')->nullable()->after('product_id');
            }
            if (!Schema::hasColumn('landingpages', 'style_template')) {
                $table->string('style_template')->nullable()->default('Template 3 (Dynamic Builder)')->after('additional_product_ids');
            }
            if (!Schema::hasColumn('landingpages', 'feature_image')) {
                $table->string('feature_image')->nullable()->after('image');
            }
            if (!Schema::hasColumn('landingpages', 'video_url')) {
                $table->string('video_url')->nullable()->after('feature_image');
            }
            if (!Schema::hasColumn('landingpages', 'checkout_image')) {
                $table->string('checkout_image')->nullable()->after('video_url');
            }
            if (!Schema::hasColumn('landingpages', 'bg_color')) {
                $table->string('bg_color')->nullable()->default('#ffffff')->after('checkout_image');
            }
            if (!Schema::hasColumn('landingpages', 'button_color')) {
                $table->string('button_color')->nullable()->default('#1e3a8a')->after('bg_color');
            }
            if (!Schema::hasColumn('landingpages', 'is_template')) {
                $table->boolean('is_template')->default(0)->after('status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('landingpages', function (Blueprint $table) {
            $table->dropColumn([
                'additional_product_ids',
                'style_template',
                'feature_image',
                'video_url',
                'checkout_image',
                'bg_color',
                'button_color',
                'is_template'
            ]);
        });
    }
};
