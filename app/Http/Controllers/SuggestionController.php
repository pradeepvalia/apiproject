<?php

namespace App\Http\Controllers;

use App\Models\Suggestion;
use Illuminate\Http\Request;

class SuggestionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Suggestion::query();

        // Search by name, email, phone, or description
        if ($request->has('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('email', 'like', "%{$searchTerm}%")
                  ->orWhere('phone', 'like', "%{$searchTerm}%")
                  ->orWhere('description', 'like', "%{$searchTerm}%");
            });
        }

        // Filter by read/unread status
        if ($request->has('is_read')) {
            $query->where('is_read', $request->boolean('is_read'));
        }

        // Date range filter
        if ($request->has('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->has('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Sort by
        $sortBy = $request->get('sort_by', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');
        $query->orderBy($sortBy, $sortDirection);

        $suggestions = $query->paginate($request->get('per_page', 10));
        return response()->json($suggestions);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'description' => 'required|string'
        ]);

        $suggestion = Suggestion::create($request->all());

        return response()->json([
            'message' => 'Suggestion submitted successfully',
            'data' => $suggestion
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Suggestion $suggestion)
    {
        return response()->json($suggestion);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Suggestion $suggestion)
    {
        $suggestion->delete();

        return response()->json([
            'message' => 'Suggestion deleted successfully'
        ]);
    }

    public function markAsRead(Suggestion $suggestion)
    {
        $suggestion->update(['is_read' => true]);

        return response()->json([
            'message' => 'Suggestion marked as read',
            'data' => $suggestion
        ]);
    }

    public function markAsUnread(Suggestion $suggestion)
    {
        $suggestion->update(['is_read' => false]);

        return response()->json([
            'message' => 'Suggestion marked as unread',
            'data' => $suggestion
        ]);
    }
}
