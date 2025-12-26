@extends('Layout.master')

@section('title', 'Lab Settings')

@section('content')
<style>
    .settings-card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
    }
    
    .settings-card:hover {
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.12);
    }
    
    .settings-header {
        background: linear-gradient(135deg, #8d2d36 0%, #a84a54 100%);
        color: white;
        border-radius: 12px 12px 0 0 !important;
        padding: 25px;
    }
    
    .settings-section {
        padding: 30px;
    }
    
    .setting-item {
        padding: 15px 0;
        border-bottom: 1px solid #f0f0f0;
    }
    
    .setting-item:last-child {
        border-bottom: none;
    }
    
    .setting-title {
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 5px;
    }
    
    .setting-description {
        color: #7f8c8d;
        font-size: 0.9rem;
    }
    
    .btn-settings {
        background-color: #8d2d36;
        border-color: #8d2d36;
        padding: 10px 25px;
        font-weight: 600;
    }
    
    .btn-settings:hover {
        background-color: #a84a54;
        border-color: #a84a54;
    }
    
    .form-control, .form-select {
        border: 1px solid #e1e5eb;
        border-radius: 8px;
        padding: 10px 15px;
    }
    
    .form-control:focus, .form-select:focus {
        border-color: #8d2d36;
        box-shadow: 0 0 0 0.2rem rgba(141, 45, 54, 0.25);
    }
    
    .section-title {
        color: #8d2d36;
        font-weight: 600;
        padding-bottom: 10px;
        margin-bottom: 20px;
        border-bottom: 2px solid #f0f0f0;
    }
    
    .icon-circle {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        background-color: #f8f9fa;
        border-radius: 50%;
        margin-right: 12px;
    }
    
    .icon-circle i {
        color: #8d2d36;
        font-size: 18px;
    }
</style>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">Lab Settings</li>
                    </ol>
                </div>
                <h4 class="page-title">Laboratory Settings</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card settings-card">
                <div class="card-header settings-header d-flex align-items-center">
                    <div class="icon-circle">
                        <i class="fas fa-cog"></i>
                    </div>
                    <div>
                        <h4 class="mb-1 text-white">Laboratory Configuration</h4>
                        <p class="mb-0 text-white-50">Manage your laboratory settings and preferences</p>
                    </div>
                </div>
                
                <div class="card-body settings-section">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle mr-2"></i>
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            {{ session('error') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    <h5 class="section-title">General Settings</h5>
                    
                    <form action="{{ route('settings.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="setting-item">
                            <div class="setting-title">
                                <i class="fas fa-clock mr-2 text-primary"></i>Timezone
                            </div>
                            <div class="setting-description">
                                Select the timezone for your laboratory system
                            </div>
                            
                            <div class="mt-3">
                                <select id="timezone" name="timezone" class="form-select" required>
                                    @foreach ($timezones as $timezone)
                                        <option value="{{ $timezone }}" {{ ($timezone == $currentTimezone) ? 'selected' : '' }}>
                                            {{ $timezone }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group mt-4 pt-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <button type="submit" class="btn btn-settings">
                                        <i class="fas fa-save mr-2"></i>Save Settings
                                    </button>
                                    <button type="reset" class="btn btn-outline-secondary ml-2">
                                        <i class="fas fa-redo mr-2"></i>Reset
                                    </button>
                                </div>
                                <div class="text-muted">
                                    <i class="fas fa-lock mr-1"></i>Changes take effect immediately
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Add any JavaScript needed for settings page
        
        // Add animation to form elements
        const formControls = document.querySelectorAll('.form-control, .form-select');
        formControls.forEach(control => {
            control.addEventListener('focus', function() {
                this.parentElement.classList.add('focused');
            });
            
            control.addEventListener('blur', function() {
                this.parentElement.classList.remove('focused');
            });
        });
    });
</script>
@endsection