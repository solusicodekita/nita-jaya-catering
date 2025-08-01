<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Stock;
use App\Models\StockTransaction;
use App\Models\StockTransactionDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockOutController extends Controller
{
    public function index(Request $request)
    {
        $query = StockTransaction::where('type', 'out')
            ->where(function($query) {
                $query->where('is_adjustment', false)
                      ->orWhereNull('is_adjustment');
            });

        if ($request->start_date && $request->end_date) {
            $start_date = date('Y-m-d 00:00:00', strtotime($request->start_date));
            $end_date = date('Y-m-d 23:59:59', strtotime($request->end_date));
            $query->whereBetween('date', [$start_date, $end_date]);
        } else {
            $start_date = date('Y-m-d 00:00:00');
            $end_date = date('Y-m-d 23:59:59');
            $query->whereBetween('date', [$start_date, $end_date]);
        }

        if ($request->item_name) {
            $query->whereHas('stockTransactionDetails', function($query) use ($request) {
                $query->whereHas('item', function($query) use ($request) {
                    $query->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($request->item_name) . '%']);
                });
            });
        }

        $model = $query->orderBy('id', 'desc')->get();
        return view('admin.stock_out.index', compact('model'));
    }

    public function create() {
        $item = Item::whereHas('stocks')->get();
        return view('admin.stock_out.create', compact('item'));
    }

    public function getHargaSatuan(Request $request)
    {
        $item = Item::find($request->item_id);
        $price = $item->price;
        $unit = $item->unit;
        return response()->json(['harga_satuan' => $price, 'unit' => $unit]);
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $request->validate([
                'item' => 'required|array',
                'item.*.item_id' => 'required|exists:items,id',
                'item.*.harga_satuan' => 'required|string',
                'item.*.warehouse_id' => 'required|exists:warehouses,id',
                'item.*.quantity' => 'required|string|min:1',
                'item.*.total_harga_item' => 'required|string',
                'item.*.description' => 'nullable|string',
                'total_harga_keseluruhan' => 'required|string',
            ]);

            $stock = StockTransaction::create([
                'type' => 'out',
                'total_harga_keseluruhan' => str_replace([',', '.'], ['.', ''], $request->total_harga_keseluruhan),
                'date' => date('Y-m-d H:i:s'),
                'created_by' => Auth::user()->id,
            ]);

            foreach ($request->item as $value) {
                StockTransactionDetail::create([
                    'stock_transaction_id' => $stock->id,
                    'item_id' => $value['item_id'],
                    'warehouse_id' => $value['warehouse_id'],
                    'quantity' => str_replace(',', '.', $value['quantity']),
                    'harga_satuan' => str_replace([',', '.'], ['.', ''], $value['harga_satuan']),
                    'total_harga' => str_replace([',', '.'], ['.', ''], $value['total_harga_item']),
                    'description' => $value['description'],
                    'created_by' => Auth::user()->id,
                    'updated_by' => Auth::user()->id,
                    'stok_sebelumnya' => Stock::liveStock($value['item_id'], $value['warehouse_id'])
                ]);
            }

            DB::commit();
            return redirect()->route('admin.out_stock.index')->with('success', 'Data berhasil disimpan');
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with('error', $th->getMessage());
        }
    }
    public function getWarehouse(Request $request)
    {
        $warehouse = Stock::select('warehouse_id')
            ->where('item_id', $request->item_id)
            ->groupBy('warehouse_id')
            ->get();

        $data = '';
        if (count($warehouse) == 0) {
            $data = '<option value="" disabled selected>-- Lokasi Item tidak ditemukan --</option>';
            return response()->json($data);
        }

        foreach ($warehouse as $row) {
            $data .= '<option value="'.$row->warehouse_id.'">'.$row->warehouse->name.'</option>';
        }
        return response()->json($data);
    }

    public function cekLiveStok(Request $request) {
        $jumlah = Stock::liveStock($request->item_id, $request->warehouse_id);
        return response()->json($jumlah);
    }
}
