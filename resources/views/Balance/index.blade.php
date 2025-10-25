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
            <div class="col-md-4">
                <div class="card dashboard-card balance-card-accent">
                    <div class="card-body">
                        <h4 class="dashboard-card-title">Outstanding Balance</h4>
                        <p class="dashboard-card-text">{{ isset($outstanding) ? number_format($outstanding, 2) : '0.00' }}</p>
                        <div class="mt-3">
                            <small class="text-secondary">Total Billed: {{ isset($totalBilled) ? number_format($totalBilled,2) : '0.00' }}</small><br>
                            <small class="text-secondary">Total Paid: {{ isset($totalPaid) ? number_format($totalPaid,2) : '0.00' }}</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <h4 class="mb-3">Recent Transactions</h4>
                        <div class="table-responsive">
                            <table class="table table-sm table-striped">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Reference</th>
                                        <th>Amount</th>
                                        <th>Note</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($transactions as $t)
                                        <tr>
                                            <td>{{ $t->created_at }}</td>
                                            <td>{{ $t->reference ?? $t->id }}</td>
                                            <td>{{ number_format($t->amount, 2) }}</td>
                                            <td>{{ $t->note ?? '' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center">No transactions found.</td>
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
