<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Appointment; // Add this import

class Calendar extends Component
{
    public $events = []; // Add a property to store events

    public function mount()
    {
        // Load events when component initializes
        $this->events = $this->getEvents();
    }

    public function render()
    {
        return view('livewire.calendar', [
            'events' => $this->events // Pass events to the view
        ]);
    }

    public function getEvents()
    {
        return Appointment::with(['customer', 'service', 'staff.user'])
            ->get()
            ->map(function ($appt) {
                return [
                    'title' => $appt->customer->full_name . ' - ' . $appt->service->name,
                    'start' => $appt->start_datetime,
                    'end' => $appt->end_datetime,
                    'color' => $appt->staff->color_code ?? '#3B82F6',
'url' => '#', // or remove the url property
                    // Add more properties as needed
                    'extendedProps' => [
                        'customer' => $appt->customer->full_name,
                        'service' => $appt->service->name,
                        'staff' => $appt->staff->user->name ?? 'Unassigned',
                    ]
                ];
            })->toArray();
    }
}