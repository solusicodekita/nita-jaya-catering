<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\MenuDetail;
use App\Models\Item;
use App\Models\SettingWebsite;
use App\Models\Stock;
use App\Models\StockTransaction;
use App\Models\StockTransactionDetail;
use App\Models\TransaksiMenu;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Models\ActivityLog;

class ResepController extends Controller
{
    public function index(Request $request)
    {
        $query = Menu::with(['menuDetails.item', 'transaksiMenus', 'createdBy', 'updatedBy', 'categories'])
            ->withCount('transaksiMenus as total_usage')
            ->orderBy('created_at', 'desc');

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('recipe_number', 'like', "%{$search}%");
            });
        }

        $menus = $query->get();

        $dapurWarehouse = \App\Models\Warehouse::where('code', 'DP')->orWhere('name', 'GUDANG DAPUR')->first();
        $warehouseId = $dapurWarehouse ? $dapurWarehouse->id : 1;

        $items = Item::where('is_active', true)
            ->whereHas('stocks', function ($q) use ($warehouseId) {
                $q->where('warehouse_id', $warehouseId);
            })
            ->orderBy('name', 'asc')
            ->get();
        $categories = Category::orderBy('name', 'asc')->get();
        $setting = SettingWebsite::first();

        return view('admin.resep.index', compact('menus', 'items', 'categories', 'setting'));
    }

    public function create()
    {
        $categories = Category::orderBy('name', 'asc')->get();
        return view('admin.resep.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:191',
            'recipe_number' => 'nullable|string|max:191',
            'category_ids' => 'nullable|array',
            'category_ids.*' => 'exists:categories,id',
            'yield' => 'nullable|string|max:191',
            'cost_factor' => 'nullable|numeric|min:0',
            'profit_margin' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'is_active' => 'required|boolean',
            'reduce_stock' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            DB::beginTransaction();
            $menu = Menu::create([
                'name' => $request->name,
                'recipe_number' => $request->recipe_number,
                // 'category_id' => $request->category_id, // Deprecated, using sync instead
                'yield' => $request->yield,
                'cost_factor' => $request->cost_factor ?? 20.00,
                'profit_margin' => $request->profit_margin ?? 30.00,
                'description' => $request->description,
                'is_active' => $request->is_active,
                'reduce_stock' => $request->reduce_stock ?? 0,
                'created_by' => Auth::id(),
                'created_at' => now(),
            ]);

            if ($request->has('category_ids')) {
                $menu->categories()->sync($request->category_ids);
            }

            ActivityLog::record('CREATE', $menu, "Membuat resep baru: {$menu->name}");

            DB::commit();
            return redirect()->back()->with('success', 'Resep berhasil ditambahkan');
        } catch (\Exception $e) {
            DB::rollBack();
            ActivityLog::record('ERROR', null, "Gagal membuat resep: " . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menambahkan resep: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $menu = Menu::with('categories')->findOrFail($id);
        $categories = Category::orderBy('name', 'asc')->get();
        return view('admin.resep.edit', compact('menu', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:191',
            'recipe_number' => 'nullable|string|max:191',
            'category_ids' => 'nullable|array',
            'category_ids.*' => 'exists:categories,id',
            'yield' => 'nullable|string|max:191',
            'cost_factor' => 'nullable|numeric|min:0',
            'profit_margin' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'is_active' => 'required|boolean',
            'reduce_stock' => 'required|boolean',
        ]);

        try {
            $menu = Menu::findOrFail($id);
            $menu->update([
                'name' => $request->name,
                'recipe_number' => $request->recipe_number,
                // 'category_id' => $request->category_id, // Deprecated
                'yield' => $request->yield,
                'cost_factor' => $request->cost_factor ?? 20.00,
                'profit_margin' => $request->profit_margin ?? 30.00,
                'description' => $request->description,
                'is_active' => $request->is_active,
                'reduce_stock' => $request->reduce_stock,
                'updated_by' => Auth::id(),
                'updated_at' => now(),
            ]);

            $menu->categories()->sync($request->category_ids ?? []);

            ActivityLog::record('UPDATE', $menu, "Memperbarui resep: {$menu->name}");

            return redirect()->back()->with('success', 'Resep berhasil diperbarui');
        } catch (\Exception $e) {
            ActivityLog::record('ERROR', null, "Gagal update resep ID $id: " . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal memperbarui resep: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $menu = Menu::findOrFail($id);
            $menu->delete();
            return redirect()->back()->with('success', 'Resep berhasil dihapus');
        } catch (\Exception $e) {
            ActivityLog::record('ERROR', null, "Gagal hapus resep ID $id: " . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menghapus resep: ' . $e->getMessage());
        }
    }

    public function manageItems($id)
    {
        $menu = Menu::with(['menuDetails.item.category'])->findOrFail($id);
        $categories = Category::all();
        $items = Item::with('category')->get();
        return view('admin.resep.manage_items', compact('menu', 'categories', 'items'));
    }

    public function updateItems(Request $request, $id)
    {
        $request->validate([
            'items' => 'nullable|array',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.quantity' => 'required|numeric|min:0.0001',
            'items.*.unit_type' => 'nullable|string',
            'items.*.retail_unit' => 'nullable|string',
            'items.*.retail_conversion' => 'nullable', // Divalidasi di bawah
        ]);

        DB::beginTransaction();
        try {
            $menu = Menu::findOrFail($id);
            MenuDetail::where('menu_id', $id)->delete();

            if ($request->has('items')) {
                foreach ($request->items as $itemData) {
                    if ($itemData['quantity'] > 0) {
                        $item = Item::find($itemData['item_id']);
                        
                        // 1. UPDATE MASTER ITEM (DENGAN VALIDASI ANGKA)
                        $retailUnit = $itemData['retail_unit'] ?? null;
                        $retailConv = $itemData['retail_conversion'];
                        
                        // Jika kosong atau bukan angka, kembalikan ke default 1
                        if (empty($retailConv) || !is_numeric($retailConv)) {
                            $retailConv = 1;
                        }

                        if ($item->retail_unit != $retailUnit || (float)$item->retail_conversion != (float)$retailConv) {
                            $oldData = ['retail_unit' => $item->retail_unit, 'retail_conversion' => $item->retail_conversion];
                            
                            $item->update([
                                'retail_unit' => $retailUnit,
                                'retail_conversion' => $retailConv
                            ]);

                            ActivityLog::record('UPDATE', $item, "Update Konversi Item via Resep: {$item->name}", [
                                'old' => $oldData,
                                'new' => ['retail_unit' => $retailUnit, 'retail_conversion' => $retailConv]
                            ]);
                        }

                        // 2. KALKULASI QUANTITY RESEP
                        $finalQuantity = $itemData['quantity'];
                        if (isset($itemData['unit_type']) && $itemData['unit_type'] == 'retail') {
                            $finalQuantity = $itemData['quantity'] / $retailConv;
                        }

                        MenuDetail::create([
                            'menu_id' => $id,
                            'item_id' => $itemData['item_id'],
                            'quantity' => $finalQuantity,
                        ]);
                    }
                }
            }

            DB::commit();
            return redirect()->route('admin.resep.index')->with('success', 'Bahan resep berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollBack();
            // CATAT ERROR KE LOG
            ActivityLog::record('ERROR', null, "Gagal update bahan resep: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->route('admin.resep.index')->with('error', 'Gagal memperbarui bahan: ' . $e->getMessage());
        }
    }

    public function showUseRecipe(Request $request, $id)
    {
        $menu = Menu::with('menuDetails.item.category')->findOrFail($id);
        
        if ($menu->menuDetails->isEmpty()) {
            return redirect()->route('admin.resep.index')->with('error', "Gagal! Resep '{$menu->name}' belum memiliki bahan baku (item kosong).");
        }

        return view('admin.resep.use', compact('menu'));
    }

    public function useRecipe(Request $request, $id)
    {
        $request->validate([
            'multiplier' => 'required|numeric|min:1',
        ]);

        $menu = Menu::with('menuDetails.item')->findOrFail($id);

        if ($menu->menuDetails->isEmpty()) {
            if ($request->ajax()) {
                return response()->json(['status' => 'error', 'message' => "Gagal! Resep '{$menu->name}' belum memiliki bahan baku (item kosong). Harap isi bahan baku terlebih dahulu."]);
            }
            return redirect()->back()->with('error', "Gagal! Resep '{$menu->name}' belum memiliki bahan baku (item kosong). Harap isi bahan baku terlebih dahulu.");
        }

        $setting = SettingWebsite::first();
        $shouldReduce = ($menu->reduce_stock || ($setting && $setting->default_reduce_stock));

        DB::beginTransaction();
        try {
            $stockTransactionId = null;

            // 1. Ensure GUDANG DAPUR exists
            $dapurWarehouse = \App\Models\Warehouse::where('code', 'DP')->orWhere('name', 'GUDANG DAPUR')->first();
            if (!$dapurWarehouse) {
                $dapurWarehouse = \App\Models\Warehouse::create([
                    'name' => 'GUDANG DAPUR',
                    'code' => 'DP',
                    'created_by' => Auth::id()
                ]);
            }
            $warehouseId = $dapurWarehouse->id;

            if ($shouldReduce) {
                // Check stock availability in Dapur
                $missingItems = [];
                $allWarehouses = \App\Models\Warehouse::where('id', '!=', $warehouseId)->get();

                foreach ($menu->menuDetails as $detail) {
                    $totalQtyNeeded = $detail->quantity * $request->multiplier;
                    $stokDapur = \App\Models\Stock::liveStock($detail->item_id, $warehouseId);

                    if ($stokDapur < $totalQtyNeeded) {
                        $shortfall = $totalQtyNeeded - $stokDapur;
                        
                        // Find where this item is available
                        $sourceWarehouses = [];
                        foreach ($allWarehouses as $wh) {
                            $whStock = \App\Models\Stock::liveStock($detail->item_id, $wh->id);
                            if ($whStock > 0) {
                                $sourceWarehouses[] = [
                                    'id' => $wh->id,
                                    'name' => $wh->name,
                                    'available' => $whStock
                                ];
                            }
                        }

                        $missingItems[] = [
                            'item_id' => $detail->item_id,
                            'item_name' => $detail->item->name,
                            'unit' => $detail->item->unit,
                            'needed' => $totalQtyNeeded,
                            'available_dapur' => $stokDapur,
                            'shortfall' => $shortfall,
                            'sources' => $sourceWarehouses
                        ];
                    }
                }

                if (count($missingItems) > 0) {
                    if (!$request->has('auto_mutasi') || $request->auto_mutasi != '1') {
                        DB::rollBack();
                        if ($request->ajax()) {
                            return response()->json([
                                'status' => 'requires_mutasi',
                                'message' => 'Stok di Gudang Dapur tidak mencukupi untuk beberapa bahan.',
                                'missing_items' => $missingItems
                            ]);
                        }
                        return redirect()->back()->with('error', 'Stok di Gudang Dapur tidak mencukupi.');
                    } else {
                        // AUTO MUTASI PROCESS
                        $mutasiStockTransaction = \App\Models\StockTransaction::create([
                            'type' => 'out', // mutasi base transaction
                            'date' => now(),
                            'alasan_adjustment' => 'Mutasi Stok Otomatis untuk Proses Resep: ' . $menu->name . ' (' . $request->multiplier . ' porsi)',
                            'created_by' => Auth::id(),
                            'created_at' => now(),
                        ]);

                        $mutasiStockTransactionIn = \App\Models\StockTransaction::create([
                            'type' => 'in',
                            'date' => now(),
                            'alasan_adjustment' => 'Penerimaan Mutasi Otomatis untuk Proses Resep: ' . $menu->name,
                            'created_by' => Auth::id(),
                            'created_at' => now(),
                        ]);

                        foreach ($missingItems as $missing) {
                            $remainingToMutate = $missing['shortfall'];
                            foreach ($missing['sources'] as $source) {
                                if ($remainingToMutate <= 0) break;
                                
                                $takeQty = min($remainingToMutate, $source['available']);
                                $remainingToMutate -= $takeQty;

                                // OUT from Source
                                \App\Models\StockTransactionDetail::create([
                                    'stock_transaction_id' => $mutasiStockTransaction->id,
                                    'item_id' => $missing['item_id'],
                                    'warehouse_id' => $source['id'],
                                    'quantity' => $takeQty,
                                    'harga_satuan' => 0,
                                    'total_harga' => 0,
                                    'description' => 'Mutasi Otomatis ke GUDANG DAPUR',
                                    'created_at' => now(),
                                ]);

                                // IN to Dapur
                                \App\Models\StockTransactionDetail::create([
                                    'stock_transaction_id' => $mutasiStockTransactionIn->id,
                                    'item_id' => $missing['item_id'],
                                    'warehouse_id' => $warehouseId,
                                    'quantity' => $takeQty,
                                    'harga_satuan' => 0,
                                    'total_harga' => 0,
                                    'description' => 'Penerimaan Mutasi Otomatis dari ' . $source['name'],
                                    'created_at' => now(),
                                ]);
                            }

                            if ($remainingToMutate > 0.001) {
                                DB::rollBack();
                                $msg = "Stok item '{$missing['item_name']}' tidak cukup di semua gudang (Kurang {$remainingToMutate} {$missing['unit']}). Mutasi otomatis dibatalkan.";
                                if ($request->ajax()) return response()->json(['status' => 'error', 'message' => $msg]);
                                return redirect()->back()->with('error', $msg);
                            }
                        }
                    }
                }
                
                // Continue with Normal Recipe Usage Logic
                $totalCost1 = 0;
                foreach ($menu->menuDetails as $detail) {
                    $totalCost1 += ($detail->item->price ?? 0) * $detail->quantity;
                }

                $costFactorVal = $totalCost1 * (($menu->cost_factor ?? 20) / 100);
                $totalCost2 = $totalCost1 + $costFactorVal;
                $profitVal = $totalCost2 * (($menu->profit_margin ?? 30) / 100);
                $sellingPricePerPortion = $totalCost2 + $profitVal;
                $totalTransactionValue = $sellingPricePerPortion * $request->multiplier;

                $stockTransaction = \App\Models\StockTransaction::create([
                    'type' => 'out',
                    'date' => now(),
                    'alasan_adjustment' => 'Proses penggunaan resep ' . $menu->name . ' (' . $request->multiplier . ' porsi) oleh ' . Auth::user()->fullname . ' pada tanggal ' . now()->format('d-m-Y H:i'),
                    'total_harga_keseluruhan' => $totalTransactionValue,
                    'created_by' => Auth::id(),
                    'created_at' => now(),
                ]);
                $stockTransactionId = $stockTransaction->id;

                foreach ($menu->menuDetails as $detail) {
                    $totalQtyNeeded = $detail->quantity * $request->multiplier;

                    \App\Models\StockTransactionDetail::create([
                        'stock_transaction_id' => $stockTransactionId,
                        'item_id' => $detail->item_id,
                        'warehouse_id' => $warehouseId,
                        'quantity' => $totalQtyNeeded,
                        'harga_satuan' => $detail->item->price ?? 0,
                        'total_harga' => ($detail->item->price ?? 0) * $totalQtyNeeded,
                        'description' => 'Bahan untuk resep ' . $menu->name,
                        'created_at' => now(),
                    ]);
                }
            } else {
                $totalCost1 = 0;
                $totalTransactionValue = 0;
            }

            \App\Models\TransaksiMenu::create([
                'menu_id' => $id,
                'recipe_name' => $menu->name,
                'recipe_number' => $menu->recipe_number,
                'stock_transaction_id' => $stockTransactionId,
                'qty' => $request->multiplier,
                'total_cost' => ($totalCost1 ?? 0) * $request->multiplier,
                'selling_price' => $totalTransactionValue ?? 0,
                'created_by' => Auth::id(),
                'created_at' => now(),
            ]);

            \App\Models\ActivityLog::record('USE_RECIPE', $menu, "Menggunakan resep: {$menu->name} sebanyak {$request->multiplier} porsi", [
                'multiplier' => $request->multiplier,
                'stock_transaction_id' => $stockTransactionId
            ]);

            DB::commit();
            
            if ($request->ajax()) {
                return response()->json(['status' => 'success', 'message' => 'Resep berhasil diproses' . ($shouldReduce ? ' dan stok telah dipotong.' : '.')]);
            }
            return redirect()->back()->with('success', 'Resep berhasil diproses' . ($shouldReduce ? ' dan stok telah dipotong.' : '.'));
        } catch (\Exception $e) {
            DB::rollBack();
            \App\Models\ActivityLog::record('ERROR', null, "Gagal memproses resep ID $id: " . $e->getMessage());
            
            if ($request->ajax()) {
                return response()->json(['status' => 'error', 'message' => 'Gagal memproses resep: ' . $e->getMessage()]);
            }
            return redirect()->back()->with('error', 'Gagal memproses resep: ' . $e->getMessage());
        }
    }

    public function generateNumber(Request $request)
    {
        try {
            $categoryId = $request->category_id;
            
            // Handle if category_id is passed as array (from multi-select)
            if (is_array($categoryId)) {
                $categoryId = !empty($categoryId) ? $categoryId[0] : null;
            }

            $year = date('Y');
            $prefix = "RESEP";

            if ($categoryId) {
                $category = Category::find($categoryId);
                if ($category && $category->code) {
                    $prefix = strtoupper($category->code);
                }
            }

            // Get last sequence for this year
            $lastRecipe = Menu::whereYear('created_at', $year)
                ->orderBy('id', 'desc')
                ->first();
            
            $nextSeq = 1;
            if ($lastRecipe && $lastRecipe->recipe_number) {
                $parts = explode('/', $lastRecipe->recipe_number);
                if (count($parts) > 0 && is_numeric($parts[0])) {
                    $nextSeq = (int)$parts[0] + 1;
                }
            }

            $sequence = str_pad($nextSeq, 3, '0', STR_PAD_LEFT);
            $newNumber = "{$sequence}/{$prefix}/NT/{$year}";

            return response()->json([
                'status' => 'success',
                'number' => $newNumber
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function downloadTemplate()
    {
        $fileName = 'Template_Import_Resep_Standar.xlsx';

        // Kelas untuk Sheet 1: Template Format Import
        $templateSheet = new class implements \Maatwebsite\Excel\Concerns\FromArray, \Maatwebsite\Excel\Concerns\WithHeadings, \Maatwebsite\Excel\Concerns\WithTitle {
            public function array(): array {
                return [
                    [
                        '1', '001/SOUP/NT/2026', 'SUP KIMLO', 'Soup', 'WORTEL BERSIH', '0.03', '3', 'KG', '10000', '300'
                    ],
                    [
                        '', '', '', '', 'PENTOL AYAM', '0.01', '1', 'KG', '16500', '165'
                    ],
                    [
                        '', '', '', '', 'AYAM FILLET', '0.005', '0.5', 'KG', '45000', '225'
                    ],
                    [
                        '2', '002/SOUP/NT/2026', 'SUP ASPARAGUS KEPITING', 'Soup', 'ASPARAGUS KALENG', '0.04', '4', 'KLG', '18000', '720'
                    ],
                    [
                        '', '', '', '', 'KEPITING KALENG', '0.03', '3', 'KLG', '25000', '750'
                    ],
                ];
            }
            public function headings(): array {
                return [
                    'No', 'Kode Resep', 'Nama Resep', 'Kategori', 'Nama Bahan Baku', 'Jumlah 1 Porsi', 'Jumlah 100 Porsi', 'Satuan', 'Harga', 'Per Porsi'
                ];
            }
            public function title(): string {
                return 'Template Import';
            }
        };

        // Kelas untuk Sheet 2: Referensi Item di Gudang
        $referenceSheet = new class implements \Maatwebsite\Excel\Concerns\FromArray, \Maatwebsite\Excel\Concerns\WithHeadings, \Maatwebsite\Excel\Concerns\WithTitle {
            public function array(): array {
                $dapurWarehouse = \App\Models\Warehouse::where('code', 'DP')->orWhere('name', 'GUDANG DAPUR')->first();
                $dapurId = $dapurWarehouse ? $dapurWarehouse->id : null;

                $items = \App\Models\Item::with('category')->get();

                $data = [];
                $no = 1;
                foreach ($items as $item) {
                    $qty = 0;
                    $lastUpdated = '-';
                    
                    if ($dapurId) {
                        $qty = \App\Models\Stock::liveStock($item->id, $dapurId);
                        $latestStock = \App\Models\Stock::where('item_id', $item->id)
                                        ->where('warehouse_id', $dapurId)
                                        ->latest('date_opname')
                                        ->first();
                        
                        if ($latestStock && $latestStock->updated_at) {
                            $lastUpdated = $latestStock->updated_at->format('d/m/Y H:i');
                        }
                    }

                    $data[] = [
                        $no++,
                        $item->code,
                        $item->category ? $item->category->name : '-',
                        $item->name,
                        $item->unit,
                        $item->price,
                        $qty,
                        $lastUpdated
                    ];
                }
                return $data;
            }
            public function headings(): array {
                return [
                    'No', 'Kode Bahan', 'Kategori', 'Nama Bahan Baku (Copy/Paste kesini untuk memastikan sama)', 'Satuan Utama', 'Harga Master', 'Stok Gudang Dapur', 'Terakhir Update'
                ];
            }
            public function title(): string {
                return 'Referensi Bahan Baku';
            }
        };

        // Kelas Utama Export Multiple Sheets
        $export = new class($templateSheet, $referenceSheet) implements \Maatwebsite\Excel\Concerns\WithMultipleSheets {
            private $sheet1;
            private $sheet2;
            public function __construct($sheet1, $sheet2) {
                $this->sheet1 = $sheet1;
                $this->sheet2 = $sheet2;
            }
            public function sheets(): array {
                return [$this->sheet1, $this->sheet2];
            }
        };

        return \Maatwebsite\Excel\Facades\Excel::download($export, $fileName);
    }

    public function importExcel(Request $request)
    {
        $request->validate([
            'file_excel' => 'required|mimes:xlsx,xls,csv|max:10240'
        ]);

        try {
            \Maatwebsite\Excel\Facades\Excel::import(new \App\Imports\ResepImport, $request->file('file_excel'));
            return redirect()->back()->with('success', 'Data resep berhasil diimport dengan format standar!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengimport data: ' . $e->getMessage());
        }
    }

    public function usageHistory(Request $request)
    {
        $histories = TransaksiMenu::with(['menu', 'stockTransaction.stockTransactionDetails.item', 'createdBy'])
            ->orderBy('created_at', 'desc')
            ->paginate(50);
            
        return view('admin.resep.history', compact('histories'));
    }
}
