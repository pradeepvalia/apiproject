<?php

namespace App\Http\Controllers;

use App\Models\Visitor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VisitorController extends Controller
{
    // 1. Track Visitor
    public function track(Request $request)
    {
        $validated = $request->validate([
            'page' => 'required|string|max:255',
            'userAgent' => 'nullable|string',
            'screenResolution' => 'nullable|string|max:50',
            'language' => 'nullable|string|max:10',
            'timezone' => 'nullable|string|max:50',
            'referrer' => 'nullable|string|max:500',
            'timestamp' => 'required|date',
            'sessionId' => 'required|string|max:100',
        ]);

        $visitor = Visitor::create([
            'page' => $validated['page'],
            'user_agent' => $validated['userAgent'] ?? null,
            'screen_resolution' => $validated['screenResolution'] ?? null,
            'language' => $validated['language'] ?? null,
            'timezone' => $validated['timezone'] ?? null,
            'referrer' => $validated['referrer'] ?? null,
            'session_id' => $validated['sessionId'],
            'ip_address' => $request->ip(),
            'created_at' => $validated['timestamp'],
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Visitor tracked successfully',
            'data' => [
                'id' => $visitor->id,
                'created_at' => $visitor->created_at,
            ]
        ]);
    }

    // 2. Get Visitor Statistics
    public function stats(Request $request)
    {
        // Total visits
        $totalVisits = Visitor::count();
        // Unique sessions
        $uniqueSessions = Visitor::distinct('session_id')->count('session_id');
        // Unique visitors by IP
        $uniqueVisitors = Visitor::distinct('ip_address')->count('ip_address');
        // Top languages
        $topLanguages = Visitor::select('language', DB::raw('count(*) as count'))
            ->whereNotNull('language')
            ->groupBy('language')
            ->orderByDesc('count')
            ->limit(5)
            ->get();
        // Top timezones
        $topTimezones = Visitor::select('timezone', DB::raw('count(*) as count'))
            ->whereNotNull('timezone')
            ->groupBy('timezone')
            ->orderByDesc('count')
            ->limit(5)
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => [
                'total_visits' => $totalVisits,
                'unique_sessions' => $uniqueSessions,
                'unique_visitors' => $uniqueVisitors,
                'top_languages' => $topLanguages,
                'top_timezones' => $topTimezones,
            ]
        ]);
    }

    // 3. Get Daily Statistics
    public function dailyStats(Request $request)
    {
        // Get daily visit counts for the last 30 days
        $dailyStats = Visitor::select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as visits'))
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date', 'asc')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $dailyStats
        ]);
    }

    // 4. Get Top Pages
    public function topPages(Request $request)
    {
        // Get top 10 most visited pages
        $topPages = Visitor::select('page', DB::raw('count(*) as visits'))
            ->groupBy('page')
            ->orderByDesc('visits')
            ->limit(10)
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $topPages
        ]);
    }
}