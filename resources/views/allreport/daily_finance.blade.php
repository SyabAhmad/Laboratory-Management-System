@extends('Layout.app')
@section('title', 'Daily Finance Report')
@section('content')
    <div class="container mt-4">
        <h3>Daily Finance Report - {{ \Carbon\Carbon::parse($date)->format('d-M-Y') }}</h3>
        <div class="mb-3">
            <form method="get" action="{{ route('report.dailyFinance') }}">
                <input type="date" name="date" class="form-control" value="{{ $date }}" style="max-width:200px; display:inline-block; margin-right:8px;">
                <button class="btn btn-primary">Filter</button>
            </form>
        </div>

        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>Time</th>
                    <th>Receipt #</th>
                    <th>Patient</th>
                    <th>Amount</th>
                    <th>Type</th>
                </tr>
            </thead>
            <tbody>
                @foreach($payments as $p)
                    <tr>
                        <td>{{ $p->created_at->format('H:i') }}</td>
                        <td>{{ $p->receipt_number ?? '-' }}</td>
                        <td>{{ optional($p->patient)->name ?? ($p->name ?? '-') }}</td>
                        <td>Rs. {{ number_format($p->amount, 2) }}</td>
                        <td>{{ $p->type ?? 'Income' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="mt-3">
            <h4>Total: Rs. {{ number_format($total, 2) }}</h4>
        </div>
    </div>
@endsection
