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
        'updated_by',
        'address',
        'city',
        'event_place',
        'phone',
        'cs_name',
        'reference',
        'event_day',
        'porsi_total',
        'event_name',
        'delivery_time',
        'ready_time',
        'invitation_qty',
        'nuansa_theme',
        'notes',
        'free_note',
        'status',
        'verified_by',
        'verified_at',
        'dp1',
        'dp1_note',
        'dp2',
        'dp2_note',
        'dp3',
        'dp3_note',
        'lunas_note',
        'kekurangan'
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

    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
