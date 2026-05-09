<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index()
    {
        $logs = ActivityLog::with('user')->orderBy('created_at', 'desc')->paginate(50);
        return view('admin.activity_logs.index', compact('logs'));
    }

    public function show($id)
    {
        $log = ActivityLog::with('user')->findOrFail($id);
        return response()->json($log);
    }
}
