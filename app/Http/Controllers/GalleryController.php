<?php

namespace App\Http\Controllers;

use App\Models\Gallery;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class GalleryController extends Controller
{
    public function index(Request $request)
    {
        $query = Gallery::with('category');

        // Only apply search filter if search term is provided and not empty
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('title', 'like', "%{$searchTerm}%")
                  ->orWhere('description', 'like', "%{$searchTerm}%");
            });
        }

        // Only apply category filter if category_id is provided and not empty
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Only apply status filter if status is provided and not empty
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Sort by
        $sortBy = $request->get('sort_by', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');
        $query->orderBy($sortBy, $sortDirection);

        $galleries = $query->paginate($request->get('per_page', 10));

        // Add image URLs to each gallery
        foreach ($galleries as $gallery) {
            if ($gallery->image_path) {
                $gallery->image_url = url('storage/' . $gallery->image_path);
            }
        }

        return response()->json($galleries);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'status' => 'required|in:active,inactive',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $request->all();

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->storeAs('public/gallery', $imageName);
            $data['image_path'] = 'gallery/' . $imageName;
        }

        $gallery = Gallery::create($data);

        // Log the activity
        ActivityLogService::logCreate(
            'gallery',
            "Created new gallery item: {$gallery->title}",
            $gallery->toArray()
        );

        // Add image URL to response
        if ($gallery->image_path) {
            $gallery->image_url = url('storage/' . $gallery->image_path);
        }

        return response()->json([
            'message' => 'Gallery created successfully',
            'gallery' => $gallery
        ], 201);
    }

    public function show(Gallery $gallery)
    {
        // Add image URL to response
        if ($gallery->image_path) {
            $gallery->image_url = url('storage/' . $gallery->image_path);
        }
        return response()->json($gallery);
    }

    public function update(Request $request, Gallery $gallery)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'status' => 'required|in:active,inactive',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $oldData = $gallery->toArray();
        $data = $request->all();

        if ($request->hasFile('image')) {
            // Delete old image
            if ($gallery->image_path) {
                Storage::delete('public/' . $gallery->image_path);
            }

            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->storeAs('public/gallery', $imageName);
            $data['image_path'] = 'gallery/' . $imageName;
        }

        $gallery->update($data);

        // Log the activity
        ActivityLogService::logUpdate(
            'gallery',
            "Updated gallery item: {$gallery->title}",
            $oldData,
            $gallery->toArray()
        );

        // Add image URL to response
        if ($gallery->image_path) {
            $gallery->image_url = url('storage/' . $gallery->image_path);
        }

        return response()->json([
            'message' => 'Gallery updated successfully',
            'gallery' => $gallery
        ]);
    }

    public function destroy(Gallery $gallery)
    {
        $galleryData = $gallery->toArray();

        if ($gallery->image_path) {
            Storage::disk('public')->delete($gallery->image_path);
        }

        $gallery->delete();

        // Log the activity
        ActivityLogService::logDelete(
            'gallery',
            "Deleted gallery item: {$galleryData['title']}",
            $galleryData
        );

        return response()->json([
            'message' => 'Gallery item deleted successfully'
        ]);
    }

    /**
     * Display a listing of active galleries for public access.
     */
    public function publicList(Request $request)
    {
        $query = Gallery::with('category')->where('status', 'active');

        // Only apply search filter if search term is provided and not empty
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('title', 'like', "%{$searchTerm}%")
                  ->orWhere('description', 'like', "%{$searchTerm}%");
            });
        }

        // Only apply category filter if category_id is provided and not empty
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Sort by
        $sortBy = $request->get('sort_by', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');
        $query->orderBy($sortBy, $sortDirection);

        $galleries = $query->paginate($request->get('per_page', 10));

        // Add image URLs to each gallery
        foreach ($galleries as $gallery) {
            if ($gallery->image_path) {
                $gallery->image_url = url('storage/' . $gallery->image_path);
            }
        }

        return response()->json([
            'status' => 'success',
            'data' => $galleries
        ]);
    }
}
