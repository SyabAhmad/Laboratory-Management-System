@extends('Layout.master')
@section('title', 'My Profile')

@section('content')

<div class="container-fluid">

    <!-- Page Title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">Profile</li>
                    </ol>
                </div>
                <h4 class="page-title">My Profile</h4>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="mdi mdi-check-all me-2"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="row">
        <!-- Left Column: Profile Card -->
        <div class="col-lg-4 col-xl-4">
            <div class="card shadow-sm border-0" style="border-radius: 15px; overflow: hidden;">
                <div class="card-body text-center p-0">
                    <!-- Profile Header / Banner -->
                    <div class="profile-header" style="background: linear-gradient(135deg, var(--primary) 0%, #6c757d 100%); height: 130px;">
                    </div>
                    
                    <!-- Avatar -->
                    <div class="profile-avatar-wrapper" style="margin-top: -65px;">
                        @if($user->profile_photo_path)
                            <img src="{{ asset('assets/HMS/employees/' . $user->profile_photo_path) }}" 
                                 class="rounded-circle avatar-xl img-thumbnail shadow-lg" 
                                 alt="profile-image" 
                                 style="width: 130px; height: 130px; object-fit: cover; border: 4px solid #fff;">
                        @else
                            <img src="{{ asset('assets/images/users/avatar-1.jpg') }}" 
                                 class="rounded-circle avatar-xl img-thumbnail shadow-lg" 
                                 alt="profile-image" 
                                 style="width: 130px; height: 130px; object-fit: cover; border: 4px solid #fff;">
                        @endif
                    </div>

                    <div class="p-4">
                        <h3 class="mb-1 text-dark font-weight-bold">{{ $user->name }}</h3>
                        <p class="text-muted mb-3">{{ $user->user_type }}</p>

                        <div class="d-flex justify-content-center mb-3">
                            <span class="badge badge-pill {{ $user->status == 'Active' ? 'badge-soft-success' : 'badge-soft-warning' }} px-3 py-2 font-size-13">
                                <i class="mdi mdi-circle-medium me-1"></i> {{ $user->status ?? 'Active' }}
                            </span>
                        </div>

                        <div class="text-start mt-4 pt-3 border-top">
                            <p class="text-muted mb-2 font-14">
                                <i class="mdi mdi-email-outline me-2 text-primary"></i> 
                                <strong>Email :</strong> <span class="ms-2 float-end">{{ $user->email }}</span>
                            </p>
                            <p class="text-muted mb-2 font-14">
                                <i class="mdi mdi-calendar-clock me-2 text-primary"></i> 
                                <strong>Member Since :</strong> <span class="ms-2 float-end">{{ $user->created_at->format('M d, Y') }}</span>
                            </p>
                            <p class="text-muted mb-0 font-14">
                                <i class="mdi mdi-account-key-outline me-2 text-primary"></i> 
                                <strong>User ID :</strong> <span class="ms-2 float-end">#{{ $user->id }}</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Quick Actions / Info Card (Optional) -->
            <div class="card shadow-sm border-0 mt-3" style="border-radius: 15px;">
                <div class="card-body">
                    <h5 class="header-title mb-3">Account Security</h5>
                    <div class="alert alert-info mb-0" role="alert">
                        <i class="mdi mdi-shield-check-outline me-1"></i> Make sure to keep your password strong and update it regularly.
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Settings Tabs -->
        <div class="col-lg-8 col-xl-8">
            <div class="card shadow-sm border-0" style="border-radius: 15px;">
                <div class="card-body p-4">
                    <ul class="nav nav-pills nav-justified bg-light p-1 rounded mb-4" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active rounded py-2" data-toggle="pill" href="#settings" role="tab">
                                <i class="mdi mdi-account-cog me-1"></i> Profile Settings
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link rounded py-2" data-toggle="pill" href="#password" role="tab">
                                <i class="mdi mdi-lock-reset me-1"></i> Change Password
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <!-- Profile Settings Tab -->
                        <div class="tab-pane fade show active" id="settings" role="tabpanel">
                            <form method="POST" action="{{ route('user.profile.update') }}">
                                @csrf
                                @method('PUT')

                                <h5 class="mb-4 text-uppercase text-muted"><i class="mdi mdi-account-details me-1"></i> Edit Personal Information</h5>
                                
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group mb-3">
                                            <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="mdi mdi-account"></i></span>
                                                </div>
                                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required placeholder="Enter your full name">
                                                @error('name')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group mb-3">
                                            <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="mdi mdi-email"></i></span>
                                                </div>
                                                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}" required placeholder="Enter your email">
                                                @error('email')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label">User Type</label>
                                            <input type="text" class="form-control bg-light" value="{{ $user->user_type }}" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label">Current Status</label>
                                            <input type="text" class="form-control bg-light" value="{{ $user->status ?? 'Active' }}" readonly>
                                        </div>
                                    </div>
                                </div>

                                <div class="text-right mt-3">
                                    <button type="submit" class="btn btn-primary px-4 rounded-pill"><i class="mdi mdi-content-save-edit me-1"></i> Save Changes</button>
                                </div>
                            </form>
                        </div>

                        <!-- Change Password Tab -->
                        <div class="tab-pane fade" id="password" role="tabpanel">
                            <form method="POST" action="{{ route('user.profile.update') }}">
                                @csrf
                                @method('PUT')
                                
                                <input type="hidden" name="name" value="{{ $user->name }}">
                                <input type="hidden" name="email" value="{{ $user->email }}">

                                <h5 class="mb-4 text-uppercase text-muted"><i class="mdi mdi-shield-lock me-1"></i> Security Settings</h5>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group mb-3">
                                            <label for="current_password" class="form-label">Current Password <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="mdi mdi-key"></i></span>
                                                </div>
                                                <input type="password" class="form-control @error('current_password') is-invalid @enderror" id="current_password" name="current_password" placeholder="Enter current password">
                                                @error('current_password')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="new_password" class="form-label">New Password <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="mdi mdi-lock-plus"></i></span>
                                                </div>
                                                <input type="password" class="form-control @error('new_password') is-invalid @enderror" id="new_password" name="new_password" placeholder="Min 8 characters">
                                                @error('new_password')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="new_password_confirmation" class="form-label">Confirm New Password <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="mdi mdi-lock-check"></i></span>
                                                </div>
                                                <input type="password" class="form-control" id="new_password_confirmation" name="new_password_confirmation" placeholder="Re-enter new password">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="text-right mt-3">
                                    <button type="submit" class="btn btn-warning px-4 rounded-pill text-white"><i class="mdi mdi-key-change me-1"></i> Update Password</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

@endsection