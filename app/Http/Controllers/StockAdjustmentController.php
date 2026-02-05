<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Stock;
use App\Models\StockTransaction;
use App\Models\StockTransactionDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockAdjustmentController extends Controller
{
    public function index()
    {
        $model = StockTransactionDetail::with(['stockTransaction.verifikasiBy'])->whereHas('stockTransaction', function ($query) {
            $query->where('is_adjustment', true);
        })->orderBy('id', 'desc')->get();
        return view('admin.stock_adjustment.index', compact('model'));
    }

    public function create()
    {
        $item = Item::whereHas('stocks')->get();
        return view('admin.stock_adjustment.create', compact('item'));
    }

    public function cekJumlahTerakhir(Request $request)
    {
        $item_id = $request->item_id;
        $warehouse_id = $request->warehouse_id;
        $stokAkhir = Stock::liveStock($item_id, $warehouse_id);
        if ($stokAkhir) {
            $data = [
                'status' => 1,
                'stokAkhir' => $stokAkhir,
            ];
        } else {
            $data = [
                'status' => 0,
                'stokAkhir' => 0,
            ];
        }
        return response()->json($data);
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
            $data .= '<option value="' . $row->warehouse_id . '">' . $row->warehouse->name . '</option>';
        }
        return response()->json($data);
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $request->validate([
                'item_id' => 'required',
                'warehouse_id' => 'required',
                'jumlah_adjustment' => 'required|numeric',
                'tipe_adjustment' => 'required',
                'kategori_adjustment' => 'required|in:stok,qty,pengembalian',
                'alasan_adjustment' => 'required',
            ]);
    
            $stock = new StockTransaction();
            $stock->type = $request->tipe_adjustment;
            $stock->is_adjustment = true;
            $stock->kategori_adjustment = $request->kategori_adjustment;
            $stock->alasan_adjustment = $request->alasan_adjustment;
            $stock->date = now();
            $stock->created_by = Auth::user()->id;
            $stock->updated_by = Auth::user()->id;
            $stock->save();

            $modItem = Item::find($request->item_id);

            StockTransactionDetail::create([
                'item_id' => $request->item_id,
                'warehouse_id' => $request->warehouse_id,
                'quantity' => $request->jumlah_adjustment,
                'harga_satuan' => $modItem->price,
                'total_harga' => $request->jumlah_adjustment * $modItem->price,
                'description' => $request->alasan_adjustment,
                'created_by' => Auth::user()->id,
                'updated_by' => Auth::user()->id,
                'stock_transaction_id' => $stock->id,
            ]);

            DB::commit();
            return redirect()->route('admin.adjustment_stock.index')->with('success', 'Data berhasil disimpan');
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menyimpan data');
        }
    }

    public function edit($id)
    {
        $stockTransactionDetail = StockTransactionDetail::with('stockTransaction')->findOrFail($id);
        
        // Cek apakah sudah diverifikasi
        if ($stockTransactionDetail->stockTransaction->is_verifikasi_adjustment == 1) {
            return redirect()->route('admin.adjustment_stock.index')
                ->with('error', 'Data yang sudah diverifikasi tidak dapat diedit');
        }

        $item = Item::whereHas('stocks')->get();
        return view('admin.stock_adjustment.edit', compact('stockTransactionDetail', 'item'));
    }

    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            
            $stockTransactionDetail = StockTransactionDetail::with('stockTransaction')->findOrFail($id);
            
            // Cek apakah sudah diverifikasi
            if ($stockTransactionDetail->stockTransaction->is_verifikasi_adjustment == 1) {
                return redirect()->route('admin.adjustment_stock.index')
                    ->with('error', 'Data yang sudah diverifikasi tidak dapat diedit');
            }

            $request->validate([
                'item_id' => 'required',
                'warehouse_id' => 'required',
                'jumlah_adjustment' => 'required|numeric',
                'tipe_adjustment' => 'required',
                'kategori_adjustment' => 'required|in:stok,qty,pengembalian',
                'alasan_adjustment' => 'required',
            ]);

            $stockTransaction = $stockTransactionDetail->stockTransaction;
            $stockTransaction->type = $request->tipe_adjustment;
            $stockTransaction->kategori_adjustment = $request->kategori_adjustment;
            $stockTransaction->alasan_adjustment = $request->alasan_adjustment;
            $stockTransaction->updated_by = Auth::user()->id;
            $stockTransaction->save();

            $modItem = Item::find($request->item_id);

            $stockTransactionDetail->item_id = $request->item_id;
            $stockTransactionDetail->warehouse_id = $request->warehouse_id;
            $stockTransactionDetail->quantity = $request->jumlah_adjustment;
            $stockTransactionDetail->harga_satuan = $modItem->price;
            $stockTransactionDetail->total_harga = $request->jumlah_adjustment * $modItem->price;
            $stockTransactionDetail->description = $request->alasan_adjustment;
            $stockTransactionDetail->updated_by = Auth::user()->id;
            $stockTransactionDetail->save();

            DB::commit();
            return redirect()->route('admin.adjustment_stock.index')->with('success', 'Data berhasil diperbarui');
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal memperbarui data: ' . $th->getMessage());
        }
    }

    public function verifikasi(Request $request)
    {
        try {
            // Cek apakah user adalah supervisor
            if (!Auth::check() || Auth::user()->is_supervisor != 1) {
                return response()->json([
                    'status' => 0,
                    'message' => 'Anda tidak memiliki akses untuk melakukan verifikasi. Hanya supervisor yang dapat melakukan verifikasi.',
                ], 403);
            }

            // ID yang dikirim adalah ID dari StockTransactionDetail
            $stockTransactionDetail = StockTransactionDetail::find($request->id);
            if (!$stockTransactionDetail) {
                return response()->json([
                    'status' => 0,
                    'message' => 'Data tidak ditemukan',
                ], 404);
            }

            // Cek apakah sudah pernah diverifikasi
            $stockTransaction = $stockTransactionDetail->stockTransaction;
            if ($stockTransaction->is_verifikasi_adjustment == 1) {
                return response()->json([
                    'status' => 0,
                    'message' => 'Data ini sudah pernah diverifikasi',
                ], 400);
            }

            $stockTransaction->is_verifikasi_adjustment = 1;
            $stockTransaction->tanggal_verifikasi_adjusment = now();
            $stockTransaction->verifikasi_by = Auth::user()->id;
            $stockTransaction->save();
            
            $data = [
                'status' => 1,
                'message' => 'Data berhasil disimpan',
            ];
            return response()->json($data);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 0,
                'message' => 'Gagal memverifikasi data: ' . $th->getMessage(),
            ], 500);
        }
    }
}
