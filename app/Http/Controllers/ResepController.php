<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\TransaksiMenu;
use Illuminate\Http\Request;

class ResepController extends Controller
{
    public function index()
    {
        // Ambil semua menu (resep) dengan relasi yang diperlukan
        $menus = Menu::with(['createdBy', 'updatedBy'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Ambil data TransaksiMenu untuk setiap menu dengan relasi yang benar
        foreach ($menus as $menu) {
            $menu->transaksiMenus = TransaksiMenu::with([
                'stockTransaction.stockTransactionDetails.item'
            ])
            ->where('menu_id', $menu->id)
            ->get();
        }

        // Ambil statistik untuk dashboard
        $totalMenus = Menu::count();
        $totalTransaksiMenu = TransaksiMenu::count();

        return view('admin.resep.index', compact('menus', 'totalMenus', 'totalTransaksiMenu'));
    }

    public function show($id)
    {
        $menu = Menu::with(['createdBy', 'updatedBy'])->findOrFail($id);
        
        // Ambil transaksi menu untuk resep ini
        $transaksiMenus = TransaksiMenu::with('menu')
            ->where('menu_id', $id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.resep.show', compact('menu', 'transaksiMenus'));
    }
}
