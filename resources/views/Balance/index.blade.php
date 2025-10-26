@extends('Layout.master')
@section('title', 'Balance Overview')

@section('content')

    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">BKLT</a></li>
                            <li class="breadcrumb-item active">Balance</li>
                        </ol>
                    </div>
                    <h4 class="page-title">Balance Overview</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->


        <div class="row">
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card dashboard-card balance-card-accent shadow-sm">
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <i class="fas fa-balance-scale fa-3x text-primary"></i>
                        </div>
                        <h5 class="dashboard-card-title text-uppercase font-weight-bold">Outstanding Balance</h5>
                        <p class="dashboard-card-text display-4 text-danger font-weight-bold">{{ isset($outstanding) ? number_format($outstanding, 2) : '0.00' }}</p>
                        <div class="mt-4">
                            <div class="row">
                                <div class="col-6">
                                    <small class="text-muted">Total Billed</small>
                                    <p class="mb-0 text-success">{{ isset($totalBilled) ? number_format($totalBilled,2) : '0.00' }}</p>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted">Total Paid</small>
                                    <p class="mb-0 text-info">{{ isset($totalPaid) ? number_format($totalPaid,2) : '0.00' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-8 col-md-6">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="mb-4 text-primary">
                            <i class="fas fa-history mr-2"></i>Recent Transactions
                        </h5>
                        <div class="table-responsive">
                            <table class="table table-hover table-striped">
                                <thead class="thead-light">
                                    <tr>
                                        <th><i class="fas fa-calendar-alt"></i> Date</th>
                                        <th><i class="fas fa-hashtag"></i> Reference</th>
                                        <th><i class="fas fa-dollar-sign"></i> Amount</th>
                                        <th><i class="fas fa-sticky-note"></i> Note</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($transactions as $t)
                                        <tr>
                                            <td>{{ \Carbon\Carbon::parse($t->created_at)->format('M d, Y') }}</td>
                                            <td>{{ $t->reference ?? $t->id }}</td>
                                            <td class="font-weight-bold">{{ number_format($t->amount, 2) }}</td>
                                            <td>{{ $t->note ?? '-' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-4">
                                                <i class="fas fa-inbox fa-2x text-muted mb-2"></i>
                                                <p class="text-muted">No transactions found.</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div> <!-- container -->

@endsection
