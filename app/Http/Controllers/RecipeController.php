<?php

namespace App\Http\Controllers;

use App\Models\MasterRecipe;
use App\Models\RecipeCategory;
use App\Models\RecipeDetail;
use App\Models\Item;
use App\Models\StockTransaction;
use App\Models\StockTransactionDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RecipeController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $model = MasterRecipe::with(['category', 'details']);
            return DataTables::of($model)
                ->addIndexColumn()
                ->addColumn('action', function($row){
                    $btn = '<a href="'.route('recipes.show', $row->id).'" class="btn btn-info btn-sm">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="'.route('recipes.edit', $row->id).'" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i>
                        </a>
                        <button type="button" class="btn btn-danger btn-sm" onclick="confirmDelete('.$row->id.')">
                            <i class="fas fa-trash"></i>
                        </button>
                        <form id="delete-form-'.$row->id.'" action="'.route('recipes.destroy', $row->id).'" method="POST" style="display:none">
                            '.csrf_field().'
                            '.method_field('DELETE').'
                        </form>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('recipes.index');
    }

    public function create()
    {
        $categories = RecipeCategory::all();
        $ingredients = Item::all();
        return view('recipes.create', compact('categories', 'ingredients'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'category_id' => 'required|exists:recipe_categories,id',
            'property' => 'required',
            'yield_quantity' => 'required|numeric|min:1',
            'yield_unit' => 'required',
            'ingredients' => 'required|array|min:1',
            'ingredients.*.ingredient_id' => 'required|exists:items,id',
            'ingredients.*.quantity' => 'required|numeric|min:0.01',
            'ingredients.*.unit' => 'required|string',
            'ingredients.*.price_per_unit' => 'required|numeric|min:0'
        ]);

        try {
            DB::transaction(function () use ($request) {
                $recipe = MasterRecipe::create($request->except('ingredients'));
                
                $totalCost = 0;
                foreach ($request->ingredients as $ingredient) {
                    $detail = new RecipeDetail($ingredient);
                    $detail->total_cost = $ingredient['quantity'] * $ingredient['price_per_unit'];
                    $recipe->details()->save($detail);
                    $totalCost += $detail->total_cost;
                }
                
                $recipe->update(['total_cost' => $totalCost]);
            });

            return redirect()->route('recipes.index')->with('success', 'Resep berhasil dibuat');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function show($id)
    {
        try {
            $recipe = MasterRecipe::with(['category', 'details.ingredient'])->findOrFail($id);
            return view('recipes.show', compact('recipe'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function edit($id)
    {
        try {
            $recipe = MasterRecipe::with('details.ingredient')->findOrFail($id);
            $categories = RecipeCategory::all();
            $ingredients = Item::all();
            return view('recipes.edit', compact('recipe', 'categories', 'ingredients'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'category_id' => 'required|exists:recipe_categories,id',
            'property' => 'required',
            'yield_quantity' => 'required|numeric|min:1',
            'yield_unit' => 'required',
            'ingredients' => 'required|array|min:1',
            'ingredients.*.ingredient_id' => 'required|exists:items,id',
            'ingredients.*.quantity' => 'required|numeric|min:0.01',
            'ingredients.*.unit' => 'required|string',
            'ingredients.*.price_per_unit' => 'required|numeric|min:0'
        ]);

        try {
            DB::transaction(function () use ($request, $id) {
                $recipe = MasterRecipe::findOrFail($id);
                $recipe->update($request->except('ingredients'));
                
                // Delete existing details
                $recipe->details()->delete();
                
                $totalCost = 0;
                foreach ($request->ingredients as $ingredient) {
                    $detail = new RecipeDetail($ingredient);
                    $detail->total_cost = $ingredient['quantity'] * $ingredient['price_per_unit'];
                    $recipe->details()->save($detail);
                    $totalCost += $detail->total_cost;
                }
                
                $recipe->update(['total_cost' => $totalCost]);
            });

            return redirect()->route('recipes.index')->with('success', 'Resep berhasil diupdate');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $recipe = MasterRecipe::findOrFail($id);
            $recipe->delete();
            return redirect()->route('recipes.index')->with('success', 'Resep berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function createStockOut($id)
    {
        try {
            $recipe = MasterRecipe::with(['details.ingredient'])->findOrFail($id);
            return view('recipes.stock-out', compact('recipe'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function processStockOut(Request $request, $id)
    {
        $request->validate([
            'portions' => 'required|numeric|min:1',
            'date' => 'required|date',
            'warehouse_id' => 'required|exists:warehouses,id'
        ]);

        try {
            DB::transaction(function () use ($recipe, $request) {
                $recipe = MasterRecipe::with('details')->findOrFail($id);
                
                // Create main stock out entry
                $stockOut = StockTransaction::create([
                    'type' => 'out',
                    'date' => $request->date,
                    'description' => "Stock out for recipe: {$recipe->name} ({$request->portions} portions)"
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
                        'description' => "From recipe: {$recipe->name}"
                    ]);
                }
            });

            return redirect()->route('recipes.show', $id)->with('success', 'Stock out berhasil diproses');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}