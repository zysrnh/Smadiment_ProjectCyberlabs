@extends('admin.layouts.app')

@section('title', 'Edit User')

@section('content')

<!-- Top Bar -->
<div class="top-bar">
  <div class="page-title">
    <h2>Edit User</h2>
    <p class="page-subtitle">Update user information and project access</p>
  </div>
  <a href="{{ route('admin.users.index') }}" class="btn-back">
    <svg viewBox="0 0 24 24" style="width: 18px; height: 18px; stroke: currentColor; fill: none; stroke-width: 2;">
      <line x1="19" y1="12" x2="5" y2="12"/>
      <polyline points="12 19 5 12 12 5"/>
    </svg>
    Back to Users
  </a>
</div>

<!-- Form Section -->
<div class="section">
  <div class="section-header">
    <h3 class="section-title">User Information</h3>
  </div>
  <div class="section-body">
    
    <form method="POST" action="{{ route('admin.users.update', $user) }}" class="user-form" id="editUserForm">
      @csrf
      @method('PUT')
      
      <!-- Name Field -->
      <div class="form-group">
        <label for="name" class="form-label">Username</label>
        <input 
          type="text" 
          id="name" 
          name="name" 
          class="form-input @error('name') is-invalid @enderror" 
          value="{{ old('name', $user->name) }}" 
          required 
          autofocus
          placeholder="Enter username"
        >
        @error('name')
        <span class="error-message">{{ $message }}</span>
        @enderror
      </div>
      
      <!-- Email Field -->
      <div class="form-group">
        <label for="email" class="form-label">Email Address</label>
        <input 
          type="email" 
          id="email" 
          name="email" 
          class="form-input @error('email') is-invalid @enderror" 
          value="{{ old('email', $user->email) }}" 
          required
          placeholder="user@smadiment.com"
        >
        @error('email')
        <span class="error-message">{{ $message }}</span>
        @enderror
      </div>
      
      <!-- Reset Password Section -->
      <div class="form-group">
        <label class="checkbox-label" id="resetPasswordLabel">
          <input 
            type="checkbox" 
            name="reset_password" 
            id="reset_password"
            value="1" 
            {{ old('reset_password') ? 'checked' : '' }}
            onchange="togglePasswordPreview()"
          >
          <span class="checkbox-text">
            <strong>Reset Password</strong>
            <small>Generate a new password for this user based on their username</small>
          </span>
        </label>
        
        <!-- Password Preview (shown when checkbox is checked) -->
        <div id="passwordPreviewBox" style="display: none; margin-top: 12px;">
          <div class="password-preview-wrapper">
            <input 
              type="text" 
              id="passwordPreview" 
              class="form-input password-preview-input" 
              readonly
              placeholder="Password will be generated..."
            >
            <button type="button" class="btn-copy-password" onclick="copyGeneratedPassword()" id="copyPasswordBtn">
              <svg viewBox="0 0 24 24" style="width: 16px; height: 16px; stroke: currentColor; fill: none; stroke-width: 2;">
                <rect x="9" y="9" width="13" height="13" rx="2" ry="2"/>
                <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/>
              </svg>
              Copy
            </button>
          </div>
          <small class="form-hint password-hint">
            <svg viewBox="0 0 24 24" style="width: 14px; height: 14px; stroke: currentColor; fill: none; stroke-width: 2;">
              <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
              <line x1="12" y1="9" x2="12" y2="13"/>
              <line x1="12" y1="17" x2="12.01" y2="17"/>
            </svg>
            <strong>Important!</strong> This password will be shown to you after saving. Make sure to save it.
          </small>
        </div>
      </div>

      <!-- Subscription & Trial -->
      <div class="form-group">
        <label for="trial_ends_at" class="form-label">Trial Ends At</label>
        <input 
          type="date" 
          id="trial_ends_at" 
          name="trial_ends_at" 
          class="form-input @error('trial_ends_at') is-invalid @enderror" 
          value="{{ old('trial_ends_at', $user->trial_ends_at ? $user->trial_ends_at->format('Y-m-d') : '') }}"
        >
        <div class="quick-select-days mt-2">
          <button type="button" class="btn-quick-day" onclick="addMonths(1)">+1 Month</button>
          <button type="button" class="btn-quick-day" onclick="addMonths(2)">+2 Months</button>
          <button type="button" class="btn-quick-day" onclick="addMonths(3)">+3 Months</button>
        </div>
        @error('trial_ends_at')
        <span class="error-message">{{ $message }}</span>
        @enderror
        <small class="form-hint">Date when the user's trial access expires</small>
      </div>
      
      <!-- Project Assignment -->
      <div class="form-group">
        <label class="form-label">Assign Projects</label>
        <p class="form-description">Select which projects this user can access</p>
        
        @error('projects')
        <span class="error-message">{{ $message }}</span>
        @enderror
        
        <div class="projects-grid">
          @forelse($projects as $project)
          <label class="project-checkbox-card">
            <input 
              type="checkbox" 
              name="projects[]" 
              value="{{ $project['id'] }}"
              {{ in_array($project['id'], old('projects', $assignedProjectIds)) ? 'checked' : '' }}
            >
            <div class="project-checkbox-content">
              <div class="project-checkbox-header">
                <span class="project-checkbox-icon">
                  <svg viewBox="0 0 24 24" style="width: 18px; height: 18px; stroke: currentColor; fill: none; stroke-width: 2;">
                    <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/>
                  </svg>
                </span>
                <div>
                  <div class="project-checkbox-name">{{ $project['name'] ?? $project['project_name'] ?? $project['title'] ?? $project['label'] ?? 'Unknown Project' }}</div>
                  <div class="project-checkbox-id">ID: {{ $project['id'] }}</div>
                </div>
              </div>
              <div class="checkbox-indicator">
                <svg viewBox="0 0 24 24" style="width: 16px; height: 16px; stroke: currentColor; fill: none; stroke-width: 3;">
                  <polyline points="20 6 9 17 4 12"/>
                </svg>
              </div>
            </div>
          </label>
          @empty
          <p class="no-projects-message">No projects available</p>
          @endforelse
        </div>
      </div>
      
      <!-- Submit Button -->
      <div class="form-actions">
        <button type="submit" class="btn-submit">
          <svg viewBox="0 0 24 24" style="width: 18px; height: 18px; stroke: currentColor; fill: none; stroke-width: 2;">
            <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
            <polyline points="17 21 17 13 7 13 7 21"/>
            <polyline points="7 3 7 8 15 8"/>
          </svg>
          Update User
        </button>
        <a href="{{ route('admin.users.index') }}" class="btn-cancel">Cancel</a>
      </div>
      
    </form>
    
  </div>
