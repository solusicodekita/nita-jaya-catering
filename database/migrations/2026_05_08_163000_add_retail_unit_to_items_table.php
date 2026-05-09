<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('items', function (Blueprint $blueprint) {
            $blueprint->string('retail_unit')->nullable()->after('unit');
            $blueprint->decimal('retail_conversion', 15, 4)->default(1)->after('retail_unit');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('items', function (Blueprint $blueprint) {
            $blueprint->dropColumn(['retail_unit', 'retail_conversion']);
        });
    }
};
