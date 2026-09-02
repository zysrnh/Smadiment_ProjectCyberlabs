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
            'email' => '4dm1n@smadiment.com',
            'password' => Hash::make('sm4d1m3nt_analytic'), // Default password
        ]);
    }
}