<?php

namespace App\Http\Controllers;

use App\Models\Commission;
use App\Models\Staff;
use App\Models\SalonSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CommissionController extends Controller
{
    /**
     * Display a listing of commissions
     */
    public function index(Request $request)
    {
        $query = Commission::with(['staff.user', 'appointment.service', 'appointment.customer']);

        // Filters
        if ($staffId = $request->get('staff_id')) {
            $query->where('staff_id', $staffId);
        }

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        if ($month = $request->get('month')) {
            $query->whereMonth('created_at', $month);
        }

        if ($year = $request->get('year')) {
            $query->whereYear('created_at', $year);
        }

        if ($dateFrom = $request->get('date_from')) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }

        if ($dateTo = $request->get('date_to')) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        $commissions = $query->orderBy('created_at', 'desc')->paginate(20);

        // Statistics
        $totalPending = Commission::pending()->sum('amount');
        $totalPaid = Commission::paid()->sum('amount');
        $totalCommission = Commission::sum('amount');

        // Get all staff for filter
        $staff = Staff::with('user')->get();

        return view('dashboard.commissions.index', compact(
            'commissions',
            'staff',
            'totalPending',
            'totalPaid',
            'totalCommission'
        ));
    }

    /**
     * Show commission details
     */
    public function show(Commission $commission)
    {
        $commission->load(['staff.user', 'appointment.service', 'appointment.customer']);
        return view('dashboard.commissions.show', compact('commission'));
    }

    /**
     * Pay commissions (single or bulk)
     */
    public function payCommissions(Request $request)
    {
        $request->validate([
            'commission_ids' => 'required|array',
            'commission_ids.*' => 'exists:commissions,id',
            'payment_date' => 'required|date',
            'payment_method' => 'required|in:cash,bank_transfer,gcash',
            'reference_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string'
        ]);

        DB::transaction(function () use ($request) {
            $commissions = Commission::whereIn('id', $request->commission_ids)
                ->pending()
                ->get();

            foreach ($commissions as $commission) {
                $commission->markAsPaid($request->payment_date);
                
                // Log the payment
                activity()
                    ->performedOn($commission)
                    ->causedBy(auth()->user())
                    ->withProperties([
                        'payment_method' => $request->payment_method,
                        'reference_number' => $request->reference_number,
                        'payment_date' => $request->payment_date
                    ])
                    ->log("Commission paid");
            }

            // Create a payment record for accounting
            $totalAmount = $commissions->sum('amount');
            
            // You might want to create a separate CommissionPayment model
            CommissionPayment::create([
                'payment_date' => $request->payment_date,
                'total_amount' => $totalAmount,
                'payment_method' => $request->payment_method,
                'reference_number' => $request->reference_number,
                'notes' => $request->notes,
                'paid_by' => auth()->id(),
                'commission_count' => $commissions->count()
            ]);
        });

        return redirect()->route('dashboard.commissions.index')
            ->with('success', 'Commissions paid successfully!');
    }

    /**
     * Update commission settings
     */
    public function updateSettings(Request $request)
    {
        $request->validate([
            'default_commission_rate' => 'required|numeric|min:0|max:100',
            'commission_payment_day' => 'required|integer|min:1|max:31'
        ]);

        $settings = SalonSetting::getSettings();
        $settings->update([
            'default_commission_rate' => $request->default_commission_rate,
            'commission_payment_day' => $request->commission_payment_day
        ]);

        return redirect()->route('dashboard.commissions.settings')
            ->with('success', 'Commission settings updated successfully!');
    }

    /**
     * Generate commission report
     */
    public function generateReport(Request $request)
    {
        $request->validate([
            'report_type' => 'required|in:monthly,staff,detailed',
            'month' => 'required_if:report_type,monthly|integer|min:1|max:12',
            'year' => 'required|integer|min:2020|max:2100',
            'staff_id' => 'nullable|exists:staff,id'
        ]);

        $query = Commission::with(['staff.user', 'appointment.service']);

        if ($request->year) {
            $query->whereYear('created_at', $request->year);
        }

        if ($request->month) {
            $query->whereMonth('created_at', $request->month);
        }

        if ($request->staff_id) {
            $query->where('staff_id', $request->staff_id);
        }

        $commissions = $query->orderBy('created_at')->get();

        // Generate report data
        $reportData = [
            'total_commissions' => $commissions->sum('amount'),
            'total_services' => $commissions->count(),
            'pending_commissions' => $commissions->where('status', 'pending')->sum('amount'),
            'paid_commissions' => $commissions->where('status', 'paid')->sum('amount'),
            'commissions_by_staff' => $commissions->groupBy('staff_id')->map(function ($staffCommissions) {
                return [
                    'staff_name' => $staffCommissions->first()->staff->user->full_name,
                    'total_amount' => $staffCommissions->sum('amount'),
                    'total_services' => $staffCommissions->count(),
                    'pending_amount' => $staffCommissions->where('status', 'pending')->sum('amount'),
                    'paid_amount' => $staffCommissions->where('status', 'paid')->sum('amount')
                ];
            })
        ];

        return view('dashboard.commissions.report', compact('reportData', 'request'));
    }

    /**
     * Get staff commission summary
     */
    public function getStaffSummary($staffId)
    {
        $staff = Staff::with('user')->findOrFail($staffId);

        $currentMonth = now()->month;
        $currentYear = now()->year;

        $summary = [
            'staff' => $staff,
            'monthly_commission' => Commission::where('staff_id', $staffId)
                ->whereMonth('created_at', $currentMonth)
                ->whereYear('created_at', $currentYear)
                ->sum('amount'),
            'pending_commission' => Commission::where('staff_id', $staffId)
                ->pending()
                ->sum('amount'),
            'total_commission' => Commission::where('staff_id', $staffId)->sum('amount'),
            'services_this_month' => Commission::where('staff_id', $staffId)
                ->whereMonth('created_at', $currentMonth)
                ->whereYear('created_at', $currentYear)
                ->count()
        ];

        return response()->json($summary);
    }
}