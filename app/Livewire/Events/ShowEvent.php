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
       // $this->isRegistered = $event->registrations()
         //   ->where('user_id', auth()->id())
           // ->exists();
        //$this->attendeeCount = $event->registrations()->count();
    }


    public function render()
    {
        return view('livewire.events.show-event')->layout('layouts.app');
    }
}
