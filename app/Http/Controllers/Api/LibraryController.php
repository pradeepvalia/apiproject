<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Library;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class LibraryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $libraries = Library::latest()->get();
        // Add full URL to each library item
        $libraries->transform(function ($library) {
            $library->file_url = Storage::url($library->file_path);
            return $library;
        });

        return response()->json([
            'status' => 'success',
            'data' => $libraries
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'file' => 'required|file|max:10240' // 10MB max
        ]);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $originalFilename = $file->getClientOriginalName();
            $fileType = $file->getClientOriginalExtension();

            // Generate unique filename
            $filename = Str::uuid() . '.' . $fileType;

            // Store file in storage/app/public/library
            $filePath = $file->storeAs('library', $filename, 'public');

            $library = Library::create([
                'title' => $request->title,
                'description' => $request->description,
                'file_path' => $filePath,
                'original_filename' => $originalFilename,
                'file_type' => $fileType
            ]);

            // Add full URL to the response
            $library->file_url = Storage::url($filePath);

            // Log the activity
            ActivityLogService::logCreate(
                'library',
                "Created new library item: {$library->title}",
                $library->toArray()
            );

            return response()->json([
                'status' => 'success',
                'message' => 'Library item created successfully',
                'data' => $library
            ], 201);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'No file uploaded'
        ], 400);
    }

    /**
     * Display the specified resource.
     */
    public function show(Library $library)
    {
        // Add full URL to the response
        $library->file_url = Storage::url($library->file_path);

        return response()->json([
            'status' => 'success',
            'data' => $library
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Library $library)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'file' => 'nullable|file|max:10240' // 10MB max
        ]);

        $oldData = $library->toArray();
        $data = [
            'title' => $request->title,
            'description' => $request->description,
        ];

        if ($request->hasFile('file')) {
            // Delete old file
            if ($library->file_path) {
                Storage::disk('public')->delete($library->file_path);
            }

            $file = $request->file('file');
            $originalFilename = $file->getClientOriginalName();
            $fileType = $file->getClientOriginalExtension();

            // Generate unique filename
            $filename = Str::uuid() . '.' . $fileType;

            // Store new file
            $filePath = $file->storeAs('library', $filename, 'public');

            $data['file_path'] = $filePath;
            $data['original_filename'] = $originalFilename;
            $data['file_type'] = $fileType;
        }

        $library->update($data);

        // Add full URL to the response
        $library->file_url = Storage::url($library->file_path);

        // Log the activity
        ActivityLogService::logUpdate(
            'library',
            "Updated library item: {$library->title}",
            $oldData,
            $library->toArray()
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Library item updated successfully',
            'data' => $library
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Library $library)
    {
        $libraryData = $library->toArray();

        // Delete file from storage
        if ($library->file_path) {
            Storage::disk('public')->delete($library->file_path);
        }

        $library->delete();

        // Log the activity
        ActivityLogService::logDelete(
            'library',
            "Deleted library item: {$libraryData['title']}",
            $libraryData
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Library item deleted successfully'
        ]);
    }

    public function download(Library $library)
    {
        if (!Storage::disk('public')->exists($library->file_path)) {
            return response()->json([
                'status' => 'error',
                'message' => 'File not found'
            ], 404);
        }

        return Storage::disk('public')->download(
            $library->file_path,
            $library->original_filename
        );
    }
}
