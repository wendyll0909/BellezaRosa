<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\User;
use App\Models\Service;
use App\Models\Customer;
use App\Models\Staff;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
{
    // Check if user has access to dashboard
    if (!auth()->user()->isAdmin() && !auth()->user()->isStaff()) {
        abort(403, 'Unauthorized access to dashboard.');
    }

    // Get date range from request or use default (today)
    $dateRange = $request->get('date_range', 'today');
    $customDate = $request->get('custom_date');
    
    // Calculate date range
    $dateRangeData = $this->getDateRange($dateRange, $customDate);

    // Get dashboard statistics based on date range
    $stats = $this->getDashboardStats($dateRangeData['start'], $dateRangeData['end'], $dateRangeData['label']);

    // Get appointments based on date range
    $appointmentsData = $this->getAppointmentsData($dateRangeData['start'], $dateRangeData['end']);

    // Get data for modals
    $customers = Customer::all();
    $services = Service::where('is_active', true)->get();
    $staff = Staff::with('user')->get();

    // Get customer services data for the selected date range
    $customersWithServices = Customer::with(['appointments.service'])
        ->has('appointments')
        ->orderBy('total_visits', 'desc')
        ->limit(10)
        ->get();

    $totalServices = Appointment::count();
    
    // Get most popular service
    $popularService = Appointment::select('service_id', DB::raw('COUNT(*) as count'))
        ->with('service')
        ->groupBy('service_id')
        ->orderBy('count', 'desc')
        ->first();

    // **Fix 5: Get initial customer services data for today**
    $todayRange = $this->getDateRange('today', null); // Use 'today' as the default date range
    $customerServicesData = $this->getCustomerServicesData($todayRange['start'], $todayRange['end']);

    // Return the view with all data, including the initial customer services data
    return view('dashboard.index', array_merge(
        $stats,
        $appointmentsData,
        [
            'customers' => $customers,
            'services' => $services,
            'staff' => $staff,
            'customersWithServices' => $customersWithServices,
            'totalServices' => $totalServices,
            'popularService' => $popularService?->service?->name ?? 'N/A',
            'currentFilter' => $dateRange,
            'currentCustomDate' => $customDate,
            // **Pass the initial customer services data for today**
            'customersWithServicesToday' => $customerServicesData['customers'],
            'totalServicesToday' => $customerServicesData['total_services'],
            'popularServiceToday' => $customerServicesData['popular_service'],
        ]
    ));
}


    private function getDateRange($range, $customDate = null)
    {
        $today = Carbon::today();
        
        switch ($range) {
            case 'today':
                $start = $today;
                $end = $today->copy()->endOfDay();
                $label = 'Today\'s';
                break;
                
            case 'this_week':
                $start = $today->copy()->startOfWeek();
                $end = $today->copy()->endOfWeek();
                $label = 'This Week\'s';
                break;
                
            case 'this_month':
                $start = $today->copy()->startOfMonth();
                $end = $today->copy()->endOfMonth();
                $label = 'This Month\'s';
                break;
                
            case 'custom':
                if ($customDate) {
                    $start = Carbon::createFromFormat('Y-m', $customDate)->startOfMonth();
                    $end = Carbon::createFromFormat('Y-m', $customDate)->endOfMonth();
                    $label = $start->format('F Y');
                } else {
                    $start = $today;
                    $end = $today->copy()->endOfDay();
                    $label = 'Today\'s';
                }
                break;
                
            default:
                $start = $today;
                $end = $today->copy()->endOfDay();
                $label = 'Today\'s';
        }

        return [
            'start' => $start,
            'end' => $end,
            'label' => $label
        ];
    }

    private function getDashboardStats($startDate, $endDate, $label)
{
    $appointmentsCount = Appointment::whereBetween('start_datetime', [$startDate, $endDate])->count();
    
    $revenue = Appointment::whereBetween('start_datetime', [$startDate, $endDate])
        ->where('status', 'completed')
        ->sum('total_amount');

    // Get unique customers for the date range
    $customersCount = Customer::whereHas('appointments', function($query) use ($startDate, $endDate) {
        $query->whereBetween('start_datetime', [$startDate, $endDate]);
    })->count();

    return [
        'appointments_count' => $appointmentsCount,
        'revenue' => $revenue,
        'customers_count' => $customersCount,
        'stats_label' => $label,
        'total_staff' => User::where('role', 'staff')->count(),
        'date_range' => [
            'start' => $startDate,
            'end' => $endDate
        ]
    ];
}

    private function getAppointmentsData($startDate, $endDate)
    {
        // Get appointments for the selected date range
        $rangeAppointments = Appointment::with(['customer', 'service', 'staff.user'])
            ->whereBetween('start_datetime', [$startDate, $endDate])
            ->orderBy('start_datetime')
            ->get();

        // Get upcoming appointments (always next 7 days regardless of filter)
        $upcomingAppointments = Appointment::with(['customer', 'service', 'staff.user'])
            ->where('start_datetime', '>', now())
            ->where('start_datetime', '<=', now()->addDays(7))
            ->orderBy('start_datetime')
            ->limit(10)
            ->get();

        return [
            'rangeAppointments' => $rangeAppointments,
            'upcomingAppointments' => $upcomingAppointments,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ];
    }

    // AJAX endpoint for filter updates
public function filter(Request $request)
{
    $dateRange = $request->get('date_range', 'today');
    $customDate = $request->get('custom_date');
    
    $dateRangeData = $this->getDateRange($dateRange, $customDate);
    $stats = $this->getDashboardStats($dateRangeData['start'], $dateRangeData['end'], $dateRangeData['label']);
    $appointmentsData = $this->getAppointmentsData($dateRangeData['start'], $dateRangeData['end']);

    // Get customer services data for the date range
    $customerServicesData = $this->getCustomerServicesData($dateRangeData['start'], $dateRangeData['end']);

    return response()->json([
        'success' => true,
        'stats' => $stats,
        'appointments' => $appointmentsData['rangeAppointments'],
        'customer_services' => $customerServicesData,
        'label' => $dateRangeData['label'],
        'date_range' => [
            'start' => $dateRangeData['start']->format('Y-m-d H:i:s'),
            'end' => $dateRangeData['end']->format('Y-m-d H:i:s')
        ]
    ]);
}

private function getCustomerServicesData($startDate, $endDate)
{
    $customersWithServices = Customer::with(['appointments' => function ($query) use ($startDate, $endDate) {
    $query->whereBetween('start_datetime', [$startDate, $endDate])
          ->with('service'); // eager load service only for appointments in range
}])
->whereHas('appointments', function($query) use ($startDate, $endDate) {
    $query->whereBetween('start_datetime', [$startDate, $endDate]);
})
->withCount(['appointments as range_appointments_count' => function($query) use ($startDate, $endDate) {
    $query->whereBetween('start_datetime', [$startDate, $endDate]);
}])
->orderBy('range_appointments_count', 'desc')
->limit(10)
->get();

    $totalServices = Appointment::whereBetween('start_datetime', [$startDate, $endDate])->count();
    
    // Get most popular service for the date range
    $popularService = Appointment::select('service_id', DB::raw('COUNT(*) as count'))
        ->with('service')
        ->whereBetween('start_datetime', [$startDate, $endDate])
        ->groupBy('service_id')
        ->orderBy('count', 'desc')
        ->first();

    return [
        'customers' => $customersWithServices,
        'total_services' => $totalServices,
        'popular_service' => $popularService?->service?->name ?? 'N/A'
    ];
}
}