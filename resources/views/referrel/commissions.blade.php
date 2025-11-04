@extends('Layout.master')
@section('title', 'Referral Commissions - {{ $referral->name }}')
@section('content')

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('referrels.list') }}">Referrals</a></li>
                        <li class="breadcrumb-item active">{{ $referral->name }} - Commissions</li>
                    </ol>
                </div>
                <h4 class="page-title">Commission Tracking</h4>
            </div>
        </div>
    </div>

    <!-- Commission Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <h6 class="text-muted text-uppercase mb-3">Total Earned</h6>
                    <h2 class="mb-0 text-primary">PKR {{ number_format($stats['total_earned'], 2) }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <h6 class="text-muted text-uppercase mb-3">Pending</h6>
                    <h2 class="mb-0 text-warning">PKR {{ number_format($stats['pending'], 2) }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <h6 class="text-muted text-uppercase mb-3">Paid</h6>
                    <h2 class="mb-0 text-success">PKR {{ number_format($stats['paid'], 2) }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <h6 class="text-muted text-uppercase mb-3">Total Commissions</h6>
                    <h2 class="mb-0 text-info">{{ $stats['count'] }}</h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Referral Info -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Referral Details</h5>
                    <div>
                        <a href="{{ route('referrals.commissions-monthly', $referral->id) }}" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-calendar"></i> Monthly View
                        </a>
                        <a href="{{ route('referrels.list') }}" class="btn btn-sm btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <strong>Name:</strong> {{ $referral->name }}
                        </div>
                        <div class="col-md-3">
                            <strong>Email:</strong> {{ $referral->email ?? 'N/A' }}
                        </div>
                        <div class="col-md-3">
                            <strong>Phone:</strong> {{ $referral->phone ?? 'N/A' }}
                        </div>
                        <div class="col-md-3">
                            <strong>Commission Rate:</strong> <span class="badge bg-primary">{{ $referral->commission_percentage }}%</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Commissions Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Individual Commission Records</h5>
                        <a href="{{ route('referrals.commissions-monthly', $referral->id) }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-calendar"></i> View Monthly Summary
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if($commissions->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Patient</th>
                                        <th>Bill #</th>
                                        <th>Bill Amount</th>
                                        <th>Commission %</th>
                                        <th>Commission Amount</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($commissions as $commission)
                                        <tr>
                                            <td>{{ $commission->created_at->format('d-m-Y') }}</td>
                                            <td>
                                                @if($commission->patient)
                                                    <a href="{{ route('patients.profile', $commission->patient->id) }}" class="text-primary">
                                                        {{ $commission->patient->name }}
                                                    </a>
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                            <td>
                                                @if($commission->bill)
                                                    <a href="{{ route('billing.details', $commission->bill->id) }}" class="text-primary">
                                                        {{ $commission->bill->bill_no ?? 'Bill #' . $commission->bill->id }}
                                                    </a>
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                            <td>PKR {{ number_format($commission->bill_amount, 2) }}</td>
                                            <td>{{ $commission->commission_percentage }}%</td>
                                            <td>
                                                <strong>PKR {{ number_format($commission->commission_amount, 2) }}</strong>
                                            </td>
                                            <td>
                                                @if($commission->status === 'pending')
                                                    <span class="badge bg-warning">Pending</span>
                                                @elseif($commission->status === 'paid')
                                                    <span class="badge bg-success">Paid</span>
                                                @else
                                                    <span class="badge bg-danger">{{ ucfirst($commission->status) }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($commission->status === 'pending')
                                                    <button type="button" class="btn btn-sm btn-success mark-paid-btn" data-commission-id="{{ $commission->id }}">
                                                        <i class="fas fa-check"></i> Mark Paid
                                                    </button>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-end mt-3">
                            {{ $commissions->links('pagination::bootstrap-4') }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-inbox fa-4x text-muted mb-4"></i>
                            <h4 class="text-muted">No Commissions Yet</h4>
                            <p class="text-muted">Commissions will appear here as patients referred by this referral create bills.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Mark commission as paid
        document.querySelectorAll('.mark-paid-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const commissionId = this.getAttribute('data-commission-id');
                console.log('Mark paid button clicked for commission ID:', commissionId);
                
                if (confirm('Mark this commission as paid?')) {
                    const url = `{{ route('referrals.mark-commission-paid', '__ID__') }}`.replace('__ID__', commissionId);
                    console.log('Sending POST to:', url);
                    
                    fetch(url, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({})
                    })
                    .then(response => {
                        console.log('Response status:', response.status);
                        return response.json();
                    })
                    .then(data => {
                        console.log('Response data:', data);
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: data.message,
                                confirmButtonClass: 'btn btn-primary'
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: data.message || 'Failed to update commission status',
                                confirmButtonClass: 'btn btn-primary'
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Fetch error:', error);
                        Swal.fire('Error', 'Failed to update commission status. Check console for details.', 'error');
                    });
                }
            });
        });
    });
</script>
@endsection
