<?php

namespace App\Livewire\Events;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Event;
use Carbon\Carbon;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $type = '';
    public $viewMode = 'list'; // list or calendar
    public $selectedDate = null;
    
    protected $queryString = ['search', 'type', 'viewMode'];
    
    public function mount()
    {
        $this->selectedDate = Carbon::now()->format('Y-m-d');
    }
    
    public function updatingSearch()
    {
        $this->resetPage();
    }
    
    public function updatingType()
    {
        $this->resetPage();
    }
    
    public function registerForEvent($eventId)
    {
        $event = Event::findOrFail($eventId);
        
        if (!$event->requires_registration) {
            session()->flash('error', 'This event does not require registration.');
            return;
        }
        
        $alreadyRegistered = $event->registrations()
            ->where('user_id', auth()->id())
            ->exists();
            
        if ($alreadyRegistered) {
            session()->flash('error', 'You are already registered for this event.');
            return;
        }
        
        if ($event->max_attendees && $event->registrations()->count() >= $event->max_attendees) {
            session()->flash('error', 'This event is fully booked.');
            return;
        }
        
        $event->registrations()->create([
            'user_id' => auth()->id(),
            'registered_at' => now(),
            'status' => 'registered'
        ]);
        
        session()->flash('message', 'Successfully registered for the event!');
    }
    
    public function render()
    {
        $events = Event::with('organizer')
            ->when($this->search, function($query) {
                $query->where('title', 'like', '%' . $this->search . '%')
                    ->orWhere('description', 'like', '%' . $this->search . '%')
                    ->orWhere('venue', 'like', '%' . $this->search . '%');
            })
            ->when($this->type, fn($q) => $q->where('type', $this->type))
            ->when($this->viewMode === 'list', function($query) {
                $query->where('start_datetime', '>=', now())
                      ->orderBy('start_datetime', 'asc');
            })
            ->paginate(10);
            
        // Get upcoming events count
        $upcomingCount = Event::where('start_datetime', '>=', now())->count();
        
        // Get events by type for statistics
        $typeStats = [
            'training' => Event::where('type', 'training')->where('start_datetime', '>=', now())->count(),
            'meeting' => Event::where('type', 'meeting')->where('start_datetime', '>=', now())->count(),
            'cme' => Event::where('type', 'cme')->where('start_datetime', '>=', now())->count(),
            'social' => Event::where('type', 'social')->where('start_datetime', '>=', now())->count(),
        ];
        
        return view('livewire.events.index', [
            'events' => $events,
            'upcomingCount' => $upcomingCount,
            'typeStats' => $typeStats,
        ])->layout('layouts.app');
    }
}