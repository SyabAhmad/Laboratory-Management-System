@extends('Layout.master')
@section('title', 'All Bills')
@section('content')
    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Home</a></li>
                            <li class="breadcrumb-item active">All Bills</li>
                        </ol>
                    </div>
                    <h4 class="page-title">All Bills</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0">Patient Bills Overview</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover allbill_datatable">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Patient ID</th>
                                <th>Patient Name</th>
                                <th>Billing Date</th>
                                <th>Status</th>
                                <th>Paid Amount</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(function() {
            var table = $('.allbill_datatable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('allbills') }}",
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex'
                    },
                    {
                        data: 'patient_id',
                        name: 'patient_id'
                    },
                    {
                        data: 'patient_name',
                        name: 'patient_name'
                    },
                    {
                        data: 'billing_date',
                        name: 'billing_date'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'paid_amount',
                        name: 'paid_amount'
                    },
                    // {
                    //     data: 'tests_completed',
                    //     name: 'tests_completed'
                    // },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: true,
                        searchable: true
                    }
                ],
            });
        });
    </script>
@endsection
