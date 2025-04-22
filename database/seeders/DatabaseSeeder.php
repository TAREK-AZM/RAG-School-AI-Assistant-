<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run()
{
    // Disable foreign key checks
    // DB::statement('SET FOREIGN_KEY_CHECKS=0;');
    
    $this->call([
        UserSeeder::class,         // Must come first
        DocumentSeeder::class,
        DocumentEmbeddingSeeder::class,
    ]);
    
    // Re-enable foreign key checks
    // DB::statement('SET FOREIGN_KEY_CHECKS=1;');
}
}