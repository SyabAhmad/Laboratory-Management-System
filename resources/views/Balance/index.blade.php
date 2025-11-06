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
                                        <th><i class="fas fa-user"></i> Patient</th>
                                        <th><i class="fas fa-hashtag"></i> Reference</th>
                                        <th><i class="fas fa-tag"></i> Type</th>
                                        <th><i class="fas fa-dollar-sign"></i> Amount</th>
                                        <th><i class="fas fa-sticky-note"></i> Note</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($transactions as $t)
                                        <tr style="background-color: {{ 
                                            $t->type === 'Commission' ? 'rgba(220, 53, 69, 0.05)' : 
                                            ($t->type === 'Payment' ? 'rgba(40, 167, 69, 0.05)' : 'rgba(255, 193, 7, 0.05)')
                                        }};">
                                            <td>{{ \Carbon\Carbon::parse($t->created_at)->format('M d, Y') }}</td>
                                            <td><strong>{{ $t->patient_name ?? '-' }}</strong></td>
                                            <td>{{ $t->reference ?? '-' }}</td>
                                            <td>
                                                @if($t->type === 'Commission')
                                                    <span class="badge badge-danger" style="font-size: 12px;">
                                                        <i class="fas fa-arrow-down"></i> Commission Paid
                                                    </span>
                                                @elseif($t->type === 'Payment')
                                                    <span class="badge badge-success" style="font-size: 12px;">
                                                        <i class="fas fa-arrow-up"></i> Payment Received
                                                    </span>
                                                @else
                                                    <span class="badge" style="background-color: #ffc107; color: #000; font-size: 12px;">
                                                        <i class="fas fa-file-invoice"></i> Bill Created
                                                    </span>
                                                @endif
                                            </td>
                                            <td style="font-weight: bold; font-size: 15px; {{ 
                                                $t->type === 'Commission' ? 'color: #dc3545;' : 
                                                ($t->type === 'Payment' ? 'color: #28a745;' : 'color: #ffc107;')
                                            }}">
                                                {{ 
                                                    $t->type === 'Commission' ? '-' : 
                                                    ($t->type === 'Payment' ? '+' : '')
                                                }}{{ number_format($t->amount, 2) }} PKR
                                            </td>
                                            <td>
                                                <small>{{ $t->note ?? '-' }}</small>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-4">
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
