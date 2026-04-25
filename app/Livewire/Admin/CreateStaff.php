<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\User;
use App\Models\Department;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CreateStaff extends Component
{
    use WithFileUploads;

    public $name;
    public $email;
    public $staff_number;
    public $password;
    public $password_confirmation;
    public $department_id;
    public $role = 'staff';
    public $phone;
    public $position;
    public $profile_photo;
    public $hire_date;
    public $is_active = true;

    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'staff_number' => 'required|string|unique:users,staff_number',
        'password' => 'required|min:8|confirmed',
        'department_id' => 'nullable|exists:departments,id',
        'role' => 'required|in:admin,hod,staff',
        'phone' => 'nullable|string|max:20',
        'position' => 'nullable|string|max:255',
        'profile_photo' => 'nullable|image|max:2048',
        'hire_date' => 'nullable|date',
        'is_active' => 'boolean',
    ];

    protected $messages = [
        'email.unique' => 'This email is already registered.',
        'staff_number.unique' => 'This staff number already exists.',
        'password.confirmed' => 'Password confirmation does not match.',
    ];

    public function generateStaffNumber()
    {
        $year = date('Y');
        $lastStaff = User::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        if ($lastStaff && $lastStaff->staff_number) {
            $parts = explode('/', $lastStaff->staff_number);
            $lastNumber = intval(end($parts));
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        $this->staff_number = "STAFF/{$year}/{$newNumber}";
    }

    public function save()
    {
        $this->validate();

        // Save profile photo - use profile_photo_path instead of profile_photo
        $photoPath = null;
        if ($this->profile_photo) {
            $photoPath = $this->profile_photo->store('profile-photos', 'public');
        }

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'staff_number' => $this->staff_number,
            'password' => Hash::make($this->password),
            'department_id' => $this->department_id,
            'role' => $this->role,
            'phone' => $this->phone,
            'position' => $this->position,
            'profile_photo_path' => $photoPath, // Changed from profile_photo to profile_photo_path
            'hire_date' => $this->hire_date,
            'is_active' => $this->is_active,
        ]);

        session()->flash('message', "Staff member {$user->name} created successfully!");
        return redirect()->route('admin.staff.index');
    }

    public function render()
    {
        return view('livewire.admin.create-staff', [
            'departments' => Department::orderBy('name')->get(),
        ])->layout('layouts.app');
    }
}
