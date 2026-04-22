<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sociallink_lists', function (Blueprint $table) {
            $table->id();
            $table->string('name');          // Facebook, Instagram …
            $table->string('platform');      // facebook, instagram … (slug for icon)
            $table->string('link')->nullable();
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });

        // Seed the 8 default platforms
        DB::table('sociallink_lists')->insert([
            ['name' => 'Facebook',    'platform' => 'facebook',    'link' => null, 'is_active' => false, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'LinkedIn',    'platform' => 'linkedin',    'link' => null, 'is_active' => false, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Instagram',   'platform' => 'instagram',   'link' => null, 'is_active' => false, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'YouTube',     'platform' => 'youtube',     'link' => null, 'is_active' => false, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'WhatsApp',    'platform' => 'whatsapp',    'link' => null, 'is_active' => false, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Twitter',     'platform' => 'twitter',     'link' => null, 'is_active' => false, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Telegram',    'platform' => 'telegram',    'link' => null, 'is_active' => false, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Google Plus', 'platform' => 'google-plus', 'link' => null, 'is_active' => false, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('sociallink_lists');
    }
};
