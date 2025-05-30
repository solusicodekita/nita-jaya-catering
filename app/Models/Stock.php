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

    public static function liveStock($item_id, $warehouse_id) {

        $model = Stock::where('item_id', $item_id)->where('warehouse_id', $warehouse_id)->latest('date_opname')->first();
        if (!$model) {
            return 0;
        }
        
        $barangMasuk = StockTransactionDetail::leftJoin('stock_transactions', 'stock_transaction_details.stock_transaction_id', '=', 'stock_transactions.id')
            ->where('stock_transaction_details.item_id', $model->item_id)
            ->where('stock_transaction_details.warehouse_id', $model->warehouse_id)
            ->where('stock_transactions.type', 'in')
            ->whereBetween('stock_transactions.date', [$model->date_opname, date('Y-m-d H:i:s')])
            ->sum('stock_transaction_details.quantity');
        
        $barangKeluar = StockTransactionDetail::leftJoin('stock_transactions', 'stock_transaction_details.stock_transaction_id', '=', 'stock_transactions.id')
            ->where('stock_transaction_details.item_id', $model->item_id)
            ->where('stock_transaction_details.warehouse_id', $model->warehouse_id)
            ->where('stock_transactions.type', 'out')
            ->whereBetween('stock_transactions.date', [$model->date_opname, date('Y-m-d H:i:s')])
            ->sum('stock_transaction_details.quantity');
            
        $jumlah = $model->final_stock + $barangMasuk - $barangKeluar;
        return $jumlah;
    }
}
