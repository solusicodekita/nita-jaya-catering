<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function transactions()
    {
        return $this->hasMany(StockTransaction::class);
    }

    public static function liveStock($item_id, $warehouse_id, $until_date = null) {

        $model = Stock::where('item_id', $item_id)->where('warehouse_id', $warehouse_id);
        
        if ($until_date) {
            $model = $model->where('date_opname', '<=', $until_date);
        }
        
        $model = $model->latest('date_opname')->first();
        
        if (!$model) {
            return 0;
        }
        
        $until_date = $until_date ?: now()->toDateTimeString();
        
        $barangMasuk = StockTransactionDetail::leftJoin('stock_transactions', 'stock_transaction_details.stock_transaction_id', '=', 'stock_transactions.id')
            ->where('stock_transaction_details.item_id', $model->item_id)
            ->where('stock_transaction_details.warehouse_id', $model->warehouse_id)
            ->where('stock_transactions.type', 'in')
            ->where('stock_transactions.date', '>=', $model->date_opname)
            ->where('stock_transactions.date', '<=', $until_date);
            
        $barangMasuk = $barangMasuk->where(function ($query) {
            $query->where('stock_transactions.is_adjustment', 0)->orWhere('stock_transactions.is_adjustment', null)
                    ->orWhere(function ($query) {
                        $query->where('stock_transactions.is_adjustment', 1)
                            ->where('stock_transactions.is_verifikasi_adjustment', 1);
                    });
        })->sum('stock_transaction_details.quantity');
        
        $barangKeluar = StockTransactionDetail::leftJoin('stock_transactions', 'stock_transaction_details.stock_transaction_id', '=', 'stock_transactions.id')
            ->where('stock_transaction_details.item_id', $model->item_id)
            ->where('stock_transaction_details.warehouse_id', $model->warehouse_id)
            ->where('stock_transactions.type', 'out')
            ->where('stock_transactions.date', '>=', $model->date_opname)
            ->where('stock_transactions.date', '<=', $until_date);

        $barangKeluar = $barangKeluar->where(function ($query) {
            $query->where('stock_transactions.is_adjustment', 0)->orWhere('stock_transactions.is_adjustment', null)
                  ->orWhere(function ($query) {
                      $query->where('stock_transactions.is_adjustment', 1)
                            ->where('stock_transactions.is_verifikasi_adjustment', 1);
                  });
        })->sum('stock_transaction_details.quantity');
            
        $jumlah = $model->final_stock + $barangMasuk - $barangKeluar;
        return $jumlah;
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
