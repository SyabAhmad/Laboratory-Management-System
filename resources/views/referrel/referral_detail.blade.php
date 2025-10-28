@extends('Layout.master')
@section('title', 'Referral Details - {{ $referral->name }}')
@section('content')

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('referrals.patients') }}">Referrals</a></li>
                        <li class="breadcrumb-item active">{{ $referral->name }}</li>
                    </ol>
                </div>
                <h4 class="page-title">Referral Details</h4>
            </div>
        </div>
    </div>

    <!-- Referral Info Card -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-user-md mr-2"></i>
                        {{ $referral->name }}
                        <span class="badge badge-light ml-2">{{ $patients->total() }} patients</span>
                    </h5>
                    <a href="{{ route('referrals.patients') }}" class="btn btn-light btn-sm">
                        <i class="fas fa-arrow-left"></i> Back to All Referrals
                    </a>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <strong>Email:</strong> {{ $referral->email ?? 'N/A' }}
                        </div>
                        <div class="col-md-3">
                            <strong>Phone:</strong> {{ $referral->phone ?? 'N/A' }}
                        </div>
                        <div class="col-md-3">
                            <strong>Hospital:</strong> {{ $referral->hospitalname ?? 'N/A' }}
                        </div>
                        <div class="col-md-3">
                            <strong>Address:</strong> {{ $referral->address ?? 'N/A' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filter -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <label for="searchInput" class="form-label">Search Patients</label>
                            <input type="text" class="form-control" id="searchInput" placeholder="Search by patient name or ID..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="button" class="btn btn-primary mr-2" id="applyFilters">
                                <i class="fas fa-search"></i> Search
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

    <!-- Patients Table -->
    @if($patients->count() > 0)
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Referred Patients</h5>
                    </div>
                    <div class="card-body">
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
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        @if($patients->hasPages())
                            <div class="d-flex justify-content-center mt-3">
                                {{ $patients->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-user-injured fa-4x text-muted mb-4"></i>
                        <h4 class="text-muted">No Patients Found</h4>
                        <p class="text-muted">This referral hasn't referred any patients yet.</p>
                        @if(request('search'))
                            <p class="text-muted">Try adjusting your search criteria.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<style>
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
    // Apply search filters
    $('#applyFilters').click(function() {
        const search = $('#searchInput').val();

        let url = '{{ route("referrals.patients") }}?referral_id={{ $referral->id }}';
        const params = new URLSearchParams();

        if (search) params.append('search', search);

        if (params.toString()) {
            url += '&' + params.toString();
        }

        window.location.href = url;
    });

    // Clear filters
    $('#clearFilters').click(function() {
        window.location.href = '{{ route("referrals.patients") }}?referral_id={{ $referral->id }}';
    });

    // Enter key on search
    $('#searchInput').keypress(function(e) {
        if (e.which == 13) {
            $('#applyFilters').click();
        }
    });
});
</script>

@endsection
