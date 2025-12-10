@extends('Layout.master')

@section('title', 'Lab Settings')

@section('content')
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
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title mb-4">
                            <i class="fas fa-cogs text-primary-custom mr-2"></i> Laboratory Configuration
                        </h4>

                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                {{ session('error') }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif

                        <form action="{{ route('settings.update') }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="form-group row">
                                <label for="timezone" class="col-md-3 col-form-label text-md-right">
                                    <i class="fas fa-clock text-primary-custom mr-1"></i> Timezone
                                </label>
                                <div class="col-md-9">
                                    <select id="timezone" name="timezone" class="form-control" required>
                                        @foreach ($timezones as $timezone)
                                            <option value="{{ $timezone }}" {{ ($timezone == $currentTimezone) ? 'selected' : '' }}>
                                                {{ $timezone }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted">Select the timezone for your laboratory</small>
                                </div>
                            </div>

                            <div class="form-group row mb-0">
                                <div class="col-md-9 offset-md-3">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save mr-1"></i> Save Settings
                                    </button>
                                    <button type="reset" class="btn btn-secondary ml-1">
                                        <i class="fas fa-redo mr-1"></i> Reset
                                    </button>
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
        });
    </script>
@endsection
