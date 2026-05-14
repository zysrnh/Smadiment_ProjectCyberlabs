@extends('mk.layouts.app')

@section('title', 'My Profile | SMADIMENT')
@section('page-title', 'User Profile')

@section('content')
<div class="row">
    <!-- Profile Info -->
    <div class="col-lg-4 col-xl-3 mb-4">
        <div class="card h-100 shadow-sm border-0">
            <div class="card-body text-center pt-5">
                <form action="{{ route('mk.profile.avatar') }}" method="POST" enctype="multipart/form-data" id="avatarForm">
                    @csrf
                    <div class="position-relative d-inline-flex align-items-center justify-content-center mb-3 profile-avatar-container"
                         style="width: 130px; height: 130px; border-radius: 50%; background: var(--primary-green, #038047); color: #fff; font-size: 48px; font-weight: 800; border: 5px solid #f8fafc; box-shadow: 0 8px 16px rgba(0,0,0,0.08); overflow: hidden; cursor: pointer;"
                         onclick="document.getElementById('avatarInput').click()">
                        
                        @if(auth()->user()->avatar)
                            <img src="{{ asset(ltrim(auth()->user()->avatar, '/')) }}" 
                                 alt="Avatar" 
                                 style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;"
                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            <span style="display:none; width:100%; height:100%; align-items:center; justify-content:center;">
                                {{ strtoupper(substr(auth()->user()->name ?? 'Admin', 0, 2)) }}
                            </span>
                        @else
                            {{ strtoupper(substr(auth()->user()->name ?? 'Admin', 0, 2)) }}
                        @endif

                        <div class="avatar-upload-overlay">
                            <i class="ph ph-camera"></i>
                            <span>Change</span>
                        </div>
                    </div>
                    <input type="file" name="avatar" id="avatarInput" style="display: none;" accept="image/*" onchange="document.getElementById('avatarForm').submit()">
                </form>
                
                @if(session('success'))
                    <div class="alert alert-success py-2 px-3 mt-2" style="font-size: 13px; border-radius: 8px;">
                        {{ session('success') }}
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger py-2 px-3 mt-2" style="font-size: 13px; border-radius: 8px;">
                        {{ session('error') }}
                    </div>
                @endif
                @error('avatar')
                    <div class="alert alert-danger py-2 px-3 mt-2" style="font-size: 13px; border-radius: 8px;">
                        {{ $message }}
                    </div>
                @enderror
                
                <h4 class="mb-1 fw-bold text-dark">{{ auth()->user()->name ?? 'Administrator' }}</h4>
                <p class="text-muted mb-3">{{ auth()->user()->email ?? 'admin@smadiment.com' }}</p>
            </div>
            
            <div class="card-body border-top p-4" style="background: #fdfdfd; border-radius: 0 0 16px 16px; border-top: 1px solid #f1f5f9 !important;">
                <div class="d-flex align-items-center mb-4 stat-box p-2 rounded-3">
                    <div style="width: 44px; height: 44px; border-radius: 12px; background: #f1f5f9; display: flex; align-items: center; justify-content: center; color: #475569; margin-right: 16px; flex-shrink: 0; border: 1px solid #e2e8f0;">
                        <i class="ph ph-calendar-blank fs-4"></i>
                    </div>
                    <div>
                        <h6 class="mb-1 fw-bold text-dark" style="font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px; color: #64748b !important;">Member Since</h6>
                        <span class="text-dark fw-bold" style="font-size: 14px;">{{ auth()->user()->created_at ? auth()->user()->created_at->format('d F Y') : 'Unknown' }}</span>
                    </div>
                </div>
                
                <div class="d-flex align-items-center mb-4 stat-box p-2 rounded-3">
                    <div style="width: 44px; height: 44px; border-radius: 12px; background: #f1f5f9; display: flex; align-items: center; justify-content: center; color: #475569; margin-right: 16px; flex-shrink: 0; border: 1px solid #e2e8f0;">
                        <i class="ph ph-folders fs-4"></i>
                    </div>
                    <div>
                        <h6 class="mb-1 fw-bold text-dark" style="font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px; color: #64748b !important;">Total Projects</h6>
                        <span class="text-dark fw-bold" style="font-size: 14px;">{{ count($projects) }} Assigned</span>
                    </div>
                </div>

                <div class="d-flex align-items-center stat-box p-2 rounded-3">
                    <div style="width: 44px; height: 44px; border-radius: 12px; background: {{ auth()->user()->trialRemainingDays() > 3 ? '#ecfdf5' : '#fff1f2' }}; display: flex; align-items: center; justify-content: center; color: {{ auth()->user()->trialRemainingDays() > 3 ? '#059669' : '#e11d48' }}; margin-right: 16px; flex-shrink: 0; border: 1px solid {{ auth()->user()->trialRemainingDays() > 3 ? '#a7f3d0' : '#fecaca' }};">
                        <i class="ph ph-shield-check fs-4"></i>
                    </div>
                    <div>
                        <h6 class="mb-1 fw-bold text-dark" style="font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px; color: #64748b !important;">Subscription Status</h6>
                        @if(auth()->user()->trial_ends_at)
                            <span class="{{ auth()->user()->trialRemainingDays() > 3 ? 'text-success' : 'text-danger' }} fw-bold" style="font-size: 14px;">
                                {{ auth()->user()->trialRemainingDays() }} Days Remaining
                            </span>
                            <div class="text-muted" style="font-size: 12px; font-weight: 500;">Ends on {{ auth()->user()->trial_ends_at->format('d M Y') }}</div>
                        @else
                            <span class="text-success fw-bold" style="font-size: 14px;">Unlimited Access</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Right Column (Settings & Projects) -->
    <div class="col-lg-8 col-xl-9 mb-4">
        
        <!-- Notification Settings (Horizontal Layout) -->
        <div class="card shadow-sm border-0 mb-4" style="border-radius: 20px; overflow: hidden; background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%); border: 1px solid #e2e8f0 !important;">
            <div class="card-body p-4 d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-4">
                <div class="d-flex align-items-center gap-4">
                    <div style="width: 60px; height: 60px; border-radius: 16px; background: #fffbeb; color: #d97706; display: flex; align-items: center; justify-content: center; flex-shrink: 0; box-shadow: 0 4px 12px rgba(217, 119, 6, 0.1);">
                        <i class="ph ph-bell-ringing fs-2"></i>
                    </div>
                    <div>
                        <h5 class="mb-1 fw-bold text-dark">Alert Preferences</h5>
                        <p class="text-muted mb-0" style="font-size: 14px; max-width: 400px;">When should we notify you before your trial ends? Choose your preferred threshold.</p>
                    </div>
                </div>
                <div class="flex-grow-1" style="max-width: 350px;">
                    <form action="{{ route('mk.profile.notice') }}" method="POST">
                        @csrf
                        <div class="input-group">
                            <select name="notice_days" class="form-select" style="border-radius: 12px 0 0 12px; font-size: 14px; font-weight: 500; border-color: #e2e8f0; height: 48px; padding-left: 16px;">
                                @php
                                    $currentDays = null;
                                    if(auth()->user()->subscription_notice_at && auth()->user()->trial_ends_at) {
                                        $currentDays = auth()->user()->subscription_notice_at->diffInDays(auth()->user()->trial_ends_at);
                                    }
                                @endphp
                                <option value="30" {{ $currentDays == 30 ? 'selected' : '' }}>1 Month Before</option>
                                <option value="7" {{ $currentDays == 7 ? 'selected' : (!isset($currentDays) ? 'selected' : '') }}>1 Week Before (Default)</option>
                                <option value="3" {{ $currentDays == 3 ? 'selected' : '' }}>3 Days Before</option>
                                <option value="1" {{ $currentDays == 1 ? 'selected' : '' }}>1 Day Before</option>
                            </select>
                            <button type="submit" class="btn btn-primary px-4" style="border-radius: 0 12px 12px 0; font-weight: 700; font-size: 14px; white-space: nowrap; background: var(--primary-green, #038047); border-color: var(--primary-green, #038047);">
                                Update
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Assigned Projects List -->
        <div class="card h-100 shadow-sm border-0" style="border-radius: 20px; overflow: hidden;">
            <div class="card-header bg-white border-bottom p-4 d-flex align-items-center justify-content-between" style="border-bottom: 1px solid #f1f5f9 !important;">
                <div>
                    <h5 class="mb-1 fw-bold text-dark">Active Project Subscriptions</h5>
                    <p class="text-muted mb-0" style="font-size: 14px;">Monitor your assigned projects and access real-time analytics.</p>
                </div>
                <div class="badge bg-primary bg-opacity-10 text-primary px-4 py-2 rounded-pill" style="font-size: 14px; font-weight: 800; border: 1px solid rgba(13, 110, 253, 0.2);">
                    {{ count($projects) }} Total
                </div>
            </div>
            <div class="card-body p-4 bg-light bg-opacity-50">
                @if(isset($projects) && count($projects) > 0)
                    <div class="row g-4">
                        @foreach($projects as $project)
                            <div class="col-md-6 col-xl-4">
                                <div class="project-card border bg-white p-3 h-100 d-flex flex-column" style="border-radius: 14px; border-color: #e2e8f0 !important; cursor: default; position: relative; overflow: hidden;">
                                    
                                    <!-- Decorative Indicator purely for premium feel -->
                                    <div style="position: absolute; top: 0; left: 0; bottom: 0; width: 4px; background: #038047; border-radius: 14px 0 0 14px; opacity: 0; transition: all 0.2s;" class="pcard-indicator"></div>

                                    <div class="d-flex align-items-start mb-3">
                                        @php
                                            $pName = $project['name'] ?? $project['project_name'] ?? $project['title'] ?? $project['label'] ?? 'Unknown Project (' . ($project['id'] ?? 'N/A') . ')';
                                        @endphp
                                        <div style="width: 48px; height: 48px; border-radius: 12px; background: #f1f5f9; color: #038047; display: flex; align-items: center; justify-content: center; font-size: 20px; font-weight: 800; flex-shrink: 0; margin-right: 16px; box-shadow: inset 0 2px 4px rgba(0,0,0,0.02);">
                                            {{ strtoupper(substr($pName, 0, 1)) }}
                                        </div>
                                        <div style="flex:1; min-width: 0;">
                                            <h6 class="mb-1 fw-bold text-dark text-truncate" title="{{ $pName }}" style="font-size: 15px; margin-top: 2px;">
                                                {{ $pName }}
                                            </h6>
                                            <span class="text-muted d-flex align-items-center" style="font-size: 12px; gap: 4px;">
                                                <i class="ph ph-hash"></i> ID: {{ $project['id'] ?? 'N/A' }}
                                            </span>
                                        </div>
                                    </div>
                                    
                                    <div class="mt-auto pt-3 border-top border-dashed d-flex align-items-center justify-content-end">
                                        <a href="{{ route('mk.dashboard', ['project_id' => $project['id'] ?? '']) }}" class="btn btn-sm btn-light pcard-btn" style="font-size: 12px; font-weight: 600; padding: 4px 10px; border-radius: 6px;">
                                            Enter <i class="ph ph-arrow-right ms-1"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-5 my-4">
                        <div class="mb-4 d-inline-flex align-items-center justify-content-center" style="width: 100px; height: 100px; background: #f8fafc; border-radius: 50%; color: #cbd5e1;">
                            <i class="ph ph-folder-dashed" style="font-size: 48px;"></i>
                        </div>
                        <h5 class="fw-bold text-dark mb-2">No Projects Assigned</h5>
                        <p class="text-muted mx-auto" style="max-width: 400px; line-height: 1.6;">You currently do not have access to any projects. Please contact your administrator to request access to the monitoring dashboards.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
