<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Service;
use App\Models\Staff;
use App\Models\Customer;
use App\Models\Appointment;
use Illuminate\Support\Facades\DB;

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
    }

    public function close()
    {
        $this->show = false;
        $this->reset(['full_name', 'phone', 'email', 'notes', 'selectedService', 'selectedStaff', 'time']);
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

        $exists = Appointment::where('staff_id', $this->selectedStaff)
            ->where('start_datetime', $start)
            ->exists();

        if ($exists) {
            $this->addError('time', 'This time slot is already booked.');
            return;
        }

        DB::transaction(function () use ($start) {
            $customer = Customer::firstOrCreate(
                ['phone' => $this->phone],
                ['full_name' => $this->full_name, 'email' => $this->email]
            );

            $service = Service::find($this->selectedService);
            $price = $service->price_premium ?? $service->price_regular;

            Appointment::create([
                'customer_id' => $customer->id,
                'staff_id' => $this->selectedStaff,
                'service_id' => $this->selectedService,
                'start_datetime' => $start,
                'total_amount' => $price,
                'status' => 'scheduled',
                'notes' => $this->notes,
            ]);
        });

        $this->close();
        $this->dispatch('toast', 'Appointment booked successfully!');
    }

    public function render()
    {
        return view('livewire.guest-booking-modal');
    }
}