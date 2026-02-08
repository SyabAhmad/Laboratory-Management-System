@extends('Layout.master')
@section('title', 'Expense Analysis')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('financial.dashboard') }}">Financial</a></li>
                        <li class="breadcrumb-item active">Expense Analysis</li>
                    </ol>
                </div>
                <h4 class="page-title">Expense Analysis</h4>
            </div>
        </div>
    </div>

    {{-- Month Selector --}}
    <div class="row mb-3">
        <div class="col-md-4">
            <form method="GET" action="{{ route('financial.expense-analysis') }}" class="d-flex align-items-center">
                <label class="mr-2 mb-0 font-weight-bold">Month:</label>
                <input type="month" name="month" value="{{ $month }}" class="form-control" onchange="this.form.submit()" style="max-width: 200px;">
            </form>
        </div>
        <div class="col-md-8 text-right">
            <a href="{{ route('expenses.create') }}" class="btn btn-primary-custom">
                <i class="fas fa-plus"></i> Add Expense
            </a>
        </div>
    </div>

    {{-- Total --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm" style="border-radius: 12px; border-left: 4px solid #ffc107;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Total Expenses This Month</h6>
                            <h2 class="mb-0 text-warning">Rs. {{ number_format($totalExpenses, 2) }}</h2>
                        </div>
                        <i class="fas fa-receipt fa-3x text-warning opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Charts --}}
    <div class="row mb-4">
        <div class="col-xl-7 mb-3">
            <div class="card shadow-sm" style="border-radius: 12px;">
                <div class="card-body">
                    <h5 class="card-title mb-3"><i class="fas fa-chart-bar text-warning mr-2"></i>Daily Expenses</h5>
                    <canvas id="dailyExpenseChart" height="100"></canvas>
                </div>
            </div>
        </div>
        <div class="col-xl-5 mb-3">
            <div class="card shadow-sm" style="border-radius: 12px;">
                <div class="card-body">
                    <h5 class="card-title mb-3"><i class="fas fa-chart-pie text-warning mr-2"></i>By Category</h5>
                    <canvas id="categoryChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Category Breakdown --}}
    <div class="row mb-4">
        <div class="col-xl-6 mb-3">
            <div class="card shadow-sm" style="border-radius: 12px;">
                <div class="card-body">
                    <h5 class="card-title mb-3"><i class="fas fa-tags text-warning mr-2"></i>Expense Categories</h5>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover mb-0">
                            <thead class="thead-light">
                                <tr><th>Category</th><th>Count</th><th>Total</th><th>% of Total</th></tr>
                            </thead>
                            <tbody>
                                @forelse($byCategory as $cat)
                                <tr>
                                    <td class="text-capitalize">{{ $cat->category }}</td>
                                    <td>{{ $cat->count }}</td>
                                    <td>Rs. {{ number_format($cat->total, 2) }}</td>
                                    <td>
                                        @php $pct = $totalExpenses > 0 ? round($cat->total / $totalExpenses * 100, 1) : 0; @endphp
                                        <div class="progress" style="height: 18px;">
                                            <div class="progress-bar bg-warning" style="width: {{ $pct }}%">{{ $pct }}%</div>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="4" class="text-center text-muted">No expenses</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-6 mb-3">
            <div class="card shadow-sm" style="border-radius: 12px;">
                <div class="card-body">
                    <h5 class="card-title mb-3"><i class="fas fa-history text-info mr-2"></i>6-Month Comparison</h5>
                    <canvas id="monthlyCompChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Expense List --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm" style="border-radius: 12px;">
                <div class="card-body">
                    <h5 class="card-title mb-3"><i class="fas fa-list text-warning mr-2"></i>All Expenses</h5>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover mb-0" id="expense-list-table">
                            <thead class="thead-light">
                                <tr>
                                    <th>#</th>
                                    <th>Date</th>
                                    <th>Purpose</th>
                                    <th>Category</th>
                                    <th>Amount</th>
                                    <th>Note</th>
                                    <th>Recorded By</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($expenses as $i => $exp)
                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td>{{ $exp->expense_date->format('d M, Y') }}</td>
                                    <td>{{ $exp->purpose }}</td>
                                    <td><span class="badge badge-soft-warning text-capitalize">{{ $exp->category ?? 'general' }}</span></td>
                                    <td>Rs. {{ number_format($exp->amount, 2) }}</td>
                                    <td>{{ $exp->note ?? '-' }}</td>
                                    <td>{{ $exp->user->name ?? '-' }}</td>
                                </tr>
                                @empty
                                <tr><td colspan="7" class="text-center text-muted">No expenses recorded</td></tr>
                                @endforelse
                            </tbody>
                        </table>
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
    // Daily Expenses
    var dailyData = @json($dailyExpenses);
    new Chart(document.getElementById('dailyExpenseChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: dailyData.map(d => d.date),
            datasets: [{
                label: 'Expenses',
                data: dailyData.map(d => d.total),
                backgroundColor: 'rgba(255,193,7,0.6)',
                borderColor: '#ffc107',
                borderWidth: 1,
            }]
        },
        options: { responsive: true, scales: { y: { beginAtZero: true } }, plugins: { legend: { display: false } } }
    });

    // Category Pie
    var catData = @json($byCategory);
    new Chart(document.getElementById('categoryChart').getContext('2d'), {
        type: 'doughnut',
        data: {
            labels: catData.map(c => c.category.charAt(0).toUpperCase() + c.category.slice(1)),
            datasets: [{
                data: catData.map(c => c.total),
                backgroundColor: ['#ffc107', '#dc3545', '#007bff', '#28a745', '#6f42c1', '#20c997', '#fd7e14', '#e83e8c'],
            }]
        },
        options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
    });

    // Monthly Comparison
    var monthlyData = @json($monthlyComparison);
    new Chart(document.getElementById('monthlyCompChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: monthlyData.map(m => m.month),
            datasets: [{
                label: 'Total Expenses',
                data: monthlyData.map(m => m.total),
                backgroundColor: 'rgba(0,123,255,0.6)',
            }]
        },
        options: { responsive: true, scales: { y: { beginAtZero: true } }, plugins: { legend: { display: false } } }
    });
});
</script>
@endsection
