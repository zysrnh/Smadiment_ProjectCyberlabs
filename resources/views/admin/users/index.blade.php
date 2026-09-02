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
    <svg viewBox="0 0 24 24" style="width: 16px; height: 16px; stroke: currentColor; fill: none; stroke-width: 2.5;">
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
  <div class="alert-icon">
    <svg viewBox="0 0 24 24" style="width: 18px; height: 18px; stroke: currentColor; fill: none; stroke-width: 2.5;">
      <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
      <polyline points="22 4 12 14.01 9 11.01"/>
    </svg>
  </div>
  <div class="alert-body">
    <strong>{{ session('success') }}</strong>
    @if(session('generated_password'))
    <div class="password-box">
      <p class="password-label">Important — Save this password now. It will not be shown again.</p>
      <div class="password-row">
        <span><strong>Email:</strong> {{ session('user_email') }}</span>
        <span><strong>Password:</strong> <code>{{ session('generated_password') }}</code></span>
      </div>
    </div>
    @endif
  </div>
</div>
@endif

<!-- Users Section -->
<div class="section">
  <div class="section-header">
    <h3 class="section-title">All Users</h3>
    <span class="item-count">{{ $users->total() }} users</span>
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
            <th>Trial Status</th>
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
                <span class="user-name">{{ $user->name }}</span>
              </div>
            </td>
            <td class="email-cell">{{ $user->email }}</td>
            <td>
              <div class="project-badges">
                @forelse($user->projectAssignments as $assignment)
                  <span class="project-badge-small">ID: {{ $assignment->project_id }}</span>
                @empty
                  <span class="no-projects">No projects</span>
                @endforelse
              </div>
            </td>
            <td>
              <div class="trial-status">
                @if($user->trial_ends_at)
                  @php $remaining = $user->trialRemainingDays(); @endphp
                  @if($remaining > 0)
                    <span class="trial-badge {{ $remaining <= 3 ? 'trial-warning' : 'trial-active' }}">
                      {{ $remaining }} Days Left
                    </span>
                  @else
                    <span class="trial-badge trial-expired">Expired</span>
                  @endif
                @else
                  <span class="trial-badge trial-permanent">Permanent</span>
                @endif
              </div>
            </td>
            <td class="date-cell">{{ $user->created_at->format('d M Y') }}</td>
            <td>
              <div class="action-buttons">
                <a href="{{ route('admin.users.edit', $user) }}" class="btn-action btn-edit" title="Edit User">
                  <svg viewBox="0 0 24 24" style="width: 15px; height: 15px; stroke: currentColor; fill: none; stroke-width: 2.5;">
                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                  </svg>
                  Edit
                </a>
                <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="delete-form" onsubmit="return confirm('Are you sure you want to delete this user?');">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn-action btn-delete" title="Delete User">
                    <svg viewBox="0 0 24 24" style="width: 15px; height: 15px; stroke: currentColor; fill: none; stroke-width: 2.5;">
                      <polyline points="3 6 5 6 21 6"/>
                      <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                    </svg>
                    Delete
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
      <div class="empty-icon">
        <svg viewBox="0 0 24 24" style="width: 48px; height: 48px; stroke: currentColor; fill: none; stroke-width: 1.5;">
          <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
          <circle cx="9" cy="7" r="4"/>
          <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
          <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
        </svg>
      </div>
      <h3 class="empty-title">No Users Found</h3>
      <p class="empty-text">Click "Add New User" to create your first user account.</p>
    </div>
    @endif

  </div>
</div>

@endsection

@section('styles')
<style>
/* ─── Layout ──────────────────────────────────────────────── */
.top-bar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 28px;
  flex-wrap: wrap;
  gap: 16px;
}

.page-title h2 {
  font-size: 22px;
  font-weight: 800;
  color: var(--dark-blue);
  margin: 0 0 4px;
  letter-spacing: -0.3px;
}

.page-subtitle {
  font-size: 13px;
  color: #6B7280;
  margin: 0;
}

/* ─── Add User Button ─────────────────────────────────────── */
.btn-add-user {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 10px 20px;
  background: var(--primary-green);
  color: #fff;
  border-radius: 8px;
  font-size: 13px;
  font-weight: 700;
  text-decoration: none;
  transition: background 0.18s, transform 0.18s, box-shadow 0.18s;
  box-shadow: 0 2px 8px rgba(0,0,0,0.12);
  white-space: nowrap;
}

.btn-add-user:hover {
  background: #025a34;
  transform: translateY(-1px);
  box-shadow: 0 4px 14px rgba(0,0,0,0.16);
  color: #fff;
}

/* ─── Alert ───────────────────────────────────────────────── */
.alert {
  display: flex;
  align-items: flex-start;
  gap: 14px;
  padding: 16px 20px;
  border-radius: 10px;
  margin-bottom: 24px;
  font-size: 14px;
}

.alert-success {
  background: #ECFDF5;
  border: 1.5px solid #6EE7B7;
  color: #065F46;
}

.alert-icon {
  flex-shrink: 0;
  width: 32px;
  height: 32px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #D1FAE5;
  border-radius: 50%;
  color: #059669;
  margin-top: 1px;
}

.alert-body {
  flex: 1;
  line-height: 1.5;
}

.password-box {
  margin-top: 12px;
  padding: 12px 16px;
  background: #FFFBEB;
  border: 1.5px solid #FCD34D;
  border-radius: 8px;
}

.password-label {
  font-size: 12px;
  font-weight: 700;
  color: #92400E;
  margin: 0 0 8px;
  text-transform: uppercase;
  letter-spacing: 0.4px;
}

.password-row {
  display: flex;
  flex-wrap: wrap;
  gap: 12px;
  font-size: 13px;
  color: #78350F;
}

