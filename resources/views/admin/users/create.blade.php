@extends('admin.layouts.app')

@section('title', 'Add New User')

@section('content')

<!-- Top Bar -->
<div class="top-bar">
  <div class="page-title">
    <h2>Add New User</h2>
    <p class="page-subtitle">Create a new user account and assign projects</p>
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
    
    <form method="POST" action="{{ route('admin.users.store') }}" class="user-form">
      @csrf
      
      <!-- Name Field -->
      <div class="form-group">
        <label for="name" class="form-label">Full Name</label>
        <input 
          type="text" 
          id="name" 
          name="name" 
          class="form-input @error('name') is-invalid @enderror" 
          value="{{ old('name') }}" 
          required 
          autofocus
          placeholder="Enter user's full name"
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
          value="{{ old('email') }}" 
          required
          placeholder="user@example.com"
        >
        @error('email')
        <span class="error-message">{{ $message }}</span>
        @enderror
      </div>
      
      <!-- Password Field with Generate Button -->
      <div class="form-group">
        <label for="password" class="form-label">Password</label>
        <div class="password-input-wrapper">
          <input 
            type="text" 
            id="password" 
            name="password" 
            class="form-input password-input @error('password') is-invalid @enderror" 
            value="{{ old('password') }}" 
            required
            placeholder="Click 'Generate Password' button"
            readonly
          >
          <button type="button" class="btn-generate-password" onclick="generatePassword()">
            <svg viewBox="0 0 24 24" style="width: 16px; height: 16px; stroke: currentColor; fill: none; stroke-width: 2;">
              <path d="M21 2l-2 2m-7.61 7.61a5.5 5.5 0 1 1-7.778 7.778 5.5 5.5 0 0 1 7.777-7.777zm0 0L15.5 7.5m0 0l3 3L22 7l-3-3m-3.5 3.5L19 4"/>
            </svg>
            Generate Password
          </button>
          <button type="button" class="btn-copy-password" onclick="copyPassword()" id="copyBtn" style="display: none;">
            <svg viewBox="0 0 24 24" style="width: 16px; height: 16px; stroke: currentColor; fill: none; stroke-width: 2;">
              <rect x="9" y="9" width="13" height="13" rx="2" ry="2"/>
              <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/>
            </svg>
            Copy
          </button>
        </div>
        @error('password')
        <span class="error-message">{{ $message }}</span>
        @enderror
        <small class="form-hint password-hint" id="passwordHint" style="display: none;">
          <svg viewBox="0 0 24 24" style="width: 14px; height: 14px; stroke: currentColor; fill: none; stroke-width: 2;">
            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
            <polyline points="22 4 12 14.01 9 11.01"/>
          </svg>
          <strong>Password generated!</strong> Make sure to save this password - it will be shown to the user only once after creation.
        </small>
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
              {{ in_array($project['id'], old('projects', [])) ? 'checked' : '' }}
            >
            <div class="project-checkbox-content">
              <div class="project-checkbox-header">
                <span class="project-checkbox-icon">
                  <svg viewBox="0 0 24 24" style="width: 18px; height: 18px; stroke: currentColor; fill: none; stroke-width: 2;">
                    <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/>
                  </svg>
                </span>
                <div>
                  <div class="project-checkbox-name">{{ $project['name'] ?? $project['title'] ?? 'Unnamed Project' }}</div>
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
          Create User
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

.form-input:focus {
  outline: none;
  border-color: var(--primary-green);
  box-shadow: 0 0 0 3px rgba(3, 128, 71, 0.1);
}

.form-input.is-invalid {
  border-color: #DC2626;
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
  color: #065F46;
  background: #D1FAE5;
  padding: 8px 12px;
  border-radius: 8px;
  border: 1px solid #10B981;
}

.error-message {
  display: block;
  color: #DC2626;
  font-size: 12px;
  font-weight: 600;
  margin-top: 6px;
}

/* Password Input Wrapper */
.password-input-wrapper {
  display: flex;
  gap: 8px;
  align-items: stretch;
}

.password-input {
  flex: 1;
  font-family: 'Courier New', monospace;
  font-weight: 600;
  letter-spacing: 1px;
  background: #FAFBFC;
}

.btn-generate-password {
  padding: 12px 20px;
  background: var(--primary-green);
  color: var(--white);
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

.btn-generate-password:hover {
  background: #025a34;
  transform: translateY(-2px);
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
 * Generate Password from Email
 * Format: username_SMADIMENT_randomstring
 * Example: john_SMADIMENT_x9K2pL4m
 */
function generatePassword() {
  const emailInput = document.getElementById('email');
  const passwordInput = document.getElementById('password');
  const copyBtn = document.getElementById('copyBtn');
  const passwordHint = document.getElementById('passwordHint');
  
  const email = emailInput.value.trim();
  
  if (!email) {
    alert('Please enter an email address first!');
    emailInput.focus();
    return;
  }
  
  // Extract username from email (before @)
  let username = email.split('@')[0].toLowerCase();
  
  // Ganti huruf jadi angka: i->1, o->0, e->3, a->4
  let password = username
    .replace(/i/g, '1')
    .replace(/o/g, '0')
    .replace(/e/g, '3')
    .replace(/a/g, '4');
  
  // Random uppercase beberapa huruf (30% chance per karakter)
  password = password.split('').map(char => {
    return Math.random() > 0.7 ? char.toUpperCase() : char;
  }).join('');
  
  // Tambahkan _SMADIMENT di belakang
  const generatedPassword = `${password}_SMADIMENT`;
  
  // Set password value
  passwordInput.value = generatedPassword;
  passwordInput.removeAttribute('readonly');
  
  // Show copy button and hint
  copyBtn.style.display = 'flex';
  passwordHint.style.display = 'flex';
  
  // Highlight the password field
  passwordInput.select();
}
/**
 * Copy Password to Clipboard
 */
function copyPassword() {
  const passwordInput = document.getElementById('password');
  
  if (!passwordInput.value) {
    alert('No password to copy!');
    return;
  }
  
  // Copy to clipboard
  passwordInput.select();
  document.execCommand('copy');
  
  // Visual feedback
  const copyBtn = document.getElementById('copyBtn');
  const originalText = copyBtn.innerHTML;
  
  copyBtn.innerHTML = `
    <svg viewBox="0 0 24 24" style="width: 16px; height: 16px; stroke: currentColor; fill: none; stroke-width: 2;">
      <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
      <polyline points="22 4 12 14.01 9 11.01"/>
    </svg>
    Copied!
  `;
  
  setTimeout(() => {
    copyBtn.innerHTML = originalText;
  }, 2000);
}

// Auto-generate password when email changes (optional)
document.getElementById('email').addEventListener('blur', function() {
  const passwordInput = document.getElementById('password');
  if (this.value && !passwordInput.value) {
    // Uncomment line below if you want auto-generate on email blur
    // generatePassword();
  }
});
</script>
@endsection