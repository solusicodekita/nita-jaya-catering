<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SitemapController extends Controller
{
    public function index()
    {
        $menus = DB::table('portal_menus')->where('is_active', true)->get();
        $events = DB::table('portal_events')->where('is_active', true)->get();

        return response()->view('portal.public.sitemap', [
            'menus' => $menus,
            'events' => $events,
        ])->header('Content-Type', 'text/xml');
    }
}
