@extends('Layout.master')
@section('title', 'Wages & Salaries')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('financial.dashboard') }}">Financial</a></li>
                        <li class="breadcrumb-item active">Wages & Salaries</li>
                    </ol>
                </div>
                <h4 class="page-title">Wages & Salaries Management</h4>
            </div>
        </div>
    </div>

    {{-- Month Selector --}}
    <div class="row mb-3">
        <div class="col-md-4">
            <form method="GET" action="{{ route('financial.wages') }}" class="d-flex align-items-center">
                <label class="mr-2 mb-0 font-weight-bold">Month:</label>
                <input type="month" name="month" value="{{ $month }}" class="form-control" onchange="this.form.submit()" style="max-width: 200px;">
            </form>
        </div>
        <div class="col-md-8 text-right">
            <button class="btn btn-primary-custom" data-toggle="modal" data-target="#addSalaryModal">
                <i class="fas fa-plus"></i> Add / Update Salary
            </button>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    @endif

    {{-- Summary Cards --}}
    <div class="row mb-4">
        <div class="col-xl-2 col-md-4 mb-3">
            <div class="card shadow-sm h-100" style="border-radius: 12px; border-left: 4px solid #007bff;">
                <div class="card-body">
                    <h6 class="text-muted mb-1">Total Payable</h6>
                    <h4 class="mb-0 text-primary">Rs. {{ number_format($stats['total_payable'] ?? 0, 2) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 mb-3">
            <div class="card shadow-sm h-100" style="border-radius: 12px; border-left: 4px solid #28a745;">
                <div class="card-body">
                    <h6 class="text-muted mb-1">Total Paid</h6>
                    <h4 class="mb-0 text-success">Rs. {{ number_format($stats['total_paid'] ?? 0, 2) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 mb-3">
            <div class="card shadow-sm h-100" style="border-radius: 12px; border-left: 4px solid #ffc107;">
                <div class="card-body">
                    <h6 class="text-muted mb-1">Pending</h6>
                    <h4 class="mb-0 text-warning">Rs. {{ number_format($stats['total_pending'] ?? 0, 2) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 mb-3">
            <div class="card shadow-sm h-100" style="border-radius: 12px; border-left: 4px solid #20c997;">
                <div class="card-body">
                    <h6 class="text-muted mb-1">Bonuses</h6>
                    <h4 class="mb-0" style="color: #20c997;">Rs. {{ number_format($stats['total_bonuses'] ?? 0, 2) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 mb-3">
            <div class="card shadow-sm h-100" style="border-radius: 12px; border-left: 4px solid #dc3545;">
                <div class="card-body">
                    <h6 class="text-muted mb-1">Deductions</h6>
                    <h4 class="mb-0 text-danger">Rs. {{ number_format($stats['total_deductions'] ?? 0, 2) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 mb-3">
            <div class="card shadow-sm h-100" style="border-radius: 12px; border-left: 4px solid #6c757d;">
                <div class="card-body">
                    <h6 class="text-muted mb-1">Employees</h6>
                    <h4 class="mb-0 text-secondary">{{ $stats['count'] ?? 0 }}</h4>
                </div>
            </div>
        </div>
    </div>

    {{-- Salary Records --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm" style="border-radius: 12px;">
                <div class="card-body">
                    <h5 class="card-title mb-3"><i class="fas fa-hand-holding-usd text-primary mr-2"></i>Salary Records - {{ \Carbon\Carbon::parse($month . '-01')->format('F Y') }}</h5>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>#</th>
                                    <th>Employee</th>
                                    <th>Base Salary</th>
                                    <th>Bonus</th>
                                    <th>Deduction</th>
                                    <th>Net Salary</th>
                                    <th>Method</th>
                                    <th>Status</th>
                                    <th>Paid Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($salaryPayments as $i => $sp)
                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td><strong>{{ $sp->employee_name }}</strong></td>
                                    <td>Rs. {{ number_format($sp->base_salary, 2) }}</td>
                                    <td class="text-success">+{{ number_format($sp->bonus, 2) }}</td>
                                    <td class="text-danger">-{{ number_format($sp->deduction, 2) }}</td>
                                    <td><strong>Rs. {{ number_format($sp->net_salary, 2) }}</strong></td>
                                    <td><span class="badge badge-info text-capitalize">{{ $sp->payment_method }}</span></td>
                                    <td>
                                        <span class="badge badge-{{ $sp->status == 'paid' ? 'success' : 'warning' }}">
                                            {{ ucfirst($sp->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $sp->payment_date ? $sp->payment_date->format('d M, Y') : '-' }}</td>
                                    <td>
                                        @if($sp->status == 'pending')
                                        <form method="POST" action="{{ route('financial.wages.mark-paid', $sp->id) }}" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Mark salary as paid?')">
                                                <i class="fas fa-check"></i> Pay
                                            </button>
                                        </form>
                                        @else
                                        <span class="text-success"><i class="fas fa-check-circle"></i></span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="10" class="text-center text-muted">No salary records for this month. Use "Add / Update Salary" to create records.</td></tr>
                                @endforelse
                            </tbody>
                            @if($salaryPayments->count() > 0)
                            <tfoot>
                                <tr class="font-weight-bold" style="background: rgba(0,0,0,0.05);">
                                    <td colspan="2">Total</td>
                                    <td>Rs. {{ number_format($salaryPayments->sum('base_salary'), 2) }}</td>
                                    <td class="text-success">+{{ number_format($salaryPayments->sum('bonus'), 2) }}</td>
                                    <td class="text-danger">-{{ number_format($salaryPayments->sum('deduction'), 2) }}</td>
                                    <td>Rs. {{ number_format($salaryPayments->sum('net_salary'), 2) }}</td>
                                    <td colspan="4"></td>
                                </tr>
                            </tfoot>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Employee List (for quick reference) --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm" style="border-radius: 12px;">
                <div class="card-body">
                    <h5 class="card-title mb-3"><i class="fas fa-users text-info mr-2"></i>All Employees (Quick Reference)</h5>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Position</th>
                                    <th>Base Salary</th>
                                    <th>Salary Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($employees as $emp)
                                @php
                                    $existingPayment = $salaryPayments->where('employee_id', $emp->id)->first();
                                @endphp
                                <tr>
                                    <td>{{ $emp->employee_id }}</td>
                                    <td>{{ $emp->users->name ?? 'N/A' }}</td>
                                    <td>{{ $emp->position ?? '-' }}</td>
                                    <td>Rs. {{ number_format($emp->salary, 2) }}</td>
                                    <td>
                                        @if($existingPayment)
                                            <span class="badge badge-{{ $existingPayment->status == 'paid' ? 'success' : 'warning' }}">
                                                {{ ucfirst($existingPayment->status) }}
                                            </span>
                                        @else
                                            <span class="badge badge-secondary">Not Created</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Add/Update Salary Modal --}}
<div class="modal fade" id="addSalaryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('financial.wages.store') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-hand-holding-usd mr-2"></i>Add / Update Salary</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="month" value="{{ $month }}">
                    <div class="form-group">
                        <label>Employee</label>
                        <select name="employee_id" class="form-control" required id="employeeSelect">
                            <option value="">Select Employee</option>
                            @foreach($employees as $emp)
                            <option value="{{ $emp->id }}" data-salary="{{ $emp->salary }}">
                                {{ $emp->users->name ?? 'Employee #' . $emp->id }} ({{ $emp->position ?? 'N/A' }})
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Base Salary (Rs.)</label>
                        <input type="number" name="base_salary" id="baseSalaryInput" class="form-control" step="0.01" min="0" required>
                    </div>
                    <div class="form-group">
                        <label>Bonus (Rs.)</label>
                        <input type="number" name="bonus" class="form-control" step="0.01" min="0" value="0">
                    </div>
                    <div class="form-group">
                        <label>Deduction (Rs.)</label>
                        <input type="number" name="deduction" class="form-control" step="0.01" min="0" value="0">
                    </div>
                    <div class="form-group">
                        <label>Payment Method</label>
                        <select name="payment_method" class="form-control">
                            <option value="cash">Cash</option>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="cheque">Cheque</option>
                            <option value="easypaisa">Easypaisa</option>
                            <option value="jazzcash">JazzCash</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Notes (optional)</label>
                        <textarea name="notes" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Salary</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.getElementById('employeeSelect').addEventListener('change', function() {
    var selected = this.options[this.selectedIndex];
    var salary = selected.getAttribute('data-salary');
    if (salary) {
        document.getElementById('baseSalaryInput').value = parseFloat(salary).toFixed(2);
    }
});
</script>
@endsection
