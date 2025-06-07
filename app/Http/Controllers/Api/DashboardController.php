<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Donation;
use App\Models\Event;
use App\Models\Gallery;
use App\Models\Suggestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function getStatistics()
    {
        // Today's statistics
        $todayStats = Donation::where('status', 'completed')
            ->whereDate('created_at', Carbon::today())
            ->select(
                DB::raw('COUNT(*) as total_donations'),
                DB::raw('SUM(amount) as total_amount')
            )
            ->first();

        // This month's statistics
        $monthStats = Donation::where('status', 'completed')
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->select(
                DB::raw('COUNT(*) as total_donations'),
                DB::raw('SUM(amount) as total_amount')
            )
            ->first();

        // This year's statistics
        $yearStats = Donation::where('status', 'completed')
            ->whereYear('created_at', Carbon::now()->year)
            ->select(
                DB::raw('COUNT(*) as total_donations'),
                DB::raw('SUM(amount) as total_amount')
            )
            ->first();

        // All time statistics
        $allTimeStats = Donation::where('status', 'completed')
            ->select(
                DB::raw('COUNT(*) as total_donations'),
                DB::raw('SUM(amount) as total_amount')
            )
            ->first();

        return response()->json([
            'today' => [
                'total_donations' => $todayStats->total_donations ?? 0,
                'total_amount' => $todayStats->total_amount ?? 0
            ],
            'this_month' => [
                'total_donations' => $monthStats->total_donations ?? 0,
                'total_amount' => $monthStats->total_amount ?? 0
            ],
            'this_year' => [
                'total_donations' => $yearStats->total_donations ?? 0,
                'total_amount' => $yearStats->total_amount ?? 0
            ],
            'all_time' => [
                'total_donations' => $allTimeStats->total_donations ?? 0,
                'total_amount' => $allTimeStats->total_amount ?? 0
            ]
        ]);
    }

    public function getMonthlyStats(Request $request)
    {
        $year = $request->input('year', Carbon::now()->year);

        $monthlyStats = Donation::where('status', 'completed')
            ->whereYear('created_at', $year)
            ->select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('COUNT(*) as total_donations'),
                DB::raw('SUM(amount) as total_amount')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return response()->json($monthlyStats);
    }

    public function getDailyStats(Request $request)
    {
        $month = $request->input('month', Carbon::now()->month);
        $year = $request->input('year', Carbon::now()->year);

        $dailyStats = Donation::where('status', 'completed')
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as total_donations'),
                DB::raw('SUM(amount) as total_amount')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return response()->json($dailyStats);
    }

    public function getPaymentMethodStats()
    {
        $paymentStats = Donation::where('status', 'completed')
            ->select(
                'payment_method',
                DB::raw('COUNT(*) as total_donations'),
                DB::raw('SUM(amount) as total_amount')
            )
            ->groupBy('payment_method')
            ->get();

        return response()->json($paymentStats);
    }

    public function getRecentDonations()
    {
        $recentDonations = Donation::where('status', 'completed')
            ->with(['user'])
            ->latest()
            ->take(10)
            ->get();

        return response()->json($recentDonations);
    }

    public function getCounts(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $query = function($model) use ($startDate, $endDate) {
            $query = $model::query();

            if ($startDate) {
                $query->whereDate('created_at', '>=', $startDate);
            }

            if ($endDate) {
                $query->whereDate('created_at', '<=', $endDate);
            }

            return $query;
        };

        // Get counts for each model
        $donationCount = $query(new Donation)->count();
        $eventCount = $query(new Event)->count();
        $galleryCount = $query(new Gallery)->count();

        // Get featured counts
        $featuredEventCount = $query(new Event)->where('featured', true)->count();
        $featuredGalleryCount = $query(new Gallery)->where('featured', true)->count();

        // Get active counts
        $activeEventCount = $query(new Event)->where('status', true)->count();
        $activeGalleryCount = $query(new Gallery)->where('status', 'active')->count();

        // Get suggestion counts
        $suggestionCount = $query(new Suggestion)->count();
        $readSuggestionCount = $query(new Suggestion)->where('is_read', true)->count();
        $unreadSuggestionCount = $query(new Suggestion)->where('is_read', false)->count();

        // Get donation amount
        $donationAmount = $query(new Donation)
            ->where('status', 'completed')
            ->sum('amount');

        return response()->json([
            'status' => 'success',
            'data' => [
                'donations' => [
                    'total_count' => $donationCount,
                    'total_amount' => $donationAmount
                ],
                'events' => [
                    'total_count' => $eventCount,
                    'featured_count' => $featuredEventCount,
                    'active_count' => $activeEventCount
                ],
                'galleries' => [
                    'total_count' => $galleryCount,
                    'featured_count' => $featuredGalleryCount,
                    'active_count' => $activeGalleryCount
                ],
                'messages' => [
                    'total_count' => $suggestionCount,
                    'read_count' => $readSuggestionCount,
                    'unread_count' => $unreadSuggestionCount
                ],
                'date_range' => [
                    'start_date' => $startDate,
                    'end_date' => $endDate
                ]
            ]
        ]);
    }
}
