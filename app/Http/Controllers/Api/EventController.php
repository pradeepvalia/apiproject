<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
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

        // Only apply date filter if date_from is provided and not empty
        if ($request->filled('date_from')) {
            $query->whereDate('event_date', '>=', $request->date_from);
        }

        // Only apply date filter if date_to is provided and not empty
        if ($request->filled('date_to')) {
            $query->whereDate('event_date', '<=', $request->date_to);
        }

        // Only apply event type filter if event_type is provided and not empty
        if ($request->filled('event_type')) {
            $query->where('event_type', $request->event_type);
        }

        // Sort by
        $sortBy = $request->get('sort_by', 'event_date');
        $sortDirection = $request->get('sort_direction', 'desc');
        $query->orderBy($sortBy, $sortDirection);

        $events = $query->paginate($request->get('per_page', 10));

        // Add image URLs to each event
        foreach ($events as $event) {
            if ($event->image) {
                $event->image_url = url('storage/' . $event->image);
            }
        }

        return response()->json($events);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'content' => 'required|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'event_date' => 'required|date',
            'event_time' => 'required',
            'venue' => 'required|string|max:255',
            'status' => 'boolean',
            'featured' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $request->all();
        $data['slug'] = Str::slug($request->title);

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->storeAs('public/events', $imageName);
            $data['image'] = 'events/' . $imageName;
        }

        $event = Event::create($data);

        // Add image URL to response
        if ($event->image) {
            $event->image_url = url('storage/' . $event->image);
        }

        return response()->json([
            'message' => 'Event created successfully',
            'event' => $event
        ], 201);
    }

    public function show(Event $event)
    {
        // Add image URL to response
        if ($event->image) {
            $event->image_url = url('storage/' . $event->image);
        }
        return response()->json($event);
    }

    public function update(Request $request, Event $event)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'event_date' => 'required|date',
            'event_time' => 'required',
            'venue' => 'required|string|max:255',
            'status' => 'boolean',
            'featured' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $request->all();
        $data['slug'] = Str::slug($request->title);

        if ($request->hasFile('image')) {
            // Delete old image
            if ($event->image) {
                Storage::delete('public/' . $event->image);
            }

            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->storeAs('public/events', $imageName);
            $data['image'] = 'events/' . $imageName;
        }

        $event->update($data);

        // Add image URL to response
        if ($event->image) {
            $event->image_url = url('storage/' . $event->image);
        }

        return response()->json([
            'message' => 'Event updated successfully',
            'event' => $event
        ]);
    }

    public function destroy(Event $event)
    {
        if ($event->image) {
            Storage::delete('public/' . $event->image);
        }

        $event->delete();

        return response()->json([
            'message' => 'Event deleted successfully'
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

        // Only apply date filter if date_from is provided and not empty
        if ($request->filled('date_from')) {
            $query->whereDate('event_date', '>=', $request->date_from);
        }

        // Only apply date filter if date_to is provided and not empty
        if ($request->filled('date_to')) {
            $query->whereDate('event_date', '<=', $request->date_to);
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
        $sortBy = $request->get('sort_by', 'event_date');
        $sortDirection = $request->get('sort_direction', 'asc');
        $query->orderBy($sortBy, $sortDirection);

        $events = $query->paginate($request->get('per_page', 10));

        // Add image URLs to each event
        foreach ($events as $event) {
            if ($event->image) {
                $event->image_url = url('storage/' . $event->image);
            }
        }

        return response()->json([
            'status' => 'success',
            'data' => $events
        ]);
    }
}
