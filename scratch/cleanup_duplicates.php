<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Include Laravel's autoloader and bootstrap the application
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$tables = ['categories', 'sub_categories', 'brands', 'products', 'seller_products'];

foreach ($tables as $table) {
    if (!Schema::hasTable($table)) {
        echo "Table $table does not exist. Skipping.\n";
        continue;
    }

    echo "Checking table: $table\n";
    
    // Find duplicates by name
    $duplicates = DB::table($table)
        ->select('name', DB::raw('COUNT(*) as count'), DB::raw('MIN(id) as keep_id'))
        ->groupBy('name')
        ->having('count', '>', 1)
        ->get();

    if ($duplicates->isEmpty()) {
        echo "No duplicates found in $table.\n";
        continue;
    }

    foreach ($duplicates as $dup) {
        echo "Found {$dup->count} entries for '{$dup->name}'. Keeping ID: {$dup->keep_id}\n";
        
        // Delete duplicates except the one we want to keep
        $deletedCount = DB::table($table)
            ->where('name', $dup->name)
            ->where('id', '!=', $dup->keep_id)
            ->delete();
            
        echo "Deleted $deletedCount duplicates for '{$dup->name}'.\n";
    }
    echo "---------------------------\n";
}

echo "Done cleanup.\n";
