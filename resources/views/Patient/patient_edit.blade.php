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

        {{-- Test Data Edit Section --}}
        @php
            // Debug: Show if variables exist
            // dd([
            //     'selectedTests' => $selectedTests ?? [],
            //     'testsWithData' => $testsWithData ?? [],
            //     'patient_test_category' => $patient->test_category ?? 'null'
            // ]);
        @endphp
        @if(!empty($testsWithData))
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="header-title mb-3">
                                <i class="fas fa-flask"></i> Edit Test Data
                            </h4>

                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Test Name</th>
                                            <th>Status</th>
                                            <th>Last Updated</th>
                                            <th>Preview</th>
                                            <th width="120">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($testsWithData as $test)
                                            @php
                                                $testSlug = \Str::slug($test['name']);
                                                $hasData = $test['has_data'];
                                                $savedData = $test['saved_data'];
                                                $template = $test['template'];
                                                $hasTemplate = $test['has_template'] ?? false;
                                                $isMllpData = $test['is_mllp_data'] ?? false;
                                            @endphp

                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <i class="fas fa-{{ $isMllpData ? 'microchip' : 'vial' }} text-primary mr-2" style="font-size: 16px;"></i>
                                                        <div>
                                                            <strong>{{ $test['name'] }}</strong>
                                                            @if($isMllpData)
                                                                <br><small class="badge badge-info"><i class="fas fa-microchip"></i> From Analyzer</small>
                                                            @elseif(!$hasTemplate)
                                                                <br><small class="badge badge-info">Generic Template</small>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    @if($hasData)
                                                        <span class="badge badge-success">
                                                            <i class="fas fa-check-circle"></i> Completed
                                                        </span>
                                                    @else
                                                        <span class="badge badge-warning">
                                                            <i class="fas fa-clock"></i> Pending
                                                        </span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($hasData)
                                                        <small class="text-muted">
                                                            {{ $savedData['test_date'] ?? $savedData['reported_at'] ?? 'Unknown' }}
                                                        </small>
                                                    @else
                                                        <small class="text-muted text-danger">-</small>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($hasData)
                                                        <div class="d-flex align-items-center">
                                                            <small class="text-muted mr-2">
                                                                @php
                                                                    $preview = '';
                                                                    foreach($template['fields'] as $field) {
                                                                        $value = $savedData[$field['name']] ?? '';
                                                                        if (!empty($value)) {
                                                                            $preview = substr($value, 0, 40);
                                                                            break;
                                                                        }
                                                                    }
                                                                @endphp
                                                                {{ $preview ?? '-' }}{{ strlen($preview ?? '') > 40 ? '...' : '' }}
                                                            </small>
                                                            <a href="{{ route('patients.printTest', ['patient' => $patient->id, 'testName' => $test['name']]) }}" target="_blank" class="btn btn-sm btn-outline-secondary" title="Print Test Report">
                                                                <i class="fas fa-file-pdf"></i>
                                                            </a>
                                                        </div>
                                                    @else
                                                        <small class="text-muted">-</small>
                                                    @endif
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#testModal_{{ $testSlug }}" title="{{ $hasData ? 'Edit Test Data' : 'Add Test Data' }}">
                                                        <i class="fas fa-{{ $hasData ? 'edit' : 'plus' }}"></i>
                                                    </button>
                                                    <!-- PDF button moved to the Preview column -->
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @elseif(empty($selectedTests))
            <div class="row">
                <div class="col-12">
                    <div class="alert alert-info alert-dismissible fade show" role="alert">
                        <i class="fas fa-info-circle"></i> <strong>No tests registered</strong> - This patient has no tests registered. Please register tests during patient creation to edit test data.
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>
            </div>
        @endif

        {{-- Test Results Summary Section --}}
        @php
            $allReports = json_decode($patient->test_report ?? '[]', true) ?? [];
            // Filter out CBC reports (they have their own section)
            $testResults = array_filter($allReports, function($report) {
                return !isset($report['test']) || $report['test'] !== 'CBC';
            });
        @endphp

        @if(!empty($testResults))
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="header-title mb-3">
                                <i class="fas fa-file-alt"></i> Test Results Summary
                            </h4>

                            <div class="accordion" id="testResultsAccordion">
                                @foreach($testResults as $testName => $testData)
                                    @php
                                        $testSlug = \Str::slug($testName);
                                        $collapseId = 'testResult_' . $testSlug;
                                        $headingId = 'heading_' . $testSlug;
                                    @endphp

                                    <div class="card border-0 mb-2">
                                        <div class="card-header p-0 bg-light" id="{{ $headingId }}">
                                            <button class="btn btn-link btn-block text-left p-3" type="button" data-toggle="collapse" data-target="#{{ $collapseId }}" aria-expanded="false" aria-controls="{{ $collapseId }}">
                                                <div class="d-flex justify-content-between align-items-center w-100">
                                                    <div>
                                                        <h6 class="mb-0">
                                                            <i class="fas fa-chevron-right collapse-icon" style="transition: transform 0.3s;"></i>
                                                            <strong>{{ $testName }}</strong>
                                                        </h6>
                                                        <small class="text-muted">
                                                            Recorded: {{ $testData['test_date'] ?? 'Unknown date' }}
                                                        </small>
                                                    </div>
                                                    <span class="badge badge-success">
                                                        <i class="fas fa-check-circle"></i> Completed
                                                    </span>
                                                </div>
                                            </button>
                                        </div>

                                        <div id="{{ $collapseId }}" class="collapse" aria-labelledby="{{ $headingId }}" data-parent="#testResultsAccordion">
                                            <div class="card-body">
                                                {{-- Try to find the template to display nicely --}}
                                                @php
                                                    $template = $testTemplates[$testName] ?? null;
                                                @endphp

                                                @if($template)
                                                    {{-- Display with template fields --}}
                                                    <div class="table-responsive">
                                                        <table class="table table-sm table-bordered mb-0">
                                                            <thead class="thead-light">
                                                                <tr>
                                                                    <th>Field</th>
                                                                    <th>Value</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach($template['fields'] as $field)
                                                                    @php
                                                                        $value = $testData[$field['name']] ?? '';
                                                                    @endphp
                                                                    <tr>
                                                                        <td><strong>{{ $field['label'] }}</strong></td>
                                                                        <td>
                                                                            @if(!empty($value))
                                                                                <pre class="mb-0" style="background-color: #f8f9fa; padding: 8px; border-radius: 4px; white-space: pre-wrap; word-wrap: break-word;">{{ $value }}</pre>
                                                                            @else
                                                                                <span class="text-muted">-</span>
                                                                            @endif
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                                <tr class="bg-light">
                                                                    <td><strong>Test Date</strong></td>
                                                                    <td>{{ $testData['test_date'] ?? 'Unknown' }}</td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                @else
                                                    {{-- Display generic test data --}}
                                                    <div class="table-responsive">
                                                        <table class="table table-sm table-bordered mb-0">
                                                            <thead class="thead-light">
                                                                <tr>
                                                                    <th>Field</th>
                                                                    <th>Value</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach($testData as $key => $value)
                                                                    @if($key !== 'test_date')
                                                                        <tr>
                                                                            <td><strong>{{ ucfirst(str_replace('_', ' ', $key)) }}</strong></td>
                                                                            <td>
                                                                                @if(!empty($value))
                                                                                    <pre class="mb-0" style="background-color: #f8f9fa; padding: 8px; border-radius: 4px; white-space: pre-wrap; word-wrap: break-word;">{{ $value }}</pre>
                                                                                @else
                                                                                    <span class="text-muted">-</span>
                                                                                @endif
                                                                            </td>
                                                                        </tr>
                                                                    @endif
                                                                @endforeach
                                                                <tr class="bg-light">
                                                                    <td><strong>Test Date</strong></td>
                                                                    <td>{{ $testData['test_date'] ?? 'Unknown' }}</td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                @endif

                                                <div class="mt-3">
                                                    <button type="button" class="btn btn-sm btn-warning" data-toggle="modal" data-target="#testModal_{{ $testSlug }}">
                                                        <i class="fas fa-edit"></i> Edit Test Data
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="header-title mb-0">CBC Test Results</h4>
                            <button type="button" class="btn btn-primary" id="fetchCBCResults">
                                <i class="fas fa-sync-alt"></i> Fetch Latest CBC Results
                            </button>
                        </div>

                        <div id="cbcResultsContainer">
                            @php
                                $allReports = json_decode($patient->test_report ?? '[]', true) ?? [];
                                // Filter only CBC reports
                                $cbcReports = array_filter($allReports, function($report) {
                                    return isset($report['test']) && $report['test'] === 'CBC';
                                });
                            @endphp

                            @forelse($cbcReports as $report)
                                @php
                                    $analytes = $report['analytes'] ?? [];
                                @endphp

                                <div class="card mb-3">
                                    <div class="card-header bg-light">
                                        <strong>{{ $report['instrument'] ?? 'CBC' }}</strong>
                                        <span class="float-right text-muted">
                                            {{ $report['reported_at'] ?? 'Unknown date' }}
                                        </span>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-sm table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>Parameter</th>
                                                        <th>Value</th>
                                                        <th>Units</th>
                                                        <th>Reference Range</th>
                                                        <th>Flag</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($analytes as $analyte)
                                                        <tr>
                                                            <td><strong>{{ $analyte['name'] }}</strong></td>
                                                            <td>{{ $analyte['value'] }}</td>
                                                            <td>{{ $analyte['units'] ?? '-' }}</td>
                                                            <td>{{ $analyte['ref_range'] ?? '-' }}</td>
                                                            <td>
                                                                @if(isset($analyte['flags']) && $analyte['flags'] !== 'N')
                                                                    <span class="badge badge-warning">{{ $analyte['flags'] }}</span>
                                                                @else
                                                                    <span class="badge badge-success">Normal</span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i> No CBC results found for this patient.
                                    Click "Fetch Latest CBC Results" to check for new data.
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    @if(!empty($testsWithData))
        @foreach($testsWithData as $test)
            @php
                $testSlug = \Str::slug($test['name']);
                $template = $test['template'];
                $saved = $test['saved_data'];
            @endphp

            <div class="modal fade" id="testModal_{{ $testSlug }}" tabindex="-1" role="dialog" aria-labelledby="testModalLabel_{{ $testSlug }}" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title" id="testModalLabel_{{ $testSlug }}">
                                <i class="fas fa-flask"></i> {{ $test['name'] }} - Test Results
                            </h5>
                            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form class="test-data-form" data-test-name="{{ $test['name'] }}" data-patient-id="{{ $patient->id }}">
                            <div class="modal-body">
                                @foreach($template['fields'] as $field)
                                    <div class="form-group">
                                        <label for="{{ $testSlug }}_{{ $field['name'] }}">
                                            {{ $field['label'] }}
                                            @if($field['required'] ?? false)
                                                <span class="text-danger">*</span>
                                            @endif
                                        </label>

                                        @switch($field['type'] ?? 'text')
                                            @case('textarea')
                                                <textarea id="{{ $testSlug }}_{{ $field['name'] }}"
                                                          name="{{ $field['name'] }}"
                                                          class="form-control"
                                                          rows="4"
                                                          {{ ($field['required'] ?? false) ? 'required' : '' }}>{{ $saved[$field['name']] ?? '' }}</textarea>
                                                @break

                                            @case('number')
                                                <input type="number"
                                                       id="{{ $testSlug }}_{{ $field['name'] }}"
                                                       name="{{ $field['name'] }}"
                                                       class="form-control"
                                                       value="{{ $saved[$field['name']] ?? '' }}"
                                                       step="{{ $field['step'] ?? '1' }}"
                                                       {{ ($field['required'] ?? false) ? 'required' : '' }}>
                                                @break

                                            @default
                                                <input type="text"
                                                       id="{{ $testSlug }}_{{ $field['name'] }}"
                                                       name="{{ $field['name'] }}"
                                                       class="form-control"
                                                       value="{{ $saved[$field['name']] ?? '' }}"
                                                       {{ ($field['required'] ?? false) ? 'required' : '' }}>
                                        @endswitch
                                    </div>
                                @endforeach

                                <div class="form-group">
                                    <label for="{{ $testSlug }}_test_date">Test Date <span class="text-danger">*</span></label>
                                    <input type="date"
                                           id="{{ $testSlug }}_test_date"
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
        @endforeach
    @endif

@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            // Chevron icon animation for test results accordion
            $('[data-toggle="collapse"]').on('click', function() {
                $(this).find('.collapse-icon').toggleClass('fa-chevron-right fa-chevron-down');
            });

            // Auto-rotate chevrons on load for already expanded items
            $('.collapse.show').each(function() {
                var target = $(this).attr('id');
                $('[data-target="#' + target + '"] .collapse-icon').addClass('fa-chevron-down').removeClass('fa-chevron-right');
            });

            // Fetch CBC Results
            $('#fetchCBCResults').on('click', function () {
                const btn = $(this);
                const originalHtml = btn.html();
                
                btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Fetching...');
                
                setTimeout(function () {
                    window.location.reload();
                }, 1000);
            });

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
                    url: "{{ route('patients.storeTestData') }}",
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