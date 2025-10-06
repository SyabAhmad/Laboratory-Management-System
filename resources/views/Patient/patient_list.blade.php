@extends('Layout.master')
@section('title', 'Patients List')

@section('content')

    <div class="container-fluid">

        <!-- start page title -->
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
        <!-- end page title -->

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
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>


    </div> <!-- container -->

    <!-- Edit Patient Modal -->
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
                                    <input type="text" class="form-control" id="edit_mobile_phone" name="mobile_phone" required>
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
                                    <label for="edit_receiving_date">Receiving Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="edit_receiving_date" name="receiving_date" required>
                                </div>

                                <div class="form-group">
                                    <label for="edit_reporting_date">Reporting Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="edit_reporting_date" name="reporting_date" required>
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

    <script>
        $(function() {
            var table = $('.patitent_datatable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('patients.list') }}",
                },
                columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex'
                }, {
                    data: 'patient_id',
                    name: 'patient_id'
                }, {
                    data: 'name',
                    name: 'name'
                },  {
                    data: 'mobile_phone',
                    name: 'mobile_phone'
                }, {
                    data: 'age',
                    name: 'age'
                }, {
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
                }, {
                    data: 'action',
                    name: 'action',
                    orderable: true,
                    searchable: true
                }, ],
                buttons: [
                    'copy', 'excel', 'pdf'
                ]
            });

            // Delete button handler
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
                        var token = $("meta[name='csrf-token']").attr("content");
                        $.ajax({
                            type: "DELETE",
                            url: "{{ URL::route('patients.destroy', '') }}/" + id,
                            data: {
                                "id": id,
                                "_token": token,
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

            // Edit button handler - open modal and populate fields
            $('body').on('click', '.editbtn', function() {
                var id = $(this).data('id');
                if (!id) { 
                    console.error('editbtn missing data-id'); 
                    return; 
                }

                var url = "{{ url('patients') }}/" + id + "/edit";

                $.get(url)
                    .done(function(res) {
                        // Populate modal fields with patient data
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
                        
                        // Show the modal
                        $('#editPatientModal').modal('show');
                    })
                    .fail(function(xhr) {
                        console.error('Failed to load patient:', xhr.status, xhr.responseText);
                        Swal.fire({
                            title: 'Error!',
                            text: 'Failed to load patient data',
                            icon: 'error',
                            showConfirmButton: false,
                            timer: 1500
                        });
                    });
            });

            // Update patient form submission
            $('#editPatientForm').on('submit', function(e) {
                e.preventDefault();
                
                var id = $('#edit_patient_id').val();
                var url = "{{ url('patients') }}/" + id;
                var formData = new FormData(this);
                
                $.ajax({
                    type: 'POST',
                    url: url,
                    data: formData,
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
                        console.error('Update failed:', xhr.responseText);
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
        });

        $(document).on('change', '#status', function() {
            var id = $(this).attr('data-id');
            console.log(id);
            if (this.checked) {
                var catstatus = 'Active';
            } else {
                var catstatus = 'Pending';
            }
            var url = "{{ URL::route('patients.status', '') }}/" + id;
            $.ajax({
                dataType: "json",
                url: url,
                method: 'get',
                data: {
                    'id': id,
                    'status': catstatus
                },
                success: function(result1) {
                    console.log(result1);
                    Swal.fire({
                        position: 'top-end',
                        icon: 'success',
                        title: catstatus,
                        text: "The user's status has been updated",
                        showConfirmButton: false,
                        timerProgressBar: true,
                        timer: 1800
                    });
                },
                error: function(error) {
                    Swal.fire({
                        position: 'top-end',
                        icon: 'error',
                        title: 'We have some error',
                        showConfirmButton: false,
                        timerProgressBar: true,
                        timer: 1800
                    });
                }
            });
        });
    </script>

@endsection
