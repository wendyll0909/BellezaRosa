<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Service;
use App\Models\Staff;
use App\Models\Customer;
use App\Models\Appointment;
use App\Models\SalonSetting;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class GuestBookingModal extends Component
{
    public $show = false;
    public $services = [];
    public $staff = [];
    public $selectedService;
    public $selectedStaff;
    public $date;
    public $time;
    public $full_name;
    public $phone;
    public $email;
    public $notes;
    
    public $availableTimes = [];

    protected $listeners = ['open-booking-modal' => 'open'];

    public function mount()
{
    $this->services = Service::where('is_active', true)->get();
    $this->staff = Staff::with('user')->get();
    $this->date = today()->format('Y-m-d');
}

    public function open()
    {
        $this->show = true;
        $this->generateAvailableTimes();
    }

    public function close()
    {
        $this->show = false;
        $this->reset([
            'full_name', 'phone', 'email', 'notes',
            'selectedService', 'selectedStaff', 'time'
        ]);
        $this->availableTimes = [];
    }

    public function updatedDate()
    {
        $this->generateAvailableTimes();
        $this->time = null;
    }

    public function updatedSelectedStaff($value)
{
    if ($value) {
        $staff = Staff::find($value);
        if ($staff) {
            // Filter services based on staff specialty
            $this->services = Service::whereHas('category', function($query) use ($staff) {
                $query->where('specialty', $staff->specialty)
                      ->orWhere('specialty', 'both');
            })
            ->where('is_active', true)
            ->get();
        }
    } else {
        // Show all services if no staff selected
        $this->services = Service::where('is_active', true)->get();
    }
    
    $this->generateAvailableTimes();
    $this->selectedService = null;
    $this->time = '';
}

    public function updatedSelectedService()
    {
        $this->generateAvailableTimes();
        $this->time = null;
    }

    public function generateAvailableTimes()
    {
        if (!$this->date || !$this->selectedStaff || !$this->selectedService) {
            $this->availableTimes = [];
            return;
        }

        $settings = SalonSetting::getSettings(); // Recommended: use your static method if available
        // Fallback if getSettings() doesn't exist
        if (!$settings) {
            $settings = SalonSetting::find(1) ?: new SalonSetting();
        }

        $openingTime = $settings->opening_time ?? '09:00:00';
        $closingTime = $settings->closing_time ?? '20:00:00';
        $interval = $settings->slot_interval_minutes ?? 30;

        // Get service duration
        $service = Service::find($this->selectedService);
        $duration = $service?->duration_minutes ?? 60;

        // Enforce minimum duration
        if ($duration < 30) {
            $duration = 30;
        }

        $dayStart = Carbon::parse($this->date . ' ' . $openingTime);
        $dayEnd = Carbon::parse($this->date . ' ' . $closingTime);

        $availableSlots = [];
        $current = $dayStart->copy();

        while ($current->lt($dayEnd)) {
            $timeSlot = $current->format('H:i');
            $slotStart = Carbon::parse($this->date . ' ' . $timeSlot . ':00');
            $slotEnd = $slotStart->copy()->addMinutes($duration);

            // Skip if appointment would end after closing time
            if ($slotEnd->gt($dayEnd)) {
                $current->addMinutes($interval);
                continue;
            }

            // Check for any overlapping appointments
            $isBooked = Appointment::where('staff_id', $this->selectedStaff)
                ->where(function ($q) use ($slotStart, $slotEnd) {
                    $q->whereBetween('start_datetime', [$slotStart, $slotEnd->copy()->subMinute()])
                      ->orWhereBetween('end_datetime', [$slotStart->copy()->addMinute(), $slotEnd])
                      ->orWhere(function ($q2) use ($slotStart, $slotEnd) {
                          $q2->where('start_datetime', '<=', $slotStart)
                             ->where('end_datetime', '>=', $slotEnd);
                      });
                })
                ->whereNotIn('status', ['cancelled', 'no_show'])
                ->exists();

            // Only add if not booked and slot start is now or in the future
            if (!$isBooked && $slotStart->isFuture() || $slotStart->isToday()) {
                $availableSlots[] = $timeSlot;
            }

            $current->addMinutes($interval);
        }

        $this->availableTimes = $availableSlots;
    }

    public function book()
    {
        $this->validate([
            'full_name'       => 'required|string|max:100',
            'phone'           => 'required|string|max:20',
            'email'           => 'nullable|email',
            'selectedService' => 'required|exists:services,id',
            'selectedStaff'   => 'required|exists:staff,id',
            'date'            => 'required|date|after_or_equal:today',
            'time'            => 'required',
        ]);

        $service = Service::findOrFail($this->selectedService);
        $duration = max($service->duration_minutes, 30); // Enforce min 30

        $startDateTime = Carbon::parse($this->date . ' ' . $this->time . ':00');
        $endDateTime = $startDateTime->copy()->addMinutes($duration);

        // Final double-check for overlaps (race condition protection)
        $isBooked = Appointment::where('staff_id', $this->selectedStaff)
            ->where(function ($q) use ($startDateTime, $endDateTime) {
                $q->whereBetween('start_datetime', [$startDateTime, $endDateTime->copy()->subMinute()])
                  ->orWhereBetween('end_datetime', [$startDateTime->copy()->addMinute(), $endDateTime])
                  ->orWhere(function ($q2) use ($startDateTime, $endDateTime) {
                      $q2->where('start_datetime', '<=', $startDateTime)
                         ->where('end_datetime', '>=', $endDateTime);
                  });
            })
            ->whereNotIn('status', ['cancelled', 'no_show'])
            ->exists();

        if ($isBooked) {
            $this->addError('time', 'This time slot is no longer available. Please select another time.');
            $this->generateAvailableTimes(); // Refresh slots
            return;
        }

        DB::transaction(function () use ($service, $startDateTime, $endDateTime) {
            $customer = Customer::firstOrCreate(
                ['phone' => $this->phone],
                [
                    'full_name' => $this->full_name,
                    'email'     => $this->email ?? null,
                ]
            );

            $price = $service->price_premium ?? $service->price_regular;

            Appointment::create([
                'customer_id'    => $customer->id,
                'staff_id'       => $this->selectedStaff,
                'service_id'     => $this->selectedService,
                'start_datetime' => $startDateTime,
                'end_datetime'   => $endDateTime,
                'total_amount'   => $price,
                'status'         => 'scheduled',
                'notes'          => $this->notes ?? null,
                'is_walk_in'     => false,
            ]);
        });

        $this->close();
        session()->flash('success', 'Appointment booked successfully! We will contact you shortly.');
        $this->dispatch('toast', message: 'Appointment booked successfully!');
    }

    public function render()
    {
        return view('livewire.guest-booking-modal');
    }
}