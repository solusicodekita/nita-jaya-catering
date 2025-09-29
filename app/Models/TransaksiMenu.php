<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransaksiMenu extends Model
{
    use HasFactory;
    protected $table = 'transaksi_menu';
    protected $guarded = [];
    public $timestamps = false;

    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
    
    // Relasi untuk mendapatkan item melalui stock transaction details
    public function items()
    {
        return $this->hasManyThrough(
            Item::class,
            StockTransactionDetail::class,
            'stock_transaction_id',
            'id',
            'stock_transaction_id',
            'item_id'
        );
    }

    public function stockTransaction()
    {
        return $this->belongsTo(StockTransaction::class);
    }
}
