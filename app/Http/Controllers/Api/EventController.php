<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $query = Event::query();

        // Only apply search filter if search term is provided and not empty
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('title', 'like', "%{$searchTerm}%")
                  ->orWhere('description', 'like', "%{$searchTerm}%")
                  ->orWhere('venue', 'like', "%{$searchTerm}%");
            });
        }

        // Only apply status filter if status is provided and not empty
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Only apply featured filter if featured is provided
        if ($request->filled('featured')) {
            $query->where('featured', $request->featured);
        }

        // Only apply date filter if date_from is provided and not empty
        if ($request->filled('date_from')) {
            $query->whereDate('start_date', '>=', $request->date_from);
        }

        // Only apply date filter if date_to is provided and not empty
        if ($request->filled('date_to')) {
            $query->whereDate('end_date', '<=', $request->date_to);
        }

        // Only apply event type filter if event_type is provided and not empty
        if ($request->filled('event_type')) {
            $query->where('event_type', $request->event_type);
        }

        // Sort by
        $sortBy = $request->get('sort_by', 'start_date');
        $sortDirection = $request->get('sort_direction', 'desc');
        $query->orderBy($sortBy, $sortDirection);

        $events = $query->paginate($request->get('per_page', 10));

        // Add photos URLs and format link for each event
        foreach ($events as $event) {
            // Add URLs for all photos
            if ($event->photos) {
                $event->photos_urls = array_map(function($photo) {
                    return url('storage/' . $photo);
                }, $event->photos);
            }

            $event->formatted_link = $event->link ? url($event->link) : null;
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Events retrieved successfully',
            'data' => $events
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'content' => 'required|string',
            'photos' => 'required|array|min:1',
            'photos.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'event_time' => 'required|date_format:H:i',
            'venue' => 'required|string|max:255',
            'link' => 'nullable|url|max:255',
            'status' => 'boolean',
            'featured' => 'boolean',
            'event_type' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $request->all();

        // Generate unique slug
        $baseSlug = Str::slug($request->title);
        $slug = $baseSlug;
        $counter = 1;

        while (Event::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        $data['slug'] = $slug;
        $data['event_time'] = date('H:i', strtotime($request->event_time));

        // Handle multiple photos upload
        $photos = [];
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $photoName = time() . '_' . uniqid() . '.' . $photo->getClientOriginalExtension();
                $photo->storeAs('public/events/photos', $photoName);
                $photos[] = 'events/photos/' . $photoName;
            }
        }
        $data['photos'] = $photos;

        $event = Event::create($data);

        // Log the activity
        ActivityLogService::logCreate(
            'event',
            "Created new event: {$event->title}",
            $event->toArray()
        );

        // Add photos URLs and formatted link to response
        if ($event->photos) {
            $event->photos_urls = array_map(function($photo) {
                return url('storage/' . $photo);
            }, $event->photos);
        }

        $event->formatted_link = $event->link ? url($event->link) : null;

        return response()->json([
            'status' => 'success',
            'message' => 'Event created successfully',
            'data' => $event
        ], 201);
    }

    public function show(Event $event)
    {
        // Add photos URLs and formatted link to response
        if ($event->photos) {
            $event->photos_urls = array_map(function($photo) {
                return url('storage/' . $photo);
            }, $event->photos);
        }

        $event->formatted_link = $event->link ? url($event->link) : null;

        return response()->json([
            'status' => 'success',
            'message' => 'Event retrieved successfully',
            'data' => $event
        ]);
    }

    public function update(Request $request, Event $event)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'content' => 'required|string',
            'existing_photos' => 'nullable|array',
            'existing_photos.*' => 'string',
            'new_photos' => 'nullable|array',
            'new_photos.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'event_time' => 'required|date_format:H:i',
            'venue' => 'required|string|max:255',
            'link' => 'nullable|url|max:255',
            'status' => 'boolean',
            'featured' => 'boolean',
            'event_type' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Custom validation: at least one photo is required
        $existingPhotos = $request->input('existing_photos', []);
        $hasNewPhotos = $request->hasFile('new_photos') && count($request->file('new_photos')) > 0;
        if (empty($existingPhotos) && !$hasNewPhotos) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => [
                    'photos' => ['At least one photo is required.']
                ]
            ], 422);
        }

        $oldData = $event->toArray();
        $data = $request->all();

        // Generate unique slug if title has changed
        if ($request->title !== $event->title) {
            $baseSlug = Str::slug($request->title);
            $slug = $baseSlug;
            $counter = 1;

            while (Event::where('slug', $slug)->where('id', '!=', $event->id)->exists()) {
                $slug = $baseSlug . '-' . $counter;
                $counter++;
            }

            $data['slug'] = $slug;
        }

        $data['event_time'] = date('H:i', strtotime($request->event_time));

        // Handle existing photos
        $existingPhotos = $request->input('existing_photos', []);
        $currentPhotos = $event->photos ?: [];

        // Find photos to delete (photos that exist in current but not in existing_photos)
        $photosToDelete = array_diff($currentPhotos, $existingPhotos);

        // Delete removed photos from storage
        foreach ($photosToDelete as $photoToDelete) {
            Storage::delete('public/' . $photoToDelete);
        }

        // Handle new photos upload
        $newPhotos = [];
        if ($request->hasFile('new_photos')) {
            foreach ($request->file('new_photos') as $photo) {
                $photoName = time() . '_' . uniqid() . '.' . $photo->getClientOriginalExtension();
                $photo->storeAs('public/events/photos', $photoName);
                $newPhotos[] = 'events/photos/' . $photoName;
            }
        }

        // Combine existing and new photos
        $allPhotos = array_merge($existingPhotos, $newPhotos);
        $data['photos'] = $allPhotos;

        $event->update($data);

        // Log the activity
        ActivityLogService::logUpdate(
            'event',
            "Updated event: {$event->title}",
            $oldData,
            $event->toArray()
        );

        // Add photos URLs and formatted link to response
        if ($event->photos) {
            $event->photos_urls = array_map(function($photo) {
                return url('storage/' . $photo);
            }, $event->photos);
        }

        $event->formatted_link = $event->link ? url($event->link) : null;

        return response()->json([
            'status' => 'success',
            'message' => 'Event updated successfully',
            'data' => $event
        ]);
    }

    public function destroy(Event $event)
    {
        $eventData = $event->toArray();

        // Delete all photos
        if ($event->photos) {
            foreach ($event->photos as $photo) {
                Storage::delete('public/' . $photo);
            }
        }

        $event->delete();

        // Log the activity
        ActivityLogService::logDelete(
            'event',
            "Deleted event: {$eventData['title']}",
            $eventData
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Event deleted successfully',
            'data' => null
        ]);
    }

    public function publicList(Request $request)
    {
        $query = Event::query();

        // Only apply status filter if status is provided and not empty
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            // Default to active events if no status is provided
            $query->where('status', 1);
        }

        // Only apply featured filter if featured is provided
        if ($request->filled('featured')) {
            $query->where('featured', $request->featured);
        }

        // Only apply date filter if date_from is provided and not empty
        if ($request->filled('date_from')) {
            $query->whereDate('start_date', '>=', $request->date_from);
        }

        // Only apply date filter if date_to is provided and not empty
        if ($request->filled('date_to')) {
            $query->whereDate('end_date', '<=', $request->date_to);
        }

        // Only apply search filter if search term is provided and not empty
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('title', 'like', "%{$searchTerm}%")
                  ->orWhere('description', 'like', "%{$searchTerm}%")
                  ->orWhere('venue', 'like', "%{$searchTerm}%");
            });
        }

        // Only apply event type filter if event_type is provided and not empty
        if ($request->filled('event_type')) {
            $query->where('event_type', $request->event_type);
        }

        // Sort by
        $sortBy = $request->get('sort_by', 'start_date');
        $sortDirection = $request->get('sort_direction', 'asc');
        $query->orderBy($sortBy, $sortDirection);

        $events = $query->paginate($request->get('per_page', 10));

        // Add photos URLs and formatted link for each event
        foreach ($events as $event) {
            // Add URLs for all photos
            if ($event->photos) {
                $event->photos_urls = array_map(function($photo) {
                    return url('storage/' . $photo);
                }, $event->photos);
            }

            $event->formatted_link = $event->link ? url($event->link) : null;
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Public events retrieved successfully',
            'data' => $events
        ]);
    }
}
