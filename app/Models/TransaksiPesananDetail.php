<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransaksiPesananDetail extends Model
{
    use HasFactory;

    protected $table = 'transaksi_pesanan_details';

    protected $fillable = [
        'transaksi_pesanan_id',
        'menu_id',
        'qty_porsi',
        'subtotal_cost',
        'subtotal_price'
    ];

    public function pesanan()
    {
        return $this->belongsTo(TransaksiPesanan::class, 'transaksi_pesanan_id');
    }

    public function menu()
    {
        return $this->belongsTo(Menu::class, 'menu_id');
    }
}
