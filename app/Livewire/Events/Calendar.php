<?php

namespace App\Livewire\Events;

use Livewire\Component;
use App\Models\Event;
use Carbon\Carbon;

class Calendar extends Component
{
    public $currentMonth;
    public $currentYear;
    public $events = [];
    public $selectedDate = null;
    public $selectedEvents = [];
    
    public function mount()
    {
        $this->currentMonth = Carbon::now()->month;
        $this->currentYear = Carbon::now()->year;
        $this->loadEvents();
    }
    
    public function loadEvents()
    {
        $startDate = Carbon::create($this->currentYear, $this->currentMonth, 1)->startOfMonth();
        $endDate = Carbon::create($this->currentYear, $this->currentMonth, 1)->endOfMonth();
        
        $this->events = Event::whereBetween('start_datetime', [$startDate, $endDate])
            ->orderBy('start_datetime')
            ->get()
            ->groupBy(function($event) {
                return Carbon::parse($event->start_datetime)->format('Y-m-d');
            });
    }
    
    public function previousMonth()
    {
        $date = Carbon::create($this->currentYear, $this->currentMonth, 1)->subMonth();
        $this->currentMonth = $date->month;
        $this->currentYear = $date->year;
        $this->loadEvents();
    }
    
    public function nextMonth()
    {
        $date = Carbon::create($this->currentYear, $this->currentMonth, 1)->addMonth();
        $this->currentMonth = $date->month;
        $this->currentYear = $date->year;
        $this->loadEvents();
    }
    
    public function viewDate($date)
    {
        $this->selectedDate = $date;
        $this->selectedEvents = $this->events[$date] ?? collect();
    }
    
    public function render()
    {
        $calendar = $this->buildCalendar();
        return view('livewire.events.calendar', [
            'calendar' => $calendar,
            'monthName' => Carbon::create($this->currentYear, $this->currentMonth, 1)->format('F Y'),
        ])->layout('layouts.app');
    }
    
    protected function buildCalendar()
    {
        $firstDayOfMonth = Carbon::create($this->currentYear, $this->currentMonth, 1);
        $daysInMonth = $firstDayOfMonth->daysInMonth;
        $startDayOfWeek = $firstDayOfMonth->dayOfWeek;
        
        $calendar = [];
        $week = [];
        
        // Add empty days for start of month
        for ($i = 0; $i < $startDayOfWeek; $i++) {
            $week[] = null;
        }
        
        // Add days of month
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = $firstDayOfMonth->copy()->day($day);
            $week[] = [
                'day' => $day,
                'date' => $date->format('Y-m-d'),
                'hasEvents' => isset($this->events[$date->format('Y-m-d')]),
                'events' => $this->events[$date->format('Y-m-d')] ?? collect()
            ];
            
            if (count($week) == 7) {
                $calendar[] = $week;
                $week = [];
            }
        }
        
        // Add empty days for end of month
        if (count($week) > 0) {
            while (count($week) < 7) {
                $week[] = null;
            }
            $calendar[] = $week;
        }
        
        return $calendar;
    }
}