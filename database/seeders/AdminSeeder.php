<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Admin::create([
            'name' => 'Admin SMADIMENT',
            'email' => 'admin@smadiment.com',
            'password' => Hash::make('admin123'), // Default password
        ]);
    }
}