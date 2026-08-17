<?php

namespace App\Http\Controllers;

use App\Models\HistoryHarga;
use App\Models\Item;
use App\Models\Stock;
use App\Models\StockTransaction;
use App\Models\StockTransactionDetail;
use App\Helper\SettingHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StokInController extends Controller
{
    public function index(Request $request)
    {
        $query = StockTransaction::where('type', 'in')
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
        return view('admin.stock_in.index', compact('model'));
    }

    public function create()
    {
        $item = Item::get();
        $selected_item_id = request('item_id');
        return view('admin.stock_in.create', compact('item', 'selected_item_id'));
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
                'item.*.warehouse_id' => 'required|exists:warehouses,id',
                'item.*.harga_satuan' => 'required|string',
                'item.*.quantity' => 'required|string|min:1',
                'item.*.total_harga_item' => 'required|string',
                'item.*.description' => 'nullable|string',
                'total_harga_keseluruhan' => 'required|string',
            ]);

            $stock = StockTransaction::create([
                'type' => 'in',
                'total_harga_keseluruhan' => SettingHelper::parseIdNumber($request->total_harga_keseluruhan),
                'date' => date('Y-m-d H:i:s'),
                'created_by' => Auth::user()->id,
                'updated_by' => Auth::user()->id,
            ]);

            foreach ($request->item as $value) {
                $qtyParsed = SettingHelper::parseIdNumber($value['quantity']);
                $hargaParsed = SettingHelper::parseIdNumber($value['harga_satuan']);
                $totalParsed = SettingHelper::parseIdNumber($value['total_harga_item']);

                if ($totalParsed > 0 && $qtyParsed > 0) {
                    $calcHarga = $totalParsed / $qtyParsed;
                    if ($hargaParsed <= 0 || ($calcHarga >= 1000 && $hargaParsed < 1000)) {
                        $hargaParsed = $calcHarga;
                    }
                }

                $modDetail = StockTransactionDetail::create([
                    'stock_transaction_id' => $stock->id,
                    'item_id' => $value['item_id'],
                    'warehouse_id' => $value['warehouse_id'],
                    'quantity' => $qtyParsed,
                    'harga_satuan' => $hargaParsed,
                    'total_harga' => $totalParsed,
                    'description' => $value['description'],
                    'created_by' => Auth::user()->id,
                    'updated_by' => Auth::user()->id,
                    'stok_sebelumnya' => Stock::liveStock($value['item_id'], $value['warehouse_id'])
                ]);

                $modItem = Item::where('id', $value['item_id'])->first();
                $hargaAwal = $modItem->price;
                if ($modDetail->harga_satuan > 0) {
                    $modItem->price = $modDetail->harga_satuan;
                    $modItem->updated_by = Auth::user()->id;
                    $modItem->save();
                }

                if ($hargaAwal != $modItem->price) {
                    HistoryHarga::create([
                        'item_id' => $value['item_id'],
                        'warehouse_id' => $value['warehouse_id'],
                        'harga_awal' => $hargaAwal,
                        'harga_baru' => $modItem->price,
                        'created_by' => Auth::user()->id,
                        'updated_by' => Auth::user()->id,
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('admin.in_stock.index')->with('success', 'Data berhasil disimpan');
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    public function getWarehouse(Request $request)
    {
        $allWarehouses = \App\Models\Warehouse::orderBy('name', 'asc')->get();

        if ($allWarehouses->isEmpty()) {
            $data = '<option value="" disabled selected>-- Belum ada master lokasi/gudang --</option>';
            return response()->json($data);
        }

        $data = '<option value="" disabled selected>-- Pilih Lokasi --</option>';
        foreach ($allWarehouses as $w) {
            $data .= '<option value="' . $w->id . '">' . $w->name . '</option>';
        }

        return response()->json($data);
    }
}
