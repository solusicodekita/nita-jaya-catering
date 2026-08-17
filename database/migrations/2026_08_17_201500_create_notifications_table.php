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
        if (!Schema::hasTable('notifications')) {
            Schema::create('notifications', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('role_target', 50)->nullable();
                $table->string('title');
                $table->text('message')->nullable();
                $table->string('url');
                $table->boolean('is_read')->default(false);
                $table->unsignedBigInteger('transaksi_pesanan_id')->nullable();
                $table->timestamps();

                $table->index('role_target');
                $table->index('user_id');
                $table->index('is_read');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
