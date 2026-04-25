<?php
// database/migrations/xxxx_xx_xx_create_duplicateordersettings_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('duplicateordersettings', function (Blueprint $table) {
            $table->id();
            $table->boolean('allow_duplicate_orders')->default(false);
            $table->string('duplicate_check_type')->default('Product + IP + Phone');
            $table->unsignedInteger('duplicate_time_limit')->default(1); // hours
            $table->text('duplicate_check_message')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('duplicateordersettings');
    }
};
