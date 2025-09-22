<?php

namespace App\Http\Controllers;

use App\Models\RecipeCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RecipeCategoryController extends Controller
{
    public function index()
    {
        $model = RecipeCategory::orderBy('id', 'desc')->get();
        return view('recipe_category.index', compact('model'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string'
        ]);

        RecipeCategory::create($request->all());
        return redirect()->route('recipe_category.index')->with('success', 'Kategori resep berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string'
        ]);

        $category = RecipeCategory::findOrFail($id);
        $category->update($request->all());
        return redirect()->route('recipe_category.index')->with('success', 'Kategori resep berhasil diupdate');
    }

    public function destroy($id)
    {
        $category = RecipeCategory::findOrFail($id);
        $category->delete();
        return redirect()->route('recipe_category.index')->with('success', 'Kategori resep berhasil dihapus');
    }
}