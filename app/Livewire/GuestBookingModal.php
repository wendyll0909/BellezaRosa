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
        $this->generateAvailableTimes();
    }

    public function open()
    {
        $this->show = true;
        $this->generateAvailableTimes();
    }

    public function close()
    {
        $this->show = false;
        $this->reset(['full_name', 'phone', 'email', 'notes', 'selectedService', 'selectedStaff', 'time']);
    }

    public function updatedDate()
    {
        $this->generateAvailableTimes();
        $this->time = '';
    }

    public function updatedSelectedStaff()
    {
        $this->generateAvailableTimes();
        $this->time = '';
    }

    public function generateAvailableTimes()
    {
        if (!$this->date || !$this->selectedStaff) {
            $this->availableTimes = [];
            return;
        }

        $settings = SalonSetting::find(1);
        $openingTime = $settings->opening_time ?? '09:00:00';
        $closingTime = $settings->closing_time ?? '20:00:00';
        $interval = $settings->slot_interval_minutes ?? 30;

        $start = Carbon::parse($this->date . ' ' . $openingTime);
        $end = Carbon::parse($this->date . ' ' . $closingTime);

        $availableSlots = [];
        $current = $start->copy();

        while ($current < $end) {
            $timeSlot = $current->format('H:i');
            $datetime = $this->date . ' ' . $timeSlot . ':00';

            $isBooked = Appointment::where('staff_id', $this->selectedStaff)
                ->where('start_datetime', $datetime)
                ->exists();

            if (!$isBooked && $current->isFuture()) {
                $availableSlots[] = $timeSlot;
            }

            $current->addMinutes($interval);
        }

        $this->availableTimes = $availableSlots;
    }

    public function book()
    {
        $this->validate([
            'full_name' => 'required|string|max:100',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email',
            'selectedService' => 'required|exists:services,id',
            'selectedStaff' => 'required|exists:staff,id',
            'date' => 'required|date|after_or_equal:today',
            'time' => 'required',
        ]);

        $start = $this->date . ' ' . $this->time . ':00';

        // Double-check availability
        $exists = Appointment::where('staff_id', $this->selectedStaff)
            ->where('start_datetime', $start)
            ->exists();

        if ($exists) {
            $this->addError('time', 'This time slot is already booked. Please select another time.');
            return;
        }

        DB::transaction(function () use ($start) {
            $customer = Customer::firstOrCreate(
                ['phone' => $this->phone],
                [
                    'full_name' => $this->full_name,
                    'email' => $this->email
                ]
            );

            $service = Service::find($this->selectedService);
            $price = $service->price_premium ?? $service->price_regular;

            // Create the appointment - this will be saved in database
            Appointment::create([
                'customer_id' => $customer->id,
                'staff_id' => $this->selectedStaff,
                'service_id' => $this->selectedService,
                'start_datetime' => $start,
                'end_datetime' => Carbon::parse($start)->addMinutes($service->duration_minutes ?? 60),
                'total_amount' => $price,
                'status' => 'scheduled',
                'notes' => $this->notes,
                'is_walk_in' => false,
            ]);
        });

        $this->close();
        
        // Show success message
        session()->flash('success', 'Appointment booked successfully! We will contact you shortly.');
        
        // Dispatch event for toast notification
        $this->dispatch('toast', message: 'Appointment booked successfully!');
    }

    public function render()
    {
        return view('livewire.guest-booking-modal');
    }
}