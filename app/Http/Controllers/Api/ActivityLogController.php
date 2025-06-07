<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    private function processFileUrls($data)
    {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                if (is_array($value)) {
                    $data[$key] = $this->processFileUrls($value);
                } else {
                    // Check for common file path fields and ensure value contains a file extension
                    if (in_array($key, ['image', 'image_path', 'file_path']) &&
                        $value &&
                        preg_match('/\.(jpg|jpeg|png|gif|pdf|doc|docx|xls|xlsx|txt)$/i', $value)) {
                        $data[$key] = url('storage/' . $value);
                    }
                }
            }
        }
        return $data;
    }

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

        // Process file URLs in old_values and new_values
        foreach ($logs->items() as $log) {
            if ($log->old_values) {
                $log->old_values = $this->processFileUrls($log->old_values);
            }
            if ($log->new_values) {
                $log->new_values = $this->processFileUrls($log->new_values);
            }
        }

        return response()->json([
            'status' => 'success',
            'data' => $logs
        ]);
    }

    public function show(ActivityLog $activityLog)
    {
        // Process file URLs in old_values and new_values
        if ($activityLog->old_values) {
            $activityLog->old_values = $this->processFileUrls($activityLog->old_values);
        }
        if ($activityLog->new_values) {
            $activityLog->new_values = $this->processFileUrls($activityLog->new_values);
        }

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
