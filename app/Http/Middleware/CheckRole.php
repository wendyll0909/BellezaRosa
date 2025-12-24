<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Appointment;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        // Check if user has any of the required roles
        foreach ($roles as $role) {
            if ($user->role === $role) {
                // Additional staff-specific restrictions
                if ($user->isStaff()) {
                    return $this->handleStaffRestrictions($request, $next);
                }
                return $next($request);
            }
        }

        abort(403, 'Unauthorized access.');
    }

    /**
     * Handle staff-specific restrictions
     */
    private function handleStaffRestrictions(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        $staff = $user->staff;
        
        if (!$staff) {
            abort(403, 'Staff profile not found.');
        }

        // Staff can only access their own data
        $this->applyStaffFilters($request, $staff);
        
        return $next($request);
    }

    /**
     * Apply staff-specific filters to the request
     */
    private function applyStaffFilters(Request $request, $staff): void
    {
        // Store staff ID for later use
        $request->attributes->set('staff_id', $staff->id);
        
        // For appointment-related routes, staff can only see their own appointments
        if ($request->routeIs('dashboard.appointments.*') || 
            $request->routeIs('appointments.*')) {
            $this->filterStaffAppointments($request, $staff);
        }
        
        // Staff cannot access user management
        if ($request->routeIs('dashboard.users.*')) {
            abort(403, 'Staff cannot access user management.');
        }
        
        // Staff cannot access certain reports
        if ($request->routeIs('dashboard.reports.*')) {
            $allowedReports = ['appointments', 'revenue']; // Staff can see these
            $currentRoute = $request->route()->getName();
            
            foreach ($allowedReports as $allowed) {
                if (str_contains($currentRoute, $allowed)) {
                    return; // Allow access
                }
            }
            
            abort(403, 'Staff cannot access this report.');
        }
    }

    /**
     * Filter appointments for staff
     */
    private function filterStaffAppointments(Request $request, $staff): void
    {
        // For index/show actions, staff can only see their own appointments
        if ($request->routeIs('*.index') || $request->routeIs('*.show')) {
            $request->merge(['staff_id' => $staff->id]);
        }
        
        // For update/delete actions, check if appointment belongs to staff
        if ($request->routeIs('*.update') || $request->routeIs('*.destroy') || 
            $request->routeIs('*.status')) {
            $appointmentId = $request->route('appointment');
            if ($appointmentId) {
                $appointment = Appointment::findOrFail($appointmentId);
                if ($appointment->staff_id !== $staff->id) {
                    abort(403, 'You can only modify your own appointments.');
                }
            }
        }
    }
}