</div>

@endsection

@section('styles')
<style>
.btn-back {
  padding: 12px 20px;
  background: var(--light-gray);
  color: var(--dark-blue);
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

.btn-back:hover {
  background: var(--dark-blue);
  color: var(--white);
}

.user-form {
  max-width: 800px;
}

.form-group {
  margin-bottom: 28px;
}

.form-group-row {
  display: flex;
  gap: 20px;
  margin-bottom: 28px;
}

.flex-1 {
  flex: 1;
}

.form-label {
  display: block;
  font-size: 14px;
  font-weight: 700;
  color: var(--dark-blue);
  margin-bottom: 8px;
}

.form-description {
  font-size: 13px;
  color: #6B7280;
  margin-bottom: 12px;
}

.form-input {
  width: 100%;
  padding: 12px 16px;
  border: 2px solid var(--light-gray);
  border-radius: 10px;
  font-family: 'Poppins', sans-serif;
  font-size: 14px;
  font-weight: 500;
  color: var(--dark-blue);
  transition: all 0.2s;
}

.quick-select-days {
  display: flex;
  gap: 8px;
  margin-top: 8px;
}

.btn-quick-day {
  padding: 6px 12px;
  background: #f1f5f9;
  border: 1.5px solid #e2e8f0;
  border-radius: 6px;
  font-size: 11px;
  font-weight: 700;
  color: #475569;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-quick-day:hover {
  background: var(--primary-green);
  color: #fff;
  border-color: var(--primary-green);
}

.form-input:focus {
  outline: none;
  border-color: var(--primary-green);
  box-shadow: 0 0 0 3px rgba(3, 128, 71, 0.1);
}

.form-input.is-invalid {
  border-color: #DC2626;
}

.error-message {
  display: block;
  color: #DC2626;
  font-size: 12px;
  font-weight: 600;
  margin-top: 6px;
}

.checkbox-label {
  display: flex;
  align-items: flex-start;
  gap: 12px;
  cursor: pointer;
  padding: 16px;
  background: #FFF3CD;
  border: 2px solid #FFE69C;
  border-radius: 10px;
  transition: all 0.2s;
}

.checkbox-label:hover {
  background: #FEF3C7;
  border-color: #FCD34D;
}

.checkbox-label input[type="checkbox"] {
  width: 20px;
  height: 20px;
  cursor: pointer;
  flex-shrink: 0;
  margin-top: 2px;
}

.checkbox-text {
  flex: 1;
}

.checkbox-text strong {
  display: block;
  font-size: 14px;
  font-weight: 700;
  color: #92400E;
  margin-bottom: 4px;
}

.checkbox-text small {
  display: block;
  font-size: 12px;
  color: #78350F;
}

/* Password Preview */
.password-preview-wrapper {
  display: flex;
  gap: 8px;
  align-items: stretch;
}

.password-preview-input {
  flex: 1;
  font-family: 'Courier New', monospace;
  font-weight: 600;
  letter-spacing: 1px;
  background: #FAFBFC !important;
  border-color: var(--primary-green) !important;
}

.form-hint {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 12px;
  color: #6B7280;
  margin-top: 6px;
}

.password-hint {
  color: #92400E;
  background: #FEF3C7;
  padding: 8px 12px;
  border-radius: 8px;
  border: 1px solid #FCD34D;
}

.btn-copy-password {
  padding: 12px 16px;
  background: #DBEAFE;
  color: #1E40AF;
  border: none;
  border-radius: 10px;
  font-family: 'Poppins', sans-serif;
  font-size: 13px;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.2s;
  display: flex;
  align-items: center;
  gap: 8px;
  white-space: nowrap;
}

.btn-copy-password:hover {
  background: #1E40AF;
  color: var(--white);
}

.projects-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 12px;
}

