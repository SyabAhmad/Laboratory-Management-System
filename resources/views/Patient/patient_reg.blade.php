@extends('Layout.master')
@section('title', 'Patients')

@section('content')

    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item active">Patient Registration</li>
                        </ol>
                    </div>
                    <h4 class="page-title">Patient Registration</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title mb-3">Register New Patient</h4>
                        
                        <form method="POST" action="{{ route('patients.store') }}" enctype="multipart/form-data" id="patientRegistrationForm">
                            @csrf
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name">Full Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                                        @error('name')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="mobile_phone">Mobile Number <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('mobile_phone') is-invalid @enderror" id="mobile_phone" name="mobile_phone" value="{{ old('mobile_phone') }}" required>
                                        @error('mobile_phone')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="gender">Gender <span class="text-danger">*</span></label>
                                        <select class="form-control @error('gender') is-invalid @enderror" id="gender" name="gender" required>
                                            <option value="">Choose One Option</option>
                                            <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>Male</option>
                                            <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>Female</option>
                                            <option value="Other" {{ old('gender') == 'Other' ? 'selected' : '' }}>Other</option>
                                        </select>
                                        @error('gender')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="age">Age <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control @error('age') is-invalid @enderror" id="age" name="age" value="{{ old('age') }}" required>
                                        @error('age')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="blood_group">Blood Group</label>
                                        <select class="form-control" id="blood_group" name="blood_group">
                                            <option value="">Choose One Option</option>
                                            <option value="A+" {{ old('blood_group') == 'A+' ? 'selected' : '' }}>A+</option>
                                            <option value="A-" {{ old('blood_group') == 'A-' ? 'selected' : '' }}>A-</option>
                                            <option value="B+" {{ old('blood_group') == 'B+' ? 'selected' : '' }}>B+</option>
                                            <option value="B-" {{ old('blood_group') == 'B-' ? 'selected' : '' }}>B-</option>
                                            <option value="O+" {{ old('blood_group') == 'O+' ? 'selected' : '' }}>O+</option>
                                            <option value="O-" {{ old('blood_group') == 'O-' ? 'selected' : '' }}>O-</option>
                                            <option value="AB+" {{ old('blood_group') == 'AB+' ? 'selected' : '' }}>AB+</option>
                                            <option value="AB-" {{ old('blood_group') == 'AB-' ? 'selected' : '' }}>AB-</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="address">Address <span class="text-danger">*</span></label>
                                        <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="3" required>{{ old('address') }}</textarea>
                                        @error('address')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="receiving_date">Receiving Date <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control @error('receiving_date') is-invalid @enderror" id="receiving_date" name="receiving_date" value="{{ old('receiving_date', date('Y-m-d')) }}" required>
                                        @error('receiving_date')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="reporting_date">Reporting Date <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control @error('reporting_date') is-invalid @enderror" id="reporting_date" name="reporting_date" value="{{ old('reporting_date', date('Y-m-d')) }}" required>
                                        @error('reporting_date')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="referred_by">Referred By</label>
                                        <input type="text" class="form-control" id="referred_by" name="referred_by" value="{{ old('referred_by') }}">
                                    </div>

                                    <div class="form-group">
                                        <label for="note">Note</label>
                                        <textarea class="form-control" id="note" name="note" rows="3">{{ old('note') }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-12">
                                    <h5 class="mb-3">Select Tests <span class="text-danger">*</span></h5>
                                    
                                    @php
                                        // Fetch all active test categories (status = 1 means active)
                                        $testCategories = DB::table('labtest_cat')
                                            ->where('status', 1)
                                            ->orderBy('cat_name', 'ASC')
                                            ->get();
                                    @endphp
                                    
                                    @if($testCategories->count() > 0)
                                        <div class="row">
                                            @foreach($testCategories as $category)
                                                <div class="col-md-4 col-sm-6 mb-2">
                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox" 
                                                               class="custom-control-input test-checkbox" 
                                                               id="test_{{ $category->id }}" 
                                                               value="{{ $category->cat_name }}" 
                                                               data-price="{{ $category->price }}">
                                                        <label class="custom-control-label" for="test_{{ $category->id }}">
                                                            {{ $category->cat_name }}
                                                            @if($category->price)
                                                                <small class="text-muted">({{ number_format($category->price, 2) }} PKR)</small>
                                                            @endif
                                                        </label>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                        
                                        <!-- Container for dynamically generated hidden inputs -->
                                        <div id="test-category-container"></div>
                                    @else
                                        <div class="alert alert-warning">
                                            <i class="fas fa-exclamation-triangle"></i> No test categories available. Please add test categories first from the <a href="{{ route('labtest') }}">Lab Test Category</a> section.
                                        </div>
                                    @endif
                                    
                                    <small class="text-muted d-block mt-2">Select at least one test</small>
                                    
                                    @error('test_category')
                                        <span class="text-danger d-block mt-1">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="text-right mt-4">
                                <a href="{{ route('patients.list') }}" class="btn btn-secondary">Cancel</a>
                                <button type="submit" class="btn btn-success" id="submitBtn">Register Patient</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection

@section('scripts')
<script>
(function() {
    'use strict';
    
    document.addEventListener('DOMContentLoaded', function () {
        var form = document.getElementById('patientRegistrationForm');
        var checkboxes = document.querySelectorAll('input.test-checkbox');
        var container = document.getElementById('test-category-container');
        
        if (!form || !container) {
            console.error('Form or container not found');
            return;
        }
        
        // Function to update hidden inputs with selected test values
        function updateTestCategory() {
            container.innerHTML = ''; // Clear previous hidden inputs
            
            var selected = [];
            for (var i = 0; i < checkboxes.length; i++) {
                if (checkboxes[i].checked) {
                    selected.push(checkboxes[i].value);
                    
                    // Create a hidden input for each selected test
                    var input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'test_category[]'; // Note the [] — this makes it an array in PHP
                    input.value = checkboxes[i].value;
                    container.appendChild(input);
                }
            }
            
            console.log('Selected tests:', selected);
            console.log('Hidden inputs created:', selected.length);
            
            return selected;
        }
        
        // Add change event listener to each checkbox - update immediately
        for (var i = 0; i < checkboxes.length; i++) {
            checkboxes[i].addEventListener('change', function() {
                updateTestCategory();
            });
        }
        
        // Handle form submission
        form.addEventListener('submit', function(e) {
            // Ensure hidden fields are up to date
            var selected = updateTestCategory();
            
            console.log('Form submit - Selected count:', selected.length);
            
            if (selected.length === 0) {
                e.preventDefault(); // Stop submission
                
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'warning',
                        title: 'No Tests Selected',
                        text: 'Please select at least one test',
                        confirmButtonColor: '#3085d6',
                    });
                } else {
                    alert('Please select at least one test');
                }
                return false;
            }
            
            // Otherwise, let the form submit normally
            console.log('Submitting form with', selected.length, 'tests selected');
        });
        
        // Initialize on page load
        updateTestCategory();
    });
})();
</script>
@endsection