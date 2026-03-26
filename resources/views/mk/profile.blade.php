@extends('mk.layouts.app')

@section('title', 'My Profile | SMADIMENT')
@section('page-title', 'User Profile')

@section('content')
<div class="row">
    <!-- Profile Info -->
    <div class="col-lg-4 col-xl-3 mb-4">
        <div class="card h-100 shadow-sm border-0">
            <div class="card-body text-center pt-5">
                <div class="position-relative d-inline-flex align-items-center justify-content-center mb-3 profile-avatar-container"
                     style="width: 130px; height: 130px; border-radius: 50%; background: var(--primary-green, #038047); color: #fff; font-size: 48px; font-weight: 800; border: 5px solid #f8fafc; box-shadow: 0 8px 16px rgba(0,0,0,0.08);">
                    {{ strtoupper(substr(auth()->user()->name ?? 'Admin', 0, 2)) }}
                </div>
                
                @if(session('success'))
                    <div class="alert alert-success py-2 px-3 mt-2" style="font-size: 13px; border-radius: 8px;">
                        {{ session('success') }}
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
            
            <div class="card-body border-top p-4" style="background: #fdfdfd; border-radius: 0 0 16px 16px;">
                <div class="d-flex align-items-center mb-4">
                    <div style="width: 44px; height: 44px; border-radius: 12px; background: #f1f5f9; display: flex; align-items: center; justify-content: center; color: #475569; margin-right: 16px; flex-shrink: 0;">
                        <i class="ph ph-calendar-blank fs-4"></i>
                    </div>
                    <div>
                        <h6 class="mb-1 fw-semibold text-dark" style="font-size: 14px;">Member Since</h6>
                        <span class="text-muted" style="font-size: 13px;">{{ auth()->user()->created_at ? auth()->user()->created_at->format('d F Y') : 'Unknown' }}</span>
                    </div>
                </div>
                
                <div class="d-flex align-items-center">
                    <div style="width: 44px; height: 44px; border-radius: 12px; background: #f1f5f9; display: flex; align-items: center; justify-content: center; color: #475569; margin-right: 16px; flex-shrink: 0;">
                        <i class="ph ph-folders fs-4"></i>
                    </div>
                    <div>
                        <h6 class="mb-1 fw-semibold text-dark" style="font-size: 14px;">Total Projects</h6>
                        <span class="text-muted" style="font-size: 13px;">{{ count($projects) }} Project(s) Assigned</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Assigned Projects List -->
    <div class="col-lg-8 col-xl-9 mb-4">
        <div class="card h-100 shadow-sm border-0">
            <div class="card-header bg-white border-bottom p-4 d-flex align-items-center justify-content-between">
                <div>
                    <h5 class="mb-1 fw-bold text-dark">Assigned Projects</h5>
                    <p class="text-muted mb-0" style="font-size: 13px;">Projects that you currently have access to monitor and analyze.</p>
                </div>
                <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill" style="font-size: 14px; font-weight: 700;">
                    {{ count($projects) }} Total
                </span>
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
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
}
.project-card:hover {
    border-color: #038047 !important;
    box-shadow: 0 12px 24px rgba(3, 128, 71, 0.08) !important;
    transform: translateY(-4px);
}
.project-card:hover .pcard-indicator {
    opacity: 1;
}
.pcard-btn {
    color: #475569;
    background: #f8fafc;
    transition: all 0.2s;
}
.project-card:hover .pcard-btn {
    background: #038047;
    color: #fff;
}
.border-dashed {
    border-top-style: dashed !important;
    border-top-color: #e2e8f0 !important;
}

/* Avatar Upload Overlay */
.profile-avatar-container {
    /* cursor: pointer; */
}
.avatar-upload-overlay {
    position: absolute;
    inset: 5px; /* respect the border */
    background: rgba(0,0,0,0.5);
    border-radius: 50%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: white;
    opacity: 0;
    transition: opacity 0.3s ease;
    z-index: 10;
}
.profile-avatar-container:hover .avatar-upload-overlay {
    opacity: 1;
}
.avatar-upload-overlay i {
    font-size: 24px;
    margin-bottom: 4px;
}
.avatar-upload-overlay span {
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
</style>
@endsection