.password-row code {
  font-size: 13px;
  background: #fff;
  padding: 3px 8px;
  border-radius: 5px;
  border: 1px solid #FCD34D;
  user-select: all;
  letter-spacing: 0.5px;
}

/* ─── Section ─────────────────────────────────────────────── */
.section {
  background: #fff;
  border-radius: 12px;
  border: 1.5px solid #E5E7EB;
  overflow: hidden;
}

.section-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 18px 24px;
  border-bottom: 1.5px solid #F3F4F6;
  background: #FAFAFA;
}

.section-title {
  font-size: 15px;
  font-weight: 800;
  color: var(--dark-blue);
  margin: 0;
  letter-spacing: -0.2px;
}

.item-count {
  display: inline-block;
  padding: 3px 12px;
  background: #F3F4F6;
  color: #374151;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 700;
}

.section-body {
  padding: 0;
}

/* ─── Table ───────────────────────────────────────────────── */
.users-table-wrapper {
  overflow-x: auto;
}

.users-table {
  width: 100%;
  border-collapse: collapse;
}

.users-table thead {
  background: #F9FAFB;
}

.users-table th {
  padding: 12px 20px;
  text-align: left;
  font-size: 11px;
  font-weight: 800;
  color: #6B7280;
  text-transform: uppercase;
  letter-spacing: 0.6px;
  border-bottom: 1.5px solid #E5E7EB;
  white-space: nowrap;
}

.users-table tbody tr {
  transition: background 0.15s;
}

.users-table tbody tr:hover {
  background: #F9FAFB;
}

.users-table td {
  padding: 14px 20px;
  border-bottom: 1px solid #F3F4F6;
  font-size: 13.5px;
  color: #374151;
  vertical-align: middle;
}

.users-table tbody tr:last-child td {
  border-bottom: none;
}

/* ─── User Cell ───────────────────────────────────────────── */
.user-name-cell {
  display: flex;
  align-items: center;
  gap: 10px;
}

.user-avatar-small {
  flex-shrink: 0;
  width: 34px;
  height: 34px;
  background: var(--primary-green);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 13px;
  font-weight: 800;
  color: #fff;
}

.user-name {
  font-weight: 700;
  color: #111827;
}

.email-cell {
  color: #6B7280;
  font-size: 13px;
}

.date-cell {
  color: #9CA3AF;
  font-size: 12.5px;
  white-space: nowrap;
}

/* ─── Project Badges ──────────────────────────────────────── */
.project-badges {
  display: flex;
  flex-wrap: wrap;
  gap: 5px;
}

.project-badge-small {
  display: inline-block;
  padding: 3px 10px;
  background: #ECFDF5;
  color: #065F46;
  border: 1px solid #6EE7B7;
  border-radius: 5px;
  font-size: 11px;
  font-weight: 700;
  letter-spacing: 0.3px;
}

.no-projects {
  color: #D1D5DB;
  font-size: 12.5px;
  font-style: italic;
}

/* ─── Trial Badges ────────────────────────────────────────── */
.trial-badge {
  display: inline-block;
  padding: 4px 10px;
  border-radius: 6px;
  font-size: 11px;
  font-weight: 800;
  letter-spacing: 0.3px;
  text-transform: uppercase;
}

.trial-active {
  background: #ECFDF5;
  color: #059669;
  border: 1px solid #6EE7B7;
}

.trial-warning {
  background: #FFFBEB;
  color: #D97706;
  border: 1px solid #FCD34D;
}

.trial-expired {
  background: #FEF2F2;
  color: #DC2626;
  border: 1px solid #FECACA;
}

.trial-permanent {
  background: #F3F4F6;
  color: #4B5563;
  border: 1px solid #D1D5DB;
}

/* ─── Action Buttons ──────────────────────────────────────── */
.action-buttons {
  display: flex;
  gap: 8px;
  align-items: center;
}

.btn-action {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 7px 14px;
  border-radius: 7px;
  font-size: 12px;
  font-weight: 700;
  cursor: pointer;
  transition: background 0.15s, color 0.15s, transform 0.15s, box-shadow 0.15s;
  text-decoration: none;
  border: none;
  letter-spacing: 0.2px;
  white-space: nowrap;
}

.btn-edit {
  background: #EFF6FF;
  color: #1D4ED8;
  border: 1.5px solid #BFDBFE;
}

.btn-edit:hover {
  background: #1D4ED8;
  color: #fff;
  border-color: #1D4ED8;
  transform: translateY(-1px);
  box-shadow: 0 3px 10px rgba(29,78,216,0.25);
}

.btn-delete {
  background: #FEF2F2;
  color: #DC2626;
  border: 1.5px solid #FECACA;
}

.btn-delete:hover {
  background: #DC2626;
  color: #fff;
  border-color: #DC2626;
  transform: translateY(-1px);
  box-shadow: 0 3px 10px rgba(220,38,38,0.25);
}

.delete-form {
  display: inline-flex;
  margin: 0;
}

/* ─── Pagination ──────────────────────────────────────────── */
.pagination-wrapper {
  padding: 16px 24px;
  display: flex;
  justify-content: flex-end;
  border-top: 1.5px solid #F3F4F6;
  background: #FAFAFA;
}

/* ─── Empty State ─────────────────────────────────────────── */
.empty-state {
  padding: 64px 24px;
  text-align: center;
}

.empty-icon {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 80px;
  height: 80px;
  background: #F3F4F6;
  border-radius: 50%;
  color: #9CA3AF;
  margin-bottom: 20px;
}

.empty-title {
  font-size: 17px;
  font-weight: 800;
  color: var(--dark-blue);
  margin: 0 0 8px;
}

.empty-text {
  font-size: 14px;
  color: #9CA3AF;
  margin: 0;
}
</style>
@endsection