@extends('layouts.app')

@section('content')
<div class="container-fluid mt-4">
    <div class="row mb-4">
        <div class="col-md-12">
            <h2 class="text-primary">
                <i class="fas fa-receipt"></i> Patient Registration Receipt
            </h2>
            <hr>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <h5 class="card-title">Receipt Number</h5>
                    <div style="font-size: 24px; font-weight: bold; color: #8d2d36; font-family: 'Courier New', monospace;">
                        {{ $receipt->getFormattedReceiptNumber() }}
                    </div>
                    <small class="text-muted">Token / Barcode</small>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-body">
                    <h5 class="card-title">Status</h5>
                    <div>
                        @if($receipt->status === 'paid')
                            <span class="badge badge-success">Paid</span>
                        @elseif($receipt->status === 'printed')
                            <span class="badge badge-info">Printed</span>
                        @else
                            <span class="badge badge-warning">Draft</span>
                        @endif
                    </div>
                    <small class="text-muted d-block mt-2">{{ $receipt->created_at->format('d-M-Y H:i A') }}</small>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Patient Details</h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <td><strong>Patient ID:</strong></td>
                            <td>{{ $patient->patient_id }}</td>
                        </tr>
                        <tr>
                            <td><strong>Name:</strong></td>
                            <td>{{ $patient->name }}</td>
                        </tr>
                        <tr>
                            <td><strong>Age:</strong></td>
                            <td>{{ $patient->age }} Years</td>
                        </tr>
                        <tr>
                            <td><strong>Gender:</strong></td>
                            <td>{{ ucfirst($patient->gender) }}</td>
                        </tr>
                        <tr>
                            <td><strong>Contact:</strong></td>
                            <td>{{ $patient->mobile_phone }}</td>
                        </tr>
                        <tr>
                            <td><strong>Address:</strong></td>
                            <td>{{ $patient->address ?? 'N/A' }}</td>
                        </tr>
                        @if($patient->referred_by)
                            <tr>
                                <td><strong>Referred By:</strong></td>
                                <td>{{ $patient->referred_by }}</td>
                            </tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0">Total Amount</h5>
                </div>
                <div class="card-body text-center">
                    <div style="font-size: 32px; font-weight: bold; color: #8d2d36;">
                        Rs. {{ number_format($receipt->total_amount, 2) }}
                    </div>
                    <small class="text-muted">Amount Payable</small>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">Tests Count</h5>
                </div>
                <div class="card-body text-center">
                    <div style="font-size: 24px; font-weight: bold;">
                        {{ $receipt->getTestCount() }}
                    </div>
                    <small class="text-muted">Total Tests</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Tests Details -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-flask"></i> Registered Tests
                    </h5>
                </div>
                <div class="card-body">
                    @if($receipt->tests && count($receipt->tests) > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="bg-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Test Name</th>
                                        <th style="text-align: right;">Price</th>
                                        <th style="text-align: center;">Discount</th>
                                        <th style="text-align: center;">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($receipt->tests as $index => $test)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $test['test_name'] ?? 'N/A' }}</td>
                                            <td style="text-align: right;">
                                                <strong>Rs. {{ number_format($test['price'] ?? 0, 2) }}</strong>
                                            </td>
                                            <td style="text-align: center;">
                                                Rs. {{ number_format($test['discount'] ?? 0, 2) }}
                                            </td>
                                            <td style="text-align: center;">
                                                @if(strtolower($test['paid_status'] ?? 'unpaid') === 'paid')
                                                    <span class="badge badge-success">
                                                        <i class="fas fa-check-circle"></i> Paid
                                                    </span>
                                                @else
                                                    <span class="badge badge-danger">
                                                        <i class="fas fa-times-circle"></i> Unpaid
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="bg-light">
                                        <td colspan="2"><strong>Total</strong></td>
                                        <td style="text-align: right;">
                                            <strong>Rs. {{ number_format($receipt->total_amount, 2) }}</strong>
                                        </td>
                                        <td colspan="2"></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> No tests registered
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Print Receipt -->
    <div class="row mt-4">
        <div class="col-md-12 text-center">
            <a href="{{ route('patients.print-receipt', $receipt->id) }}" target="_blank" class="btn btn-primary btn-lg">
                <i class="fas fa-print"></i> Print Receipt
            </a>
            <a href="{{ route('patients.list') }}" class="btn btn-secondary btn-lg ml-2">
                <i class="fas fa-arrow-left"></i> Back to Patients
            </a>
        </div>
    </div>
</div>

<style>
    .card {
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        border: none;
    }

    .card-header {
        font-weight: 600;
        letter-spacing: 0.5px;
    }

    .badge {
        font-size: 12px;
        padding: 6px 12px;
    }
</style>
@endsection
