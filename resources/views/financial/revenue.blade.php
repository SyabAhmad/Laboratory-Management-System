@extends('Layout.master')
@section('title', 'Revenue Analysis')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('financial.dashboard') }}">Financial</a></li>
                        <li class="breadcrumb-item active">Revenue Analysis</li>
                    </ol>
                </div>
                <h4 class="page-title">Revenue Analysis</h4>
            </div>
        </div>
    </div>

    {{-- Month Selector --}}
    <div class="row mb-3">
        <div class="col-md-4">
            <form method="GET" action="{{ route('financial.revenue') }}" class="d-flex align-items-center">
                <label class="mr-2 mb-0 font-weight-bold">Month:</label>
                <input type="month" name="month" value="{{ $month }}" class="form-control" onchange="this.form.submit()" style="max-width: 200px;">
            </form>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="row mb-4">
        <div class="col-xl-4 col-md-6 mb-3">
            <div class="card shadow-sm h-100" style="border-radius: 12px; border-left: 4px solid #28a745;">
                <div class="card-body">
                    <h6 class="text-muted mb-1">Total Revenue</h6>
                    <h3 class="mb-0 text-success">Rs. {{ number_format($totalRevenue, 2) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6 mb-3">
            <div class="card shadow-sm h-100" style="border-radius: 12px; border-left: 4px solid #007bff;">
                <div class="card-body">
                    <h6 class="text-muted mb-1">Total Collected</h6>
                    <h3 class="mb-0 text-primary">Rs. {{ number_format($totalCollected, 2) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6 mb-3">
            <div class="card shadow-sm h-100" style="border-radius: 12px; border-left: 4px solid #dc3545;">
                <div class="card-body">
                    <h6 class="text-muted mb-1">Total Due</h6>
                    <h3 class="mb-0 text-danger">Rs. {{ number_format($totalDue, 2) }}</h3>
                </div>
            </div>
        </div>
    </div>

    {{-- Revenue by Status --}}
    <div class="row mb-4">
        <div class="col-xl-8 mb-3">
            <div class="card shadow-sm" style="border-radius: 12px;">
                <div class="card-body">
                    <h5 class="card-title mb-3"><i class="fas fa-chart-bar text-primary mr-2"></i>Daily Revenue</h5>
                    <canvas id="dailyRevenueChart" height="100"></canvas>
                </div>
            </div>
        </div>
        <div class="col-xl-4 mb-3">
            <div class="card shadow-sm" style="border-radius: 12px;">
                <div class="card-body">
                    <h5 class="card-title mb-3"><i class="fas fa-clipboard-list text-primary mr-2"></i>Revenue by Status</h5>
                    @forelse($revenueByStatus as $status)
                    <div class="d-flex justify-content-between align-items-center mb-3 p-2 rounded" style="background: rgba(0,0,0,0.03);">
                        <div>
                            <span class="badge badge-{{ $status->status == 'Paid' ? 'success' : ($status->status == 'Pending' ? 'warning' : 'danger') }} mr-2">
                                {{ $status->status ?? 'Unknown' }}
                            </span>
                            <small>{{ $status->count }} bills</small>
                        </div>
                        <strong>Rs. {{ number_format($status->total, 2) }}</strong>
                    </div>
                    @empty
                    <p class="text-muted text-center">No data</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- Top Tests by Revenue --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm" style="border-radius: 12px;">
                <div class="card-body">
                    <h5 class="card-title mb-3"><i class="fas fa-flask text-success mr-2"></i>Top Tests by Revenue</h5>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>#</th>
                                    <th>Test Name</th>
                                    <th>Times Ordered</th>
                                    <th>Total Revenue</th>
                                    <th>Avg. Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($testRevenue as $i => $test)
                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td>{{ $test['name'] }}</td>
                                    <td>{{ $test['count'] }}</td>
                                    <td>Rs. {{ number_format($test['total'], 2) }}</td>
                                    <td>Rs. {{ $test['count'] > 0 ? number_format($test['total'] / $test['count'], 2) : '0.00' }}</td>
                                </tr>
                                @empty
                                <tr><td colspan="5" class="text-center text-muted">No test data</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Daily Revenue Detail Table --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm" style="border-radius: 12px;">
                <div class="card-body">
                    <h5 class="card-title mb-3"><i class="fas fa-calendar-day text-info mr-2"></i>Daily Breakdown</h5>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Bills</th>
                                    <th>Revenue</th>
                                    <th>Collected</th>
                                    <th>Due</th>
                                    <th>Collection %</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($dailyRevenue as $day)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($day->date)->format('d M, Y (D)') }}</td>
                                    <td>{{ $day->bill_count }}</td>
                                    <td>Rs. {{ number_format($day->revenue, 2) }}</td>
                                    <td class="text-success">Rs. {{ number_format($day->collected, 2) }}</td>
                                    <td class="text-danger">Rs. {{ number_format($day->due, 2) }}</td>
                                    <td>
                                        @php $pct = $day->revenue > 0 ? round($day->collected / $day->revenue * 100) : 0; @endphp
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar bg-{{ $pct >= 80 ? 'success' : ($pct >= 50 ? 'warning' : 'danger') }}"
                                                 style="width: {{ $pct }}%">{{ $pct }}%</div>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="6" class="text-center text-muted">No data for this month</td></tr>
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
    var ctx = document.getElementById('dailyRevenueChart').getContext('2d');
    var dailyData = @json($dailyRevenue);
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: dailyData.map(d => d.date),
            datasets: [
                {
                    label: 'Revenue',
                    data: dailyData.map(d => d.revenue),
                    backgroundColor: 'rgba(40,167,69,0.6)',
                },
                {
                    label: 'Collected',
                    data: dailyData.map(d => d.collected),
                    backgroundColor: 'rgba(0,123,255,0.6)',
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
