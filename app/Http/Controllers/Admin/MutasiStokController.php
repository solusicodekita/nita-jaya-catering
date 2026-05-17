<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Stock;
use App\Models\StockTransaction;
use App\Models\StockTransactionDetail;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\ActivityLog;

class MutasiStokController extends Controller
{
    public function index(Request $request)
    {
        // Get all IN and OUT transactions that are part of a mutation
        $query = StockTransactionDetail::with(['stockTransaction', 'item', 'warehouse', 'createdBy'])
            ->whereHas('stockTransaction', function ($q) {
                $q->where('alasan_adjustment', 'LIKE', 'Mutasi Stok%')
                  ->whereNull('is_adjustment'); // Ensure it's not marked as adjustment to avoid confusing the audit
            });

        if ($request->start_date && $request->end_date) {
            $start_date = date('Y-m-d 00:00:00', strtotime($request->start_date));
            $end_date = date('Y-m-d 23:59:59', strtotime($request->end_date));
            $query->whereHas('stockTransaction', function ($q) use ($start_date, $end_date) {
                $q->whereBetween('date', [$start_date, $end_date]);
            });
        }

        if ($request->item_name) {
            $query->whereHas('item', function($q) use ($request) {
                $q->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($request->item_name) . '%']);
            });
        }

        // We only want to show the 'OUT' part as the primary record, to avoid double listing
        $query->whereHas('stockTransaction', function ($q) {
            $q->where('type', 'out');
        });

        $model = $query->orderBy('id', 'desc')->paginate(50);
        
        return view('admin.mutasi_stok.index', compact('model'));
    }

    public function create()
    {
        $warehouses = Warehouse::orderBy('name', 'asc')->get();
        return view('admin.mutasi_stok.create', compact('warehouses'));
    }

    public function getItemsByWarehouse(Request $request)
    {
        $warehouseId = $request->warehouse_id;
        
        $itemIds = Stock::where('warehouse_id', $warehouseId)
            ->pluck('item_id')
            ->unique();
            
        $items = Item::whereIn('id', $itemIds)->where('is_active', true)->orderBy('name', 'asc')->get();
        
        $data = '<option value="" disabled selected>-- Pilih Item --</option>';
        foreach ($items as $item) {
            $retailUnit = $item->retail_unit ?? '';
            $retailConv = $item->retail_conversion ?? 1;
            
            $data .= '<option value="' . $item->id . '" data-unit="' . $item->unit . '" data-retail="' . $retailUnit . '" data-conv="' . $retailConv . '">' . $item->name . ' (' . $item->unit . ')</option>';
        }
        
        return response()->json($data);
    }

    public function checkStock(Request $request)
    {
        $item_id = $request->item_id;
        $warehouse_id = $request->warehouse_id;
        
        $stokAkhir = Stock::liveStock($item_id, $warehouse_id);
        
        return response()->json([
            'status' => $stokAkhir > 0 ? 1 : 0,
            'stokAkhir' => $stokAkhir,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'from_warehouse_id' => 'required|exists:warehouses,id',
            'to_warehouse_id' => 'required|exists:warehouses,id|different:from_warehouse_id',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.quantity' => 'required|numeric|min:0.0001',
            'items.*.unit_type' => 'required|in:main,retail',
            'description' => 'nullable|string',
        ]);

        $from_warehouse_id = $request->from_warehouse_id;
        $to_warehouse_id = $request->to_warehouse_id;
        $fromWarehouse = Warehouse::find($from_warehouse_id);
        $toWarehouse = Warehouse::find($to_warehouse_id);

        $desc = "Mutasi Stok dari {$fromWarehouse->name} ke {$toWarehouse->name}";
        if ($request->description) {
            $desc .= " | Catatan: " . $request->description;
        }

        // 1. Persiapkan data dan lakukan validasi stok secara keseluruhan terlebih dahulu
        $processedItems = [];
        $totalHargaKeseluruhan = 0;

        foreach ($request->items as $index => $itemData) {
            $item_id = $itemData['item_id'];
            $quantity = str_replace(',', '.', $itemData['quantity']);
            $item = Item::find($item_id);

            // 1. UPDATE MASTER ITEM JIKA ADA INPUT KONVERSI DINAMIS
            $inputRetailUnit = $itemData['retail_unit'] ?? null;
            $inputRetailConv = $itemData['retail_conversion'] ?? null;
            
            if (!empty($inputRetailUnit) && !empty($inputRetailConv) && is_numeric($inputRetailConv) && $inputRetailConv > 1) {
                $inputRetailUnit = trim($inputRetailUnit);
                $inputRetailConv = (float) $inputRetailConv;
                
                if ($item->retail_unit !== $inputRetailUnit || (float)$item->retail_conversion !== $inputRetailConv) {
                    $item->update([
                        'retail_unit' => $inputRetailUnit,
                        'retail_conversion' => $inputRetailConv
                    ]);
                }
            }

            // Konversi dari retail ke main unit jika user memilih input satuan retail (Dapur)
            if ($itemData['unit_type'] == 'retail') {
                $retailConv = $item->retail_conversion ?? 1;
                $quantity = $quantity / $retailConv;
            }

            // Validasi Live Stock
            $stokTersedia = Stock::liveStock($item_id, $from_warehouse_id);
            if ($quantity > $stokTersedia) {
                return redirect()->back()->with('error', "Mutasi gagal. Stok barang '{$item->name}' di gudang asal tidak mencukupi. (Sisa: $stokTersedia)")->withInput();
            }

            $totalHargaItem = $quantity * $item->price;
            $totalHargaKeseluruhan += $totalHargaItem;

            // Gabungkan qty jika ada item ganda (berjaga-jaga user masukin item yang sama 2 baris)
            if (isset($processedItems[$item_id])) {
                $processedItems[$item_id]['quantity'] += $quantity;
                $processedItems[$item_id]['total_harga'] += $totalHargaItem;
                
                // Re-validasi gabungan
                if ($processedItems[$item_id]['quantity'] > $stokTersedia) {
                    return redirect()->back()->with('error', "Mutasi gagal. Total stok barang '{$item->name}' melebihi batas yang tersedia. (Sisa: $stokTersedia)")->withInput();
                }
            } else {
                $processedItems[$item_id] = [
                    'item' => $item,
                    'quantity' => $quantity,
                    'harga_satuan' => $item->price,
                    'total_harga' => $totalHargaItem,
                ];
            }
        }

        DB::beginTransaction();
        try {
            // Buat Transaksi Keluar Utama
            $trxOut = StockTransaction::create([
                'type' => 'out',
                'alasan_adjustment' => $desc,
                'total_harga_keseluruhan' => $totalHargaKeseluruhan,
                'date' => now(),
                'created_by' => Auth::id(),
                'created_at' => now(),
            ]);

            // Buat Transaksi Masuk Utama
            $trxIn = StockTransaction::create([
                'type' => 'in',
                'alasan_adjustment' => $desc,
                'total_harga_keseluruhan' => $totalHargaKeseluruhan,
                'date' => now(),
                'created_by' => Auth::id(),
                'created_at' => now(),
            ]);

            $totalItemsCount = 0;
            // Insert Details
            foreach ($processedItems as $item_id => $data) {
                $item = $data['item'];
                
                StockTransactionDetail::create([
                    'stock_transaction_id' => $trxOut->id,
                    'item_id' => $item_id,
                    'warehouse_id' => $from_warehouse_id,
                    'quantity' => $data['quantity'],
                    'harga_satuan' => $data['harga_satuan'],
                    'total_harga' => $data['total_harga'],
                    'description' => 'Barang Keluar untuk Mutasi',
                    'created_by' => Auth::id(),
                    'created_at' => now(),
                ]);

                StockTransactionDetail::create([
                    'stock_transaction_id' => $trxIn->id,
                    'item_id' => $item_id,
                    'warehouse_id' => $to_warehouse_id,
                    'quantity' => $data['quantity'],
                    'harga_satuan' => $data['harga_satuan'],
                    'total_harga' => $data['total_harga'],
                    'description' => 'Barang Masuk dari Mutasi',
                    'created_by' => Auth::id(),
                    'created_at' => now(),
                ]);

                $totalItemsCount++;
            }

            ActivityLog::record('MUTATION', null, "Mutasi $totalItemsCount jenis barang dari {$fromWarehouse->name} ke {$toWarehouse->name}");

            DB::commit();
            return redirect()->route('admin.mutasi_stok.index')->with('success', 'Mutasi multi-item berhasil diproses.');
        } catch (\Exception $e) {
            DB::rollBack();
            ActivityLog::record('ERROR', null, "Gagal mutasi stok: " . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage())->withInput();
        }
    }
}
