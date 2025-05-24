<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Donation;
use App\Services\DonationEmailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DonationController extends Controller
{
    protected $emailService;

    public function __construct(DonationEmailService $emailService)
    {
        $this->emailService = $emailService;
    }

    public function index(Request $request)
    {
        $query = Donation::with('user');

        // Search by donor name, email, or transaction ID
        if ($request->has('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('donor_name', 'like', "%{$searchTerm}%")
                  ->orWhere('donor_email', 'like', "%{$searchTerm}%")
                  ->orWhere('transaction_id', 'like', "%{$searchTerm}%");
            });
        }

        // Filter by payment status
        if ($request->has('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        // Filter by payment method
        if ($request->has('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        // Amount range filter
        if ($request->has('min_amount')) {
            $query->where('amount', '>=', $request->min_amount);
        }
        if ($request->has('max_amount')) {
            $query->where('amount', '<=', $request->max_amount);
        }

        // Date range filter
        if ($request->has('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->has('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Sort by
        $sortBy = $request->get('sort_by', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');
        $query->orderBy($sortBy, $sortDirection);

        $donations = $query->paginate($request->get('per_page', 10));
        return response()->json($donations);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'mobile_number' => 'required|string|max:20',
            'address' => 'required|string|max:255',
            'amount' => 'required|numeric|min:1',
            'payment_method' => 'required|string|in:credit_card,debit_card,bank_transfer,cash',
            'status' => 'required|string|in:pending,completed,failed',
            'transaction_id' => 'nullable|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $donation = Donation::create($request->all());

        // Send email if donation is completed
        if ($donation->status === 'completed') {
            $this->emailService->sendDonationEmail($donation);
        }

        return response()->json([
            'message' => 'Donation recorded successfully',
            'donation' => $donation
        ], 201);
    }

    public function show(Donation $donation)
    {
        return response()->json($donation);
    }

    public function update(Request $request, Donation $donation)
    {
        $validator = Validator::make($request->all(), [
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'mobile_number' => 'required|string|max:20',
            'address' => 'required|string|max:255',
            'amount' => 'required|numeric|min:1',
            'payment_method' => 'required|string|in:credit_card,debit_card,bank_transfer,cash',
            'status' => 'required|string|in:pending,completed,failed',
            'transaction_id' => 'nullable|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $oldStatus = $donation->status;
        $donation->update($request->all());

        // Send email if status changed to completed
        if ($oldStatus !== 'completed' && $donation->status === 'completed') {
            $this->emailService->sendDonationEmail($donation);
        }

        return response()->json([
            'message' => 'Donation updated successfully',
            'donation' => $donation
        ]);
    }

    public function destroy(Donation $donation)
    {
        $donation->delete();

        return response()->json([
            'message' => 'Donation deleted successfully'
        ]);
    }

    public function getTotalDonations()
    {
        $total = Donation::where('status', 'completed')->sum('amount');
        $count = Donation::where('status', 'completed')->count();

        return response()->json([
            'total_amount' => $total,
            'total_donations' => $count
        ]);
    }

    public function getRecentDonations()
    {
        $donations = Donation::where('status', 'completed')
            ->latest()
            ->take(5)
            ->get();

        return response()->json($donations);
    }
}
