<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\ReportHistory;
use App\Models\Payment;
use App\Models\InventoryItem;
use App\Models\InventoryUpdate;
use App\Models\Customer;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportsController extends Controller
{
    // Main reports dashboard
public function index()
{
    // Get recent report history (last 10 reports)
    $recentReports = ReportHistory::with('user')
        ->orderBy('created_at', 'desc')
        ->limit(10)
        ->get();

    // Get today's quick stats
    $today = now()->toDateString();
    $todayAppointments = Appointment::whereDate('start_datetime', $today)->count();
    $todayRevenue = Appointment::where('status', 'completed')
        ->whereDate('start_datetime', $today)
        ->sum('total_amount');
    $lowStockItems = InventoryItem::whereRaw('current_stock <= minimum_stock')->count();
    $pendingPayments = Payment::where('status', 'pending')->count();

    return view('dashboard.reports.index', compact(
        'recentReports',
        'todayAppointments',
        'todayRevenue',
        'lowStockItems',
        'pendingPayments'
    ));
}
    // Appointment Reports
    public function appointments(Request $request)
    {
        $startDate = $request->get('start_date', now()->subDays(7)->toDateString());
        $endDate = $request->get('end_date', now()->toDateString());
        
        $data = $this->getAppointmentReportData($startDate, $endDate);
        
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'data' => $data,
                'start_date' => $startDate,
                'end_date' => $endDate
            ]);
        }
        
        return view('dashboard.reports.appointments', compact('data', 'startDate', 'endDate'));
    }

    // Revenue Reports
    public function revenue(Request $request)
    {
        $startDate = $request->get('start_date', now()->subDays(7)->toDateString());
        $endDate = $request->get('end_date', now()->toDateString());
        
        $data = $this->getRevenueReportData($startDate, $endDate);
        
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'data' => $data,
                'start_date' => $startDate,
                'end_date' => $endDate
            ]);
        }
        
        return view('dashboard.reports.revenue', compact('data', 'startDate', 'endDate'));
    }

    // Inventory Reports
    public function inventory(Request $request)
    {
        $startDate = $request->get('start_date', now()->subDays(7)->toDateString());
        $endDate = $request->get('end_date', now()->toDateString());
        
        $data = $this->getInventoryReportData($startDate, $endDate);
        
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'data' => $data,
                'start_date' => $startDate,
                'end_date' => $endDate
            ]);
        }
        
        return view('dashboard.reports.inventory', compact('data', 'startDate', 'endDate'));
    }

    // Download Report
