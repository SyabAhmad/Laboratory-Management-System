@extends('Layout.master')
@section('title', 'Lab Test Category')
@section('content')
    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Home</a></li>
                            <li class="breadcrumb-item active">Billing System</li>
                        </ol>
                    </div>
                    <h4 class="page-title">Patient Billing System</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="card">
            <div class="card-body">
                <table class="table-hover table allbill_datatable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Bill No</th>
                            <th>Patient ID</th>
                            <th>Patient Name</th>
                            <th>Status</th>
                            <th>Paid Amount</th>
                            {{-- <th>Tests Status</th> --}}
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
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
