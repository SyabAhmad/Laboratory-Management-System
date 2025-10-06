
@extends('Layout.master')
@section('title', 'Edit Patient')

@section('content')

<div class="container-fluid">

    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('patients.list') }}">Patients</a></li>
                        <li class="breadcrumb-item active">Edit Patient</li>
                    </ol>
                </div>
                <h4 class="page-title">Edit Patient</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title mb-3">Patient Information</h4>
                    
                    <form method="POST" action="{{ route('patients.update', $patient->id) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Full Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $patient->name) }}" required>
                                    @error('name')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="mobile_phone">Mobile Number <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('mobile_phone') is-invalid @enderror" id="mobile_phone" name="mobile_phone" value="{{ old('mobile_phone', $patient->mobile_phone) }}" required>
                                    @error('mobile_phone')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="gender">Gender <span class="text-danger">*</span></label>
                                    <select class="form-control @error('gender') is-invalid @enderror" id="gender" name="gender" required>
                                        <option value="">Choose One Option</option>
                                        <option value="Male" {{ old('gender', $patient->gender) == 'Male' ? 'selected' : '' }}>Male</option>
                                        <option value="Female" {{ old('gender', $patient->gender) == 'Female' ? 'selected' : '' }}>Female</option>
                                        <option value="Other" {{ old('gender', $patient->gender) == 'Other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                    @error('gender')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="age">Age <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('age') is-invalid @enderror" id="age" name="age" value="{{ old('age', $patient->age) }}" required>
                                    @error('age')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="blood_group">Blood Group</label>
                                    <select class="form-control" id="blood_group" name="blood_group">
                                        <option value="">Choose One Option</option>
                                        <option value="A+" {{ old('blood_group', $patient->blood_group) == 'A+' ? 'selected' : '' }}>A+</option>
                                        <option value="A-" {{ old('blood_group', $patient->blood_group) == 'A-' ? 'selected' : '' }}>A-</option>
                                        <option value="B+" {{ old('blood_group', $patient->blood_group) == 'B+' ? 'selected' : '' }}>B+</option>
                                        <option value="B-" {{ old('blood_group', $patient->blood_group) == 'B-' ? 'selected' : '' }}>B-</option>
                                        <option value="O+" {{ old('blood_group', $patient->blood_group) == 'O+' ? 'selected' : '' }}>O+</option>
                                        <option value="O-" {{ old('blood_group', $patient->blood_group) == 'O-' ? 'selected' : '' }}>O-</option>
                                        <option value="AB+" {{ old('blood_group', $patient->blood_group) == 'AB+' ? 'selected' : '' }}>AB+</option>
                                        <option value="AB-" {{ old('blood_group', $patient->blood_group) == 'AB-' ? 'selected' : '' }}>AB-</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="address">Address <span class="text-danger">*</span></label>
                                    <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="3" required>{{ old('address', $patient->address) }}</textarea>
                                    @error('address')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="receiving_date">Receiving Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('receiving_date') is-invalid @enderror" id="receiving_date" name="receiving_date" value="{{ old('receiving_date', $patient->receiving_date) }}" required>
                                    @error('receiving_date')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="reporting_date">Reporting Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('reporting_date') is-invalid @enderror" id="reporting_date" name="reporting_date" value="{{ old('reporting_date', $patient->reporting_date) }}" required>
                                    @error('reporting_date')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="referred_by">Referred By</label>
                                    <input type="text" class="form-control" id="referred_by" name="referred_by" value="{{ old('referred_by', $patient->referred_by) }}">
                                </div>

                                <div class="form-group">
                                    <label for="note">Note</label>
                                    <textarea class="form-control" id="note" name="note" rows="3">{{ old('note', $patient->note) }}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="text-right mt-3">
                            <a href="{{ route('patients.list') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-success">Update Patient</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Selected Tests Section -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title mb-3">Selected Tests</h4>
                    
                    @php
                        $selectedTests = $patient->test_category ? explode(',', $patient->test_category) : [];
                    @endphp

                    @if(count($selectedTests) > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Test Name</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($selectedTests as $index => $test)
                                        @if(trim($test))
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ trim($test) }}</td>
                                                <td>
                                                    <span class="badge badge-warning">Pending</span>
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-sm btn-primary add-test-data-btn" 
                                                            data-toggle="modal" 
                                                            data-target="#testDataModal"
                                                            data-test-name="{{ trim($test) }}"
                                                            data-patient-id="{{ $patient->id }}">
                                                        <i class="fas fa-plus"></i> Add Test Data
                                                    </button>
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info">
                            No tests selected for this patient.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Test Data Modal -->
<div class="modal fade" id="testDataModal" tabindex="-1" aria-labelledby="testDataModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="testDataModalLabel">Add Test Data</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="testDataForm">
                @csrf
                <input type="hidden" id="test_patient_id" name="patient_id">
                <input type="hidden" id="test_name" name="test_name">
                
                <div class="modal-body">
                    <div class="form-group">
                        <label>Test Name</label>
                        <input type="text" class="form-control" id="display_test_name" readonly>
                    </div>

                    <div class="form-group">
                        <label for="test_result">Test Result</label>
                        <textarea class="form-control" id="test_result" name="test_result" rows="4" placeholder="Enter test result here..."></textarea>
                    </div>

                    <div class="form-group">
                        <label for="test_notes">Notes</label>
                        <textarea class="form-control" id="test_notes" name="test_notes" rows="3" placeholder="Additional notes..."></textarea>
                    </div>

                    <div class="form-group">
                        <label for="test_date">Test Date</label>
                        <input type="date" class="form-control" id="test_date" name="test_date" value="{{ date('Y-m-d') }}">
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Test Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    // Handle Add Test Data button click
    $('.add-test-data-btn').on('click', function() {
        var testName = $(this).data('test-name');
        var patientId = $(this).data('patient-id');
        
        $('#test_patient_id').val(patientId);
        $('#test_name').val(testName);
        $('#display_test_name').val(testName);
    });

    // Handle Test Data Form submission
    $('#testDataForm').on('submit', function(e) {
        e.preventDefault();
        
        var formData = $(this).serialize();
        
        $.ajax({
            type: 'POST',
            url: '{{ route("patients.test.data.store") }}',
            data: formData,
            success: function(response) {
                $('#testDataModal').modal('hide');
                $('#testDataForm')[0].reset();
                
                Swal.fire({
                    title: 'Success!',
                    text: 'Test data saved successfully',
                    icon: 'success',
                    showConfirmButton: false,
                    timer: 1500
                });
                
                // Reload the page to show updated data
                setTimeout(function() {
                    location.reload();
                }, 1500);
            },
            error: function(xhr) {
                Swal.fire({
                    title: 'Error!',
                    text: 'Failed to save test data',
                    icon: 'error',
                    showConfirmButton: false,
                    timer: 1500
                });
            }
        });
    });
</script>
@endsection