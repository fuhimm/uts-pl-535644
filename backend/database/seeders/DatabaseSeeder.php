<?php

namespace Database\Seeders;

use App\Models\Internship;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'fahim@ugm.ac.id'],
            [
                'name' => 'Ahmad Fahim',
                'password' => Hash::make('password123'),
            ]
        );

        if (Internship::count() === 0) {
            Internship::create([
                'student_name' => 'Ahmad Fahim',
                'company_name' => 'PT Telkom Indonesia',
                'position' => 'DevOps & Cloud Intern',
                'start_date' => '2026-08-01',
                'status' => 'Diterima',
            ]);

            Internship::create([
                'student_name' => 'Budi Santoso',
                'company_name' => 'PT GoTo Gojek Tokopedia',
                'position' => 'Backend Software Engineer Intern',
                'start_date' => '2026-09-01',
                'status' => 'Pending',
            ]);

            Internship::create([
                'student_name' => 'Siti Aminah',
                'company_name' => 'PT Bank Rakyat Indonesia',
                'position' => 'Fullstack Web Developer Intern',
                'start_date' => '2026-02-01',
                'status' => 'Selesai',
            ]);
        }
    }
}
