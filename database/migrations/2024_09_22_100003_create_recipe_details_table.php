<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::disableForeignKeyConstraints();

        Schema::create('recipe_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recipe_id')->constrained('master_recipes')->onDelete('cascade');
            $table->foreignId('ingredient_id')->constrained('items');
            $table->decimal('quantity', 10, 2);
            $table->string('unit', 50);
            $table->decimal('price_per_unit', 15, 2);
            $table->decimal('total_cost', 15, 2);
            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();
    }

    public function down()
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('recipe_details');
        Schema::enableForeignKeyConstraints();
    }
};