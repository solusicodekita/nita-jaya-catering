<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MasterRecipe;
use App\Models\RecipeCategory;
use App\Models\RecipeDetail;
use App\Models\Item;
use App\Models\StockTransaction;
use App\Models\StockTransactionDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class RecipeController extends Controller
{
    public function index()
    {
        $data = MasterRecipe::with(['category', 'details'])->orderBy('id', 'desc')->get();
        return view('admin.recipes.index', compact('data'));
    }

    public function create()
    {
        $categories = RecipeCategory::latest('id')->get();
        $ingredients = Item::orderBy('name')->get();
        return view('admin.recipes.create', compact('categories', 'ingredients'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'required|string|max:255',
                'category_id' => 'required|exists:recipe_categories,id',
                'property' => 'required|string|max:255',
                'yield_quantity' => 'required|numeric|min:1',
                'yield_unit' => 'required|string|max:50',
                'ingredients' => 'required|array|min:1',
                'ingredients.*.ingredient_id' => 'required|exists:items,id',
                'ingredients.*.quantity' => 'required|numeric|min:0.01',
                'ingredients.*.unit' => 'required|string|max:50',
                'ingredients.*.price_per_unit' => 'required|numeric|min:0'
            ],
            [
                'name.required' => 'Nama resep wajib diisi.',
                'category_id.required' => 'Kategori wajib diisi.',
                'category_id.exists' => 'Kategori tidak valid.',
                'property.required' => 'Property wajib diisi.',
                'yield_quantity.required' => 'Jumlah porsi wajib diisi.',
                'yield_quantity.min' => 'Jumlah porsi minimal 1.',
                'yield_unit.required' => 'Unit porsi wajib diisi.',
                'ingredients.required' => 'Bahan-bahan wajib diisi.',
                'ingredients.min' => 'Minimal harus ada 1 bahan.',
                'ingredients.*.ingredient_id.required' => 'Bahan wajib diisi.',
                'ingredients.*.ingredient_id.exists' => 'Bahan tidak valid.',
                'ingredients.*.quantity.required' => 'Jumlah bahan wajib diisi.',
                'ingredients.*.quantity.min' => 'Jumlah bahan minimal 0.01.',
                'ingredients.*.unit.required' => 'Unit bahan wajib diisi.',
                'ingredients.*.price_per_unit.required' => 'Harga per unit wajib diisi.',
                'ingredients.*.price_per_unit.min' => 'Harga per unit minimal 0.'
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();
            
            $recipe = new MasterRecipe();
            $recipe->name = $request->input('name');
            $recipe->category_id = $request->input('category_id');
            $recipe->property = $request->input('property');
            $recipe->yield_quantity = $request->input('yield_quantity');
            $recipe->yield_unit = $request->input('yield_unit');
            $recipe->created_by = Auth::user()->id;
            $recipe->created_at = Carbon::now();
            $recipe->save();

            $totalCost = 0;
            foreach ($request->ingredients as $ingredient) {
                $detail = new RecipeDetail();
                $detail->recipe_id = $recipe->id;
                $detail->ingredient_id = $ingredient['ingredient_id'];
                $detail->quantity = $ingredient['quantity'];
                $detail->unit = $ingredient['unit'];
                $detail->price_per_unit = (int) str_replace(['Rp', '.', ' '], '', $ingredient['price_per_unit']);
                $detail->total_cost = $detail->quantity * $detail->price_per_unit;
                $detail->save();
                
                $totalCost += $detail->total_cost;
            }

            $recipe->total_cost = $totalCost;
            $recipe->save();

            DB::commit();
            return response()->json([
                'status' => 200,
                'message' => 'Resep berhasil disimpan'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 400,
                'message' => 'Gagal menyimpan data. Pesan Kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function edit($id)
    {
        $categories = RecipeCategory::latest('id')->get();
        $ingredients = Item::orderBy('name')->get();
        $data = MasterRecipe::with('details')->find($id);
        
        return view('admin.recipes.edit', compact('categories', 'ingredients', 'data'));
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'required|string|max:255',
                'category_id' => 'required|exists:recipe_categories,id',
                'property' => 'required|string|max:255',
                'yield_quantity' => 'required|numeric|min:1',
                'yield_unit' => 'required|string|max:50',
                'ingredients' => 'required|array|min:1',
                'ingredients.*.ingredient_id' => 'required|exists:items,id',
                'ingredients.*.quantity' => 'required|numeric|min:0.01',
                'ingredients.*.unit' => 'required|string|max:50',
                'ingredients.*.price_per_unit' => 'required|numeric|min:0'
            ],
            [
                'name.required' => 'Nama resep wajib diisi.',
                'category_id.required' => 'Kategori wajib diisi.',
                'category_id.exists' => 'Kategori tidak valid.',
                'property.required' => 'Property wajib diisi.',
                'yield_quantity.required' => 'Jumlah porsi wajib diisi.',
                'yield_quantity.min' => 'Jumlah porsi minimal 1.',
                'yield_unit.required' => 'Unit porsi wajib diisi.',
                'ingredients.required' => 'Bahan-bahan wajib diisi.',
                'ingredients.min' => 'Minimal harus ada 1 bahan.',
                'ingredients.*.ingredient_id.required' => 'Bahan wajib diisi.',
                'ingredients.*.ingredient_id.exists' => 'Bahan tidak valid.',
                'ingredients.*.quantity.required' => 'Jumlah bahan wajib diisi.',
                'ingredients.*.quantity.min' => 'Jumlah bahan minimal 0.01.',
                'ingredients.*.unit.required' => 'Unit bahan wajib diisi.',
                'ingredients.*.price_per_unit.required' => 'Harga per unit wajib diisi.',
                'ingredients.*.price_per_unit.min' => 'Harga per unit minimal 0.'
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();
            
            $recipe = MasterRecipe::findOrFail($id);
            $recipe->name = $request->input('name');
            $recipe->category_id = $request->input('category_id');
            $recipe->property = $request->input('property');
            $recipe->yield_quantity = $request->input('yield_quantity');
            $recipe->yield_unit = $request->input('yield_unit');
            $recipe->updated_by = Auth::user()->id;
            $recipe->updated_at = Carbon::now();
            $recipe->save();

            // Delete existing details
            $recipe->details()->delete();

            $totalCost = 0;
            foreach ($request->ingredients as $ingredient) {
                $detail = new RecipeDetail();
                $detail->recipe_id = $recipe->id;
                $detail->ingredient_id = $ingredient['ingredient_id'];
                $detail->quantity = $ingredient['quantity'];
                $detail->unit = $ingredient['unit'];
                $detail->price_per_unit = (int) str_replace(['Rp', '.', ' '], '', $ingredient['price_per_unit']);
                $detail->total_cost = $detail->quantity * $detail->price_per_unit;
                $detail->save();
                
                $totalCost += $detail->total_cost;
            }

            $recipe->total_cost = $totalCost;
            $recipe->save();

            DB::commit();
            return response()->json([
                'status' => 200,
                'message' => 'Resep berhasil diupdate'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 400,
                'message' => 'Gagal mengupdate data. Pesan Kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $recipe = MasterRecipe::findOrFail($id);
            $recipe->details()->delete();
            $recipe->delete();
            
            return response()->json([
                'status' => 200,
                'message' => 'Resep berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 400,
                'message' => 'Gagal menghapus data. Pesan Kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function createStockOut($id)
    {
        try {
            $recipe = MasterRecipe::with(['details.ingredient'])->findOrFail($id);
            return view('admin.recipes.stock-out', compact('recipe'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function processStockOut(Request $request, $id)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'portions' => 'required|numeric|min:1',
                'date' => 'required|date',
                'warehouse_id' => 'required|exists:warehouses,id'
            ],
            [
                'portions.required' => 'Jumlah porsi wajib diisi.',
                'portions.min' => 'Jumlah porsi minimal 1.',
                'date.required' => 'Tanggal wajib diisi.',
                'date.date' => 'Format tanggal tidak valid.',
                'warehouse_id.required' => 'Gudang wajib diisi.',
                'warehouse_id.exists' => 'Gudang tidak valid.'
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $recipe = MasterRecipe::with('details')->findOrFail($id);
            
            // Create main stock out entry
            $stockOut = StockTransaction::create([
                'type' => 'out',
                'date' => $request->date,
                'description' => "Stock out for recipe: {$recipe->name} ({$request->portions} portions)",
                'created_by' => Auth::user()->id,
                'created_at' => Carbon::now()
            ]);
            
            // Create stock out details for each ingredient
            foreach ($recipe->details as $detail) {
                $quantity = $detail->quantity * $request->portions;
                
                StockTransactionDetail::create([
                    'stock_transaction_id' => $stockOut->id,
                    'item_id' => $detail->ingredient_id,
                    'warehouse_id' => $request->warehouse_id,
                    'quantity' => $quantity,
                    'harga_satuan' => $detail->price_per_unit,
                    'total_harga' => $detail->price_per_unit * $quantity,
                    'description' => "From recipe: {$recipe->name}",
                    'created_by' => Auth::user()->id,
                    'created_at' => Carbon::now()
                ]);
            }

            DB::commit();
            return response()->json([
                'status' => 200,
                'message' => 'Stock out berhasil diproses'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 400,
                'message' => 'Gagal memproses stock out. Pesan Kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}
