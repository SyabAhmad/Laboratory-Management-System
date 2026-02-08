@extends('Layout.master')
@section('title', 'Monthly Financial Report')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0 d-print-none">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('financial.dashboard') }}">Financial</a></li>
                        <li class="breadcrumb-item active">Monthly Report</li>
                    </ol>
                </div>
                <h4 class="page-title">Monthly Financial Report</h4>
            </div>
        </div>
    </div>

    {{-- Month Selector & Print --}}
    <div class="row mb-3 d-print-none">
        <div class="col-md-4">
            <form method="GET" action="{{ route('financial.monthly-report') }}" class="d-flex align-items-center">
                <label class="mr-2 mb-0 font-weight-bold">Month:</label>
                <input type="month" name="month" value="{{ $month }}" class="form-control" onchange="this.form.submit()" style="max-width: 200px;">
            </form>
        </div>
        <div class="col-md-8 text-right">
            <button class="btn btn-primary" onclick="window.print()">
                <i class="fas fa-print"></i> Print Report
            </button>
        </div>
    </div>

    {{-- Report Header --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm" style="border-radius: 12px; border: 2px solid #007bff;">
                <div class="card-body text-center py-4">
                    <h2 class="mb-1">Monthly Financial Report</h2>
                    <h4 class="text-muted mb-0">{{ \Carbon\Carbon::parse($month . '-01')->format('F Y') }}</h4>
                    <small class="text-muted">{{ $startDate->format('d M, Y') }} - {{ $endDate->format('d M, Y') }}</small>
                </div>
            </div>
        </div>
    </div>

    {{-- Key Metrics --}}
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card shadow-sm h-100" style="border-radius: 12px; border-left: 4px solid #28a745;">
                <div class="card-body text-center">
                    <h6 class="text-muted">Total Revenue</h6>
                    <h3 class="text-success">Rs. {{ number_format($totalRevenue, 2) }}</h3>
                    <small>{{ $billCount }} bills | {{ $patientCount }} patients</small>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card shadow-sm h-100" style="border-radius: 12px; border-left: 4px solid #007bff;">
                <div class="card-body text-center">
                    <h6 class="text-muted">Collected</h6>
                    <h3 class="text-primary">Rs. {{ number_format($totalCollected, 2) }}</h3>
                    <small>Due: Rs. {{ number_format($totalDue, 2) }}</small>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card shadow-sm h-100" style="border-radius: 12px; border-left: 4px solid #dc3545;">
                <div class="card-body text-center">
                    <h6 class="text-muted">Total Outgoing</h6>
                    <h3 class="text-danger">Rs. {{ number_format($totalOutgoing, 2) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card shadow-sm h-100" style="border-radius: 12px; border-left: 4px solid {{ $netProfit >= 0 ? '#28a745' : '#dc3545' }};">
                <div class="card-body text-center">
                    <h6 class="text-muted">Net Profit/Loss</h6>
                    <h3 class="{{ $netProfit >= 0 ? 'text-success' : 'text-danger' }}">Rs. {{ number_format(abs($netProfit), 2) }}</h3>
                    <small>{{ $netProfit >= 0 ? 'Profit' : 'Loss' }}</small>
                </div>
            </div>
        </div>
    </div>

    {{-- Detailed Income Section --}}
    <div class="row mb-4">
        <div class="col-xl-6 mb-3">
            <div class="card shadow-sm" style="border-radius: 12px;">
                <div class="card-body">
                    <h5 class="card-title text-success"><i class="fas fa-arrow-circle-up mr-2"></i>Income Details</h5>
                    <table class="table mb-0">
                        <tr><td>Total Bills Generated</td><td class="text-right">{{ $billCount }}</td></tr>
                        <tr><td>Total Patients</td><td class="text-right">{{ $patientCount }}</td></tr>
                        <tr><td>Gross Revenue</td><td class="text-right">Rs. {{ number_format($totalRevenue, 2) }}</td></tr>
                        <tr><td>Discounts Given</td><td class="text-right text-danger">- Rs. {{ number_format($totalDiscount, 2) }}</td></tr>
                        <tr><td>Amount Collected</td><td class="text-right text-success">Rs. {{ number_format($totalCollected, 2) }}</td></tr>
                        <tr><td>Outstanding Dues</td><td class="text-right text-warning">Rs. {{ number_format($totalDue, 2) }}</td></tr>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-xl-6 mb-3">
            <div class="card shadow-sm" style="border-radius: 12px;">
                <div class="card-body">
                    <h5 class="card-title text-danger"><i class="fas fa-arrow-circle-down mr-2"></i>Expenditure Details</h5>
                    <table class="table mb-0">
                        <tr><td>Operating Expenses</td><td class="text-right">Rs. {{ number_format($totalExpenses, 2) }}</td></tr>
                        <tr><td>Salaries (Total Payable)</td><td class="text-right">Rs. {{ number_format($totalSalaries, 2) }}</td></tr>
                        <tr><td>&nbsp;&nbsp;- Salaries Paid</td><td class="text-right text-success">Rs. {{ number_format($salariesPaid, 2) }}</td></tr>
                        <tr><td>&nbsp;&nbsp;- Salaries Pending</td><td class="text-right text-warning">Rs. {{ number_format($salariesPending, 2) }}</td></tr>
                        <tr><td>Doctor Commissions</td><td class="text-right">Rs. {{ number_format($totalDoctorCommissions, 2) }}</td></tr>
                        <tr><td>Referral Commissions</td><td class="text-right">Rs. {{ number_format($totalReferralCommissions, 2) }}</td></tr>
                        <tr class="border-top font-weight-bold">
                            <td>Total Expenditure</td>
                            <td class="text-right text-danger">Rs. {{ number_format($totalOutgoing, 2) }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Expense Breakdown --}}
    @if($expenseBreakdown->count() > 0)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm" style="border-radius: 12px;">
                <div class="card-body">
                    <h5 class="card-title"><i class="fas fa-tags text-warning mr-2"></i>Expense Breakdown by Category</h5>
                    <div class="table-responsive">
                        <table class="table table-striped mb-0">
                            <thead class="thead-light">
                                <tr><th>Category</th><th>Transactions</th><th>Total Amount</th><th>% of Expenses</th></tr>
                            </thead>
                            <tbody>
                                @foreach($expenseBreakdown as $cat)
                                <tr>
                                    <td class="text-capitalize">{{ $cat->category }}</td>
                                    <td>{{ $cat->count }}</td>
                                    <td>Rs. {{ number_format($cat->total, 2) }}</td>
                                    <td>{{ $totalExpenses > 0 ? number_format($cat->total / $totalExpenses * 100, 1) : 0 }}%</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Top Referrals --}}
    @if($topReferrals->count() > 0)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm" style="border-radius: 12px;">
                <div class="card-body">
                    <h5 class="card-title"><i class="fas fa-handshake mr-2" style="color: #20c997;"></i>Top Referrals by Commission</h5>
                    <div class="table-responsive">
                        <table class="table table-striped mb-0">
                            <thead class="thead-light">
                                <tr><th>#</th><th>Referral Name</th><th>Total Commission</th></tr>
                            </thead>
                            <tbody>
                                @foreach($topReferrals as $i => $ref)
                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td>{{ $ref->referral->name ?? 'Unknown' }}</td>
                                    <td>Rs. {{ number_format($ref->total, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Final Summary --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm" style="border-radius: 12px; {{ $netProfit >= 0 ? 'background: linear-gradient(135deg, rgba(40,167,69,0.05), rgba(40,167,69,0.02));' : 'background: linear-gradient(135deg, rgba(220,53,69,0.05), rgba(220,53,69,0.02));' }}">
                <div class="card-body text-center py-5">
                    <h4 class="text-muted mb-3">BOTTOM LINE - {{ \Carbon\Carbon::parse($month . '-01')->format('F Y') }}</h4>
                    <h1 class="{{ $netProfit >= 0 ? 'text-success' : 'text-danger' }}" style="font-size: 3rem;">
                        Rs. {{ number_format(abs($netProfit), 2) }}
                    </h1>
                    <h5 class="{{ $netProfit >= 0 ? 'text-success' : 'text-danger' }}">
                        <i class="fas {{ $netProfit >= 0 ? 'fa-thumbs-up' : 'fa-thumbs-down' }} mr-2"></i>
                        {{ $netProfit >= 0 ? 'NET PROFIT' : 'NET LOSS' }}
                    </h5>
                    <p class="text-muted mt-3 mb-0">
                        Generated on {{ now()->format('d M, Y h:i A') }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    .left-side-menu, .topnav, .page-title-right, .d-print-none,
    .navbar-custom, #topnav-menu-content, .button-menu-mobile {
        display: none !important;
    }
    .content-page {
        margin-left: 0 !important;
        padding: 0 !important;
    }
    .card {
        border: 1px solid #ddd !important;
        box-shadow: none !important;
    }
}
</style>
@endsection
