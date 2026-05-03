<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Item;

class PortalMenuController extends Controller
{
    public function index()
    {
        $reseps = \DB::table('portal_menus')->orderBy('created_at', 'desc')->get();
        return view('portal.resep.index', compact('reseps'));
    }

    public function create()
    {
        $posItems = Item::all();
        $categories = \DB::table('portal_menus')->whereNotNull('category')->distinct()->pluck('category');
        return view('portal.resep.create', compact('posItems', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'image' => 'nullable|image|max:2048',
            'description' => 'nullable'
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('resep', 'public');
        }

        // Handle Select2 multi-select items
        $items = $request->items;
        if (is_array($items)) {
            $items = implode("\n", $items);
        }

        \DB::table('portal_menus')->insert([
            'name' => $request->name,
            'slug' => \Illuminate\Support\Str::slug($request->name),
            'image' => $imagePath,
            'description' => $request->description,
            'items' => $items,
            'price' => $request->price,
            'category' => $request->category,
            'is_promoted' => $request->has('is_promoted'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('portal.menus.index')->with('success', 'Resepi berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $resep = \DB::table('portal_menus')->where('id', $id)->first();
        $posItems = Item::all();
        $categories = \DB::table('portal_menus')->whereNotNull('category')->distinct()->pluck('category');
        return view('portal.resep.edit', compact('resep', 'posItems', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'image' => 'nullable|image|max:2048',
        ]);

        // Handle Select2 multi-select items
        $items = $request->items;
        if (is_array($items)) {
            $items = implode("\n", $items);
        }

        $updateData = [
            'name' => $request->name,
            'slug' => \Illuminate\Support\Str::slug($request->name),
            'description' => $request->description,
            'items' => $items,
            'price' => $request->price,
            'category' => $request->category,
            'is_promoted' => $request->has('is_promoted'),
            'updated_at' => now(),
        ];

        if ($request->hasFile('image')) {
            $updateData['image'] = $request->file('image')->store('resep', 'public');
        }

        \DB::table('portal_menus')->where('id', $id)->update($updateData);

        return redirect()->route('portal.menus.index')->with('success', 'Resepi berhasil diperbarui.');
    }

    public function destroy($id)
    {
        \DB::table('portal_menus')->where('id', $id)->delete();
        return redirect()->route('portal.menus.index')->with('success', 'Resepi berhasil dihapus.');
    }
}
