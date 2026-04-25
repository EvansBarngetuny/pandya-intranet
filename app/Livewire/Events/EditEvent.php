<?php

namespace App\Livewire\Events;

use Livewire\Component;
use App\Models\Event;
use App\Models\Department;
use Carbon\Carbon;

class EditEvent extends Component
{
    public Event $event;
    public $title;
    public $description;
    public $type;
    public $venue;
    public $start_datetime;
    public $end_datetime;
    public $target_departments = [];
    public $contact_person;
    public $contact_phone;
    public $requires_registration;
    public $max_attendees;

    public function mount(Event $event)
    {
        // Check permission - only admin or event organizer can edit
        if (!auth()->user()->isAdmin() && auth()->id() !== $event->organizer_id) {
            abort(403, 'You do not have permission to edit this event.');
        }

        $this->event = $event;
        $this->title = $event->title;
        $this->description = $event->description;
        $this->type = $event->type;
        $this->venue = $event->venue;
        $this->start_datetime = $event->start_datetime ? $event->start_datetime->format('Y-m-d\TH:i') : null;
        $this->end_datetime = $event->end_datetime ? $event->end_datetime->format('Y-m-d\TH:i') : null;
        $this->target_departments = $event->target_departments ?? [];
        $this->contact_person = $event->contact_person;
        $this->contact_phone = $event->contact_phone;
        $this->requires_registration = $event->requires_registration;
        $this->max_attendees = $event->max_attendees;
    }

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

    public function update()
    {
        $this->validate();

        $this->event->update([
            'title' => $this->title,
            'description' => $this->description,
            'type' => $this->type,
            'venue' => $this->venue,
            'start_datetime' => Carbon::parse($this->start_datetime),
            'end_datetime' => Carbon::parse($this->end_datetime),
            'target_departments' => $this->target_departments,
            'contact_person' => $this->contact_person,
            'contact_phone' => $this->contact_phone,
            'requires_registration' => $this->requires_registration,
            'max_attendees' => $this->max_attendees,
        ]);

        session()->flash('message', 'Event updated successfully!');
        return redirect()->route('events.show', $this->event);
    }

    public function render()
    {
        $departments = Department::orderBy('name')->get();

        return view('livewire.events.edit-event', [
            'departments' => $departments,
        ])->layout('layouts.app');
    }
}
