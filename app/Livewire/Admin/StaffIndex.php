<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use App\Models\Department;

class StaffIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $department_filter = '';
    public $role_filter = '';
    public $status_filter = '';
    public $showCreateModal = false;
    public $showEditModal = false;
    public $selectedUser = null;

    protected $queryString = ['search', 'department_filter', 'role_filter', 'status_filter'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function toggleUserStatus($userId)
    {
        $user = User::findOrFail($userId);
        $user->update(['is_active' => !$user->is_active]);
        session()->flash('message', "User {$user->name} has been " . ($user->is_active ? 'activated' : 'deactivated'));
    }

    public function deleteUser($userId)
    {
        $user = User::findOrFail($userId);
        
        // Prevent deleting yourself
        if ($user->id === auth()->id()) {
            session()->flash('error', 'You cannot delete your own account.');
            return;
        }
        
        $user->delete();
        session()->flash('message', 'User deleted successfully.');
    }

    public function render()
    {
        $staff = User::with('department')
            ->when($this->search, function($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%')
                    ->orWhere('staff_number', 'like', '%' . $this->search . '%');
            })
            ->when($this->department_filter, fn($q) => $q->where('department_id', $this->department_filter))
            ->when($this->role_filter, fn($q) => $q->where('role', $this->role_filter))
            ->when($this->status_filter !== '', fn($q) => $q->where('is_active', $this->status_filter == 'active'))
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $departments = Department::orderBy('name')->get();
        
        $stats = [
            'total' => User::count(),
            'active' => User::where('is_active', true)->count(),
            'admin' => User::where('role', 'admin')->count(),
            'hod' => User::where('role', 'hod')->count(),
            'staff' => User::where('role', 'staff')->count(),
        ];

        return view('livewire.admin.staff-index', [
            'staff' => $staff,
            'departments' => $departments,
            'stats' => $stats,
        ])->layout('layouts.app');
    }
}