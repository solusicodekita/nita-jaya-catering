<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Stock;
use App\Models\StockTransaction;
use App\Models\StockTransactionDetail;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FixingMutasiController extends Controller
{
    public function index()
    {
        $items = Item::orderBy('name', 'asc')->get();
        $warehouses = Warehouse::orderBy('name', 'asc')->get();
        return view('admin.fixing-mutasi.index', compact('items', 'warehouses'));
    }

    public function recalculate(Request $request)
    {
        $itemId = $request->item_id;
        $warehouseId = $request->warehouse_id;

        DB::beginTransaction();
        try {
            // Get all transactions for this item and warehouse, ordered by date
            $details = StockTransactionDetail::join('stock_transactions', 'stock_transaction_details.stock_transaction_id', '=', 'stock_transactions.id')
                ->where('item_id', $itemId)
                ->where('warehouse_id', $warehouseId)
                ->orderBy('stock_transactions.date', 'asc')
                ->orderBy('stock_transaction_details.id', 'asc')
                ->select('stock_transaction_details.*', 'stock_transactions.type', 'stock_transactions.date')
                ->get();

            foreach ($details as $detail) {
                // Calculate stock BEFORE this transaction
                $stockBefore = Stock::liveStock($itemId, $warehouseId, $detail->date);
                
                // Wait, liveStock includes transactions UP TO the given date.
                // If there are multiple transactions on the same second, this might be tricky.
                // Actually, liveStock should exclude the CURRENT transaction if we want "stock before".
                
                // Let's refine the logic: 
                // Stock before = Opname before this date + Sum(in) before this transaction - Sum(out) before this transaction.
                
                $calculatedStockBefore = $this->calculateStockAt($itemId, $warehouseId, $detail->date, $detail->id);
                
                $detail->stok_sebelumnya = $calculatedStockBefore;
                $detail->save();
            }

            DB::commit();
            return redirect()->back()->with('success', 'Berhasil melakukan kalkulasi ulang mutasi stok.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    private function calculateStockAt($itemId, $warehouseId, $date, $detailId)
    {
        $model = Stock::where('item_id', $itemId)
            ->where('warehouse_id', $warehouseId)
            ->where('date_opname', '<=', $date)
            ->latest('date_opname')
            ->first();

        if (!$model) return 0;

        $in = StockTransactionDetail::join('stock_transactions', 'stock_transaction_details.stock_transaction_id', '=', 'stock_transactions.id')
            ->where('item_id', $itemId)
            ->where('warehouse_id', $warehouseId)
            ->where('type', 'in')
            ->where('date', '>=', $model->date_opname)
            ->where(function($q) use ($date, $detailId) {
                $q->where('date', '<', $date)
                  ->orWhere(function($q2) use ($date, $detailId) {
                      $q2->where('date', '=', $date)->where('stock_transaction_details.id', '<', $detailId);
                  });
            })->sum('quantity');

        $out = StockTransactionDetail::join('stock_transactions', 'stock_transaction_details.stock_transaction_id', '=', 'stock_transactions.id')
            ->where('item_id', $itemId)
            ->where('warehouse_id', $warehouseId)
            ->where('type', 'out')
            ->where('date', '>=', $model->date_opname)
            ->where(function($q) use ($date, $detailId) {
                $q->where('date', '<', $date)
                  ->orWhere(function($q2) use ($date, $detailId) {
                      $q2->where('date', '=', $date)->where('stock_transaction_details.id', '<', $detailId);
                  });
            })->sum('quantity');

        return $model->final_stock + $in - $out;
    }

    public function fixOpname(Request $request)
    {
        $itemId = $request->item_id;
        $warehouseId = $request->warehouse_id;
        $date = $request->date; // e.g. '2026-04-30 23:59:59'
        $newFinalStock = $request->final_stock;

        $stock = Stock::where('item_id', $itemId)
            ->where('warehouse_id', $warehouseId)
            ->where('date_opname', $date)
            ->first();

        if ($stock) {
            $stock->final_stock = $newFinalStock;
            $stock->save();
            return redirect()->back()->with('success', 'Berhasil memperbaiki data opname.');
        }

        return redirect()->back()->with('error', 'Data opname tidak ditemukan untuk tanggal: ' . $date);
    }

    public function ledger(Request $request)
    {
        $itemId = $request->item_id;
        $warehouseId = $request->warehouse_id;

        if (!$itemId || !$warehouseId) {
            return redirect()->route('admin.fixing-mutasi.index')->with('error', 'Item dan Lokasi harus dipilih.');
        }

        $item = Item::find($itemId);
        $warehouse = Warehouse::find($warehouseId);

        $opname = Stock::where('item_id', $itemId)
            ->where('warehouse_id', $warehouseId)
            ->latest('date_opname')
            ->first();

        if (!$opname) {
            return redirect()->back()->with('error', 'Data opname tidak ditemukan untuk item ini.');
        }

        $details = StockTransactionDetail::join('stock_transactions', 'stock_transaction_details.stock_transaction_id', '=', 'stock_transactions.id')
            ->where('item_id', $itemId)
            ->where('warehouse_id', $warehouseId)
            ->where('stock_transactions.date', '>=', $opname->date_opname)
            ->orderBy('stock_transactions.date', 'asc')
            ->orderBy('stock_transaction_details.id', 'asc')
            ->select('stock_transaction_details.*', 'stock_transactions.type', 'stock_transactions.date', 'stock_transactions.id as tx_id')
            ->get();

        $runningBalance = $opname->final_stock;
        $ledger = [];
        foreach ($details as $detail) {
            $prevBalance = $runningBalance;
            if ($detail->type == 'in') {
                $runningBalance += $detail->quantity;
            } else {
                $runningBalance -= $detail->quantity;
            }
            $ledger[] = [
                'date' => $detail->date,
                'tx_id' => $detail->tx_id,
                'type' => $detail->type,
                'qty' => $detail->quantity,
                'stok_sebelumnya_recorded' => $detail->stok_sebelumnya,
                'expected_before' => $prevBalance,
                'running_balance' => $runningBalance,
            ];
        }

        return view('admin.fixing-mutasi.ledger', compact('ledger', 'item', 'warehouse', 'opname'));
    }

    public function getLatestOpname(Request $request)
    {
        $opname = Stock::where('item_id', $request->item_id)
            ->where('warehouse_id', $request->warehouse_id)
            ->latest('date_opname')
            ->first();

        return response()->json([
            'date' => $opname ? $opname->date_opname : null
        ]);
    }
}
