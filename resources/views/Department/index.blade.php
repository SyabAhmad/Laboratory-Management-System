@extends('Layout.master')
@section('title', 'Department Management')
@section('content')

    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Home</a></li>
                            <li class="breadcrumb-item active">Department Management</li>
                        </ol>
                    </div>
                    <h4 class="page-title">Department Management</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="card bg-cards">
            <div class="card-body">
                <h4 class="text-center text-primary-custom">Department Management</h4>
                <p class="text-right">
                    <a href="{{ route('labtest.index') }}" class="btn btn btn-secondary waves-effect waves-light">
                        <i class="fas fa-flask"></i> Manage Test Categories
                    </a>
                    <button type="button" class="btn btn btn-primary-custom waves-effect waves-light" data-toggle="modal" data-target="#addDepartmentModal">
                        <i class="fas fa-plus"></i> Add Department
                    </button>
                </p>
                <div class="table-responsive">
                    <table id="departmentsTable" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Department Name</th>
                                    <th>Created At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($departments as $department)
                                <tr>
                                    <td>{{ $department->id }}</td>
                                    <td>{{ $department->name }}</td>
                                    <td>{{ $department->created_at->format('d-M-Y H:i') }}</td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-info edit-department" 
                                                data-id="{{ $department->id }}" 
                                                data-name="{{ $department->name }}">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                        <button type="button" class="btn btn-sm btn-danger delete-department" 
                                                data-id="{{ $department->id }}" 
                                                data-name="{{ $department->name }}">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div> <!-- end container-fluid -->

<!-- Add Department Modal -->
<div class="modal fade" id="addDepartmentModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Add New Department</h4>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="addDepartmentForm">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="departmentName">Department Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="departmentName" name="name" required maxlength="255">
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Department</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Department Modal -->
<div class="modal fade" id="editDepartmentModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Edit Department</h4>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="editDepartmentForm">
                <div class="modal-body">
                    <input type="hidden" id="editDepartmentId" name="id">
                    <div class="form-group">
                        <label for="editDepartmentName">Department Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="editDepartmentName" name="name" required maxlength="255">
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Department</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteDepartmentModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Confirm Delete</h4>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete the department "<span id="deleteDepartmentName"></span>"?</p>
                <p class="text-danger">This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteDepartment">Delete</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Initialize DataTable
    $('#departmentsTable').DataTable({
        "order": [[ 1, "asc" ]],
        "pageLength": 25,
        "language": {
            "emptyTable": "No departments found"
        }
    });

    // Add Department Form
    $('#addDepartmentForm').on('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        $.ajax({
            url: '{{ route("departments.store") }}',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        title: 'Success!',
                        text: response.message,
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    let errorMessage = 'Please correct the following errors:<br>';
                    for (const field in errors) {
                        errorMessage += '- ' + errors[field][0] + '<br>';
                    }
                    Swal.fire({
                        title: 'Validation Error',
                        html: errorMessage,
                        icon: 'error'
                    });
                } else {
                    Swal.fire({
                        title: 'Error!',
                        text: xhr.responseJSON?.message || 'An error occurred',
                        icon: 'error'
                    });
                }
            }
        });
    });

    // Edit Department
    $(document).on('click', '.edit-department', function() {
        const departmentId = $(this).data('id');
        const departmentName = $(this).data('name');
        
        $('#editDepartmentId').val(departmentId);
        $('#editDepartmentName').val(departmentName);
        $('#editDepartmentModal').modal('show');
    });

    // Update Department Form
    $('#editDepartmentForm').on('submit', function(e) {
        e.preventDefault();
        
        const departmentId = $('#editDepartmentId').val();
        const formData = new FormData(this);
        
        $.ajax({
            url: `/departments/${departmentId}`,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'X-HTTP-Method-Override': 'PUT'
            },
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        title: 'Success!',
                        text: response.message,
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    let errorMessage = 'Please correct the following errors:<br>';
                    for (const field in errors) {
                        errorMessage += '- ' + errors[field][0] + '<br>';
                    }
                    Swal.fire({
                        title: 'Validation Error',
                        html: errorMessage,
                        icon: 'error'
                    });
                } else {
                    Swal.fire({
                        title: 'Error!',
                        text: xhr.responseJSON?.message || 'An error occurred',
                        icon: 'error'
                    });
                }
            }
        });
    });

    // Delete Department
    $(document).on('click', '.delete-department', function() {
        const departmentId = $(this).data('id');
        const departmentName = $(this).data('name');
        
        $('#deleteDepartmentName').text(departmentName);
        $('#deleteDepartmentModal').data('id', departmentId).modal('show');
    });

    // Confirm Delete
    $('#confirmDeleteDepartment').on('click', function() {
        const departmentId = $('#deleteDepartmentModal').data('id');
        
        $.ajax({
            url: `/departments/${departmentId}`,
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        title: 'Success!',
                        text: response.message,
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                }
            },
            error: function(xhr) {
                Swal.fire({
                    title: 'Error!',
                    text: xhr.responseJSON?.message || 'An error occurred',
                    icon: 'error'
                });
            }
        });
        
        $('#deleteDepartmentModal').modal('hide');
    });

    // Clear forms when modals are closed
    $('#addDepartmentModal').on('hidden.bs.modal', function() {
        $('#addDepartmentForm')[0].reset();
        $('.invalid-feedback').empty();
    });

    $('#editDepartmentModal').on('hidden.bs.modal', function() {
        $('#editDepartmentForm')[0].reset();
        $('.invalid-feedback').empty();
    });
});
</script>
@endsection