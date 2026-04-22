<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
          $departments = [
            ['name' => 'Administration', 'code' => 'ADMIN', 'head_of_department' => 'Henry Longei', 'icon' => '👔'],
            ['name' => 'Medical Services', 'code' => 'MED', 'head_of_department' => 'Dr. Herbert Kayonga', 'icon' => '🩺'],
            ['name' => 'Nursing', 'code' => 'NURSE', 'head_of_department' => 'Ismail Onchari', 'icon' => '💉'],
            ['name' => 'Pharmacy', 'code' => 'PHARM', 'head_of_department' => 'Lilian Kibuchi', 'icon' => '💊'],
            ['name' => 'Laboratory', 'code' => 'LAB', 'head_of_department' => 'Peter Ndalu', 'icon' => '🔬'],
            ['name' => 'Radiology', 'code' => 'RAD', 'head_of_department' => 'Fredrick Mukie', 'icon' => '📊'],
            ['name' => 'Finance', 'code' => 'FIN', 'head_of_department' => 'Vivek Sethi', 'icon' => '💰'],
            ['name' => 'Human Resources', 'code' => 'HR', 'head_of_department' => 'Miriam Murage', 'icon' => '👥'],
            ['name' => 'ICT', 'code' => 'ICT', 'head_of_department' => 'Vincent Mutua', 'icon' => '💻'],
        ];
        foreach ($departments as $dept) {
            Department::create($dept);
        }
        // User::factory(10)->withPersonalTeam()->create();
        User::create([
            'name' => 'System Admin',
            'email' => 'admin@pandya-hospital.org',
            'staff_number' => 'ADMIN001',
            'password' => Hash::make('Admin@123'),
            'department_id' => Department::where('code', 'ADMIN')->first()->id,
            'role' => 'admin',
            'phone' => '+254700000000',
            'position' => 'System Administrator',
            'is_active' => true,
        ]);
         // Create HOD users
        $hods = [
            ['name' => 'Henry Longei', 'email' => 'hod.admin@pandya-hospital.org', 'staff_number' => 'HOD001', 'dept' => 'ADMIN'],
            ['name' => 'Dr. Herbert Kayonga', 'email' => 'hod.medical@pandya-hospital.org', 'staff_number' => 'HOD002', 'dept' => 'MED'],
            ['name' => 'Ismail Onchari', 'email' => 'hod.nursing@pandya-hospital.org', 'staff_number' => 'HOD003', 'dept' => 'NURSE'],
            ['name' => 'Vincent Mutua', 'email' => 'hod.ict@pandya-hospital.org', 'staff_number' => 'HOD004', 'dept' => 'ICT'],
        ];
           foreach ($hods as $hod) {
            User::create([
                'name' => $hod['name'],
                'email' => $hod['email'],
                'staff_number' => $hod['staff_number'],
                'password' => Hash::make('Hod@123'),
                'department_id' => Department::where('code', $hod['dept'])->first()->id,
                'role' => 'hod',
                'phone' => '+254700000000',
                'position' => 'Head of Department',
                'is_active' => true,
            ]);
        }

        // Create sample staff
        for ($i = 1; $i <= 20; $i++) {
            User::create([
                'name' => "Staff Member $i",
                'email' => "staff$i@pandya-hospital.org",
                'staff_number' => "STAFF" . str_pad($i, 4, '0', STR_PAD_LEFT),
                'password' => Hash::make('Staff@123'),
                'department_id' => Department::inRandomOrder()->first()->id,
                'role' => 'staff',
                'phone' => '+2547' . rand(10000000, 99999999),
                'position' => ['Nurse', 'Doctor', 'Technician', 'Administrator', 'Accountant'][array_rand(['Nurse', 'Doctor', 'Technician', 'Administrator', 'Accountant'])],
                'is_active' => true,
            ]);
        }
    }
}
