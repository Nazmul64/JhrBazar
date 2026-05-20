<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class UserCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [];

        foreach ($categories as $name) {
            Category::firstOrCreate(['name' => trim($name)]);
        }
    }
}
