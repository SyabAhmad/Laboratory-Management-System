@extends('Layout.master')
@section('title', 'Referrals & Patients')
@section('content')

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Home</a></li>
                        <li class="breadcrumb-item active">Referrals & Patients</li>
                    </ol>
                </div>
                <h4 class="page-title">Referral Management</h4>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <label for="referralFilter" class="form-label">Select Referral</label>
                            <select class="form-control" id="referralFilter">
                                <option value="">All Referrals</option>
                                @foreach($referrals as $referral)
                                    <option value="{{ $referral->id }}" {{ request('referral_id') == $referral->id ? 'selected' : '' }}>
                                        {{ $referral->name }} ({{ $referral->patients->count() }} patients)
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="searchInput" class="form-label">Search Patients</label>
                            <input type="text" class="form-control" id="searchInput" placeholder="Search by patient name or ID..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="button" class="btn btn-primary-custom mr-2" id="applyFilters">
                                <i class="fas fa-filter"></i> Apply Filters
                            </button>
                            <button type="button" class="btn btn-secondary" id="clearFilters">
                                <i class="fas fa-times"></i> Clear
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <i class="fas fa-users fa-2x text-primary mb-2"></i>
                    <h4 class="mb-0">{{ $referrals->count() }}</h4>
                    <p class="text-muted mb-0">Total Referrals</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <i class="fas fa-user-injured fa-2x text-success mb-2"></i>
                    <h4 class="mb-0">{{ $totalPatients }}</h4>
                    <p class="text-muted mb-0">Total Patients</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <i class="fas fa-chart-line fa-2x text-info mb-2"></i>
                    <h4 class="mb-0">{{ $referrals->count() > 0 ? round($totalPatients / $referrals->count(), 1) : 0 }}</h4>
                    <p class="text-muted mb-0">Avg Patients/Referral</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <i class="fas fa-star fa-2x text-warning mb-2"></i>
                    <h4 class="mb-0">{{ $referrals->where('patients_count', '>', 0)->count() }}</h4>
                    <p class="text-muted mb-0">Active Referrals</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Results Section -->
    <div id="resultsContainer">
        @if(request('referral_id'))
            <!-- Single Referral View -->
            @php $selectedReferral = $referrals->find(request('referral_id')); @endphp
            @if($selectedReferral)
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">
                                    <i class="fas fa-user-md mr-2"></i>
                                    {{ $selectedReferral->name }}
                                    <span class="badge badge-light ml-2">{{ $selectedReferral->patients->count() }} patients</span>
                                </h5>
                                <button class="btn btn-light btn-sm" onclick="window.history.back()">
                                    <i class="fas fa-arrow-left"></i> Back to All
                                </button>
                            </div>
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-md-3">
                                        <strong>Email:</strong> {{ $selectedReferral->email ?? 'N/A' }}
                                    </div>
                                    <div class="col-md-3">
                                        <strong>Phone:</strong> {{ $selectedReferral->phone ?? 'N/A' }}
                                    </div>
                                    <div class="col-md-3">
                                        <strong>Hospital:</strong> {{ $selectedReferral->hospitalname ?? 'N/A' }}
                                    </div>
                                    <div class="col-md-3">
                                        <strong>Address:</strong> {{ $selectedReferral->address ?? 'N/A' }}
                                    </div>
                                </div>

                                @if($selectedReferral->patients->count() > 0)
                                    <div class="table-responsive">
                                        <table class="table table-hover" id="patientsTable">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th>Patient ID</th>
                                                    <th>Name</th>
                                                    <th>Mobile</th>
                                                    <th>Age/Gender</th>
                                                    <th>Receiving Date</th>
                                                    <th>Reporting Date</th>
                                                    <th>Status</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if(isset($patients))
                                                    @foreach($patients as $patient)
                                                        <tr>
                                                            <td><strong>{{ $patient->patient_id }}</strong></td>
                                                            <td>{{ $patient->name }}</td>
                                                            <td>{{ $patient->mobile_phone ?? 'N/A' }}</td>
                                                            <td>{{ $patient->age ?? 'N/A' }} / {{ ucfirst($patient->gender ?? 'N/A') }}</td>
                                                            <td>{{ $patient->receiving_date ? $patient->receiving_date->format('d/m/Y') : 'N/A' }}</td>
                                                            <td>{{ $patient->reporting_date ? $patient->reporting_date->format('d/m/Y') : 'N/A' }}</td>
                                                            <td>
                                                                @if($patient->reporting_date && $patient->reporting_date->isPast())
                                                                    <span class="badge badge-success">Completed</span>
                                                                @elseif($patient->receiving_date && $patient->receiving_date->isPast())
                                                                    <span class="badge badge-warning">In Progress</span>
                                                                @else
                                                                    <span class="badge badge-secondary">Pending</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                <a href="{{ route('patients.profile', $patient->id) }}" class="btn btn-sm btn-outline-primary">
                                                                    <i class="fas fa-eye"></i> View
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @else
                                                    @foreach($selectedReferral->patients->sortByDesc('receiving_date') as $patient)
                                                        <tr>
                                                            <td><strong>{{ $patient->patient_id }}</strong></td>
                                                            <td>{{ $patient->name }}</td>
                                                            <td>{{ $patient->mobile_phone ?? 'N/A' }}</td>
                                                            <td>{{ $patient->age ?? 'N/A' }} / {{ ucfirst($patient->gender ?? 'N/A') }}</td>
                                                            <td>{{ $patient->receiving_date ? $patient->receiving_date->format('d/m/Y') : 'N/A' }}</td>
                                                            <td>{{ $patient->reporting_date ? $patient->reporting_date->format('d/m/Y') : 'N/A' }}</td>
                                                            <td>
                                                                @if($patient->reporting_date && $patient->reporting_date->isPast())
                                                                    <span class="badge badge-success">Completed</span>
                                                                @elseif($patient->receiving_date && $patient->receiving_date->isPast())
                                                                    <span class="badge badge-warning">In Progress</span>
                                                                @else
                                                                    <span class="badge badge-secondary">Pending</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                <a href="{{ route('patients.profile', $patient->id) }}" class="btn btn-sm btn-outline-primary">
                                                                    <i class="fas fa-eye"></i> View
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>

                                    <!-- Pagination -->
                                    @if(isset($patients) && $patients->hasPages())
                                        <div class="d-flex justify-content-center mt-3">
                                            {{ $patients->links() }}
                                        </div>
                                    @endif
                                @else
                                    <div class="text-center py-4">
                                        <i class="fas fa-user-injured fa-3x text-muted mb-3"></i>
                                        <h5 class="text-muted">No Patients Yet</h5>
                                        <p class="text-muted">This referral hasn't referred any patients yet.</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="alert alert-warning">Selected referral not found.</div>
            @endif
        @else
            <!-- All Referrals Grid View -->
            <div class="row">
                @foreach($referrals as $referral)
                    <div class="col-lg-6 col-xl-4 mb-4">
                        <div class="card h-100 referral-card" data-referral-id="{{ $referral->id }}">
                            <div class="card-header bg-gradient-primary text-white">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0">
                                        <i class="fas fa-user-md mr-2"></i>
                                        {{ Str::limit($referral->name, 20) }}
                                    </h6>
                                    <span class="badge badge-light">{{ $referral->patients->count() }}</span>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="mb-2">
                                    <small class="text-muted">
                                        <i class="fas fa-envelope mr-1"></i>{{ Str::limit($referral->email ?? 'No email', 25) }}
                                    </small>
                                </div>
                                <div class="mb-3">
                                    <small class="text-muted">
                                        <i class="fas fa-phone mr-1"></i>{{ $referral->phone ?? 'No phone' }}
                                    </small>
                                </div>

                                <div class="progress mb-2" style="height: 6px;">
                                    <div class="progress-bar bg-success" style="width: {{ $referral->patients->count() > 0 ? min(($referral->patients->count() / 10) * 100, 100) : 0 }}%"></div>
                                </div>
                                <small class="text-muted">{{ $referral->patients->count() }} patients referred</small>
                            </div>
                            <div class="card-footer bg-light">
                                <button class="btn btn-primary-custom btn-sm btn-block view-patients" data-referral-id="{{ $referral->id }}">
                                    <i class="fas fa-users mr-1"></i> View Patients
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            @if($referrals->isEmpty())
                <div class="text-center py-5">
                    <i class="fas fa-users fa-4x text-muted mb-4"></i>
                    <h4 class="text-muted">No Referrals Found</h4>
                    <p class="text-muted">Start by adding some referrals to track their patients.</p>
                    <a href="{{ route('referrels.list') }}" class="btn btn-primary-custom btn-lg">
                        <i class="fas fa-plus mr-2"></i> Add First Referral
                    </a>
                </div>
            @endif
        @endif
    </div>
</div>

<style>
.referral-card {
    transition: transform 0.2s, box-shadow 0.2s;
    cursor: pointer;
}

.referral-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.table th {
    border-top: none;
    font-weight: 600;
    font-size: 0.875rem;
}

.table td {
    vertical-align: middle;
}

.badge {
    font-size: 0.75rem;
}
</style>

<script>
$(document).ready(function() {
    // Apply filters
    $('#applyFilters').click(function() {
        const referralId = $('#referralFilter').val();
        const search = $('#searchInput').val();

        let url = '{{ route("referrals.patients") }}';
        const params = new URLSearchParams();

        if (referralId) params.append('referral_id', referralId);
        if (search) params.append('search', search);

        if (params.toString()) {
            url += '?' + params.toString();
        }

        window.location.href = url;
    });

    // Clear filters
    $('#clearFilters').click(function() {
        window.location.href = '{{ route("referrals.patients") }}';
    });

    // Enter key on search
    $('#searchInput').keypress(function(e) {
        if (e.which == 13) {
            $('#applyFilters').click();
        }
    });

    // View patients button
    $('.view-patients').click(function() {
        const referralId = $(this).data('referral-id');
        window.location.href = '{{ route("referrals.patients") }}?referral_id=' + referralId;
    });

    // Card click
    $('.referral-card').click(function() {
        const referralId = $(this).data('referral-id');
        window.location.href = '{{ route("referrals.patients") }}?referral_id=' + referralId;
    });
});
</script>

@endsection
