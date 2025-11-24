@extends('Layout.master')
@section('title', 'Add Expense')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('expenses.index') }}">Expenses</a></li>
                            <li class="breadcrumb-item active">Add Expense</li>
                        </ol>
                    </div>
                    <h4 class="page-title">Add Expense</h4>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm" style="border-radius: 12px; border: 1px solid rgba(37, 99, 235, 0.1);">
                    <div class="card-body" style="padding: 2rem;">
                        <h4 class="header-title mb-4" style="color: var(--text-heading); font-weight: 600;">
                            <i class="fas fa-money-bill-wave text-primary-custom mr-2"></i> Record New Expense
                        </h4>

                        <form method="POST" action="{{ route('expenses.store') }}">
                            @csrf

                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <div class="form-group mb-4">
                                        <label for="amount" class="font-weight-bold">
                                            <i class="fas fa-dollar-sign text-primary-custom mr-1"></i> Amount <span class="text-danger">*</span>
                                        </label>
                                        <input type="number" id="amount" name="amount" placeholder="Enter amount"
                                            class="form-control @error('amount') is-invalid @enderror"
                                            value="{{ old('amount') }}" step="0.01" min="0" required>
                                        @error('amount')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="form-group mb-4">
                                        <label for="purpose" class="font-weight-bold">
                                            <i class="fas fa-tag text-primary-custom mr-1"></i> Purpose <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" id="purpose" name="purpose" placeholder="e.g., Lunch, Tea, Coffee, Stationery"
                                            class="form-control @error('purpose') is-invalid @enderror"
                                            value="{{ old('purpose') }}" required>
                                        @error('purpose')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-12 col-md-6">
                                    <div class="form-group mb-4">
                                        <label for="expense_date" class="font-weight-bold">
                                            <i class="fas fa-calendar-alt text-primary-custom mr-1"></i> Expense Date <span class="text-danger">*</span>
                                        </label>
                                        <input type="date" id="expense_date" name="expense_date"
                                            class="form-control @error('expense_date') is-invalid @enderror"
                                            value="{{ old('expense_date', date('Y-m-d')) }}" required>
                                        @error('expense_date')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="form-group mb-4">
                                        <label for="note" class="font-weight-bold">
                                            <i class="fas fa-sticky-note text-primary-custom mr-1"></i> Note/Description
                                        </label>
                                        <textarea id="note" name="note" rows="3" placeholder="Additional details about the expense"
                                            class="form-control">{{ old('note') }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="text-right">
                                <a href="{{ route('expenses.index') }}" class="btn btn-secondary">Cancel</a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Save Expense
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection