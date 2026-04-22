<?php

namespace App\Livewire\Events;

use Livewire\Component;
use App\Models\Event;

class ShowEvent extends Component
{
    public Event $event;
    public $isRegistered = false;
    public $attendeeCount = 0;
    
    public function mount(Event $event)
    {
        $this->event = $event;
        $this->isRegistered = $event->registrations()
            ->where('user_id', auth()->id())
            ->exists();
        $this->attendeeCount = $event->registrations()->count();
    }
    
    public function register()
    {
        if (!$this->event->requires_registration) {
            session()->flash('error', 'This event does not require registration.');
            return;
        }
        
        if ($this->isRegistered) {
            session()->flash('error', 'You are already registered for this event.');
            return;
        }
        
        if ($this->event->max_attendees && $this->attendeeCount >= $this->event->max_attendees) {
            session()->flash('error', 'This event is fully booked.');
            return;
        }
        
        $this->event->registrations()->create([
            'user_id' => auth()->id(),
            'registered_at' => now(),
            'status' => 'registered'
        ]);
        
        $this->isRegistered = true;
        $this->attendeeCount++;
        session()->flash('message', 'Successfully registered for the event!');
    }
    
    public function cancelRegistration()
    {
        $registration = $this->event->registrations()
            ->where('user_id', auth()->id())
            ->first();
            
        if ($registration) {
            $registration->delete();
            $this->isRegistered = false;
            $this->attendeeCount--;
            session()->flash('message', 'Registration cancelled successfully.');
        }
    }
    
    public function render()
    {
        return view('livewire.events.show-event')->layout('layouts.app');
    }
}