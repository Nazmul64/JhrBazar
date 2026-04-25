<?php
// database/seeders/DuplicateordersettingSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Duplicateordersetting;

class DuplicateordersettingSeeder extends Seeder
{
    public function run(): void
    {
        if (Duplicateordersetting::count() === 0) {
            Duplicateordersetting::create([
                'allow_duplicate_orders'  => false,
                'duplicate_check_type'    => 'Product + IP + Phone',
                'duplicate_time_limit'    => 1,
                'duplicate_check_message' => 'Duplicate order detected. Please wait before placing the same order again.',
            ]);
        }
    }
}
