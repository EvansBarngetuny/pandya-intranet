<?php

namespace App\Livewire\Events;

use Livewire\Component;
use App\Models\Event;
use App\Models\Department;
use Carbon\Carbon;

class CreateEvent extends Component
{
    public $title;
    public $description;
    public $type = 'meeting';
    public $venue;
    public $start_datetime;
    public $end_datetime;
    public $target_departments = [];
    public $contact_person;
    public $contact_phone;
    public $requires_registration = false;
    public $max_attendees;

    protected $rules = [
        'title' => 'required|string|max:255',
        'description' => 'required|string',
        'type' => 'required|in:training,meeting,cme,social,other',
        'venue' => 'required|string|max:255',
        'start_datetime' => 'required|date',
        'end_datetime' => 'required|date|after:start_datetime',
        'target_departments' => 'nullable|array',
        'contact_person' => 'nullable|string|max:255',
        'contact_phone' => 'nullable|string|max:20',
        'requires_registration' => 'boolean',
        'max_attendees' => 'nullable|integer|min:1',
    ];

    protected $messages = [
        'title.required' => 'Please enter an event title.',
        'description.required' => 'Please enter event description.',
        'venue.required' => 'Please enter the venue.',
        'start_datetime.required' => 'Please select start date and time.',
        'end_datetime.required' => 'Please select end date and time.',
        'end_datetime.after' => 'End date must be after start date.',
    ];

    public function save()
    {
        $this->validate();

        Event::create([
            'title' => $this->title,
            'description' => $this->description,
            'type' => $this->type,
            'venue' => $this->venue,
            'start_datetime' => Carbon::parse($this->start_datetime),
            'end_datetime' => Carbon::parse($this->end_datetime),
            'target_departments' => $this->target_departments,
            'organizer_id' => auth()->id(),
            'contact_person' => $this->contact_person,
            'contact_phone' => $this->contact_phone,
            'requires_registration' => $this->requires_registration,
            'max_attendees' => $this->max_attendees,
        ]);

        session()->flash('message', 'Event created successfully!');
        return redirect()->route('events.index');
    }

    public function render()
    {
        $departments = Department::orderBy('name')->get();

        return view('livewire.events.create-event', [
            'departments' => $departments,
        ])->layout('layouts.app');
    }
}
