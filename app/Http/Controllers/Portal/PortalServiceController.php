<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PortalServiceController extends Controller
{
    public function index()
    {
        $services = \DB::table('portal_services')->orderBy('created_at', 'desc')->get();
        return view('portal.services.index', compact('services'));
    }

    public function create()
    {
        return view('portal.services.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'image' => 'nullable|image|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('services', 'public');
        }

        \DB::table('portal_services')->insert([
            'title' => $request->title,
            'description' => $request->description,
            'image' => $imagePath,
            'is_active' => $request->has('is_active'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('portal.services.index')->with('success', 'Layanan berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $service = \DB::table('portal_services')->where('id', $id)->first();
        return view('portal.services.edit', compact('service'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required',
            'image' => 'nullable|image|max:2048',
        ]);

        $updateData = [
            'title' => $request->title,
            'description' => $request->description,
            'is_active' => $request->has('is_active'),
            'updated_at' => now(),
        ];

        if ($request->hasFile('image')) {
            $updateData['image'] = $request->file('image')->store('services', 'public');
        }

        \DB::table('portal_services')->where('id', $id)->update($updateData);

        return redirect()->route('portal.services.index')->with('success', 'Layanan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        \DB::table('portal_services')->where('id', $id)->delete();
        return redirect()->route('portal.services.index')->with('success', 'Layanan berhasil dihapus.');
    }
}
