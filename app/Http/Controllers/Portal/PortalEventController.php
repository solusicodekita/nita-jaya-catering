<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PortalEventController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $events = \DB::table('portal_events')->orderBy('event_date', 'desc')->get();
        return view('portal.events.index', compact('events'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('portal.events.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'image' => 'nullable|image|max:3072',
            'event_date' => 'nullable|date'
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('events', 'public');
        }

        $eventId = \DB::table('portal_events')->insertGetId([
            'title' => $request->title,
            'slug' => \Illuminate\Support\Str::slug($request->title),
            'image' => $imagePath,
            'description' => $request->description,
            'event_date' => $request->event_date,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        if ($request->hasFile('gallery_images')) {
            foreach ($request->file('gallery_images') as $file) {
                $path = $file->store('events/gallery', 'public');
                \DB::table('portal_event_images')->insert([
                    'event_id' => $eventId,
                    'image' => $path,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        return redirect()->route('portal.events.index')->with('success', 'Event berhasil didokumentasikan dengan galeri foto.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $event = \DB::table('portal_events')->where('id', $id)->first();
        $gallery = \DB::table('portal_event_images')->where('event_id', $id)->get();
        return view('portal.events.edit', compact('event', 'gallery'));
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
        $request->validate([
            'title' => 'required',
            'image' => 'nullable|image|max:3072',
        ]);

        $updateData = [
            'title' => $request->title,
            'slug' => \Illuminate\Support\Str::slug($request->title),
            'description' => $request->description,
            'event_date' => $request->event_date,
            'updated_at' => now(),
        ];

        if ($request->hasFile('image')) {
            $updateData['image'] = $request->file('image')->store('events', 'public');
        }

        \DB::table('portal_events')->where('id', $id)->update($updateData);

        if ($request->hasFile('gallery_images')) {
            foreach ($request->file('gallery_images') as $file) {
                $path = $file->store('events/gallery', 'public');
                \DB::table('portal_event_images')->insert([
                    'event_id' => $id,
                    'image' => $path,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        return redirect()->route('portal.events.index')->with('success', 'Data event dan galeri foto diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        \DB::table('portal_events')->where('id', $id)->delete();
        return redirect()->route('portal.events.index')->with('success', 'Event dihapus.');
    }
}