.project-checkbox-card {
  position: relative;
  display: block;
  cursor: pointer;
}

.project-checkbox-card input[type="checkbox"] {
  position: absolute;
  opacity: 0;
  width: 0;
  height: 0;
}

.project-checkbox-content {
  padding: 16px;
  border: 2px solid var(--light-gray);
  border-radius: 12px;
  background: var(--white);
  transition: all 0.2s;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.project-checkbox-card:hover .project-checkbox-content {
  border-color: var(--primary-green);
  background: #F0FDF4;
}

.project-checkbox-card input:checked ~ .project-checkbox-content {
  border-color: var(--primary-green);
  background: #DCFCE7;
}

.project-checkbox-header {
  display: flex;
  align-items: center;
  gap: 12px;
}

.project-checkbox-icon {
  width: 40px;
  height: 40px;
  background: var(--light-gray);
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: var(--primary-green);
  flex-shrink: 0;
}

.project-checkbox-name {
  font-size: 14px;
  font-weight: 700;
  color: var(--dark-blue);
  margin-bottom: 2px;
}

.project-checkbox-id {
  font-size: 11px;
  font-weight: 600;
  color: #6B7280;
}

.checkbox-indicator {
  width: 24px;
  height: 24px;
  border: 2px solid var(--light-gray);
  border-radius: 6px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: transparent;
  transition: all 0.2s;
  flex-shrink: 0;
}

.project-checkbox-card input:checked ~ .project-checkbox-content .checkbox-indicator {
  background: var(--primary-green);
  border-color: var(--primary-green);
  color: var(--white);
}

.no-projects-message {
  color: #9CA3AF;
  font-style: italic;
  text-align: center;
  padding: 40px;
}

.form-actions {
  display: flex;
  gap: 12px;
  margin-top: 32px;
  padding-top: 24px;
  border-top: 2px solid var(--light-gray);
}

.btn-submit {
  flex: 1;
  padding: 14px 24px;
  background: var(--primary-green);
  color: var(--white);
  border: none;
  border-radius: 10px;
  font-family: 'Poppins', sans-serif;
  font-size: 14px;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.2s;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
}

.btn-submit:hover {
  background: #025a34;
  transform: translateY(-2px);
}

.btn-cancel {
  padding: 14px 24px;
  background: var(--light-gray);
  color: var(--dark-blue);
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
  justify-content: center;
}

.btn-cancel:hover {
  background: var(--dark-blue);
  color: var(--white);
}
</style>
@endsection

@section('scripts')
<script>
/**
 * Toggle password preview box visibility
 */
function togglePasswordPreview() {
  const checkbox = document.getElementById('reset_password');
  const previewBox = document.getElementById('passwordPreviewBox');
  const nameInput = document.getElementById('name');
  
  if (checkbox.checked) {
    previewBox.style.display = 'block';
    generatePasswordPreview();
  } else {
    previewBox.style.display = 'none';
  }
}

/**
 * Generate password preview based on username
 */
function generatePasswordPreview() {
  const nameInput = document.getElementById('name');
  const passwordPreview = document.getElementById('passwordPreview');
  
  let username = nameInput.value.trim().toLowerCase();
  username = username.replace(/\s+/g, '');
  
  if (!username) {
    passwordPreview.value = '';
    return;
  }
  
  // Generate password sama seperti di create form
  let password = username
    .replace(/i/g, '1')
    .replace(/o/g, '0')
    .replace(/e/g, '3')
    .replace(/a/g, '4');
  
  // Random uppercase (30% chance per karakter)
  password = password.split('').map(char => {
    return Math.random() > 0.7 ? char.toUpperCase() : char;
  }).join('');
  
  const generatedPassword = `${password}_SMADIMENT`;
  passwordPreview.value = generatedPassword;
}

/**
 * Copy generated password to clipboard
 */
function copyGeneratedPassword() {
  const passwordPreview = document.getElementById('passwordPreview');
  
  if (!passwordPreview.value) {
    alert('No password to copy!');
    return;
  }
  
  // Copy to clipboard
  passwordPreview.select();
  document.execCommand('copy');
  
  // Visual feedback
  const copyBtn = document.getElementById('copyPasswordBtn');
  const originalHTML = copyBtn.innerHTML;
  
  copyBtn.innerHTML = `
    <svg viewBox="0 0 24 24" style="width: 16px; height: 16px; stroke: currentColor; fill: none; stroke-width: 2;">
      <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
      <polyline points="22 4 12 14.01 9 11.01"/>
    </svg>
    Copied!
  `;
  
  setTimeout(() => {
    copyBtn.innerHTML = originalHTML;
  }, 2000);
}

/**
 * Add months to the trial_ends_at date
 */
function addMonths(numMonths) {
  const trialEndsInput = document.getElementById('trial_ends_at');
  let currentDate;
  
  if (trialEndsInput.value) {
    currentDate = new Date(trialEndsInput.value);
    // If current date is past, start from today
    if (currentDate < new Date()) {
      currentDate = new Date();
    }
  } else {
    currentDate = new Date();
  }
  
  currentDate.setMonth(currentDate.getMonth() + numMonths);
  
  // Format to YYYY-MM-DD
  const year = currentDate.getFullYear();
  const month = String(currentDate.getMonth() + 1).padStart(2, '0');
  const day = String(currentDate.getDate()).padStart(2, '0');
  
  trialEndsInput.value = `${year}-${month}-${day}`;
}

// Auto-update password preview when username changes
document.getElementById('name').addEventListener('input', function() {
  const checkbox = document.getElementById('reset_password');
  if (checkbox.checked) {
    generatePasswordPreview();
  }
});

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
  togglePasswordPreview();
});
</script>
@endsection