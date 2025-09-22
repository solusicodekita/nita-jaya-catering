<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DropRecipeDetailsSeeder extends Seeder
{
    public function run()
    {
        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        // Drop table if exists
        DB::statement('DROP TABLE IF EXISTS recipe_details');

        // Enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
