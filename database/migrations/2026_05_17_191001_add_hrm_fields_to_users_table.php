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
        Schema::table('users', function (Blueprint $table) {
            $table->date('joining_date')->nullable();
            $table->decimal('salary', 10, 2)->nullable();
            $table->string('father_name')->nullable();
            $table->string('mother_name')->nullable();
            $table->string('father_nid')->nullable();
            $table->string('mother_nid')->nullable();
            $table->string('father_nid_copy')->nullable();
            $table->string('mother_nid_copy')->nullable();
            $table->text('address')->nullable();
            $table->string('district')->nullable();
            $table->string('thana')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'joining_date',
                'salary',
                'father_name',
                'mother_name',
                'father_nid',
                'mother_nid',
                'father_nid_copy',
                'mother_nid_copy',
                'address',
                'district',
                'thana'
            ]);
        });
    }
};
