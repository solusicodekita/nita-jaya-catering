<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Address;
use App\Models\Category;
use App\Models\Item;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\OrderProduct;
use App\Models\Warehouse;
use App\Models\Stock;
use App\Models\HistoryHarga;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard / Module Hub.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $user = auth()->user();

        // Jika superadmin, tampilkan halaman pemilih modul
        if ($user->role == 'superadmin') {
            return view('admin.module_selection', [
                'title' => 'Pilih Modul'
            ]);
        }

        // Jika staff atau memiliki role admin, arahkan ke dashboard POS
        if ($user->hasRole('admin') || $user->role == 'staff') {
            return redirect()->route('admin.dashboard');
        }

        // Default untuk customer
        $tr = Address::where([
            ['user_id', Auth::user()->id],
            ['type', 'UTAMA'],
        ])->get();

        if (empty($tr) || $tr == NULL) {
            return redirect()->route('fe.alamat');
        } else {
            return redirect()->route('fe.index');
        }
    }

    /**
     * Show the POS dashboard content.
     */
    public function pos_dashboard()
    {
        if (!auth()->user()->hasRole('admin') && auth()->user()->role != 'superadmin' && auth()->user()->role != 'staff') {
            return redirect()->route('home')->with('error', 'Anda tidak memiliki akses ke POS.');
        }

        $total = 0;
        $products = Product::with('order_product')->orderBy('id')->get()->groupBy(function($data) { return $data->qty; });;
        $data = OrderProduct::with('product')->orderBy('product_id')->get()->groupBy(function($data) { return $data->product->id; });

        // PIE CHART
        $success = Transaction::where('status', 'SUCCESS')->sum('total_harga');
        $pending = Transaction::where('status', 'PENDING')->sum('total_harga');
        $proses = Transaction::where('status', 'PROSES')->sum('total_harga');
        $fail = Transaction::whereNotIn('status', ['SUCCESS','PENDING','PROSES'])->sum('total_harga');
        $total_kategori = Category::count();
        $total_lokasi = Warehouse::count();
        $total_item = Item::count();

        // Ambil semua warehouse
        $warehouses = \App\Models\Warehouse::all();

        // Untuk setiap warehouse, hitung jumlah item dengan stok > 0
        $warehouse_stocks = [];
        foreach ($warehouses as $warehouse) {
            $item_count = Stock::where('warehouse_id', $warehouse->id)
                ->where('final_stock', '>', 0)
                ->count();
            $warehouse_stocks[] = [
                'name' => $warehouse->name,
                'item_count' => $item_count,
            ];
        }

        // Ambil item habis (stok 0 di semua warehouse)
        $empty_items = Stock::where('final_stock', '<=', 0)
            ->with('item', 'warehouse')
            ->get();

        // Ambil history harga
        $history_harga_by_item = \App\Models\HistoryHarga::with(['item', 'warehouse'])
            ->orderBy('created_at', 'asc')
            ->get()
            ->groupBy('item_id');

        $items = \App\Models\Item::whereIn('id', $history_harga_by_item->keys())->get();

        return view('admin.dashboard.index',[
            'title' => 'Dashboard POS',
            'products' => $products,
            'data' => $data,
            'total' => $total,
            'user' => User::all()->count(),
            'transaction' => Transaction::where('status', 'SUCCESS')->get()->count(),
            'money' => Transaction::where('status', 'SUCCESS')->sum('total_harga'),
            'success' => $success,
            'pending' => $pending,
            'proses' => $proses,
            'fail' => $fail,
            'total_kategori' => $total_kategori,
            'total_lokasi' => $total_lokasi,
            'total_item' => $total_item,
            'warehouse_stocks' => $warehouse_stocks,
            'empty_items' => $empty_items,
            'history_harga' => $history_harga_by_item,
            'history_harga_by_item' => $history_harga_by_item,
            'items' => $items,
        ]);
    }
}
