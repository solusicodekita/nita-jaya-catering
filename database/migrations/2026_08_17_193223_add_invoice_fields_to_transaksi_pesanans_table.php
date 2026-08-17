<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInvoiceFieldsToTransaksiPesanansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transaksi_pesanans', function (Blueprint $table) {
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('event_place')->nullable();
            $table->string('phone')->nullable();
            $table->string('cs_name')->nullable();
            $table->string('reference')->nullable();
            $table->string('event_day')->nullable();
            $table->string('porsi_total')->nullable();
            $table->string('event_name')->nullable();
            $table->string('delivery_time')->nullable();
            $table->string('ready_time')->nullable();
            $table->string('invitation_qty')->nullable();
            $table->string('nuansa_theme')->nullable();
            $table->text('notes')->nullable();
            $table->string('status')->default('PESANAN')->nullable();
            $table->decimal('dp1', 15, 2)->nullable();
            $table->string('dp1_note')->nullable();
            $table->decimal('dp2', 15, 2)->nullable();
            $table->string('dp2_note')->nullable();
            $table->decimal('dp3', 15, 2)->nullable();
            $table->string('dp3_note')->nullable();
            $table->string('lunas_note')->nullable();
            $table->decimal('kekurangan', 15, 2)->nullable();
        });
    }

    public function down()
    {
        Schema::table('transaksi_pesanans', function (Blueprint $table) {
            $table->dropColumn([
                'address', 'city', 'event_place', 'phone', 'cs_name', 'reference',
                'event_day', 'porsi_total', 'event_name', 'delivery_time', 'ready_time',
                'invitation_qty', 'nuansa_theme', 'notes', 'free_note',
                'dp1', 'dp1_note', 'dp2', 'dp2_note', 'dp3', 'dp3_note',
                'lunas_note', 'kekurangan'
            ]);
        });
    }
}
