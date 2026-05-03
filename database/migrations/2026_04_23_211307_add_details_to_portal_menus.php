<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDetailsToPortalMenus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('portal_menus', function (Blueprint $table) {
            $table->text('items')->nullable()->after('description');
            $table->string('price')->nullable()->after('items');
            $table->string('category')->nullable()->after('price');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('portal_menus', function (Blueprint $table) {
            $table->dropColumn(['items', 'price', 'category']);
        });
    }
}
