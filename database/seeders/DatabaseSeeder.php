<?php

namespace Database\Seeders;

use App\Models\ResearchRecord;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin user
        User::create([
            'name'     => 'System Administrator',
            'email'    => env('ADMIN_EMAIL', 'admin@research.edu.ph'),
            'password' => Hash::make(env('ADMIN_PASSWORD', 'Admin@1234')),
        ]);


        foreach ($records as $record) {
            ResearchRecord::create($record);
        }
    }
}