.project-card {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border: 1px solid #f1f5f9 !important;
}
.project-card:hover {
    border-color: var(--primary-green, #038047) !important;
    box-shadow: 0 20px 40px rgba(3, 128, 71, 0.1) !important;
    transform: translateY(-8px);
}
.project-card:hover .pcard-indicator {
    opacity: 1;
    width: 6px;
}
.pcard-btn {
    color: #475569;
    background: #f8fafc;
    transition: all 0.3s;
    border-radius: 10px !important;
    padding: 8px 16px !important;
}
.project-card:hover .pcard-btn {
    background: var(--primary-green, #038047);
    color: #fff;
    box-shadow: 0 4px 12px rgba(3, 128, 71, 0.2);
}
.border-dashed {
    border-top-style: dashed !important;
    border-top-color: #f1f5f9 !important;
}

/* Avatar Upload Overlay */
.profile-avatar-container {
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}
.profile-avatar-container:hover {
    transform: scale(1.05);
    box-shadow: 0 15px 30px rgba(0,0,0,0.15) !important;
}
.avatar-upload-overlay {
    position: absolute;
    inset: 0;
    background: rgba(3, 128, 71, 0.8);
    backdrop-filter: blur(4px);
    border-radius: 50%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: white;
    opacity: 0;
    transition: all 0.3s ease;
    z-index: 10;
}
.profile-avatar-container:hover .avatar-upload-overlay {
    opacity: 1;
}
.avatar-upload-overlay i {
    font-size: 28px;
    margin-bottom: 6px;
}
.avatar-upload-overlay span {
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
}
.stat-box {
    transition: all 0.2s;
}
.stat-box:hover {
    background: #fff !important;
    box-shadow: 0 4px 12px rgba(0,0,0,0.03);
}
</style>
@endsection
