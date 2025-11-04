@extends('Layout.master')
@section('title', 'Monthly Commission Report - {{ $referral->name }}')
@section('content')

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('referrels.list') }}">Referrals</a></li>
                        <li class="breadcrumb-item active">{{ $referral->name }} - Monthly Report</li>
                    </ol>
                </div>
                <h4 class="page-title">Monthly Commission Report</h4>
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
                    <h6 class="text-muted text-uppercase mb-3">Total Months</h6>
                    <h2 class="mb-0 text-info">{{ count($monthlyData) }}</h2>
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
                        <a href="{{ route('referrals.commissions', $referral->id) }}" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-list"></i> View Individual Records
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

    <!-- Monthly Summary Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Monthly Commission Summary</h5>
                </div>
                <div class="card-body">
                    @if(count($monthlyData) > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Month</th>
                                        <th>Total Commission</th>
                                        <th>Pending</th>
                                        <th>Paid</th>
                                        <th>Partial/Status</th>
                                        <th>Details</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($monthlyData as $month)
                                        <tr>
                                            <td>
                                                <strong>{{ $month['month_label'] }}</strong>
                                            </td>
                                            <td>
                                                <strong class="text-primary">PKR {{ number_format($month['total_amount'], 2) }}</strong>
                                            </td>
                                            <td>
                                                @if($month['pending_amount'] > 0)
                                                    <span class="badge bg-warning">
                                                        PKR {{ number_format($month['pending_amount'], 2) }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($month['paid_amount'] > 0)
                                                    <span class="badge bg-success">
                                                        PKR {{ number_format($month['paid_amount'], 2) }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($month['status'] === 'pending')
                                                    <span class="badge bg-warning text-dark">{{ $month['pending_count'] }} Pending</span>
                                                @elseif($month['status'] === 'paid')
                                                    <span class="badge bg-success">All Paid</span>
                                                @elseif($month['status'] === 'partial')
                                                    <span class="badge bg-info">{{ $month['pending_count'] }} Pending / {{ $month['paid_count'] }} Paid</span>
                                                @endif
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-outline-primary expand-details" 
                                                    data-month="{{ $month['month_key'] }}"
                                                    data-target="month-{{ str_replace('-', '', $month['month_key']) }}">
                                                    <i class="fas fa-chevron-down"></i> Show Invoices
                                                </button>
                                            </td>
                                            <td>
                                                @if($month['pending_count'] > 0)
                                                    <button type="button" class="btn btn-sm btn-success mark-month-paid" 
                                                        data-referral-id="{{ $referral->id }}"
                                                        data-month-key="{{ $month['month_key'] }}"
                                                        data-month-label="{{ $month['month_label'] }}"
                                                        data-total-amount="{{ $month['pending_amount'] }}">
                                                        <i class="fas fa-check-double"></i> Mark Paid
                                                    </button>
                                                @else
                                                    <span class="badge bg-success">Paid</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <!-- Expandable Details Row -->
                                        <tr class="details-row" id="month-{{ str_replace('-', '', $month['month_key']) }}" style="display: none;">
                                            <td colspan="7">
                                                <div class="p-3 bg-light">
                                                    <h6 class="mb-3">Commissions for {{ $month['month_label'] }}</h6>
                                                    <div class="table-responsive">
                                                        <table class="table table-sm table-bordered">
                                                            <thead>
                                                                <tr>
                                                                    <th>Date</th>
                                                                    <th>Patient</th>
                                                                    <th>Bill #</th>
                                                                    <th>Bill Amount</th>
                                                                    <th>Commission %</th>
                                                                    <th>Commission</th>
                                                                    <th>Status</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach($month['commissions'] as $commission)
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
                                                                        <td><strong>PKR {{ number_format($commission->commission_amount, 2) }}</strong></td>
                                                                        <td>
                                                                            @if($commission->status === 'pending')
                                                                                <span class="badge bg-warning">Pending</span>
                                                                            @elseif($commission->status === 'paid')
                                                                                <span class="badge bg-success">Paid</span>
                                                                            @else
                                                                                <span class="badge bg-danger">{{ ucfirst($commission->status) }}</span>
                                                                            @endif
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
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
        // Toggle details visibility
        document.querySelectorAll('.expand-details').forEach(btn => {
            btn.addEventListener('click', function() {
                const target = this.getAttribute('data-target');
                const row = document.getElementById(target);
                const icon = this.querySelector('i');
                
                if (row.style.display === 'none' || row.style.display === '') {
                    row.style.display = 'table-row';
                    icon.classList.remove('fa-chevron-down');
                    icon.classList.add('fa-chevron-up');
                    this.textContent = '';
                    this.innerHTML = '<i class="fas fa-chevron-up"></i> Hide Invoices';
                } else {
                    row.style.display = 'none';
                    icon.classList.remove('fa-chevron-up');
                    icon.classList.add('fa-chevron-down');
                    this.textContent = '';
                    this.innerHTML = '<i class="fas fa-chevron-down"></i> Show Invoices';
                }
            });
        });

        // Mark entire month as paid
        document.querySelectorAll('.mark-month-paid').forEach(btn => {
            btn.addEventListener('click', function() {
                const referralId = this.getAttribute('data-referral-id');
                const monthKey = this.getAttribute('data-month-key');
                const monthLabel = this.getAttribute('data-month-label');
                const totalAmount = this.getAttribute('data-total-amount');
                
                Swal.fire({
                    title: 'Mark Month as Paid?',
                    html: `<div style="text-align: left;">
                        <p><strong>Month:</strong> ${monthLabel}</p>
                        <p><strong>Total Amount:</strong> PKR ${parseFloat(totalAmount).toLocaleString('en-PK', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</p>
                    </div>`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonClass: 'btn btn-primary',
                    cancelButtonClass: 'btn btn-secondary',
                    confirmButtonText: 'Yes, Mark Paid',
                    cancelButtonText: 'Cancel'
                }).then(result => {
                    if (result.isConfirmed) {
                        const url = `{{ route('referrals.mark-month-paid', ['__REF__', '__MONTH__']) }}`
                            .replace('__REF__', referralId)
                            .replace('__MONTH__', monthKey);
                        
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
                            if (!response.ok) {
                                throw new Error(`HTTP error! status: ${response.status}`);
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success',
                                    html: `<p>${data.message}</p>
                                           <p><strong>Amount Marked:</strong> PKR ${parseFloat(data.total_amount).toLocaleString('en-PK', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</p>
                                           <p><strong>Commissions Updated:</strong> ${data.commission_count}</p>`,
                                    confirmButtonClass: 'btn btn-primary'
                                }).then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: data.message || 'Failed to update commissions',
                                    confirmButtonClass: 'btn btn-primary'
                                });
                            }
                        })
                        .catch(error => {
                            console.error('Fetch error:', error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Failed to update commissions. Check console for details.',
                                confirmButtonClass: 'btn btn-primary'
                            });
                        });
                    }
                });
            });
        });
    });
</script>

@endsection
