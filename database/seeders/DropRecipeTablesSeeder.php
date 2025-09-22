<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DropRecipeTablesSeeder extends Seeder
{
    public function run()
    {
        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        // Drop tables if they exist
        DB::statement('DROP TABLE IF EXISTS recipe_details');
        DB::statement('DROP TABLE IF EXISTS master_recipes');
        DB::statement('DROP TABLE IF EXISTS recipe_categories');

        // Enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
