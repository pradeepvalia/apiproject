<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $query = ActivityLog::with('user');

        // Filter by module
        if ($request->filled('module')) {
            $query->where('module', $request->module);
        }

        // Filter by action
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Search in description
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where('description', 'like', "%{$searchTerm}%");
        }

        // Date range filter
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Sort by
        $sortBy = $request->get('sort_by', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');
        $query->orderBy($sortBy, $sortDirection);

        // Pagination
        $perPage = $request->get('per_page', 15);
        $logs = $query->paginate($perPage);

        return response()->json([
            'status' => 'success',
            'data' => $logs
        ]);
    }

    public function show(ActivityLog $activityLog)
    {
        return response()->json([
            'status' => 'success',
            'data' => $activityLog->load('user')
        ]);
    }

    public function getModules()
    {
        $modules = ActivityLog::distinct()->pluck('module');
        return response()->json([
            'status' => 'success',
            'data' => $modules
        ]);
    }

    public function getActions()
    {
        $actions = ActivityLog::distinct()->pluck('action');
        return response()->json([
            'status' => 'success',
            'data' => $actions
        ]);
    }
}
