<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Models\Menu;
use App\Models\MenuDetail;
use App\Models\Item;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ImportMasterResepCommand extends Command
{
    protected $signature = 'resep:import';
    protected $description = 'Import master resep dari file excel di public/master-resep';

    public function handle()
    {
        $dir = public_path('master-resep');
        $files = glob($dir . '/*.xlsx');

        if (empty($files)) {
            $this->error("Tidak ada file xlsx di folder public/master-resep");
            return;
        }

        foreach ($files as $file) {
            $this->info("Membaca file: " . basename($file));
            try {
                $spreadsheet = IOFactory::load($file);
                foreach ($spreadsheet->getSheetNames() as $sheetName) {
                    $sheet = $spreadsheet->getSheetByName($sheetName);
                    $data = $sheet->toArray(null, true, true, true);
                    
                    // Coba baca dari baris yang bervariasi (karena tidak konsisten)
                    $recipeNumber = $data[3]['B'] ?? ($data[4]['B'] ?? ($data[2]['B'] ?? null));
                    $menuName = $data[3]['F'] ?? ($data[4]['F'] ?? ($data[2]['F'] ?? null));
                    
                    if (empty($menuName)) {
                        $this->warn("Sheet '$sheetName' diabaikan (Format Nama Resep tidak dikenali).");
                        continue;
                    }

                    // Bersihkan prefix "Nama :" 
                    $menuName = trim(str_ireplace(['Nama  :', 'Nama :'], '', $menuName));
                    $recipeNumber = trim($recipeNumber);

                    $this->info("Menemukan Menu: $menuName ($recipeNumber)");

                    DB::beginTransaction();
                    try {
                        // Ambil kategori dari baris 4, 5, atau 3
                        $categoryName = trim($data[5]['B'] ?? ($data[4]['B'] ?? 'Umum'));
                        if (empty($categoryName)) $categoryName = 'Umum';

                        $category = Category::firstOrCreate([
                            'name' => $categoryName
                        ], [
                            'slug' => Str::slug($categoryName),
                            'code' => strtoupper(Str::random(5))
                        ]);

                        $menu = Menu::updateOrCreate(
                            ['name' => $menuName],
                            [
                                'recipe_number' => $recipeNumber,
                                'category_id' => $category->id,
                                'cost_factor' => 20,
                                'profit_margin' => 30,
                            ]
                        );

                        // Hapus detail lama jika update
                        $menu->menuDetails()->delete();

                        // Cari header ingredients
                        $startRow = 0;
                        for ($i = 1; $i <= 15; $i++) {
                            if (isset($data[$i]['A']) && str_contains(strtolower($data[$i]['A']), 'ingredients')) {
                                $startRow = $i + 2; 
                                break;
                            }
                        }
                        
                        if ($startRow == 0) $startRow = 10;

                        for ($i = $startRow; $i <= count($data); $i++) {
                            $itemName = trim($data[$i]['A'] ?? '');
                            if (empty($itemName) || str_contains(strtolower($itemName), 'total cost')) break;

                            $unit = trim($data[$i]['C'] ?? '');
                            $qtyUsed = floatval($data[$i]['F'] ?? 0);
                            $unitUsed = trim($data[$i]['G'] ?? '');

                            if ($qtyUsed <= 0) continue;

                            $itemCode = 'ITM-' . strtoupper(Str::random(5));
                            $item = Item::firstOrCreate(
                                ['name' => $itemName],
                                [
                                    'code' => $itemCode,
                                    'category_id' => $category->id,
                                    'unit' => $unitUsed ?: $unit,
                                    'price' => 0 
                                ]
                            );

                            MenuDetail::create([
                                'menu_id' => $menu->id,
                                'item_id' => $item->id,
                                'quantity' => $qtyUsed
                            ]);
                        }

                        DB::commit();
                        $this->info("Berhasil import: $menuName");

                    } catch (\Exception $e) {
                        DB::rollBack();
                        $this->error("Gagal insert $menuName: " . $e->getMessage());
                    }
                }
            } catch (\Exception $e) {
                $this->error("Gagal membaca file $file: " . $e->getMessage());
            }
        }
        $this->info("Proses import selesai!");
    }
}
