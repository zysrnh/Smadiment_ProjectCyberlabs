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

        // 2. Fetch available projects from MediaKernels API or fallback
        $projectIds = [];

        try {
            /** @var MediaKernelsClient $client */
            $client = app(MediaKernelsClient::class);
            $rawProjects = $client->listProjects(0, 100);

            if (isset($rawProjects['data']) && is_array($rawProjects['data'])) {
                $projects = $rawProjects['data'];
            } elseif (isset($rawProjects['projects']) && is_array($rawProjects['projects'])) {
                $projects = $rawProjects['projects'];
            } elseif (is_array($rawProjects)) {
                $projects = $rawProjects;
            } else {
                $projects = [];
            }

            foreach ($projects as $key => $project) {
                if (is_array($project) && (isset($project['id']) || isset($project['project_id']))) {
                    $projectIds[] = (int) ($project['id'] ?? $project['project_id']);
                } elseif (is_numeric($key)) {
                    $projectIds[] = (int) $key;
                }
            }
        } catch (\Throwable $e) {
            Log::warning('UserSeeder: Failed to fetch projects from API, using fallback ID: ' . $e->getMessage());
        }

        // Fallback default project ID jika API kosong / tidak reachable
        if (empty($projectIds)) {
            $projectIds = [16978];
        }

        $uniqueProjectIds = array_unique($projectIds);

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
