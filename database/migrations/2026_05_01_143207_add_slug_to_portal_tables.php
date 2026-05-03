<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('portal_menus', function (Blueprint $table) {
            $table->string('slug')->nullable()->after('name');
        });

        Schema::table('portal_events', function (Blueprint $table) {
            $table->string('slug')->nullable()->after('title');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('portal_menus', function (Blueprint $table) {
            $table->dropColumn('slug');
        });

        Schema::table('portal_events', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
};
