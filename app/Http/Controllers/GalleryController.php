<?php

namespace App\Http\Controllers;

use App\Models\Gallery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GalleryController extends Controller
{
    public function index(Request $request)
    {
        $query = Gallery::with('category');

        // Search by title or description
        if ($request->has('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('title', 'like', "%{$searchTerm}%")
                  ->orWhere('description', 'like', "%{$searchTerm}%");
            });
        }

        // Filter by category
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Sort by
        $sortBy = $request->get('sort_by', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');
        $query->orderBy($sortBy, $sortDirection);

        $galleries = $query->paginate($request->get('per_page', 10));
        return response()->json($galleries);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'category_id' => 'required|exists:categories,id',
            'status' => 'required|in:active,inactive'
        ]);

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imagePath = $image->store('gallery', 'public');

            $gallery = Gallery::create([
                'title' => $request->title,
                'description' => $request->description,
                'image_path' => $imagePath,
                'category_id' => $request->category_id,
                'status' => $request->status
            ]);

            return response()->json([
                'message' => 'Gallery item created successfully',
                'data' => $gallery->load('category')
            ], 201);
        }

        return response()->json(['message' => 'Image upload failed'], 400);
    }

    public function show(Gallery $gallery)
    {
        return response()->json($gallery->load('category'));
    }

    public function update(Request $request, Gallery $gallery)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'category_id' => 'required|exists:categories,id',
            'status' => 'required|in:active,inactive'
        ]);

        if ($request->hasFile('image')) {
            if ($gallery->image_path) {
                Storage::disk('public')->delete($gallery->image_path);
            }

            $image = $request->file('image');
            $imagePath = $image->store('gallery', 'public');
            $gallery->image_path = $imagePath;
        }

        $gallery->title = $request->title;
        $gallery->description = $request->description;
        $gallery->category_id = $request->category_id;
        $gallery->status = $request->status;
        $gallery->save();

        return response()->json([
            'message' => 'Gallery item updated successfully',
            'data' => $gallery->load('category')
        ]);
    }

    public function destroy(Gallery $gallery)
    {
        if ($gallery->image_path) {
            Storage::disk('public')->delete($gallery->image_path);
        }

        $gallery->delete();

        return response()->json([
            'message' => 'Gallery item deleted successfully'
        ]);
    }
}
