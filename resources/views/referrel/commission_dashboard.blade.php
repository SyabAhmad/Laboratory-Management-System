@extends('Layout.master')
@section('title', 'Commission Dashboard')
@section('content')

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">Commission Management Dashboard</h4>
            </div>
        </div>
    </div>

    <!-- Overall Statistics -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <h6 class="text-uppercase mb-3">Total Earned</h6>
                    <h2 class="mb-0">PKR {{ number_format($stats['total_earned'], 2) }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body text-center">
                    <h6 class="text-uppercase mb-3">Pending Commissions</h6>
                    <h2 class="mb-0">PKR {{ number_format($stats['pending'], 2) }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <h6 class="text-uppercase mb-3">Paid Commissions</h6>
                    <h2 class="mb-0">PKR {{ number_format($stats['paid'], 2) }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    <h6 class="text-uppercase mb-3">Total Transactions</h6>
                    <h2 class="mb-0">{{ $stats['total_commissions'] }}</h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Referrals by Commission -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Top 10 Referrals by Total Commission</h5>
                </div>
                <div class="card-body">
                    @if($topReferrals->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Rank</th>
                                        <th>Referral Name</th>
                                        <th>Commission Rate</th>
                                        <th>Total Commissions</th>
                                        <th>Transactions</th>
                                        <th>Email</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($topReferrals as $idx => $referral)
                                        <tr>
                                            <td>
                                                <span class="badge bg-primary">{{ $idx + 1 }}</span>
                                            </td>
                                            <td>
                                                <strong>{{ $referral->name }}</strong>
                                            </td>
                                            <td>
                                                <span class="badge bg-info">{{ $referral->commission_percentage }}%</span>
                                            </td>
                                            <td>
                                                <strong>PKR {{ number_format($referral->commissions->sum('commission_amount'), 2) }}</strong>
                                            </td>
                                            <td>
                                                {{ $referral->commissions_count }}
                                            </td>
                                            <td>{{ $referral->email ?? 'N/A' }}</td>
                                            <td>
                                                <a href="{{ route('referrals.commissions', $referral->id) }}" class="btn btn-sm btn-info">
                                                    <i class="fas fa-chart-pie"></i> View Details
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-inbox fa-4x text-muted mb-4"></i>
                            <h4 class="text-muted">No Commission Data Available</h4>
                            <p class="text-muted">Commissions will appear here once referrals create bills.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- All Referrals with Commission Tracking -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">All Referrals - Commission Overview</h5>
                </div>
                <div class="card-body">
                    @if($referrals->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover" id="referralsTable">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Commission Rate</th>
                                        <th>Total Earned</th>
                                        <th>Pending</th>
                                        <th>Paid</th>
                                        <th>Commissions</th>
                                        <th>Phone</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($referrals as $referral)
                                        @php
                                            $commissionStats = App\Models\ReferralCommission::getCommissionStats($referral->id);
                                        @endphp
                                        <tr>
                                            <td>
                                                <strong>{{ $referral->name }}</strong>
                                            </td>
                                            <td>
                                                <span class="badge bg-primary">{{ $referral->commission_percentage }}%</span>
                                            </td>
                                            <td>
                                                <strong>PKR {{ number_format($commissionStats['total_earned'], 2) }}</strong>
                                            </td>
                                            <td>
                                                <span class="text-warning">PKR {{ number_format($commissionStats['pending'], 2) }}</span>
                                            </td>
                                            <td>
                                                <span class="text-success">PKR {{ number_format($commissionStats['paid'], 2) }}</span>
                                            </td>
                                            <td>
                                                {{ $commissionStats['count'] }}
                                            </td>
                                            <td>{{ $referral->phone ?? 'N/A' }}</td>
                                            <td>
                                                <a href="{{ route('referrals.commissions', $referral->id) }}" class="btn btn-sm btn-info" title="View Commission Details">
                                                    <i class="fas fa-chart-line"></i> View
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-user-tie fa-4x text-muted mb-4"></i>
                            <h4 class="text-muted">No Referrals Available</h4>
                            <p class="text-muted">Start by adding referrals to the system.</p>
                            <a href="{{ route('referrals.create') }}" class="btn btn-primary-custom">
                                <i class="fas fa-plus"></i> Add Referral
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
