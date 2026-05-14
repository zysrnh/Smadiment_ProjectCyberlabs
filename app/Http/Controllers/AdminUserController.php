<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserProject;
use App\Services\MediaKernelsClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminUserController extends Controller
{
    /**
     * Display list of all users
     */
    public function index()
    {
        $users = User::with('projectAssignments')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.users.index', compact('users'));
    }

    /**
     * Show form to create new user
     */
    public function create(MediaKernelsClient $mk)
    {
        // Fetch all available projects from Drone Emprit
        $rawProjects = $mk->listProjects(0, 100);
        $projects = array_values($rawProjects);

        return view('admin.users.create', compact('projects'));
    }

    /**
     * Store new user
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|alpha_dash', // username format
            'email' => 'required|email|unique:users,email|ends_with:@smadiment.com',
            'password' => 'required|string|min:6',
            'projects' => 'required|array|min:1',
            'projects.*' => 'required|integer',
            'trial_days' => 'nullable|integer|min:0',
            'notice_days' => 'nullable|integer|in:1,7,30',
        ], [
            'email.ends_with' => 'Email must be in format: username@smadiment.com',
            'name.alpha_dash' => 'Username can only contain letters, numbers, dashes and underscores',
        ]);

        // Pakai password dari form (yang udah di-generate di frontend)
        $generatedPassword = $validated['password'];

        $trialEndsAt = null;
        if ($request->filled('trial_days')) {
            $trialEndsAt = now()->addDays($request->integer('trial_days'));
        }

        // Create user
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($generatedPassword),
            'trial_ends_at' => $trialEndsAt,
            'subscription_notice_at' => ($trialEndsAt && $request->filled('notice_days')) 
                ? $trialEndsAt->copy()->subDays($request->integer('notice_days')) 
                : null,
        ]);

        // Assign projects
        foreach ($validated['projects'] as $projectId) {
            UserProject::create([
                'user_id' => $user->id,
                'project_id' => $projectId,
            ]);
        }

        // Flash success message with password (shown only once)
        return redirect()
            ->route('admin.users.index')
            ->with('success', "User created successfully!")
            ->with('generated_password', $generatedPassword)
            ->with('user_email', $validated['email']);
    }

    /**
     * Show edit form
     */
    public function edit(User $user, MediaKernelsClient $mk)
    {
        // Fetch all available projects
        $rawProjects = $mk->listProjects(0, 100);
        $projects = array_values($rawProjects);

        // Get currently assigned project IDs
        $assignedProjectIds = $user->assignedProjectIds();

        return view('admin.users.edit', compact('user', 'projects', 'assignedProjectIds'));
    }

    /**
     * Update user
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|alpha_dash',
            'email' => 'required|email|unique:users,email,' . $user->id . '|ends_with:@smadiment.com',
            'projects' => 'required|array|min:1',
            'projects.*' => 'required|integer',
            'reset_password' => 'nullable|boolean',
            'trial_ends_at' => 'nullable|date',
            'notice_days' => 'nullable|integer|in:1,7,30',
        ], [
            'email.ends_with' => 'Email must be in format: username@smadiment.com',
            'name.alpha_dash' => 'Username can only contain letters, numbers, dashes and underscores',
        ]);

        // Update user info
        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'trial_ends_at' => $request->input('trial_ends_at'),
            'subscription_notice_at' => ($request->filled('trial_ends_at') && $request->filled('notice_days')) 
                ? \Carbon\Carbon::parse($request->input('trial_ends_at'))->subDays($request->integer('notice_days')) 
                : null,
        ]);

        // Reset password if requested - generate new password automatically
        $newPassword = null;
        if ($request->boolean('reset_password')) {
            // Generate password sama seperti saat create user
            $username = strtolower(str_replace(' ', '', $validated['name']));
            
            $password = $username;
            $password = str_replace(['i', 'o', 'e', 'a'], ['1', '0', '3', '4'], $password);
            
            // Random uppercase (30% per karakter)
            $password = implode('', array_map(function($char) {
                return rand(1, 10) > 7 ? strtoupper($char) : $char;
            }, str_split($password)));
            
            $newPassword = $password . '_SMADIMENT';
            
            $user->update([
                'password' => Hash::make($newPassword),
            ]);
        }

        // Update project assignments
        // Delete old assignments
        UserProject::where('user_id', $user->id)->delete();

        // Create new assignments
        foreach ($validated['projects'] as $projectId) {
            UserProject::create([
                'user_id' => $user->id,
                'project_id' => $projectId,
            ]);
        }

        $message = "User updated successfully!";
        $redirect = redirect()->route('admin.users.index')->with('success', $message);

        if ($newPassword) {
            $redirect->with('generated_password', $newPassword)
                     ->with('user_email', $user->email);
        }

        return $redirect;
    }

    /**
     * Delete user
     */
    public function destroy(User $user)
    {
        $userName = $user->name;
        $user->delete(); // Cascade will delete user_projects

        return redirect()
            ->route('admin.users.index')
            ->with('success', "User '{$userName}' has been deleted.");
    }
}