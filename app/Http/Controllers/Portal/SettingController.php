<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function edit()
    {
        $setting = \DB::table('company_settings')->where('id', 1)->first();
        return view('portal.settings.edit', compact('setting'));
    }

    public function hero()
    {
        $setting = \DB::table('company_settings')->where('id', 1)->first();
        return view('portal.settings.hero', compact('setting'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'company_name' => 'required',
            'phone' => 'required',
            'address' => 'required',
            'about_us' => 'nullable',
            'custom_scripts' => 'nullable',
            'logo' => 'nullable|image|max:2048',
            'hero_image' => 'nullable|image|max:2048',
        ]);

        $updateData = [
            'company_name' => $request->company_name,
            'phone' => $request->phone,
            'address' => $request->address,
            'about_us' => $request->about_us,
            'custom_scripts' => $request->custom_scripts,
            'updated_at' => now(),
        ];

        if ($request->hasFile('logo')) {
            $updateData['logo'] = $request->file('logo')->store('company', 'public');
        }

        if ($request->hasFile('hero_image')) {
            $updateData['hero_image'] = $request->file('hero_image')->store('company', 'public');
        }

        \DB::table('company_settings')->where('id', 1)->update($updateData);

        return redirect()->route('portal.settings.edit')->with('success', 'Profil katering berhasil diperbarui.');
    }
}
