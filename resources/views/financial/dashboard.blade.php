@extends('Layout.master')
@section('title', 'Financial Dashboard')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">Financial Dashboard</li>
                    </ol>
                </div>
                <h4 class="page-title">Financial Dashboard</h4>
            </div>
        </div>
    </div>

    {{-- Month Selector --}}
    <div class="row mb-3">
        <div class="col-md-4">
            <form method="GET" action="{{ route('financial.dashboard') }}" class="d-flex align-items-center">
                <label class="mr-2 mb-0 font-weight-bold">Month:</label>
                <input type="month" name="month" value="{{ $month }}" class="form-control" onchange="this.form.submit()" style="max-width: 200px;">
            </form>
        </div>
        <div class="col-md-8 text-right">
            <a href="{{ route('financial.monthly-report', ['month' => $month]) }}" class="btn btn-outline-primary">
                <i class="fas fa-print"></i> Monthly Report
            </a>
        </div>
    </div>

    {{-- Top Summary Cards --}}
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card shadow-sm h-100" style="border-radius: 12px; border-left: 4px solid #28a745;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Total Revenue</h6>
                            <h3 class="mb-0 text-success">Rs. {{ number_format($totalRevenue, 2) }}</h3>
                            <small class="text-muted">{{ $billCount }} bills</small>
                        </div>
                        <div><i class="fas fa-chart-line fa-2x text-success opacity-50"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card shadow-sm h-100" style="border-radius: 12px; border-left: 4px solid #007bff;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Collected</h6>
                            <h3 class="mb-0 text-primary">Rs. {{ number_format($totalCollected, 2) }}</h3>
                            <small class="text-danger">Due: Rs. {{ number_format($totalDue, 2) }}</small>
                        </div>
                        <div><i class="fas fa-hand-holding-usd fa-2x text-primary opacity-50"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card shadow-sm h-100" style="border-radius: 12px; border-left: 4px solid #dc3545;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Total Outgoing</h6>
                            <h3 class="mb-0 text-danger">Rs. {{ number_format($totalOutgoing, 2) }}</h3>
                            <small class="text-muted">Expenses + Salaries + Commissions</small>
                        </div>
                        <div><i class="fas fa-arrow-circle-down fa-2x text-danger opacity-50"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card shadow-sm h-100" style="border-radius: 12px; border-left: 4px solid {{ $netProfit >= 0 ? '#28a745' : '#dc3545' }};">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Net Profit</h6>
                            <h3 class="mb-0 {{ $netProfit >= 0 ? 'text-success' : 'text-danger' }}">Rs. {{ number_format($netProfit, 2) }}</h3>
                            <small class="text-muted">Collected - Outgoing</small>
                        </div>
                        <div><i class="fas fa-balance-scale fa-2x {{ $netProfit >= 0 ? 'text-success' : 'text-danger' }} opacity-50"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Detailed Breakdown Cards --}}
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card shadow-sm h-100" style="border-radius: 12px;">
                <div class="card-body text-center">
                    <i class="fas fa-money-bill-wave fa-2x text-warning mb-2"></i>
                    <h6 class="text-muted">Expenses</h6>
                    <h4>Rs. {{ number_format($totalExpenses, 2) }}</h4>
                    <a href="{{ route('financial.expense-analysis', ['month' => $month]) }}" class="btn btn-sm btn-outline-warning mt-2">Details</a>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card shadow-sm h-100" style="border-radius: 12px;">
                <div class="card-body text-center">
                    <i class="fas fa-users fa-2x text-info mb-2"></i>
                    <h6 class="text-muted">Salaries</h6>
                    <h4>Rs. {{ number_format($salaryStats['total_payable'] ?? 0, 2) }}</h4>
                    <small>Paid: Rs. {{ number_format($salaryStats['total_paid'] ?? 0, 2) }}</small>
                    <br>
                    <a href="{{ route('financial.wages', ['month' => $month]) }}" class="btn btn-sm btn-outline-info mt-2">Details</a>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card shadow-sm h-100" style="border-radius: 12px;">
                <div class="card-body text-center">
                    <i class="fas fa-user-md fa-2x text-purple mb-2" style="color: #6f42c1;"></i>
                    <h6 class="text-muted">Doctor Commissions</h6>
                    <h4>Rs. {{ number_format($commissionStats['total'] ?? 0, 2) }}</h4>
                    <small>Pending: Rs. {{ number_format($commissionStats['pending'] ?? 0, 2) }}</small>
                    <br>
                    <a href="{{ route('financial.doctor-commissions', ['month' => $month]) }}" class="btn btn-sm btn-outline-secondary mt-2">Details</a>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card shadow-sm h-100" style="border-radius: 12px;">
                <div class="card-body text-center">
                    <i class="fas fa-handshake fa-2x text-teal mb-2" style="color: #20c997;"></i>
                    <h6 class="text-muted">Referral Commissions</h6>
                    <h4>Rs. {{ number_format($referralCommissionTotal, 2) }}</h4>
                    <small>Pending: Rs. {{ number_format($referralCommissionPending, 2) }}</small>
                    <br>
                    <a href="{{ route('commissions.dashboard') }}" class="btn btn-sm btn-outline-success mt-2">Details</a>
                </div>
            </div>
        </div>
    </div>

    {{-- Charts Row --}}
    <div class="row mb-4">
        <div class="col-xl-8 mb-3">
            <div class="card shadow-sm" style="border-radius: 12px;">
                <div class="card-body">
                    <h5 class="card-title mb-3"><i class="fas fa-chart-area text-primary mr-2"></i>6-Month Revenue Trend</h5>
                    <canvas id="revenueChart" height="100"></canvas>
                </div>
            </div>
        </div>
        <div class="col-xl-4 mb-3">
            <div class="card shadow-sm" style="border-radius: 12px;">
                <div class="card-body">
                    <h5 class="card-title mb-3"><i class="fas fa-credit-card text-primary mr-2"></i>Payment Methods</h5>
                    <canvas id="paymentChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Daily Revenue Table --}}
    <div class="row mb-4">
        <div class="col-xl-8 mb-3">
            <div class="card shadow-sm" style="border-radius: 12px;">
                <div class="card-body">
                    <h5 class="card-title mb-3"><i class="fas fa-calendar-day text-primary mr-2"></i>Daily Revenue Breakdown</h5>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Revenue</th>
                                    <th>Collected</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($dailyRevenue as $day)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($day->date)->format('d M, Y (D)') }}</td>
                                    <td>Rs. {{ number_format($day->revenue, 2) }}</td>
                                    <td>Rs. {{ number_format($day->collected, 2) }}</td>
                                </tr>
                                @empty
                                <tr><td colspan="3" class="text-center text-muted">No data for this month</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 mb-3">
            <div class="card shadow-sm" style="border-radius: 12px;">
                <div class="card-body">
                    <h5 class="card-title mb-3"><i class="fas fa-tags text-warning mr-2"></i>Expenses by Category</h5>
                    @forelse($expensesByCategory as $cat)
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-capitalize">{{ $cat->category }}</span>
                        <strong>Rs. {{ number_format($cat->total, 2) }}</strong>
                    </div>
                    <div class="progress mb-3" style="height: 6px;">
                        <div class="progress-bar bg-warning" style="width: {{ $totalExpenses > 0 ? ($cat->total / $totalExpenses * 100) : 0 }}%"></div>
                    </div>
                    @empty
                    <p class="text-muted text-center">No expenses recorded</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Links --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm" style="border-radius: 12px;">
                <div class="card-body">
                    <h5 class="card-title mb-3"><i class="fas fa-link text-primary mr-2"></i>Quick Actions</h5>
                    <div class="row">
                        <div class="col-md-2 col-6 mb-2">
                            <a href="{{ route('financial.revenue', ['month' => $month]) }}" class="btn btn-outline-success btn-block">
                                <i class="fas fa-chart-line"></i><br>Revenue
                            </a>
                        </div>
                        <div class="col-md-2 col-6 mb-2">
                            <a href="{{ route('financial.expense-analysis', ['month' => $month]) }}" class="btn btn-outline-warning btn-block">
                                <i class="fas fa-money-bill-wave"></i><br>Expenses
                            </a>
                        </div>
                        <div class="col-md-2 col-6 mb-2">
                            <a href="{{ route('financial.doctor-commissions', ['month' => $month]) }}" class="btn btn-outline-info btn-block">
                                <i class="fas fa-user-md"></i><br>Commissions
                            </a>
                        </div>
                        <div class="col-md-2 col-6 mb-2">
                            <a href="{{ route('financial.wages', ['month' => $month]) }}" class="btn btn-outline-primary btn-block">
                                <i class="fas fa-hand-holding-usd"></i><br>Wages
                            </a>
                        </div>
                        <div class="col-md-2 col-6 mb-2">
                            <a href="{{ route('financial.profit-loss', ['month' => $month]) }}" class="btn btn-outline-danger btn-block">
                                <i class="fas fa-balance-scale"></i><br>P&L
                            </a>
                        </div>
                        <div class="col-md-2 col-6 mb-2">
                            <a href="{{ route('financial.monthly-report', ['month' => $month]) }}" class="btn btn-outline-secondary btn-block">
                                <i class="fas fa-file-invoice-dollar"></i><br>Report
                            </a>
                        </div>
                    </div>
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
    // Revenue Trend Chart
    var ctx1 = document.getElementById('revenueChart').getContext('2d');
    new Chart(ctx1, {
        type: 'line',
        data: {
            labels: {!! json_encode(collect($monthlyTrends)->pluck('month')) !!},
            datasets: [
                {
                    label: 'Revenue',
                    data: {!! json_encode(collect($monthlyTrends)->pluck('revenue')) !!},
                    borderColor: '#28a745',
                    backgroundColor: 'rgba(40,167,69,0.1)',
                    fill: true,
                    tension: 0.3,
                },
                {
                    label: 'Collected',
                    data: {!! json_encode(collect($monthlyTrends)->pluck('collected')) !!},
                    borderColor: '#007bff',
                    backgroundColor: 'rgba(0,123,255,0.1)',
                    fill: true,
                    tension: 0.3,
                },
                {
                    label: 'Expenses',
                    data: {!! json_encode(collect($monthlyTrends)->pluck('expenses')) !!},
                    borderColor: '#dc3545',
                    backgroundColor: 'rgba(220,53,69,0.1)',
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

    // Payment Methods Chart
    var ctx2 = document.getElementById('paymentChart').getContext('2d');
    var paymentData = @json($paymentMethods);
    new Chart(ctx2, {
        type: 'doughnut',
        data: {
            labels: paymentData.map(p => (p.payment_type || 'Cash').charAt(0).toUpperCase() + (p.payment_type || 'cash').slice(1)),
            datasets: [{
                data: paymentData.map(p => p.total),
                backgroundColor: ['#007bff', '#28a745', '#ffc107', '#dc3545', '#6f42c1', '#20c997'],
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { position: 'bottom' } }
        }
    });
});
</script>
@endsection
