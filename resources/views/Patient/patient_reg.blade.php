@extends('Layout.master')
@section('title', 'Patients')

@section('content')

    <div class="container-fluid patient-reg-advanced">
        <!-- Modern Card Layout -->
        <div class="row justify-content-center">
            <div class="col-lg-10 col-xl-9">
                <div class="card shadow-lg border-0 mt-4 mb-5">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-4">
                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mr-3" style="width: 56px; height: 56px; font-size: 2rem;">
                                <i class="fas fa-user-plus"></i>
                            </div>
                            <div>
                                <h3 class="mb-0 font-weight-bold" style="letter-spacing: 1px;">Patient Registration</h3>
                                <div class="text-muted small">Fill in the details to register a new patient</div>
                            </div>
                        </div>
                        <form method="POST" action="{{ route('patients.store') }}" enctype="multipart/form-data" id="patientRegistrationForm" autocomplete="off">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card mb-3 border-0 shadow-sm">
                                        <div class="card-body py-3">
                                            <h5 class="mb-3 text-primary"><i class="fas fa-user mr-2"></i>Personal Information</h5>
                                            <div class="form-group mb-3">
                                                <label for="name">Full Name <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control form-control-lg @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                                                @error('name')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                            </div>
                                            <div class="form-group mb-3">
                                                <label for="mobile_phone">Mobile Number <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control form-control-lg @error('mobile_phone') is-invalid @enderror" id="mobile_phone" name="mobile_phone" value="{{ old('mobile_phone') }}" required>
                                                @error('mobile_phone')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                            </div>
                                            <div class="form-row">
                                                <div class="form-group col-md-6 mb-3">
                                                    <label for="gender">Gender <span class="text-danger">*</span></label>
                                                    <select class="form-control form-control-lg @error('gender') is-invalid @enderror" id="gender" name="gender" required>
                                                        <option value="">Choose</option>
                                                        <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>Male</option>
                                                        <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>Female</option>
                                                        <option value="Other" {{ old('gender') == 'Other' ? 'selected' : '' }}>Other</option>
                                                    </select>
                                                    @error('gender')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                                </div>
                                                <div class="form-group col-md-6 mb-3">
                                                    <label for="age">Age <span class="text-danger">*</span></label>
                                                    <input type="number" class="form-control form-control-lg @error('age') is-invalid @enderror" id="age" name="age" value="{{ old('age') }}" required>
                                                    @error('age')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                                </div>
                                            </div>
                                            <div class="form-group mb-3">
                                                <label for="blood_group">Blood Group</label>
                                                <select class="form-control form-control-lg" id="blood_group" name="blood_group">
                                                    <option value="">Choose</option>
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
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card mb-3 border-0 shadow-sm">
                                        <div class="card-body py-3">
                                            <h5 class="mb-3 text-primary"><i class="fas fa-address-card mr-2"></i>Contact & Dates</h5>
                                            <div class="form-group mb-3">
                                                <label for="address">Address <span class="text-danger">*</span></label>
                                                <textarea class="form-control form-control-lg @error('address') is-invalid @enderror" id="address" name="address" rows="2" required>{{ old('address') }}</textarea>
                                                @error('address')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                            </div>
                                            <div class="form-row">
                                                <div class="form-group col-md-6 mb-3">
                                                    <label for="receiving_date">Receiving Date <span class="text-danger">*</span></label>
                                                    <input type="date" class="form-control form-control-lg @error('receiving_date') is-invalid @enderror" id="receiving_date" name="receiving_date" value="{{ old('receiving_date', date('Y-m-d')) }}" required>
                                                    @error('receiving_date')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                                </div>
                                                <div class="form-group col-md-6 mb-3">
                                                    <label for="reporting_date">Reporting Date <span class="text-danger">*</span></label>
                                                    <input type="date" class="form-control form-control-lg @error('reporting_date') is-invalid @enderror" id="reporting_date" name="reporting_date" value="{{ old('reporting_date', date('Y-m-d')) }}" required>
                                                    @error('reporting_date')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                                </div>
                                            </div>
                                            <div class="form-row">
                                                <div class="form-group col-md-6 mb-3">
                                                    <label for="referred_by">Referred By</label>
                                                    <input type="text" class="form-control form-control-lg" id="referred_by" name="referred_by" value="{{ old('referred_by') }}">
                                                </div>
                                                <div class="form-group col-md-6 mb-3">
                                                    <label for="note">Note</label>
                                                    <textarea class="form-control form-control-lg" id="note" name="note" rows="2">{{ old('note') }}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-12">
                                    <div class="card border-0 shadow-sm mb-3">
                                        <div class="card-body py-3">
                                            <h5 class="mb-3 text-primary"><i class="fas fa-vials mr-2"></i>Select Tests <span class="text-danger">*</span></h5>
                                            @php
                                                $testCategories = DB::table('labtest_cat')
                                                    ->where('status', 1)
                                                    ->orderBy('cat_name', 'ASC')
                                                    ->get();
                                            @endphp
                                            @if($testCategories->count() > 0)
                                                <div class="row">
                                                    @foreach($testCategories as $category)
                                                        <div class="col-md-4 col-sm-6 mb-2">
                                                            <div class="custom-control custom-checkbox custom-checkbox-adv">
                                                                <input type="checkbox" 
                                                                       class="custom-control-input test-checkbox" 
                                                                       id="test_{{ $category->id }}" 
                                                                       value="{{ $category->cat_name }}" 
                                                                       data-price="{{ $category->price }}">
                                                                <label class="custom-control-label font-weight-bold" for="test_{{ $category->id }}">
                                                                    <i class="fas fa-flask text-info mr-1"></i>{{ $category->cat_name }}
                                                                    @if($category->price)
                                                                        <small class="text-muted">({{ number_format($category->price, 2) }} PKR)</small>
                                                                    @endif
                                                                </label>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                                <div id="test-category-container"></div>
                                            @else
                                                <div class="alert alert-warning">
                                                    <i class="fas fa-exclamation-triangle"></i> No test categories available. Please add test categories first from the <a href="{{ route('labtest') }}">Lab Test Category</a> section.
                                                </div>
                                            @endif
                                            <small class="text-muted d-block mt-2">Select at least one test</small>
                                            @error('test_category')<span class="text-danger d-block mt-1">{{ $message }}</span>@enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="text-right mt-4">
                                <a href="{{ route('patients.list') }}" class="btn btn-outline-secondary btn-lg mr-2"><i class="fas fa-arrow-left mr-1"></i>Cancel</a>
                                <button type="submit" class="btn btn-gradient-primary btn-lg" id="submitBtn"><i class="fas fa-user-plus mr-1"></i>Register Patient</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <style>
        .patient-reg-advanced .card {
            border-radius: 1rem;
        }
        .patient-reg-advanced .form-control-lg {
            font-size: 1.1rem;
            border-radius: 0.5rem;
        }
        .patient-reg-advanced .custom-checkbox-adv .custom-control-label {
            padding-left: 0.5rem;
            font-size: 1.08rem;
        }
        .patient-reg-advanced .custom-checkbox-adv .custom-control-input:checked ~ .custom-control-label {
            color: #007bff;
        }
        .patient-reg-advanced .btn-gradient-primary {
            background: linear-gradient(90deg, #007bff 0%, #00c6ff 100%);
            border: none;
            color: #fff;
            box-shadow: 0 2px 8px rgba(0,123,255,0.08);
        }
        .patient-reg-advanced .btn-gradient-primary:hover {
            background: linear-gradient(90deg, #0056b3 0%, #00aaff 100%);
        }
        .patient-reg-advanced .card-body h5 {
            letter-spacing: 0.5px;
        }
        .patient-reg-advanced .custom-checkbox-adv .custom-control-input:focus ~ .custom-control-label::before {
            box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
        }
    </style>
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