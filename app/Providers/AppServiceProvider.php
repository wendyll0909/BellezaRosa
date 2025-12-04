<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Appointment;
use App\Observers\AppointmentObserver;
use Carbon\Carbon;

Appointment::observe(AppointmentObserver::class);
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
 // Set application timezone
        config(['app.timezone' => 'Asia/Manila']);
        
        // Set Carbon (Laravel's date library) timezone
        Carbon::setLocale('en');
        date_default_timezone_set('Asia/Manila');    }
}
