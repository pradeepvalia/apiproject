<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Category::with('galleries');

        // Search by name or description
        if ($request->has('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('description', 'like', "%{$searchTerm}%");
            });
        }

        // Sort by
        $sortBy = $request->get('sort_by', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');
        $query->orderBy($sortBy, $sortDirection);

        $categories = $query->paginate($request->get('per_page', 10));
        return response()->json($categories);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);

        $category = Category::create($request->all());

        // Log the activity
        ActivityLogService::logCreate(
            'category',
            "Created new category: {$category->name}",
            $category->toArray()
        );

        return response()->json([
            'message' => 'Category created successfully',
            'data' => $category
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        return response()->json($category->load('galleries'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);

        $oldData = $category->toArray();
        $category->update($request->all());

        // Log the activity
        ActivityLogService::logUpdate(
            'category',
            "Updated category: {$category->name}",
            $oldData,
            $category->toArray()
        );

        return response()->json([
            'message' => 'Category updated successfully',
            'data' => $category
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        $categoryData = $category->toArray();
        $category->delete();

        // Log the activity
        ActivityLogService::logDelete(
            'category',
            "Deleted category: {$categoryData['name']}",
            $categoryData
        );

        return response()->json([
            'message' => 'Category deleted successfully'
        ]);
    }

    /**
     * Display a listing of active categories for public access.
     */
    public function publicList(Request $request)
    {
        $query = Category::with(['galleries' => function($query) {
            $query->where('status', 'active');
        }])->where('status', 'active');

        // Search by name or description
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('description', 'like', "%{$searchTerm}%");
            });
        }

        // Sort by
        $sortBy = $request->get('sort_by', 'name');
        $sortDirection = $request->get('sort_direction', 'asc');
        $query->orderBy($sortBy, $sortDirection);

        $categories = $query->paginate($request->get('per_page', 10));

        return response()->json([
            'status' => 'success',
            'data' => $categories
        ]);
    }
}
