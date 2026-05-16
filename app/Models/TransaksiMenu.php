<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransaksiMenu extends Model
{
    use HasFactory;
    protected $table = 'transaksi_menu';
    protected $guarded = [];
    public $timestamps = true;
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }

    public function stockTransaction()
    {
        return $this->belongsTo(StockTransaction::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
