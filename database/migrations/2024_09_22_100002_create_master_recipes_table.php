<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('master_recipes', function (Blueprint $table) {
            $table->id();
            $table->string('recipe_number', 50)->unique();
            $table->string('name');
            $table->foreignId('category_id')->constrained('recipe_categories');
            $table->string('property');
            $table->integer('yield_quantity');
            $table->string('yield_unit', 50);
            $table->decimal('total_cost', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('master_recipes');
    }
};
