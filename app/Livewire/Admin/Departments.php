<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Department;
use App\Models\User;

class Departments extends Component
{
    public $departments;
    public $showCreateForm = false;
    public $showEditForm = false;
    public $editingDepartment = null;
    
    // Form fields
    public $name;
    public $code;
    public $hod_name;
    public $icon = '🏥';
    public $color = '#3B82F6';
    
    protected $rules = [
        'name' => 'required|string|max:255',
        'code' => 'required|string|max:50|unique:departments,code',
        'hod_name' => 'nullable|string|max:255',
        'icon' => 'nullable|string|max:10',
    ];
    
    public function mount()
    {
        $this->loadDepartments();
    }
    
    public function loadDepartments()
    {
        $this->departments = Department::withCount('users')->orderBy('name')->get();
    }
    
    public function createDepartment()
    {
        $this->validate();
        
        Department::create([
            'name' => $this->name,
            'code' => strtoupper($this->code),
            'hod_name' => $this->hod_name,
            'icon' => $this->icon,
            'color' => $this->color,
        ]);
        
        $this->reset(['showCreateForm', 'name', 'code', 'hod_name', 'icon']);
        $this->loadDepartments();
        session()->flash('message', 'Department created successfully!');
    }
    
    public function editDepartment($id)
    {
        $this->editingDepartment = Department::findOrFail($id);
        $this->name = $this->editingDepartment->name;
        $this->code = $this->editingDepartment->code;
        $this->hod_name = $this->editingDepartment->hod_name;
        $this->icon = $this->editingDepartment->icon;
        $this->color = $this->editingDepartment->color;
        $this->showEditForm = true;
    }
    
    public function updateDepartment()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:departments,code,' . $this->editingDepartment->id,
            'hod_name' => 'nullable|string|max:255',
        ]);
        
        $this->editingDepartment->update([
            'name' => $this->name,
            'code' => strtoupper($this->code),
            'hod_name' => $this->hod_name,
            'icon' => $this->icon,
            'color' => $this->color,
        ]);
        
        $this->reset(['showEditForm', 'editingDepartment', 'name', 'code', 'hod_name', 'icon']);
        $this->loadDepartments();
        session()->flash('message', 'Department updated successfully!');
    }
    
    public function deleteDepartment($id)
    {
        $department = Department::findOrFail($id);
        
        // Check if department has staff
        if ($department->users()->count() > 0) {
            session()->flash('error', 'Cannot delete department with assigned staff.');
            return;
        }
        
        $department->delete();
        $this->loadDepartments();
        session()->flash('message', 'Department deleted successfully!');
    }
    
    public function render()
    {
        return view('livewire.admin.departments')->layout('layouts.app');
    }
}