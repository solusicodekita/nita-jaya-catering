<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockTransaction extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function stock()
    {
        return $this->belongsTo(Stock::class);
    }

    public function stockTransactionDetails()
    {
        return $this->hasMany(StockTransactionDetail::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function transaksiMenu()
    {
        return $this->hasMany(TransaksiMenu::class);
    }

}
