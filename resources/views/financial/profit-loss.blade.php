@extends('Layout.master')
@section('title', 'Profit & Loss Statement')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('financial.dashboard') }}">Financial</a></li>
                        <li class="breadcrumb-item active">Profit & Loss</li>
                    </ol>
                </div>
                <h4 class="page-title">Profit & Loss Statement</h4>
            </div>
        </div>
    </div>

    {{-- Month Selector --}}
    <div class="row mb-3">
        <div class="col-md-4">
            <form method="GET" action="{{ route('financial.profit-loss') }}" class="d-flex align-items-center">
                <label class="mr-2 mb-0 font-weight-bold">Month:</label>
                <input type="month" name="month" value="{{ $month }}" class="form-control" onchange="this.form.submit()" style="max-width: 200px;">
            </form>
        </div>
    </div>

    {{-- P&L Summary --}}
    <div class="row mb-4">
        <div class="col-xl-6 mb-3">
            <div class="card shadow-sm" style="border-radius: 12px;">
                <div class="card-body">
                    <h5 class="card-title mb-4 text-success"><i class="fas fa-arrow-circle-up mr-2"></i>INCOME</h5>
                    <table class="table table-borderless mb-0">
                        <tr>
                            <td>Gross Revenue (Total Bills)</td>
                            <td class="text-right"><strong>Rs. {{ number_format($totalRevenue, 2) }}</strong></td>
                        </tr>
                        <tr>
                            <td>Less: Discounts Given</td>
                            <td class="text-right text-danger">- Rs. {{ number_format($totalDiscount, 2) }}</td>
                        </tr>
                        <tr class="border-top">
                            <td><strong>Gross Revenue After Discount</strong></td>
                            <td class="text-right"><strong>Rs. {{ number_format($grossProfit, 2) }}</strong></td>
                        </tr>
                        <tr>
                            <td>Amount Collected</td>
                            <td class="text-right text-success">Rs. {{ number_format($totalCollected, 2) }}</td>
                        </tr>
                        <tr>
                            <td>Amount Due (Outstanding)</td>
                            <td class="text-right text-warning">Rs. {{ number_format($totalDue, 2) }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-xl-6 mb-3">
            <div class="card shadow-sm" style="border-radius: 12px;">
                <div class="card-body">
                    <h5 class="card-title mb-4 text-danger"><i class="fas fa-arrow-circle-down mr-2"></i>EXPENDITURE</h5>
                    <table class="table table-borderless mb-0">
                        <tr>
                            <td>Operating Expenses</td>
                            <td class="text-right">Rs. {{ number_format($totalExpenses, 2) }}</td>
                        </tr>
                        <tr>
                            <td>Salaries & Wages (Paid)</td>
                            <td class="text-right">Rs. {{ number_format($totalSalaries, 2) }}</td>
                        </tr>
                        <tr>
                            <td>Doctor Commissions</td>
                            <td class="text-right">Rs. {{ number_format($totalDoctorCommissions, 2) }}</td>
                        </tr>
                        <tr>
                            <td>Referral Commissions</td>
                            <td class="text-right">Rs. {{ number_format($totalReferralCommissions, 2) }}</td>
                        </tr>
                        <tr class="border-top">
                            <td><strong>Total Expenditure</strong></td>
                            <td class="text-right"><strong class="text-danger">Rs. {{ number_format($totalOutgoing, 2) }}</strong></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Net Profit Banner --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm" style="border-radius: 12px; border: 2px solid {{ $netProfit >= 0 ? '#28a745' : '#dc3545' }};">
                <div class="card-body text-center py-4">
                    <h6 class="text-muted mb-2">NET PROFIT / LOSS</h6>
                    <h1 class="{{ $netProfit >= 0 ? 'text-success' : 'text-danger' }}">
                        <i class="fas {{ $netProfit >= 0 ? 'fa-arrow-up' : 'fa-arrow-down' }} mr-2"></i>
                        Rs. {{ number_format(abs($netProfit), 2) }}
                        <small class="h5">({{ $netProfit >= 0 ? 'Profit' : 'Loss' }})</small>
                    </h1>
                    <p class="text-muted mb-0">Collected (Rs. {{ number_format($totalCollected, 2) }}) - Expenditure (Rs. {{ number_format($totalOutgoing, 2) }})</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Expense Breakdown & P&L Trend --}}
    <div class="row mb-4">
        <div class="col-xl-5 mb-3">
            <div class="card shadow-sm" style="border-radius: 12px;">
                <div class="card-body">
                    <h5 class="card-title mb-3"><i class="fas fa-tags text-warning mr-2"></i>Expense Breakdown</h5>
                    @forelse($expenseBreakdown as $cat)
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-capitalize">{{ $cat->category }}</span>
                        <strong>Rs. {{ number_format($cat->total, 2) }}</strong>
                    </div>
                    <div class="progress mb-3" style="height: 6px;">
                        <div class="progress-bar bg-warning" style="width: {{ $totalExpenses > 0 ? ($cat->total / $totalExpenses * 100) : 0 }}%"></div>
                    </div>
                    @empty
                    <p class="text-muted text-center">No expenses</p>
                    @endforelse
                </div>
            </div>
        </div>
        <div class="col-xl-7 mb-3">
            <div class="card shadow-sm" style="border-radius: 12px;">
                <div class="card-body">
                    <h5 class="card-title mb-3"><i class="fas fa-chart-area text-primary mr-2"></i>6-Month P&L Trend</h5>
                    <canvas id="plChart" height="120"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var plData = @json($plTrends);
    new Chart(document.getElementById('plChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: plData.map(d => d.month),
            datasets: [
                {
                    label: 'Income',
                    data: plData.map(d => d.income),
                    backgroundColor: 'rgba(40,167,69,0.7)',
                },
                {
                    label: 'Expenditure',
                    data: plData.map(d => d.outgoing),
                    backgroundColor: 'rgba(220,53,69,0.7)',
                },
                {
                    label: 'Net Profit',
                    data: plData.map(d => d.profit),
                    type: 'line',
                    borderColor: '#007bff',
                    backgroundColor: 'rgba(0,123,255,0.1)',
                    fill: true,
                    tension: 0.3,
                }
            ]
        },
        options: {
            responsive: true,
            plugins: { legend: { position: 'bottom' } },
            scales: { y: { beginAtZero: true } }
        }
    });
});
</script>
@endsection
