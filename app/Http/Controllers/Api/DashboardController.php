<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Donation;
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
}
