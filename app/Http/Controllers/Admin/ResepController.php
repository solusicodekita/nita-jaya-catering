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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Models\ActivityLog;

class ResepController extends Controller
{
    public function index()
    {
        $menus = Menu::with(['menuDetails.item', 'transaksiMenus', 'createdBy', 'updatedBy'])
            ->withCount('transaksiMenus as total_usage')
            ->orderBy('created_at', 'desc')
            ->get();

        $items = Item::where('is_active', true)->orderBy('name', 'asc')->get();
        $setting = SettingWebsite::first();

        return view('admin.resep.index', compact('menus', 'items', 'setting'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:191',
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
                'description' => $request->description,
                'is_active' => $request->is_active,
                'reduce_stock' => $request->reduce_stock ?? 0,
                'created_by' => Auth::id(),
                'created_at' => now(),
            ]);

            ActivityLog::record('CREATE', $menu, "Membuat resep baru: {$menu->name}");

            DB::commit();
            return redirect()->back()->with('success', 'Resep berhasil ditambahkan');
        } catch (\Exception $e) {
            DB::rollBack();
            ActivityLog::record('ERROR', null, "Gagal membuat resep: " . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menambahkan resep: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:191',
            'description' => 'nullable|string',
            'is_active' => 'required|boolean',
            'reduce_stock' => 'required|boolean',
        ]);

        try {
            $menu = Menu::findOrFail($id);
            $menu->update([
                'name' => $request->name,
                'description' => $request->description,
                'is_active' => $request->is_active,
                'reduce_stock' => $request->reduce_stock,
                'updated_by' => Auth::id(),
                'updated_at' => now(),
            ]);

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
            return redirect()->back()->with('success', 'Bahan resep berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollBack();
            // CATAT ERROR KE LOG
            ActivityLog::record('ERROR', null, "Gagal update bahan resep: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('error', 'Gagal memperbarui bahan: ' . $e->getMessage());
        }
    }

    public function useRecipe(Request $request, $id)
    {
        $request->validate([
            'multiplier' => 'required|numeric|min:1',
        ]);

        $menu = Menu::with('menuDetails.item')->findOrFail($id);
        $setting = SettingWebsite::first();
        $shouldReduce = ($menu->reduce_stock || ($setting && $setting->default_reduce_stock));

        DB::beginTransaction();
        try {
            $stockTransactionId = null;

            if ($shouldReduce) {
                $stockTransaction = StockTransaction::create([
                    'type' => 'out',
                    'date' => now(),
                    'description' => 'Penggunaan Resep: ' . $menu->name . ' (' . $request->multiplier . ' porsi)',
                    'created_by' => Auth::id(),
                    'created_at' => now(),
                ]);
                $stockTransactionId = $stockTransaction->id;

                foreach ($menu->menuDetails as $detail) {
                    $totalQtyNeeded = $detail->quantity * $request->multiplier;
                    $warehouseId = 1;
                    $stockEntry = Stock::where('item_id', $detail->item_id)
                        ->where('warehouse_id', $warehouseId)
                        ->latest('date_opname')
                        ->first();

                    if ($stockEntry) {
                        $warehouseId = $stockEntry->warehouse_id;
                    }

                    StockTransactionDetail::create([
                        'stock_transaction_id' => $stockTransactionId,
                        'item_id' => $detail->item_id,
                        'warehouse_id' => $warehouseId,
                        'quantity' => $totalQtyNeeded,
                        'created_at' => now(),
                    ]);
                }
            }

            TransaksiMenu::create([
                'menu_id' => $id,
                'stock_transaction_id' => $stockTransactionId,
                'qty' => $request->multiplier,
                'created_by' => Auth::id(),
                'created_at' => now(),
            ]);

            ActivityLog::record('USE_RECIPE', $menu, "Menggunakan resep: {$menu->name} sebanyak {$request->multiplier} porsi", [
                'multiplier' => $request->multiplier,
                'stock_transaction_id' => $stockTransactionId
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Resep berhasil diproses' . ($shouldReduce ? ' dan stok telah dipotong.' : '.'));
        } catch (\Exception $e) {
            DB::rollBack();
            ActivityLog::record('ERROR', null, "Gagal memproses resep ID $id: " . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal memproses resep: ' . $e->getMessage());
        }
    }
}
