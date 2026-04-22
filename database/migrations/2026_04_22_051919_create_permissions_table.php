<?php
// FILE 2: database/migrations/2024_01_01_000002_create_permissions_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('group');        // e.g. "Admin", "Shop"
            $table->string('name');         // e.g. "Shop", "Product"
            $table->string('key');          // e.g. "list", "create"
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permissions');
    }
};
