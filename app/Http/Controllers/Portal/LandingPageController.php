<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LandingPageController extends Controller
{
    public function index()
    {
        $company = \DB::table('company_settings')->first();
        $promotedMenus = \DB::table('portal_menus')->where('is_promoted', true)->where('is_active', true)->get();
        $services = \DB::table('portal_services')->where('is_active', true)->get();
        $events = \DB::table('portal_events')->where('is_active', true)->orderBy('event_date', 'desc')->take(6)->get();

        return view('welcome', compact('company', 'promotedMenus', 'services', 'events'));
    }

    public function show($slug)
    {
        $company = \DB::table('company_settings')->first();
        $menu = \DB::table('portal_menus')->where('slug', $slug)->where('is_active', true)->first();
        
        if (!$menu) abort(404);

        return view('portal.public.menu_detail', compact('company', 'menu'));
    }

    public function event_show($slug)
    {
        $company = \DB::table('company_settings')->first();
        $event = \DB::table('portal_events')->where('slug', $slug)->where('is_active', true)->first();
        
        if (!$event) abort(404);

        $gallery = \DB::table('portal_event_images')->where('event_id', $event->id)->get();

        return view('portal.public.event_detail', compact('company', 'event', 'gallery'));
    }

    public function privacy()
    {
        $company = \DB::table('company_settings')->first();
        return view('portal.public.privacy', compact('company'));
    }
}
