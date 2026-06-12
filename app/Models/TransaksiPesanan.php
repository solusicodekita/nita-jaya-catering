<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransaksiPesanan extends Model
{
    use HasFactory;

    protected $table = 'transaksi_pesanans';

    protected $fillable = [
        'order_number',
        'customer_name',
        'event_date',
        'total_cost',
        'grand_total',
        'stock_transaction_id',
        'created_by',
        'updated_by'
    ];

    public function details()
    {
        return $this->hasMany(TransaksiPesananDetail::class, 'transaksi_pesanan_id');
    }

    public function stockTransaction()
    {
        return $this->belongsTo(StockTransaction::class, 'stock_transaction_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
