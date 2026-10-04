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
        if (Schema::hasTable('contacts')) {
            Schema::table('contacts', function (Blueprint $table) {
                if (!Schema::hasColumn('contacts', 'contact_image')) {
                    $table->string('contact_image')->nullable()->after('email_address');
                }
                if (!Schema::hasColumn('contacts', 'map_embed_code')) {
                    $table->longText('map_embed_code')->nullable()->after('contact_image');
                }
            });
        }

        if (!Schema::hasTable('contact_messages')) {
            Schema::create('contact_messages', function (Blueprint $table) {
                $table->id();
                $table->string('full_name')->nullable();
                $table->string('phone_number')->nullable();
                $table->string('subject')->nullable();
                $table->text('message')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('contacts')) {
            Schema::table('contacts', function (Blueprint $table) {
                if (Schema::hasColumn('contacts', 'map_embed_code')) {
                    $table->dropColumn('map_embed_code');
                }
                if (Schema::hasColumn('contacts', 'contact_image')) {
                    $table->dropColumn('contact_image');
                }
            });
        }

        Schema::dropIfExists('contact_messages');
    }
};
