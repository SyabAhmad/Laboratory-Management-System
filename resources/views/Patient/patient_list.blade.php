@extends('Layout.master')
@section('title', 'Patient List')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Home</a></li>
                            <li class="breadcrumb-item active">Patients</li>
                        </ol>
                    </div>
                    <h4 class="page-title">Patients</h4>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h4 class="text-center">Patients</h4>
                <h6 class="text-center">List of all Patients</h6>
                <div class="table-responsive">
                    <table class="table table-hover mb-0 patitent_datatable" id="patients-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Patient ID</th>
                                <th>Name</th>
                                <th>Phone</th>
                                <th>Age</th>
                                <th>Gender</th>
                                <th>Referred By</th>
                                <th>Blood Group</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editPatientModal" tabindex="-1" aria-labelledby="editPatientModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editPatientModalLabel">Edit Patient</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="editPatientForm">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="edit_patient_id" name="patient_id">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="edit_name">Full Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="edit_name" name="name" required>
                                </div>
                                <div class="form-group">
                                    <label for="edit_mobile_phone">Mobile Number <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="edit_mobile_phone" name="mobile_phone"
                                        required>
                                </div>
                                <div class="form-group">
                                    <label for="edit_gender">Gender <span class="text-danger">*</span></label>
                                    <select class="form-control" id="edit_gender" name="gender" required>
                                        <option value="">Choose One Option</option>
                                        <option value="Male">Male</option>
                                        <option value="Female">Female</option>
                                        <option value="Other">Other</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="edit_age">Age <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="edit_age" name="age" required>
                                </div>
                                <div class="form-group">
                                    <label for="edit_blood_group">Blood Group</label>
                                    <select class="form-control" id="edit_blood_group" name="blood_group">
                                        <option value="">Choose One Option</option>
                                        <option value="A+">A+</option>
                                        <option value="A-">A-</option>
                                        <option value="B+">B+</option>
                                        <option value="B-">B-</option>
                                        <option value="O+">O+</option>
                                        <option value="O-">O-</option>
                                        <option value="AB+">AB+</option>
                                        <option value="AB-">AB-</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="edit_address">Address <span class="text-danger">*</span></label>
                                    <textarea class="form-control" id="edit_address" name="address" rows="3" required></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="edit_receiving_date">Receiving Date <span
                                            class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="edit_receiving_date"
                                        name="receiving_date" required>
                                </div>
                                <div class="form-group">
                                    <label for="edit_reporting_date">Reporting Date <span
                                            class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="edit_reporting_date"
                                        name="reporting_date" required>
                                </div>
                                <div class="form-group">
                                    <label for="edit_referred_by">Referred By</label>
                                    <input type="text" class="form-control" id="edit_referred_by" name="referred_by">
                                </div>
                                <div class="form-group">
                                    <label for="edit_note">Note</label>
                                    <textarea class="form-control" id="edit_note" name="note" rows="3"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Update Patient</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="patientIdModal" tabindex="-1" role="dialog" aria-labelledby="patientIdModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="patientIdModalLabel">
                        <i class="fas fa-check-circle"></i> Patient Registered Successfully
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center py-4">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Use this ID on the CBC Machine</strong>
                    </div>
                    <h3 class="mb-3">Patient Name</h3>
                    <h4 class="text-primary mb-4" id="modalPatientName"></h4>
                    <h3 class="mb-3">Patient ID (For CBC Machine)</h3>
                    <div class="card bg-light">
                        <div class="card-body">
                            <h1 class="display-4 text-primary font-weight-bold mb-0" id="modalPatientId"></h1>
                        </div>
                    </div>
                    <div class="mt-4">
                        <button type="button" class="btn btn-primary btn-lg" onclick="copyPatientId()">
                            <i class="fas fa-copy"></i> Copy Patient ID
                        </button>
                        <button type="button" class="btn btn-success btn-lg" onclick="printPatientId()">
                            <i class="fas fa-print"></i> Print Label
                        </button>
                    </div>
                    <div class="alert alert-warning mt-4">
                        <small>
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>Important:</strong> Enter this Patient ID on the CBC analyzer (PID-3 field) before
                            running the test.
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div id="printLabelTemplate" style="display: none;">
        <div style="text-align: center; padding: 20px; font-family: Arial, sans-serif;">
            <h2>Patient Label</h2>
            <p><strong>Name:</strong> <span id="printPatientName"></span></p>
            <h1 style="font-size: 48px; margin: 20px 0;">
                Patient ID: <span id="printPatientId"></span>
            </h1>
            <p style="font-size: 14px; color: #666;">Enter this ID on CBC Analyzer</p>
            <hr>
            <p style="font-size: 12px;">Date: {{ date('Y-m-d H:i:s') }}</p>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(function() {
            var table = $('.patitent_datatable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('patients.list') }}"
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
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'mobile_phone',
                        name: 'mobile_phone'
                    },
                    {
                        data: 'age',
                        name: 'age'
                    },
                    {
                        data: 'gender',
                        name: 'gender'
                    },
                    {
                        data: 'referred_by',
                        name: 'referred_by'
                    },
                    {
                        data: 'blood_group',
                        name: 'blood_group'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: true,
                        searchable: true
                    }
                ],
                buttons: ['copy', 'excel', 'pdf']
            });

            $('body').on('click', '.deletebtn', function() {
                var id = $(this).data("id");
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You will not be able to recover this patient record!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.value === true) {
                        $.ajax({
                            type: "DELETE",
                            url: "{{ URL::route('patients.destroy', '') }}/" + id,
                            data: {
                                "id": id,
                                "_token": $("meta[name='csrf-token']").attr("content")
                            },
                            success: function(data) {
                                Swal.fire({
                                    title: 'Deleted!',
                                    text: 'Patient record has been deleted.',
                                    icon: 'success',
                                    showConfirmButton: false,
                                    timer: 1500
                                });
                                table.draw();
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

            $('body').on('click', '.editbtn', function() {
                var id = $(this).data('id');
                if (!id) return;
                $.get("{{ url('patients') }}/" + id + "/edit").done(function(res) {
                    $('#edit_patient_id').val(res.id);
                    $('#edit_name').val(res.name);
                    $('#edit_mobile_phone').val(res.mobile_phone);
                    $('#edit_gender').val(res.gender);
                    $('#edit_age').val(res.age);
                    $('#edit_blood_group').val(res.blood_group);
                    $('#edit_address').val(res.address);
                    $('#edit_receiving_date').val(res.receiving_date);
                    $('#edit_reporting_date').val(res.reporting_date);
                    $('#edit_referred_by').val(res.referred_by);
                    $('#edit_note').val(res.note);
                    $('#editPatientModal').modal('show');
                }).fail(function(xhr) {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Failed to load patient data',
                        icon: 'error',
                        showConfirmButton: false,
                        timer: 1500
                    });
                });
            });

            $('#editPatientForm').on('submit', function(e) {
                e.preventDefault();
                var id = $('#edit_patient_id').val();
                $.ajax({
                    type: 'POST',
                    url: "{{ url('patients') }}/" + id,
                    data: new FormData(this),
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        $('#editPatientModal').modal('hide');
                        $('#editPatientForm')[0].reset();
                        Swal.fire({
                            title: 'Success!',
                            text: 'Patient updated successfully',
                            icon: 'success',
                            showConfirmButton: false,
                            timer: 1500
                        });
                        table.draw();
                    },
                    error: function(xhr) {
                        Swal.fire({
                            title: 'Error!',
                            text: 'Failed to update patient',
                            icon: 'error',
                            showConfirmButton: false,
                            timer: 1500
                        });
                    }
                });
            });

            $('body').on('click', '.billbtn', function() {
                var id = $(this).data('id');
                if (id) window.location.href = "{{ route('billing.create', '') }}/" + id;
            });

            @if (session('show_patient_id_modal'))
                $('#modalPatientId').text('{{ session('patient_id') }}');
                $('#modalPatientName').text('{{ session('patient_name') }}');
                $('#printPatientId').text('{{ session('patient_id') }}');
                $('#printPatientName').text('{{ session('patient_name') }}');
                $('#patientIdModal').modal('show');
            @endif
        });

        // Expose as globals
        window.copyPatientId = function() {
            const patientId = $('#modalPatientId').text();
            const temp = document.createElement('input');
            temp.value = patientId;
            document.body.appendChild(temp);
            temp.select();
            document.execCommand('copy');
            document.body.removeChild(temp);
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'success',
                    title: 'Copied!',
                    text: 'Patient ID copied to clipboard',
                    timer: 1500,
                    showConfirmButton: false
                });
            }
        };
    </script>
@endsection
