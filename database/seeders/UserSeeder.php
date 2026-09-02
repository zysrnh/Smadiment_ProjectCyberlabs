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
        // 1. Create or update test user
        $user = User::updateOrCreate(
            ['email' => 'user@smadiment.com'],
            [
                'name' => 'User SMADIMENT',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
                'trial_ends_at' => now()->addDays(30),
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

        // 3. Assign projects to user
        foreach (array_unique($projectIds) as $projectId) {
            UserProject::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'project_id' => $projectId,
                ]
            );
        }

        $this->command->info("User [{$user->email}] successfully seeded with project IDs: " . implode(', ', array_unique($projectIds)));
    }
}
