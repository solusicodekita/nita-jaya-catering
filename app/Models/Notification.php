<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'user_id',
        'role_target',
        'title',
        'message',
        'url',
        'is_read',
        'transaksi_pesanan_id',
    ];

    public function transaksiPesanan()
    {
        return $this->belongsTo(TransaksiPesanan::class, 'transaksi_pesanan_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
