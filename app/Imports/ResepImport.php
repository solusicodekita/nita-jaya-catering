<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use App\Models\Menu;
use App\Models\MenuDetail;
use App\Models\Item;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class ResepImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        $currentMenu = null;
        $currentCategory = null;
        
        foreach ($rows as $row) {
            $kodeResep = trim($row['kode_resep'] ?? '');
            $namaResep = trim($row['nama_resep'] ?? '');
            
            if ($namaResep != '') {
                $kategoriName = trim($row['kategori'] ?? 'Umum');
                $kategoriName = empty($kategoriName) ? 'Umum' : $kategoriName;
                
                $currentCategory = Category::firstOrCreate([
                    'name' => $kategoriName
                ], [
                    'slug' => Str::slug($kategoriName),
                    'code' => strtoupper(Str::random(5))
                ]);
                
                $currentMenu = Menu::updateOrCreate(
                    ['name' => $namaResep],
                    [
                        'recipe_number' => $kodeResep,
                        'yield' => '1 Porsi',
                        'cost_factor' => 20, // default if not in template
                        'profit_margin' => 30, // default if not in template
                        'description' => '',
                        'is_active' => 1,
                        'reduce_stock' => 1,
                        'created_by' => Auth::id() ?? 1,
                    ]
                );
                
                $currentMenu->categories()->syncWithoutDetaching([$currentCategory->id]);
                $currentMenu->menuDetails()->delete();
            }
            
            $namaBahan = trim($row['nama_bahan_baku'] ?? '');
            $qty = floatval($row['jumlah_1_porsi'] ?? 0);
            $satuan = trim($row['satuan'] ?? '');
            $harga = floatval($row['harga'] ?? 0);

            if ($currentMenu && $namaBahan != '' && $qty > 0) {
                $item = Item::firstOrCreate(
                    ['name' => $namaBahan],
                    [
                        'code' => 'ITM-' . strtoupper(Str::random(5)),
                        'category_id' => $currentCategory ? $currentCategory->id : 1,
                        'unit' => $satuan,
                        'price' => $harga,
                    ]
                );

                // Update price if item already existed and price is provided
                if ($item->price == 0 && $harga > 0) {
                    $item->update(['price' => $harga]);
                }
                
                MenuDetail::create([
                    'menu_id' => $currentMenu->id,
                    'item_id' => $item->id,
                    'quantity' => $qty,
                ]);
            }
        }
    }
}
