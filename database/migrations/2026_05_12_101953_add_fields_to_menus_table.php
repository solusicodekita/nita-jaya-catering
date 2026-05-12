<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToMenusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('menus', function (Blueprint $table) {
            $table->string('recipe_number')->nullable()->after('id');
            $table->unsignedBigInteger('category_id')->nullable()->after('name');
            $table->string('yield')->nullable()->after('description');
            $table->decimal('cost_factor', 8, 2)->default(20.00)->after('yield');
            $table->decimal('profit_margin', 8, 2)->default(30.00)->after('cost_factor');

            $table->foreign('category_id')->references('id')->on('categories')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('menus', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropColumn(['recipe_number', 'category_id', 'yield', 'cost_factor', 'profit_margin']);
        });
    }
}
