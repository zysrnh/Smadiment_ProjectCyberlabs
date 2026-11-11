<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserProject;
use App\Services\MediaKernelsClient;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create or update Unlimited User (trial_ends_at = null -> Unlimited / Lifetime Access)
        $unlimitedUser = User::updateOrCreate(
            ['email' => 'unlimited@smadiment.com'],
            [
                'name'              => 'VIP Unlimited User',
                'password'          => Hash::make('password123'),
                'email_verified_at' => now(),
                'trial_ends_at'     => null, // Null = Unlimited access
            ]
        );

        // 2. Project resmi SMADIMENT: Prabowo (16978)
        $uniqueProjectIds = [16978];

        // 3. Assign projects to Unlimited account
        foreach ($uniqueProjectIds as $projectId) {
            UserProject::updateOrCreate(
                [
                    'user_id'    => $unlimitedUser->id,
                    'project_id' => $projectId,
                ]
            );
        }

        $this->command->info("✅ Unlimited User [{$unlimitedUser->email}] seeded successfully (Password: password123, Access: Unlimited)");
        $this->command->info("   Project IDs assigned: " . implode(', ', $uniqueProjectIds));
    }
}
