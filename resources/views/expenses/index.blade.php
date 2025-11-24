@extends('Layout.master')
@section('title', 'Expenses')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item active">Expenses</li>
                        </ol>
                    </div>
                    <h4 class="page-title">Expenses</h4>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm" style="border-radius: 12px; border: 1px solid rgba(37, 99, 235, 0.1);">
                    <div class="card-body" style="padding: 2rem;">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h4 class="header-title mb-0" style="color: var(--text-heading); font-weight: 600;">
                                <i class="fas fa-money-bill-wave text-primary-custom mr-2"></i> Expense Records
                            </h4>
                            <a href="{{ route('expenses.create') }}" class="btn btn-primary-custom">
                                <i class="fas fa-plus"></i> Add Expense
                            </a>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-striped table-hover mb-0 expenses-table" id="expenses-table">
                                <thead class="thead-light" style="background: linear-gradient(135deg, rgba(248, 250, 252, 0.9), rgba(255, 255, 255, 0.9));">
                                    <tr>
                                        <th>#</th>
                                        <th>Date</th>
                                        <th>Purpose</th>
                                        <th>Amount</th>
                                        <th>Note</th>
                                        <th>Recorded By</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    $('.expenses-table').DataTable({
        processing: true,
        serverSide: true,
        searchDelay: 500, // Add 500ms debouncing to prevent database overload
        ajax: "{{ route('expenses.index') }}",
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex' },
            { data: 'expense_date', name: 'expense_date' },
            { data: 'purpose', name: 'purpose' },
            { data: 'amount', name: 'amount', render: function(data) { return 'Rs. ' + parseFloat(data).toFixed(2); } },
            { data: 'note', name: 'note' },
            { data: 'user_name', name: 'user_name' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ]
    });

    $('body').on('click', '.deletebtn', function() {
        var id = $(this).data("id");
        Swal.fire({
            title: 'Are you sure?',
            text: "You will not be able to recover this expense record!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.value === true) {
                $.ajax({
                    type: "DELETE",
                    url: "{{ route('expenses.destroy', '') }}/" + id,
                    data: {
                        "_token": $("meta[name='csrf-token']").attr("content")
                    },
                    success: function(data) {
                        Swal.fire({
                            title: 'Deleted!',
                            text: 'Expense record has been deleted.',
                            icon: 'success',
                            showConfirmButton: false,
                            timer: 1500
                        });
                        $('.expenses-table').DataTable().draw();
                    },
                    error: function(data) {
                        Swal.fire({
                            title: 'Error!',
                            text: 'Something went wrong',
                            icon: 'error',
                            showConfirmButton: false,
                            timer: 1500
                        });
                    }
                });
            }
        });
    });
});
</script>
@endsection