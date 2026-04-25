<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\User;
use App\Models\Department;
use Illuminate\Support\Facades\Hash;

class EditStaff extends Component
{
    use WithFileUploads;

    public User $user;
    public $name;
    public $email;
    public $staff_number;
    public $department_id;
    public $role;
    public $phone;
    public $position;
    public $profile_photo;
    public $existing_photo;
    public $hire_date;
    public $is_active;
    public $password;
    public $password_confirmation;

    public function mount(User $user)
    {
        $this->user = $user;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->staff_number = $user->staff_number;
        $this->department_id = $user->department_id;
        $this->role = $user->role;
        $this->phone = $user->phone;
        $this->position = $user->position;
        $this->existing_photo = $user->profile_photo_path; // Changed from profile_photo to profile_photo_path
        $this->hire_date = $user->hire_date?->format('Y-m-d');
        $this->is_active = $user->is_active;
    }

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $this->user->id,
            'staff_number' => 'required|string|unique:users,staff_number,' . $this->user->id,
            'department_id' => 'nullable|exists:departments,id',
            'role' => 'required|in:admin,hod,staff',
            'phone' => 'nullable|string|max:20',
            'position' => 'nullable|string|max:255',
            'profile_photo' => 'nullable|image|max:2048',
            'hire_date' => 'nullable|date',
            'is_active' => 'boolean',
            'password' => 'nullable|min:8|confirmed',
        ];
    }

    public function update()
    {
        $this->validate();

        // Save profile photo if uploaded - use profile_photo_path
        if ($this->profile_photo) {
            $photoPath = $this->profile_photo->store('profile-photos', 'public');
            $this->user->profile_photo_path = $photoPath;
        }

        $updateData = [
            'name' => $this->name,
            'email' => $this->email,
            'staff_number' => $this->staff_number,
            'department_id' => $this->department_id,
            'role' => $this->role,
            'phone' => $this->phone,
            'position' => $this->position,
            'hire_date' => $this->hire_date,
            'is_active' => $this->is_active,
        ];

        // Update password if provided
        if ($this->password) {
            $updateData['password'] = Hash::make($this->password);
        }

        $this->user->update($updateData);

        session()->flash('message', "Staff member {$this->user->name} updated successfully!");
        return redirect()->route('admin.staff.index');
    }

    public function render()
    {
        return view('livewire.admin.edit-staff', [
            'departments' => Department::orderBy('name')->get(),
        ])->layout('layouts.app');
    }
}
