@extends('Layout.master')
@section('title', 'Patients')

@section('content')

    <div class="container-fluid patient-reg-advanced">
        <!-- Modern Card Layout -->
        <div class="row justify-content-center">
            <div class="col-lg-11 col-xl-10">
                <div class="card dashboard-card mt-4 mb-5">
                    <div class="card-body p-5">
                        <div class="text-center mb-5">
                            <div class="icon-header mb-3">
                                <i class="fas fa-user-plus"></i>
                            </div>
                            <h2 class="mb-2 font-weight-bold text-heading">Patient Registration</h2>
                            <p class="text-muted mb-0">Fill in the details to register a new patient</p>
                        </div>
                        <form method="POST" action="{{ route('patients.store') }}" enctype="multipart/form-data"
                            id="patientRegistrationForm" autocomplete="off">
                            @csrf
                            <div class="row">
                                <div class="col-lg-6 col-md-12 mb-4">
                                    <div class="section-card">
                                        <div class="section-header">
                                            <i class="fas fa-user"></i>
                                            <h4>Personal Information</h4>
                                        </div>
                                        <div class="section-body">
                                            <div class="form-group mb-4">
                                                <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                                <input type="text"
                                                    class="form-control modern-input @error('name') is-invalid @enderror"
                                                    id="name" name="name" value="{{ old('name') }}" required
                                                    placeholder="Enter full name">
                                                @error('name')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div class="form-group mb-4">
                                                <label for="mobile_phone" class="form-label">Mobile Number <span class="text-danger">*</span></label>
                                                <input type="text"
                                                    class="form-control modern-input @error('mobile_phone') is-invalid @enderror"
                                                    id="mobile_phone" name="mobile_phone" value="{{ old('mobile_phone') }}"
                                                    required placeholder="Enter mobile number">
                                                @error('mobile_phone')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 mb-4">
                                                    <label for="gender" class="form-label">Gender <span class="text-danger">*</span></label>
                                                    <select class="form-control modern-input @error('gender') is-invalid @enderror"
                                                        id="gender" name="gender" required>
                                                        <option value="">Choose gender</option>
                                                        <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>Male</option>
                                                        <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>Female</option>
                                                        <option value="Other" {{ old('gender') == 'Other' ? 'selected' : '' }}>Other</option>
                                                    </select>
                                                    @error('gender')
                                                        <span class="invalid-feedback">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                                <div class="col-md-6 mb-4">
                                                    <label for="age" class="form-label">Age <span class="text-danger">*</span></label>
                                                    <input type="number"
                                                        class="form-control modern-input @error('age') is-invalid @enderror"
                                                        id="age" name="age" value="{{ old('age') }}" required
                                                        placeholder="Enter age" min="1" max="120">
                                                    @error('age')
                                                        <span class="invalid-feedback">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="form-group mb-0">
                                                <label for="blood_group" class="form-label">Blood Group</label>
                                                <select class="form-control modern-input" id="blood_group" name="blood_group">
                                                    <option value="">Choose blood group</option>
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
                                <div class="col-lg-6 col-md-12 mb-4">
                                    <div class="section-card">
                                        <div class="section-header">
                                            <i class="fas fa-address-card"></i>
                                            <h4>Contact & Dates</h4>
                                        </div>
                                        <div class="section-body">
                                            <div class="form-group mb-4">
                                                <label for="address" class="form-label">Address <span class="text-danger">*</span></label>
                                                <textarea class="form-control modern-input @error('address') is-invalid @enderror"
                                                    id="address" name="address" rows="3" required
                                                    placeholder="Enter complete address">{{ old('address') }}</textarea>
                                                @error('address')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 mb-4">
                                                    <label for="receiving_date" class="form-label">Receiving Date <span class="text-danger">*</span></label>
                                                    <input type="date"
                                                        class="form-control modern-input @error('receiving_date') is-invalid @enderror"
                                                        id="receiving_date" name="receiving_date"
                                                        value="{{ old('receiving_date', date('Y-m-d')) }}" required>
                                                    @error('receiving_date')
                                                        <span class="invalid-feedback">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                                <div class="col-md-6 mb-4">
                                                    <label for="reporting_date" class="form-label">Reporting Date <span class="text-danger">*</span></label>
                                                    <input type="date"
                                                        class="form-control modern-input @error('reporting_date') is-invalid @enderror"
                                                        id="reporting_date" name="reporting_date"
                                                        value="{{ old('reporting_date', date('Y-m-d')) }}" required>
                                                    @error('reporting_date')
                                                        <span class="invalid-feedback">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 mb-4">
                                                    <label for="referred_by" class="form-label">Referred By</label>
                                                    <select class="form-control modern-input" id="referred_by" name="referred_by">
                                                        <option value="">Select Referral</option>
                                                        @php
                                                            $referrals = DB::table('referrals')->orderBy('name')->get();
                                                        @endphp
                                                        @foreach($referrals as $referral)
                                                            <option value="{{ $referral->name }}" {{ old('referred_by') == $referral->name ? 'selected' : '' }}>
                                                                {{ $referral->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-6 mb-0">
                                                    <label for="note" class="form-label">Note</label>
                                                    <textarea class="form-control modern-input" id="note" name="note"
                                                        rows="3" placeholder="Additional notes">{{ old('note') }}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="section-card">
                                        <div class="section-header">
                                            <i class="fas fa-vials"></i>
                                            <h4>Select Tests <span class="text-danger">*</span></h4>
                                        </div>
                                        <div class="section-body">
                                            @php
                                                $testCategories = DB::table('labtest_cat')
                                                    ->where('status', 1)
                                                    ->orderBy('cat_name', 'ASC')
                                                    ->get();
                                            @endphp
                                            @if ($testCategories->count() > 0)
                                                <div class="test-grid">
                                                    @foreach ($testCategories as $category)
                                                        <div class="test-item">
                                                            <div class="custom-control custom-checkbox modern-checkbox">
                                                                <input type="checkbox"
                                                                    class="custom-control-input test-checkbox"
                                                                    id="test_{{ $category->id }}"
                                                                    value="{{ $category->cat_name }}"
                                                                    data-price="{{ $category->price }}">
                                                                <label class="custom-control-label" for="test_{{ $category->id }}">
                                                                    <div class="test-content">
                                                                        <i class="fas fa-flask test-icon"></i>
                                                                        <div class="test-details">
                                                                            <span class="test-name">{{ $category->cat_name }}</span>
                                                                            @if ($category->price)
                                                                                <span class="test-price">{{ number_format($category->price, 2) }} PKR</span>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                </label>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                                <div id="test-category-container"></div>
                                            @else
                                                <div class="alert alert-warning modern-alert">
                                                    <i class="fas fa-exclamation-triangle"></i>
                                                    <strong>No test categories available.</strong> Please add test categories first from the
                                                    <a href="{{ route('labtest.index') }}" class="alert-link">Lab Test Category</a> section.
                                                </div>
                                            @endif
                                            <div class="form-text mt-3">
                                                <i class="fas fa-info-circle text-muted mr-1"></i>
                                                Select at least one test to proceed with registration
                                            </div>
                                            @error('test_category')
                                                <div class="invalid-feedback d-block mt-2">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-actions mt-5">
                                <a href="{{ route('patients.list') }}" class="btn btn-secondary-modern">
                                    <i class="fas fa-arrow-left mr-2"></i>Cancel
                                </a>
                                <button type="submit" class="btn btn-primary-modern" id="submitBtn">
                                    <i class="fas fa-user-plus mr-2"></i>Register Patient
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <style>
        /* Modern Form Styles */
        .patient-reg-advanced {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            min-height: 100vh;
            padding: 2rem 0;
        }

        .icon-header {
            width: 80px;
            height: 80px;
            background: var(--primary);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--surface);
            font-size: 2.5rem;
            margin: 0 auto;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .text-heading {
            color: var(--primary);
            font-size: 2.2rem;
        }

        .section-card {
            background: white;
            border-radius: 1rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(0, 0, 0, 0.05);
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .section-card:hover {
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
            transform: translateY(-2px);
        }

        .section-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            padding: 1.5rem 2rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .section-header i {
            font-size: 1.5rem;
            opacity: 0.9;
        }

        .section-header h4 {
            margin: 0;
            font-size: 1.25rem;
            font-weight: 600;
        }

        .section-body {
            padding: 2rem;
        }

        .modern-input {
            border: 2px solid #e2e8f0;
            border-radius: 0.75rem;
            padding: 0rem 1rem;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: #f8fafc;
        }

        .modern-input:focus {
            border-color: var(--primary);
            background: white;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
            outline: none;
        }

        .modern-input.is-invalid {
            border-color: #ef4444;
            background: #fef2f2;
        }

        .modern-input.is-invalid:focus {
            border-color: #ef4444;
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
        }

        .form-label {
            font-weight: 600;
            color: #374151;
            margin-bottom: 0.5rem;
            font-size: 0.95rem;
        }

        .test-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .test-item {
            background: #f8fafc;
            border: 2px solid #e2e8f0;
            border-radius: 0.75rem;
            padding: 1rem;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .test-item:hover {
            border-color: var(--primary);
            background: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .modern-checkbox .custom-control-input:checked ~ .custom-control-label {
            background: transparent;
        }

        .modern-checkbox .custom-control-input:checked ~ .custom-control-label .test-item {
            border-color: var(--primary);
            background: rgba(37, 99, 235, 0.05);
        }

        .test-content {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .test-icon {
            color: var(--primary);
            font-size: 1.5rem;
            width: 40px;
            text-align: center;
        }

        .test-details {
            flex: 1;
        }

        .test-name {
            display: block;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 0.25rem;
        }

        .test-price {
            color: #6b7280;
            font-size: 0.9rem;
        }

        .modern-alert {
            border-radius: 0.75rem;
            border: none;
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            color: #92400e;
        }

        .form-actions {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-top: 2rem;
        }

        .btn-secondary-modern {
            background: #6b7280;
            border: none;
            color: white;
            padding: 0.75rem 2rem;
            border-radius: 0.75rem;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-secondary-modern:hover {
            background: #4b5563;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            color: white;
            text-decoration: none;
        }

        .btn-primary-modern {
            background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary) 100%);
            border: none;
            color: black;
            padding: 0.75rem 2rem;
            border-radius: 0.75rem;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            box-shadow: 0 6px 20px rgba(37, 99, 235, 0.4);
        }

        .btn-primary-modern:hover {
            transform: translateY(-2px);
            box-shadow:  0 4px 15px rgba(37, 99, 235, 0.3);
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            
        }

        .form-text {
            color: #6b7280;
            font-size: 0.9rem;
        }

        @media (max-width: 768px) {
            .patient-reg-advanced {
                padding: 1rem 0;
            }

            .icon-header {
                width: 60px;
                height: 60px;
                font-size: 2rem;
            }

            .text-heading {
                font-size: 1.8rem;
            }

            .section-body {
                padding: 1.5rem;
            }

            .test-grid {
                grid-template-columns: 1fr;
            }

            .form-actions {
                flex-direction: column;
                align-items: stretch;
            }

            .btn-secondary-modern,
            .btn-primary-modern {
                justify-content: center;
            }
        }
    </style>
@endsection

@section('scripts')
    <script>
        (function() {
            'use strict';

            document.addEventListener('DOMContentLoaded', function() {
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
