<?php

namespace App\Http\Controllers;

use App\Exports\LiveStockExport;
use App\Models\HistoryHarga;
use App\Models\Item;
use App\Models\Stock;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Mpdf\Mpdf;
use App\Models\ActivityLog;

class StockController extends Controller
{
    public function index()
    {
        $model = Stock::latest('id')->get();
        return view('admin.stock.index', compact('model'));
    }

    public function create()
    {
        $item = Item::latest('id')->get();
        $warehouse = Warehouse::latest('id')->get();
        return view('admin.stock.create', compact('item', 'warehouse'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'item' => 'required|array',
                'item.*.item_id' => 'required|exists:items,id',
                'item.*.warehouse_id' => 'required|exists:warehouses,id', 
                'item.*.initial_stock' => 'required|numeric',
                'item.*.final_stock' => 'required|numeric',
            ]);
            
            foreach ($request->item as $item) {
                $stock = new Stock();
                $stock->item_id = $item['item_id'];
                $stock->warehouse_id = $item['warehouse_id']; 
                $stock->initial_stock = $item['initial_stock'];
                $stock->final_stock = $item['final_stock'];
                $stock->date_opname = now();
                $stock->created_by = Auth::user()->id;
                $stock->updated_by = Auth::user()->id;
                $stock->save();
                
                HistoryHarga::create([
                    'item_id' => $item['item_id'],
                    'warehouse_id' => $item['warehouse_id'],
                    'harga_awal' => 0,
                    'harga_baru' => $stock->item->price,
                    'created_by' => Auth::user()->id,
                    'updated_by' => Auth::user()->id,
                ]);
                
                ActivityLog::record('STOCK_OPNAME', $stock, "Melakukan Stok Opname untuk item: {$stock->item->name} di {$stock->warehouse->name}", [
                    'initial' => $stock->initial_stock,
                    'final' => $stock->final_stock
                ]);
            }
    
            return redirect()->route('admin.stock.index')->with('success', 'Data stok berhasil disimpan');
        } catch (\Exception $e) {
            return redirect()->route('admin.stock.index')->with('error', 'Data stok gagal disimpan: ' . $e->getMessage());
        }
        
    }

    public function cekStokAkhir(Request $request)
    {
        $existingStock = Stock::where('item_id', $request->item_id)
            ->where('warehouse_id', $request->warehouse_id)
            ->exists();

        if ($existingStock) {
            return response()->json([
                'status' => 2,
                'msg' => 'Barang sudah pernah di stock awal di lokasi ini.',
            ]);
        }
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

    public function live_stock()
    {
        $model = Stock::select('item_id', 'warehouse_id')
            ->groupBy('item_id', 'warehouse_id')
            ->get();
        return view('admin.live_stock.index', compact('model'));
    }

    public function export_excel()
    {
        $current_time = now()->format('Ymd_His');
        return Excel::download(new LiveStockExport(), 'live_stock_' . $current_time . '.xlsx');
    }

    public function export_pdf()
    {
        $mpdf = new Mpdf();

        $model = Stock::select('item_id', 'warehouse_id')
            ->groupBy('item_id', 'warehouse_id')
            ->get();

        $data = [
            'model' => $model,
            'title' => 'Laporan Live Stock',
        ];

        $html = view('admin.live_stock.pdf', $data)->render();
        $mpdf->WriteHTML($html);
        return response($mpdf->Output('', 'S'))
            ->header('Content-Type', 'application/pdf');
    }
}
