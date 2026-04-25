<?php

namespace App\Livewire\Events;

use Livewire\Component;
use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class Calendar extends Component
{
    public $currentMonth;
    public $currentYear;
    public $events = [];
    public $selectedDate = null;
    public $selectedEvents = [];
    public $viewMode = 'month';

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

        // Get events and convert to array to avoid Collection issues
        $eventsQuery = Event::whereBetween('start_datetime', [$startDate, $endDate])
            ->orWhereBetween('end_datetime', [$startDate, $endDate])
            ->orderBy('start_datetime')
            ->get();

        // Manually group events by date
        $this->events = [];
        foreach ($eventsQuery as $event) {
            $date = Carbon::parse($event->start_datetime)->format('Y-m-d');
            if (!isset($this->events[$date])) {
                $this->events[$date] = [];
            }
            $this->events[$date][] = $event;
        }
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

    public function goToToday()
    {
        $this->currentMonth = Carbon::now()->month;
        $this->currentYear = Carbon::now()->year;
        $this->loadEvents();
        $this->selectedDate = null;
        $this->selectedEvents = [];
    }

    public function viewDate($date)
    {
        $this->selectedDate = $date;
        $this->selectedEvents = isset($this->events[$date]) ? $this->events[$date] : [];
    }

    public function closeModal()
    {
        $this->selectedDate = null;
        $this->selectedEvents = [];
    }

    public function render()
    {
        $calendar = $this->buildCalendar();
        $weekDays = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];

        return view('livewire.events.calendar', [
            'calendar' => $calendar,
            'monthName' => Carbon::create($this->currentYear, $this->currentMonth, 1)->format('F Y'),
            'weekDays' => $weekDays,
            'today' => Carbon::today()->format('Y-m-d'),
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
            $dateString = $date->format('Y-m-d');

            $hasEvents = isset($this->events[$dateString]);
            $eventCount = $hasEvents ? count($this->events[$dateString]) : 0;
            $dayEvents = $hasEvents ? $this->events[$dateString] : [];

            $week[] = [
                'day' => $day,
                'date' => $dateString,
                'isToday' => $dateString === Carbon::today()->format('Y-m-d'),
                'hasEvents' => $hasEvents,
                'eventCount' => $eventCount,
                'events' => $dayEvents
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
