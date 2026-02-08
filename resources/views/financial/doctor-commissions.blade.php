@extends('Layout.master')
@section('title', 'Doctor Commissions')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('financial.dashboard') }}">Financial</a></li>
                        <li class="breadcrumb-item active">Doctor Commissions</li>
                    </ol>
                </div>
                <h4 class="page-title">Doctor Commissions</h4>
            </div>
        </div>
    </div>

    {{-- Month Selector --}}
    <div class="row mb-3">
        <div class="col-md-4">
            <form method="GET" action="{{ route('financial.doctor-commissions') }}" class="d-flex align-items-center">
                <label class="mr-2 mb-0 font-weight-bold">Month:</label>
                <input type="month" name="month" value="{{ $month }}" class="form-control" onchange="this.form.submit()" style="max-width: 200px;">
            </form>
        </div>
        <div class="col-md-8 text-right">
            <button class="btn btn-primary-custom" data-toggle="modal" data-target="#addCommissionModal">
                <i class="fas fa-plus"></i> Add Commission
            </button>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card shadow-sm h-100" style="border-radius: 12px; border-left: 4px solid #6f42c1;">
                <div class="card-body">
                    <h6 class="text-muted mb-1">Total Commissions</h6>
                    <h3 class="mb-0" style="color: #6f42c1;">Rs. {{ number_format($stats['total'] ?? 0, 2) }}</h3>
                    <small class="text-muted">{{ $stats['count'] ?? 0 }} records</small>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card shadow-sm h-100" style="border-radius: 12px; border-left: 4px solid #ffc107;">
                <div class="card-body">
                    <h6 class="text-muted mb-1">Pending</h6>
                    <h3 class="mb-0 text-warning">Rs. {{ number_format($stats['pending'] ?? 0, 2) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card shadow-sm h-100" style="border-radius: 12px; border-left: 4px solid #28a745;">
                <div class="card-body">
                    <h6 class="text-muted mb-1">Paid</h6>
                    <h3 class="mb-0 text-success">Rs. {{ number_format($stats['paid'] ?? 0, 2) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card shadow-sm h-100" style="border-radius: 12px; border-left: 4px solid #007bff;">
                <div class="card-body">
                    <h6 class="text-muted mb-1">Doctors</h6>
                    <h3 class="mb-0 text-primary">{{ $byDoctor->count() }}</h3>
                </div>
            </div>
        </div>
    </div>

    {{-- By Doctor Summary --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm" style="border-radius: 12px;">
                <div class="card-body">
                    <h5 class="card-title mb-3"><i class="fas fa-user-md mr-2" style="color: #6f42c1;"></i>Commission by Doctor</h5>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>Doctor Name</th>
                                    <th>Bills</th>
                                    <th>Total Commission</th>
                                    <th>Pending</th>
                                    <th>Paid</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($byDoctor as $doc)
                                <tr>
                                    <td><strong>{{ $doc->doctor_name }}</strong></td>
                                    <td>{{ $doc->count }}</td>
                                    <td>Rs. {{ number_format($doc->total, 2) }}</td>
                                    <td class="text-warning">Rs. {{ number_format($doc->pending, 2) }}</td>
                                    <td class="text-success">Rs. {{ number_format($doc->paid, 2) }}</td>
                                    <td>
                                        @if($doc->pending > 0)
                                        <form method="POST" action="{{ route('financial.doctor-commissions.mark-doctor-paid') }}" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="doctor_name" value="{{ $doc->doctor_name }}">
                                            <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Mark all pending commissions for {{ $doc->doctor_name }} as paid?')">
                                                <i class="fas fa-check"></i> Pay All
                                            </button>
                                        </form>
                                        @else
                                        <span class="badge badge-success">All Paid</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="6" class="text-center text-muted">No commission records</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- All Commissions --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm" style="border-radius: 12px;">
                <div class="card-body">
                    <h5 class="card-title mb-3"><i class="fas fa-list mr-2" style="color: #6f42c1;"></i>All Commission Records</h5>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>#</th>
                                    <th>Date</th>
                                    <th>Doctor</th>
                                    <th>Patient</th>
                                    <th>Bill Amount</th>
                                    <th>Commission %</th>
                                    <th>Commission Amount</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($commissions as $i => $c)
                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td>{{ $c->created_at->format('d M, Y') }}</td>
                                    <td>{{ $c->doctor_name }}</td>
                                    <td>{{ $c->patient->name ?? '-' }}</td>
                                    <td>Rs. {{ number_format($c->bill_amount, 2) }}</td>
                                    <td>{{ number_format($c->commission_percentage, 1) }}%</td>
                                    <td><strong>Rs. {{ number_format($c->commission_amount, 2) }}</strong></td>
                                    <td>
                                        <span class="badge badge-{{ $c->status == 'paid' ? 'success' : ($c->status == 'pending' ? 'warning' : 'danger') }}">
                                            {{ ucfirst($c->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($c->status == 'pending')
                                        <form method="POST" action="{{ route('financial.doctor-commissions.mark-paid', $c->id) }}" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success" title="Mark Paid"><i class="fas fa-check"></i></button>
                                        </form>
                                        @endif
                                        <form method="POST" action="{{ route('financial.doctor-commissions.destroy', $c->id) }}" class="d-inline">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Delete" onclick="return confirm('Delete this record?')"><i class="fas fa-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="9" class="text-center text-muted">No commission records</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Add Commission Modal --}}
<div class="modal fade" id="addCommissionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('financial.doctor-commissions.store') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-user-md mr-2"></i>Add Doctor Commission</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Bill ID</label>
                        <input type="number" name="bill_id" class="form-control" required placeholder="Enter Bill ID">
                    </div>
                    <div class="form-group">
                        <label>Doctor Name</label>
                        <input type="text" name="doctor_name" class="form-control" required placeholder="Dr. Name">
                    </div>
                    <div class="form-group">
                        <label>Commission Percentage (%)</label>
                        <input type="number" name="commission_percentage" class="form-control" step="0.1" min="0" max="100" required placeholder="e.g. 10">
                    </div>
                    <div class="form-group">
                        <label>Notes (optional)</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="Optional notes"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Commission</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
