<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PortalDashboardController extends Controller
{
    public function index()
    {
        $promotedCount = \DB::table('portal_menus')->where('is_promoted', true)->count();
        $totalMenus = \DB::table('portal_menus')->count();
        $company = \DB::table('company_settings')->first();

        return view('portal.dashboard', compact('promotedCount', 'totalMenus', 'company'));
    }
}
