<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class MenuController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = Menu::latest('id')->get();
        return view('admin.menu.index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.menu.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'required|string|max:255',
                'is_active' => 'required',
            ],
            [
                'name.required' => 'Nama menu wajib diisi.',
                'is_active.required' => 'Status wajib diisi.',
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->errors()
            ], 422);
        }

        $check = Menu::where('name', $request->input('name'))->exists();
        if ($check) {
            return response()->json([
                'status' => 500,
                'message' => 'Nama Menu sudah ada dalam database'
            ], 500);
        }

        try {
            DB::beginTransaction();
            $kategori = new Menu();
            $kategori->name = $request->input('name');
            $kategori->is_active = $request->input('is_active');
            $kategori->created_by = Auth::user()->id;
            $kategori->created_at = Carbon::now();
            $kategori->save();
            DB::commit();
            return response()->json([
                'status' => 200,
                'message' => 'Data menu berhasil di Simpan',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 400,
                'message' => 'Gagal menyimpan data. Pesan Kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $menu = Menu::find($id);
        return view('admin.menu.edit', compact('menu'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'required|string|max:255',
                'is_active' => 'required',
            ],
            [
                'name.required' => 'Nama menu wajib diisi.',
                'is_active.required' => 'Status wajib diisi.',
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->errors()
            ], 422);
        }

        $cekDataLama = Menu::find($request->input('id'));

        if ($cekDataLama->name == $request->input('name')) {
            $check = false;
        } else {
            $check = Menu::where('name', $request->input('name'))->where('id', '!=', $id)->exists();
        }

        if ($check) {
            return response()->json([
                'status' => 500,
                'message' => 'Nama menu sudah ada dalam database'
            ], 500);
        }

        try {
            DB::beginTransaction();
            $kategori = Menu::find($request->input('id'));
            $kategori->name = $request->input('name');
            $kategori->is_active = $request->input('is_active');
            $kategori->updated_by = Auth::user()->id;
            $kategori->updated_at = Carbon::now();
            $kategori->save();
            DB::commit();
            return response()->json([
                'status' => 200,
                'message' => 'Data menu berhasil diubah',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 400,
                'message' => 'Gagal menyimpan data. Pesan Kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $kategori = Menu::find($id);
        $kategori->delete();
        return response()->json([
            'status' => 200,
            'message' => 'Data menu berhasil dihapus',
        ]);
    }
}
