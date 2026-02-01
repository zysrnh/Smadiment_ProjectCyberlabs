@extends('admin.layouts.app')

@section('title', 'User Management')

@section('content')

<!-- Top Bar -->
<div class="top-bar">
  <div class="page-title">
    <h2>User Management</h2>
    <p class="page-subtitle">Manage user accounts and project access</p>
  </div>
  <a href="{{ route('admin.users.create') }}" class="btn-add-user">
    <svg viewBox="0 0 24 24" style="width: 18px; height: 18px; stroke: currentColor; fill: none; stroke-width: 2;">
      <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
      <circle cx="9" cy="7" r="4"/>
      <line x1="19" y1="8" x2="19" y2="14"/>
      <line x1="22" y1="11" x2="16" y2="11"/>
    </svg>
    Add New User
  </a>
</div>

<!-- Success Messages -->
@if(session('success'))
<div class="alert alert-success">
  <svg viewBox="0 0 24 24" style="width: 20px; height: 20px; stroke: currentColor; fill: none; stroke-width: 2;">
    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
    <polyline points="22 4 12 14.01 9 11.01"/>
  </svg>
  <div>
    <strong>{{ session('success') }}</strong>
    
    @if(session('generated_password'))
    <div style="margin-top: 12px; padding: 12px; background: #FFF3CD; border: 1px solid #FFE69C; border-radius: 8px;">
      <strong>⚠️ Important - Save this password now!</strong><br>
      <strong>Email:</strong> {{ session('user_email') }}<br>
      <strong>Password:</strong> <code style="font-size: 14px; background: #fff; padding: 4px 8px; border-radius: 4px; user-select: all;">{{ session('generated_password') }}</code>
      <br><small style="color: #856404;">This password will not be shown again.</small>
    </div>
    @endif
  </div>
</div>
@endif

<!-- Users Section -->
<div class="section">
  <div class="section-header">
    <h3 class="section-title">All Users</h3>
    <span class="item-count">{{ $users->total() }}</span>
  </div>
  <div class="section-body">
    
    @if($users->count() > 0)
    <div class="users-table-wrapper">
      <table class="users-table">
        <thead>
          <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Assigned Projects</th>
            <th>Created</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @foreach($users as $user)
          <tr>
            <td>
              <div class="user-name-cell">
                <div class="user-avatar-small">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
                <strong>{{ $user->name }}</strong>
              </div>
            </td>
            <td>{{ $user->email }}</td>
            <td>
              <div class="project-badges">
                @forelse($user->projectAssignments as $assignment)
                  <span class="project-badge-small">ID: {{ $assignment->project_id }}</span>
                @empty
                  <span class="no-projects">No projects</span>
                @endforelse
              </div>
            </td>
            <td>{{ $user->created_at->format('d M Y') }}</td>
            <td>
              <div class="action-buttons">
                <a href="{{ route('admin.users.edit', $user) }}" class="btn-action btn-edit" title="Edit">
                  <svg viewBox="0 0 24 24" style="width: 16px; height: 16px; stroke: currentColor; fill: none; stroke-width: 2;">
                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                  </svg>
                </a>
                <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="delete-form" onsubmit="return confirm('Are you sure you want to delete this user?');">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn-action btn-delete" title="Delete">
                    <svg viewBox="0 0 24 24" style="width: 16px; height: 16px; stroke: currentColor; fill: none; stroke-width: 2;">
                      <polyline points="3 6 5 6 21 6"/>
                      <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                    </svg>
                  </button>
                </form>
              </div>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    
    <!-- Pagination -->
    <div class="pagination-wrapper">
      {{ $users->links() }}
    </div>
    @else
    <!-- Empty State -->
    <div class="empty-state">
      <svg viewBox="0 0 24 24" style="width: 72px; height: 72px; stroke: var(--dark-blue); fill: none; stroke-width: 2; opacity: 0.2;">
        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
        <circle cx="9" cy="7" r="4"/>
        <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
      </svg>
      <h3 class="empty-title">No Users Found</h3>
      <p class="empty-text">Click "Add New User" to create your first user account.</p>
    </div>
    @endif
    
  </div>
</div>

@endsection

@section('styles')
<style>
.btn-add-user {
  padding: 12px 24px;
  background: var(--primary-green);
  color: var(--white);
  border: none;
  border-radius: 10px;
  font-family: 'Poppins', sans-serif;
  font-size: 14px;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.2s;
  text-decoration: none;
  display: flex;
  align-items: center;
  gap: 8px;
}

.btn-add-user:hover {
  background: #025a34;
  transform: translateY(-2px);
  color: var(--white);
}

.alert {
  padding: 16px 20px;
  border-radius: 12px;
  margin-bottom: 24px;
  display: flex;
  align-items: flex-start;
  gap: 12px;
  font-size: 14px;
}

.alert-success {
  background: #D1FAE5;
  border: 2px solid #10B981;
  color: #065F46;
}

.alert strong {
  font-weight: 700;
}

.users-table-wrapper {
  overflow-x: auto;
}

.users-table {
  width: 100%;
  border-collapse: collapse;
}

.users-table thead {
  background: var(--light-gray);
}

.users-table th {
  padding: 14px 16px;
  text-align: left;
  font-size: 12px;
  font-weight: 800;
  color: var(--dark-blue);
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.users-table td {
  padding: 16px;
  border-bottom: 1px solid var(--light-gray);
  font-size: 14px;
  color: var(--text-dark);
}

.user-name-cell {
  display: flex;
  align-items: center;
  gap: 12px;
}

.user-avatar-small {
  width: 36px;
  height: 36px;
  background: var(--primary-green);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 14px;
  font-weight: 800;
  color: var(--white);
}

.project-badges {
  display: flex;
  flex-wrap: wrap;
  gap: 6px;
}

.project-badge-small {
  padding: 4px 10px;
  background: var(--primary-green);
  color: var(--white);
  border-radius: 6px;
  font-size: 11px;
  font-weight: 700;
}

.no-projects {
  color: #9CA3AF;
  font-style: italic;
  font-size: 13px;
}

.action-buttons {
  display: flex;
  gap: 8px;
}

.btn-action {
  width: 36px;
  height: 36px;
  display: flex;
  align-items: center;
  justify-content: center;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.2s;
  text-decoration: none;
}

.btn-edit {
  background: #DBEAFE;
  color: #1E40AF;
}

.btn-edit:hover {
  background: #1E40AF;
  color: white;
}

.btn-delete {
  background: #FEE2E2;
  color: #DC2626;
}

.btn-delete:hover {
  background: #DC2626;
  color: white;
}

.delete-form {
  display: inline;
  margin: 0;
}

.pagination-wrapper {
  margin-top: 24px;
  display: flex;
  justify-content: center;
}
</style>
@endsection