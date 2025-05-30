<?php

namespace App\Http\Controllers;

use App\Models\Suggestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SuggestionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Suggestion::query();

        // Only apply search filter if search term is provided and not empty
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('email', 'like', "%{$searchTerm}%")
                  ->orWhere('phone', 'like', "%{$searchTerm}%")
                  ->orWhere('subject', 'like', "%{$searchTerm}%")
                  ->orWhere('description', 'like', "%{$searchTerm}%");
            });
        }

        // Only apply read status filter if is_read is provided and not empty
        if ($request->filled('is_read')) {
            $query->where('is_read', $request->is_read);
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

        $suggestions = $query->paginate($request->get('per_page', 10));
        return response()->json($suggestions);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'subject' => 'required|string|max:255',
            'description' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $suggestion = Suggestion::create($request->all());

        return response()->json([
            'message' => 'Suggestion submitted successfully',
            'suggestion' => $suggestion
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
    public function update(Request $request, Suggestion $suggestion)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
            'is_read' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $suggestion->update($request->all());

        return response()->json([
            'message' => 'Suggestion updated successfully',
            'suggestion' => $suggestion
        ]);
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
            'suggestion' => $suggestion
        ]);
    }

    public function markAsUnread(Suggestion $suggestion)
    {
        $suggestion->update(['is_read' => false]);

        return response()->json([
            'message' => 'Suggestion marked as unread',
            'suggestion' => $suggestion
        ]);
    }
}
