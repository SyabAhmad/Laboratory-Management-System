@extends('Layout.master')
@section('title', 'Edit Patient')

@section('content')

    <div class="container-fluid">

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
                    <h4 class="page-title">Edit Patient - {{ $patient->name }}</h4>
                </div>
            </div>
        </div>

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

                        <form method="POST" action="{{ route('patients.update', $patient->id) }}">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name">Full Name <span class="text-danger">*</span></label>
                                        <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $patient->name) }}" required>
                                        @error('name')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="mobile_phone">Mobile Number <span class="text-danger">*</span></label>
                                        <input type="text" id="mobile_phone" name="mobile_phone" class="form-control @error('mobile_phone') is-invalid @enderror" value="{{ old('mobile_phone', $patient->mobile_phone) }}" required>
                                        @error('mobile_phone')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="gender">Gender <span class="text-danger">*</span></label>
                                        <select id="gender" name="gender" class="form-control @error('gender') is-invalid @enderror" required>
                                            <option value="">Choose One Option</option>
                                            <option value="Male" {{ old('gender', $patient->gender) === 'Male' ? 'selected' : '' }}>Male</option>
                                            <option value="Female" {{ old('gender', $patient->gender) === 'Female' ? 'selected' : '' }}>Female</option>
                                            <option value="Other" {{ old('gender', $patient->gender) === 'Other' ? 'selected' : '' }}>Other</option>
                                        </select>
                                        @error('gender')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="age">Age <span class="text-danger">*</span></label>
                                        <input type="number" id="age" name="age" class="form-control @error('age') is-invalid @enderror" value="{{ old('age', $patient->age) }}" required>
                                        @error('age')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="blood_group">Blood Group</label>
                                        <select id="blood_group" name="blood_group" class="form-control">
                                            <option value="">Choose One Option</option>
                                            @foreach(['A+','A-','B+','B-','O+','O-','AB+','AB-'] as $group)
                                                <option value="{{ $group }}" {{ old('blood_group', $patient->blood_group) === $group ? 'selected' : '' }}>{{ $group }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="address">Address <span class="text-danger">*</span></label>
                                        <textarea id="address" name="address" rows="3" class="form-control @error('address') is-invalid @enderror" required>{{ old('address', $patient->address) }}</textarea>
                                        @error('address')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="receiving_date">Receiving Date <span class="text-danger">*</span></label>
                                        <input type="date" id="receiving_date" name="receiving_date" class="form-control @error('receiving_date') is-invalid @enderror" value="{{ old('receiving_date', $patient->receiving_date?->format('Y-m-d')) }}" required>
                                        @error('receiving_date')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="reporting_date">Reporting Date <span class="text-danger">*</span></label>
                                        <input type="date" id="reporting_date" name="reporting_date" class="form-control @error('reporting_date') is-invalid @enderror" value="{{ old('reporting_date', $patient->reporting_date?->format('Y-m-d')) }}" required>
                                        @error('reporting_date')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="referred_by">Referred By</label>
                                        <input type="text" id="referred_by" name="referred_by" class="form-control" value="{{ old('referred_by', $patient->referred_by) }}">
                                    </div>

                                    <div class="form-group">
                                        <label for="note">Note</label>
                                        <textarea id="note" name="note" rows="3" class="form-control">{{ old('note', $patient->note) }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="text-right">
                                <a href="{{ route('patients.list') }}" class="btn btn-secondary">Cancel</a>
                                <button type="submit" class="btn btn-primary">Update Patient</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        @php
            $selectedTests = $patient->test_category_array ?? [];
            $testTemplates = config('test_templates');
            $existingTestReports = json_decode($patient->test_report ?? '[]', true) ?? [];
        @endphp

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title mb-3">Test Results</h4>

                        @if(!empty($selectedTests))
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Test Name</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($selectedTests as $test)
                                            <tr>
                                                <td><strong>{{ $test }}</strong></td>
                                                <td>
                                                    @if(isset($existingTestReports[$test]))
                                                        <span class="badge badge-success">Completed</span>
                                                    @else
                                                        <span class="badge badge-warning">Pending</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <button type="button"
                                                            class="btn btn-sm btn-info"
                                                            data-toggle="modal"
                                                            data-target="#testModal_{{ \Str::slug($test) }}">
                                                        <i class="fas fa-edit"></i>
                                                        {{ isset($existingTestReports[$test]) ? 'Edit' : 'Add' }} Data
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> No tests selected for this patient.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    </div>

    @if(!empty($selectedTests))
        @foreach($selectedTests as $test)
            @php
                $testSlug = \Str::slug($test);
                $template = $testTemplates[$test] ?? null;
                $saved = $existingTestReports[$test] ?? [];
            @endphp

            @if($template)
                <div class="modal fade" id="testModal_{{ $testSlug }}" tabindex="-1" role="dialog" aria-labelledby="testModalLabel_{{ $testSlug }}" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header bg-primary text-white">
                                <h5 class="modal-title" id="testModalLabel_{{ $testSlug }}">
                                    <i class="fas fa-flask"></i> {{ $test }} - Test Results
                                </h5>
                                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <form class="test-data-form" data-test-name="{{ $test }}" data-patient-id="{{ $patient->id }}">
                                <div class="modal-body">
                                    @foreach($template['fields'] as $field)
                                        <div class="form-group">
                                            <label for="{{ $testSlug }}_{{ $field['name'] }}">
                                                {{ $field['label'] }}
                                                @if($field['required'])
                                                    <span class="text-danger">*</span>
                                                @endif
                                            </label>

                                            @switch($field['type'])
                                                @case('textarea')
                                                    <textarea id="{{ $testSlug }}_{{ $field['name'] }}"
                                                              name="{{ $field['name'] }}"
                                                              class="form-control"
                                                              rows="4"
                                                              {{ $field['required'] ? 'required' : '' }}>{{ $saved[$field['name']] ?? '' }}</textarea>
                                                    @break

                                                @case('number')
                                                    <input type="number"
                                                           id="{{ $testSlug }}_{{ $field['name'] }}"
                                                           name="{{ $field['name'] }}"
                                                           class="form-control"
                                                           value="{{ $saved[$field['name']] ?? '' }}"
                                                           step="{{ $field['step'] ?? '1' }}"
                                                           {{ $field['required'] ? 'required' : '' }}>
                                                    @break

                                                @default
                                                    <input type="text"
                                                           id="{{ $testSlug }}_{{ $field['name'] }}"
                                                           name="{{ $field['name'] }}"
                                                           class="form-control"
                                                           value="{{ $saved[$field['name']] ?? '' }}"
                                                           {{ $field['required'] ? 'required' : '' }}>
                                            @endswitch
                                        </div>
                                    @endforeach

                                    <div class="form-group">
                                        <label for="{{ $testSlug }}_test_date">Test Date <span class="text-danger">*</span></label>
                                        <input type="date"
                                               id="{{ $testSlug }}_test_date }}"
                                               name="test_date"
                                               class="form-control"
                                               value="{{ $saved['test_date'] ?? now()->format('Y-m-d') }}"
                                               required>
                                    </div>
                                </div>

                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Save Test Data
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endif
        @endforeach
    @endif

@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            $('.test-data-form').on('submit', function (e) {
                e.preventDefault();

                const form = $(this);
                const submitBtn = form.find('button[type="submit"]');
                const originalBtnHtml = submitBtn.html();

                const testName = form.data('test-name');
                const patientId = form.data('patient-id');
                const csrfToken = $('meta[name="csrf-token"]').attr('content');

                const formData = new FormData();
                formData.append('patient_id', patientId);
                formData.append('test_name', testName);
                formData.append('_token', csrfToken);

                form.find('input, textarea, select').each(function () {
                    const field = $(this);
                    const name = field.attr('name');
                    if (name) {
                        formData.append(`test_data[${name}]`, field.val());
                    }
                });

                submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving…');

                $.ajax({
                    url: '{{ route('patients.storeTestData') }}',
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'json'
                })
                .done(function (response) {
                    if (response.success) {
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Saved',
                                text: response.message ?? 'Test data saved.',
                                timer: 1800,
                                showConfirmButton: false
                            });
                        }
                        setTimeout(function () {
                            form.closest('.modal').modal('hide');
                            window.location.reload();
                        }, 1800);
                    } else {
                        const message = response.message ?? 'Failed to save test data.';
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({ icon: 'error', title: 'Error', text: message });
                        } else {
                            alert(message);
                        }
                    }
                })
                .fail(function (xhr) {
                    let message = 'Failed to save test data.';
                    if (xhr.status === 422 && xhr.responseJSON?.errors) {
                        message = Object.values(xhr.responseJSON.errors).flat().join(' ');
                    } else if (xhr.responseJSON?.message) {
                        message = xhr.responseJSON.message;
                    }
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({ icon: 'error', title: 'Error', text: message });
                    } else {
                        alert(message);
                    }
                })
                .always(function () {
                    submitBtn.prop('disabled', false).html(originalBtnHtml);
                });
            });
        });
    </script>
@endsection