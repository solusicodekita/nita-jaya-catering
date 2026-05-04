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

class ResepController extends Controller
{
    public function index()
    {
        // Ambil semua menu (resep) dengan relasi menuDetails.item dan transaksiMenus
        $menus = Menu::with(['menuDetails.item', 'transaksiMenus', 'createdBy', 'updatedBy'])
            ->withCount('transaksiMenus as total_usage')
            ->orderBy('created_at', 'desc')
            ->get();

        // Ambil semua bahan untuk keperluan modal kelola bahan
        $items = Item::where('is_active', true)->orderBy('name', 'asc')->get();
        
        // Ambil setting website untuk cek default reduce stock
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
            Menu::create([
                'name' => $request->name,
                'description' => $request->description,
                'is_active' => $request->is_active,
                'reduce_stock' => $request->reduce_stock ?? 0,
                'created_by' => Auth::id(),
                'created_at' => now(),
            ]);

            return redirect()->back()->with('success', 'Resep berhasil ditambahkan');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menambahkan resep: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
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
            $menu = Menu::findOrFail($id);
            $menu->update([
                'name' => $request->name,
                'description' => $request->description,
                'is_active' => $request->is_active,
                'reduce_stock' => $request->reduce_stock ?? $menu->reduce_stock,
                'updated_by' => Auth::id(),
                'updated_at' => now(),
            ]);

            return redirect()->back()->with('success', 'Resep berhasil diperbarui');
        } catch (\Exception $e) {
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
            return redirect()->back()->with('error', 'Gagal menghapus resep: ' . $e->getMessage());
        }
    }

    public function updateItems(Request $request, $id)
    {
        $request->validate([
            'items' => 'nullable|array',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.quantity' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $menu = Menu::findOrFail($id);
            
            // Hapus bahan lama
            MenuDetail::where('menu_id', $id)->delete();

            // Insert bahan baru jika ada
            if ($request->has('items')) {
                foreach ($request->items as $itemData) {
                    if ($itemData['quantity'] > 0) {
                        MenuDetail::create([
                            'menu_id' => $id,
                            'item_id' => $itemData['item_id'],
                            'quantity' => $itemData['quantity'],
                        ]);
                    }
                }
            }

            DB::commit();
            return redirect()->back()->with('success', 'Bahan resep berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollBack();
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
        
        // Cek apakah harus potong stok (Flag di menu ATAU Global Setting aktif)
        $shouldReduce = ($menu->reduce_stock || ($setting && $setting->default_reduce_stock));

        DB::beginTransaction();
        try {
            $stockTransactionId = null;

            if ($shouldReduce) {
                // Buat Stock Transaction Header
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
                    
                    // Cari stok yang tersedia (pilih gudang pertama yang punya stok)
                    $stockEntry = Stock::where('item_id', $detail->item_id)
                        ->where('stock', '>', 0)
                        ->first();

                    if (!$stockEntry) {
                        // Jika tidak ada stok, kita tetap catat transaksi tapi mungkin beri peringatan atau biarkan minus tergantung kebijakan
                        // Disini kita asumsikan pakai gudang default (id 1) jika tidak ketemu
                        $warehouseId = 1; 
                    } else {
                        $warehouseId = $stockEntry->warehouse_id;
                        // Kurangi stok di tabel stocks
                        $stockEntry->decrement('stock', $totalQtyNeeded);
                    }

                    // Buat Stock Transaction Detail
                    StockTransactionDetail::create([
                        'stock_transaction_id' => $stockTransactionId,
                        'item_id' => $detail->item_id,
                        'warehouse_id' => $warehouseId,
                        'qty' => $totalQtyNeeded,
                        'created_at' => now(),
                    ]);
                }
            }

            // Catat Riwayat Penggunaan Menu
            TransaksiMenu::create([
                'menu_id' => $id,
                'stock_transaction_id' => $stockTransactionId,
                'qty' => $request->multiplier,
                'created_by' => Auth::id(),
                'created_at' => now(),
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Resep berhasil diproses' . ($shouldReduce ? ' dan stok telah dipotong.' : '.'));
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal memproses resep: ' . $e->getMessage());
        }
    }
}
