<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('pointofsalepos', function (Blueprint $table) {
            if (!Schema::hasColumn('pointofsalepos', 'status')) {
                $table->enum('status', ['pending','confirmed','processing','shipped','delivered','cancelled'])->default('pending');
            }
            if (!Schema::hasColumn('pointofsalepos', 'staff_id')) {
                $table->unsignedBigInteger('staff_id')->nullable()->after('phone');
                $table->foreign('staff_id')->references('id')->on('users')->onDelete('set null');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pointofsalepos', function (Blueprint $table) {
            if (Schema::hasColumn('pointofsalepos', 'staff_id')) {
                $table->dropForeign(['staff_id']);
                $table->dropColumn('staff_id');
            }
            if (Schema::hasColumn('pointofsalepos', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
};