public function download(Request $request)
{
    $request->validate([
        'type' => 'required|in:appointments,revenue,inventory',
        'start_date' => 'required|date',
        'end_date' => 'required|date|after_or_equal:start_date'
    ]);

    $type = $request->type;
    $startDate = $request->start_date;
    $endDate = $request->end_date;

    switch ($type) {
        case 'appointments':
            $data = $this->getAppointmentReportData($startDate, $endDate);
            $filename = "appointments_report_{$startDate}_to_{$endDate}.txt";
            $recordCount = $data['total'] ?? 0;
            break;
            
        case 'revenue':
            $data = $this->getRevenueReportData($startDate, $endDate);
            $filename = "revenue_report_{$startDate}_to_{$endDate}.txt";
            $recordCount = ($data['completed_appointments']->count() ?? 0) + ($data['payments']->count() ?? 0);
            break;
            
        case 'inventory':
            $data = $this->getInventoryReportData($startDate, $endDate);
            $filename = "inventory_report_{$startDate}_to_{$endDate}.txt";
            $recordCount = $data['updates']->count() ?? 0;
            break;
    }

    // Record this download in report history
    ReportHistory::create([
        'user_id' => auth()->id(),
        'report_type' => $type,
        'start_date' => $startDate,
        'end_date' => $endDate,
        'filename' => $filename,
        'record_count' => $recordCount,
        'parameters' => [
            'type' => $type,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'generated_at' => now()->toDateTimeString()
        ]
    ]);

    $content = $this->formatReportForDownload($type, $data, $startDate, $endDate);
    
    return response($content)
        ->header('Content-Type', 'text/plain')
        ->header('Content-Disposition', "attachment; filename=\"{$filename}\"");
}

    // Private methods for data retrieval
    private function getAppointmentReportData($startDate, $endDate)
    {
        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->endOfDay();
        
        $appointments = Appointment::with([
            'customer', 
            'service', 
            'staff.user',
            'payment'
        ])
        ->whereBetween('start_datetime', [$start, $end])
        ->orderBy('start_datetime')
        ->get();

        // Calculate statistics
        $totalAppointments = $appointments->count();
        $completed = $appointments->where('status', 'completed')->count();
        $cancelled = $appointments->where('status', 'cancelled')->count();
        $noShow = $appointments->where('status', 'no_show')->count();
        
        $statusBreakdown = $appointments->groupBy('status')->map->count();
        $serviceBreakdown = $appointments->groupBy(function($appointment) {
            return $appointment->service->name ?? 'Unknown Service';
        })->map->count();
        
        $staffBreakdown = $appointments->groupBy(function($appointment) {
            return $appointment->staff->user->full_name ?? 'Unknown Staff';
        })->map->count();

        return [
            'appointments' => $appointments,
            'total' => $totalAppointments,
            'completed' => $completed,
            'cancelled' => $cancelled,
            'no_show' => $noShow,
            'status_breakdown' => $statusBreakdown,
            'service_breakdown' => $serviceBreakdown,
            'staff_breakdown' => $staffBreakdown,
            'start_date' => $start,
            'end_date' => $end,
            'date_range_label' => $this->getDateRangeLabel($start, $end)
        ];
    }

    private function getRevenueReportData($startDate, $endDate)
    {
        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->endOfDay();

        // Get completed appointments with payments
        $completedAppointments = Appointment::with([
            'service', 
            'payment',
            'customer',
            'staff.user'
        ])
        ->where('status', 'completed')
        ->whereBetween('start_datetime', [$start, $end])
        ->get();

        // Get direct payments with customer and appointment data
        $payments = Payment::with([
            'customer',
            'appointment.service',
            'appointment.staff.user'
        ])
        ->whereBetween('paid_at', [$start, $end])
        ->orWhereBetween('created_at', [$start, $end])
        ->get();

        $totalRevenue = $completedAppointments->sum('total_amount') + $payments->sum('amount');
        
        $serviceRevenue = $completedAppointments->groupBy(function($appointment) {
            return $appointment->service->name ?? 'Unknown Service';
        })
        ->map(function ($appointments) {
            return $appointments->sum('total_amount');
        });
        
        $paymentMethodBreakdown = $payments->groupBy('method')
            ->map->sum('amount');

        // Daily revenue breakdown
        $dailyRevenue = $completedAppointments->groupBy(function($item) {
            return Carbon::parse($item->start_datetime)->format('Y-m-d');
        })->map->sum('total_amount');

        return [
            'total_revenue' => $totalRevenue,
            'completed_appointments' => $completedAppointments,
            'payments' => $payments,
            'service_revenue' => $serviceRevenue,
            'payment_method_breakdown' => $paymentMethodBreakdown,
            'daily_revenue' => $dailyRevenue,
            'start_date' => $start,
            'end_date' => $end,
            'date_range_label' => $this->getDateRangeLabel($start, $end)
        ];
    }

    private function getInventoryReportData($startDate, $endDate)
    {
        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->endOfDay();

        $updates = InventoryUpdate::with([
            'item', 
            'updatedBy'
        ])
        ->whereBetween('update_date', [$start, $end])
        ->orWhereBetween('created_at', [$start, $end])
        ->orderBy('created_at', 'desc')
        ->get();

        $items = InventoryItem::all();
        $lowStockItems = $items->where('current_stock', '<=', DB::raw('minimum_stock'))->values();
        
        $updateTypes = $updates->groupBy('type')
            ->map(function ($group) {
                return [
                    'count' => $group->count(),
                    'total_quantity' => $group->sum('quantity')
                ];
            });

        $mostUpdatedItems = $updates->groupBy(function($update) {
            return $update->item->name ?? 'Unknown Item';
        })
        ->map(function ($group) {
            return [
                'count' => $group->count(),
                'total_quantity' => $group->sum('quantity')
            ];
        })->sortByDesc('count')->take(10);

        return [
            'updates' => $updates,
            'items' => $items,
            'low_stock_items' => $lowStockItems,
            'total_items' => $items->count(),
            'low_stock_count' => $lowStockItems->count(),
            'update_types' => $updateTypes,
            'most_updated_items' => $mostUpdatedItems,
            'start_date' => $start,
            'end_date' => $end,
            'date_range_label' => $this->getDateRangeLabel($start, $end)
        ];
    }

    private function getDateRangeLabel($startDate, $endDate)
    {
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);
        
        if ($start->isSameDay($end)) {
            return $start->format('F j, Y');
        }
        
        if ($start->isSameMonth($end)) {
            return $start->format('F j') . ' - ' . $end->format('j, Y');
        }
        
        if ($start->isSameYear($end)) {
            return $start->format('M j') . ' - ' . $end->format('M j, Y');
        }
        
        return $start->format('M j, Y') . ' - ' . $end->format('M j, Y');
    }

    private function formatReportForDownload($type, $data, $startDate, $endDate)
    {
        $content = "=== BELLEZA ROSA REPORT ===\n";
        $content .= "Report Type: " . ucfirst($type) . "\n";
        $content .= "Date Range: " . $data['date_range_label'] . "\n";
        $content .= "Period: " . Carbon::parse($startDate)->format('M j, Y') . " to " . Carbon::parse($endDate)->format('M j, Y') . "\n";
        $content .= "Generated: " . now()->format('F j, Y h:i A') . "\n";
        $content .= "Generated By: " . auth()->user()->full_name . "\n";
        $content .= "=================================\n\n";

        switch ($type) {
            case 'appointments':
                $content .= $this->formatAppointmentReport($data);
                break;
                
            case 'revenue':
                $content .= $this->formatRevenueReport($data);
                break;
                
            case 'inventory':
                $content .= $this->formatInventoryReport($data);
                break;
        }

        return $content;
    }

    private function formatAppointmentReport($data)
    {
        $content = "APPOINTMENT REPORT SUMMARY\n";
        $content .= "=========================\n";
        $content .= "Total Appointments: {$data['total']}\n";
        $content .= "Completed: {$data['completed']}\n";
        $content .= "Cancelled: {$data['cancelled']}\n";
        $content .= "No Show: {$data['no_show']}\n\n";
        
        $content .= "STATUS BREAKDOWN:\n";
        foreach ($data['status_breakdown'] as $status => $count) {
            $content .= "  - " . ucfirst(str_replace('_', ' ', $status)) . ": {$count}\n";
        }
        
        $content .= "\nSERVICE BREAKDOWN:\n";
        foreach ($data['service_breakdown'] as $service => $count) {
            $content .= "  - {$service}: {$count}\n";
        }
        
        $content .= "\nSTAFF PERFORMANCE:\n";
        foreach ($data['staff_breakdown'] as $staff => $count) {
            $content .= "  - {$staff}: {$count} appointments\n";
        }
        
        $content .= "\n\nDETAILED APPOINTMENT LIST:\n";
        $content .= "===============================================================================================================\n";
        $content .= "Date/Time       | Customer           | Service                | Staff                | Status    | Amount\n";
        $content .= "===============================================================================================================\n";
        
        foreach ($data['appointments'] as $appointment) {
            $customerName = $appointment->customer->full_name ?? 'Unknown Customer';
            $serviceName = $appointment->service->name ?? 'Unknown Service';
            $staffName = $appointment->staff->user->full_name ?? 'Unknown Staff';
            $payerName = $appointment->customer->full_name ?? 'Unknown Payer';
            
            $content .= sprintf("%-15s | %-18s | %-22s | %-20s | %-9s | ₱%8.2f\n",
                $appointment->start_datetime->format('M d, h:i A'),
                substr($customerName, 0, 18),
                substr($serviceName, 0, 22),
                substr($staffName, 0, 20),
                ucfirst($appointment->status),
                $appointment->total_amount
            );
            $content .= "Payer: {$payerName}\n";
            $content .= "---------------------------------------------------------------------------------------------------------------\n";
        }
        
        return $content;
    }

    private function formatRevenueReport($data)
    {
        $content = "REVENUE REPORT SUMMARY\n";
        $content .= "======================\n";
        $content .= "Total Revenue: ₱" . number_format($data['total_revenue'], 2) . "\n";
        $content .= "Completed Services: " . $data['completed_appointments']->count() . "\n";
        $content .= "Payment Transactions: " . $data['payments']->count() . "\n\n";
        
        $content .= "SERVICE REVENUE BREAKDOWN:\n";
        foreach ($data['service_revenue'] as $service => $amount) {
            $content .= "  - {$service}: ₱" . number_format($amount, 2) . "\n";
        }
        
        $content .= "\nPAYMENT METHOD BREAKDOWN:\n";
        foreach ($data['payment_method_breakdown'] as $method => $amount) {
            $content .= "  - " . ucfirst($method) . ": ₱" . number_format($amount, 2) . "\n";
        }
        
        $content .= "\n\nDETAILED PAYMENT TRANSACTIONS:\n";
        $content .= "===================================================================================================================================\n";
        $content .= "Date/Time       | Customer           | Service                | Staff                | Payer              | Method      | Amount     | Status\n";
        $content .= "===================================================================================================================================\n";
        
        foreach ($data['payments'] as $payment) {
            $customerName = $payment->customer->full_name ?? 'Unknown Customer';
            $serviceName = $payment->appointment->service->name ?? 'Unknown Service';
            $staffName = $payment->appointment->staff->user->full_name ?? 'Unknown Staff';
            $payerName = $payment->customer->full_name ?? 'Unknown Payer';
            
            $content .= sprintf("%-15s | %-18s | %-22s | %-20s | %-18s | %-11s | ₱%9.2f | %s\n",
                $payment->created_at->format('M d, h:i A'),
                substr($customerName, 0, 18),
                substr($serviceName, 0, 22),
                substr($staffName, 0, 20),
                substr($payerName, 0, 18),
                ucfirst($payment->method),
                $payment->amount,
                ucfirst($payment->status)
            );
        }
        
        $content .= "\n\nCOMPLETED SERVICES (No Separate Payment):\n";
        $content .= "===================================================================================================\n";
        $content .= "Date/Time       | Customer           | Service                | Staff                | Amount\n";
        $content .= "===================================================================================================\n";
        
        foreach ($data['completed_appointments']->where('payment', null) as $appointment) {
            $customerName = $appointment->customer->full_name ?? 'Unknown Customer';
            $serviceName = $appointment->service->name ?? 'Unknown Service';
            $staffName = $appointment->staff->user->full_name ?? 'Unknown Staff';
            
            $content .= sprintf("%-15s | %-18s | %-22s | %-20s | ₱%8.2f\n",
                $appointment->start_datetime->format('M d, h:i A'),
                substr($customerName, 0, 18),
                substr($serviceName, 0, 22),
                substr($staffName, 0, 20),
                $appointment->total_amount
            );
        }
        
        return $content;
    }

    private function formatInventoryReport($data)
    {
        $content = "INVENTORY REPORT SUMMARY\n";
        $content .= "========================\n";
        $content .= "Total Items: {$data['total_items']}\n";
        $content .= "Low Stock Items: {$data['low_stock_count']}\n";
        $content .= "Total Updates: " . $data['updates']->count() . "\n\n";
        
        $content .= "UPDATE ACTIVITY:\n";
        foreach ($data['update_types'] as $type => $stats) {
            $content .= "  - " . ucfirst($type) . ": {$stats['count']} updates ({$stats['total_quantity']} units)\n";
        }
        
        $content .= "\nLOW STOCK ITEMS (REQUIRE ATTENTION):\n";
        foreach ($data['low_stock_items'] as $item) {
            $content .= "  - {$item->name}: {$item->current_stock} {$item->unit} (Minimum: {$item->minimum_stock} {$item->unit})\n";
        }
        
        $content .= "\n\nINVENTORY UPDATES DETAIL:\n";
        $content .= "===========================================================================================================================\n";
        $content .= "Date/Time       | Item Name          | Type      | Quantity | Previous | New Stock | Updated By          | Remark\n";
        $content .= "===========================================================================================================================\n";
        
        foreach ($data['updates'] as $update) {
            $itemName = $update->item->name ?? 'Unknown Item';
            $updatedByName = $update->updatedBy->full_name ?? 'System';
            
            $content .= sprintf("%-15s | %-18s | %-9s | %-8s | %-8s | %-9s | %-19s | %s\n",
                $update->created_at->format('M d, h:i A'),
                substr($itemName, 0, 18),
                ucfirst($update->type),
                $update->quantity . ' ' . ($update->item->unit ?? ''),
                $update->previous_stock,
                $update->new_stock,
                substr($updatedByName, 0, 19),
                substr($update->remark, 0, 40)
            );
        }
        
        $content .= "\n\nCURRENT INVENTORY STATUS:\n";
        $content .= "=====================================================================================\n";
        $content .= "Item Name          | Category      | Current Stock | Minimum | Unit  | Status\n";
        $content .= "=====================================================================================\n";
        
        foreach ($data['items'] as $item) {
            $status = $item->current_stock <= $item->minimum_stock ? 'LOW STOCK' : 'OK';
            $statusColor = $status === 'LOW STOCK' ? '(!)' : '';
            
            $content .= sprintf("%-18s | %-13s | %-13s | %-7s | %-5s | %s%s\n",
                substr($item->name, 0, 18),
                substr(ucfirst(str_replace('_', ' ', $item->category)), 0, 13),
                $item->current_stock,
                $item->minimum_stock,
                $item->unit,
                $status,
                $statusColor
            );
        }
        
        return $content;
    }
}