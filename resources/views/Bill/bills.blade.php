@extends('Layout.master')
@section('title', 'Patient Billing')
@section('content')

    <div class="container-fluid">
        <!-- PAGE HEADER -->
        <div class="row mb-5">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center flex-wrap">
                            <div>
                                <!-- <h2 class="mb-2 text-white fw-bold">
                                    <i class="fas fa-file-invoice-dollar me-3"></i>Patient Billing System
                                </h2>
                                <p class="text-white-50 mb-0 fs-5">Create and manage patient bills efficiently</p> -->
                            </div>
                            <nav aria-label="breadcrumb" class="mt-3 mt-md-0">
                                <ol class="breadcrumb bg-transparent p-0 m-0">
                                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-white-75"><i class="fas fa-home me-1"></i>Home</a></li>
                                    <li class="breadcrumb-item"><a href="{{ route('patients.list') }}" class="text-white-75">Patients</a></li>
                                    <li class="breadcrumb-item active text-white" aria-current="page">Billing</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-between align-items-center flex-wrap bg-primary p-4 rounded shadow-sm">
                            <div>
                                <h2 class="mb-2 text-white fw-bold">
                                    <i class="fas fa-file-invoice-dollar me-3"></i>Patient Billing System
                                </h2>
                                <p class="text-white-50 mb-0 fs-5">Create and manage patient bills efficiently</p>
                            </div>
                            <!-- <nav aria-label="breadcrumb" class="mt-3 mt-md-0">
                                <ol class="breadcrumb bg-transparent p-0 m-0">
                                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-white-75"><i class="fas fa-home me-1"></i>Home</a></li>
                                    <li class="breadcrumb-item"><a href="{{ route('patients.list') }}" class="text-white-75">Patients</a></li>
                                    <li class="breadcrumb-item active text-white" aria-current="page">Billing</li>
                                </ol>
                            </nav> -->
                        </div>
                
            </div>
            
        </div>

        @php
            $latest = App\Models\Bills::latest()->first();
            $nextInvoiceNumber = !$latest
                ? '#000001'
                : '#' . str_pad((int) preg_replace('/\D/', '', $latest->bill_no) + 1, 6, '0', STR_PAD_LEFT);
        @endphp

        <!-- BILLING FORM -->
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <strong>New Bill Entry - {{ $patient->name }}</strong>
            </div>

            <div class="card-body">
                <form id="CustomerBillForm" method="POST" class="row g-3">
                    @csrf
                    <input type="hidden" name="bill_no" value="{{ $nextInvoiceNumber }}">
                    <input type="hidden" name="patient_id" value="{{ $patient->id }}">

                    <!-- Patient Info Display -->
                    <div class="col-12 mb-4">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-light">
                                <h6 class="mb-0 text-primary">
                                    <i class="fas fa-user-circle me-2"></i>Patient Information
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-id-card text-muted me-2"></i>
                                            <div>
                                                <small class="text-muted d-block">Patient ID</small>
                                                <strong>{{ $patient->patient_id }}</strong>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-user text-muted me-2"></i>
                                            <div>
                                                <small class="text-muted d-block">Name</small>
                                                <strong>{{ $patient->name }}</strong>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-birthday-cake text-muted me-2"></i>
                                            <div>
                                                <small class="text-muted d-block">Age / Gender</small>
                                                <strong>{{ $patient->age }} / {{ $patient->gender }}</strong>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-phone text-muted me-2"></i>
                                            <div>
                                                <small class="text-muted d-block">Phone</small>
                                                <strong>{{ $patient->mobile_phone }}</strong>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Invoice Number -->
                    <div class="col-md-6">
                        <label for="bill_display" class="form-label fw-semibold">Invoice Number</label>
                        <input type="text" class="form-control" id="bill_display" value="{{ $nextInvoiceNumber }}"
                            readonly>
                    </div>

                    {{-- <!-- Payment Type -->
                    <div class="col-md-6">
                        <label for="paidby" class="form-label fw-semibold">Payment Type</label>
                        <select class="form-select" id="paidby" name="paidby" required>
                            <option value="Cash">Cash</option>
                            <option value="Card">Card</option>
                            <option value="Mobile Banking">Mobile Banking</option>
                        </select>
                    </div> --}}

                    <!-- Registered Tests Section -->
                    @if (isset($registeredTests) && $registeredTests->count() > 0)
                        <div class="col-12 mt-4">
                            <div class="card border-0 shadow-sm">
                                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-0">
                                            <i class="fas fa-check-circle me-2"></i>Registered Tests for This Patient
                                        </h6>
                                        <small class="text-white-50">These tests will be automatically added to the bill</small>
                                    </div>
                                    <button type="button" id="refreshRegisteredTests" class="btn btn-light btn-sm"
                                        data-fetch-url="{{ route('billing.registeredTests', $patient->id) }}">
                                        <i class="fas fa-sync-alt me-1"></i> Refresh
                                    </button>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-hover align-middle mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th class="border-0 fw-semibold">#</th>
                                                    <th class="border-0 fw-semibold">Test Name</th>
                                                    <th class="border-0 fw-semibold">Department</th>
                                                    <th class="border-0 fw-semibold">Price (PKR)</th>
                                                    <th class="border-0 fw-semibold">Status</th>
                                                </tr>
                                            </thead>
                                            <tbody id="registeredTestsList">
                                                @foreach ($registeredTests as $test)
                                                    <tr data-test-id="{{ $test->id }}" data-test-name="{{ $test->cat_name }}"
                                                        data-test-price="{{ $test->price }}"
                                                        data-test-dept="{{ $test->department ?? 'N/A' }}"
                                                        class="table-row-hover">
                                                        <td class="fw-semibold">{{ $loop->iteration }}</td>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <i class="fas fa-flask text-success me-2"></i>
                                                                {{ $test->cat_name }}
                                                            </div>
                                                        </td>
                                                        <td><span class="badge bg-light text-dark">{{ $test->department ?? 'N/A' }}</span></td>
                                                        <td class="fw-semibold text-success">{{ number_format($test->price, 2) }}</td>
                                                        <td><span class="badge bg-success"><i class="fas fa-check me-1"></i>Auto-added</span></td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Additional Tests Section -->
                    <div class="col-12 mt-4">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-primary text-white">
                                <h6 class="mb-0">
                                    <i class="fas fa-flask me-2"></i>Add Additional Tests (Optional)
                                </h6>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="border-0 fw-semibold">#</th>
                                                <th class="border-0 fw-semibold">Test Name</th>
                                                <th class="border-0 fw-semibold">Department</th>
                                                <th class="border-0 fw-semibold">Price (PKR)</th>
                                                <th class="border-0 fw-semibold">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($tests as $test)
                                                <tr class="table-row-hover">
                                                    <td class="fw-semibold">{{ $loop->iteration }}</td>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <i class="fas fa-vial text-primary me-2"></i>
                                                            {{ $test->cat_name }}
                                                        </div>
                                                    </td>
                                                    <td><span class="badge bg-light text-dark">{{ $test->department }}</span></td>
                                                    <td class="fw-semibold text-primary">{{ number_format($test->price, 2) }}</td>
                                                    <td>
                                                        <button type="button" class="btn btn-sm btn-outline-primary add-test-btn"
                                                            data-id="{{ $test->id }}" data-name="{{ $test->cat_name }}"
                                                            data-price="{{ $test->price }}" data-dept="{{ $test->department }}">
                                                            <i class="fas fa-plus me-1"></i>Add
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

                    <!-- Selected Tests for Bill -->
                    <div class="col-12 mt-4">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-info text-white">
                                <h6 class="mb-0">
                                    <i class="fas fa-clipboard-list me-2"></i>Tests in Current Bill
                                </h6>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0" id="selectedTests">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="border-0 fw-semibold">#</th>
                                                <th class="border-0 fw-semibold">Test Name</th>
                                                <th class="border-0 fw-semibold">Price (PKR)</th>
                                                <th class="border-0 fw-semibold">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if (!isset($registeredTests) || $registeredTests->count() === 0)
                                                <tr class="no-tests-row">
                                                    <td colspan="4" class="text-center py-5">
                                                        <div class="text-muted">
                                                            <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                                            <h6>No tests selected yet</h6>
                                                            <p class="mb-0">Add tests from the sections above to create your bill</p>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Summary Section -->
                    <div class="col-12 mt-5">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-warning text-dark py-3">
                                <h6 class="mb-0 fw-bold">
                                    <i class="fas fa-calculator me-2 ml-2"></i>   Bill Summary & Payment
                                </h6>
                            </div>
                            <div class="card-body py-4">
                                <div class="row g-4">
                                    <div class="col-md-12">
                                        <div class="row g-4">
                                            <div class="col-12">
                                                <div class="d-flex justify-content-between align-items-center p-4 bg-light rounded shadow-sm">
                                                    <span class="fw-semibold fs-6">Subtotal:</span>
                                                    <span class="fs-4 fw-bold text-primary" id="subtotal-display">PKR 0.00</span>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <label for="discount" class="form-label fw-semibold mb-2">Discount (PKR)</label>
                                                <input type="number" step="0.01" class="form-control form-control-lg py-3" id="discount"
                                                    name="discount" value="0" placeholder="0.00">
                                            </div>
                                            <div class="col-12 mt-4">
                                                <div class="d-flex justify-content-between align-items-center p-4 bg-success text-white rounded shadow-sm">
                                                    <span class="fw-semibold fs-6">Total Amount:</span>
                                                    <span class="fs-4 fw-bold" id="total-display">PKR 0.00</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="row g-4 mt-4">
                                            <div class="col-12 ">
                                                <label for="pay_" class="form-label fw-semibold mb-2">Amount Paid (PKR)</label>
                                                <input type="number" step="0.01" class="form-control form-control-lg py-3" id="pay_"
                                                    name="pay" value="0" placeholder="0.00">
                                            </div>
                                            <div class="col-12 mt-4">
                                                <div class="d-flex justify-content-between align-items-center p-4 bg-info text-white rounded shadow-sm">
                                                    <span class="fw-semibold fs-6">Due / Return:</span>
                                                    <span class="fs-4 fw-bold" id="due-display">PKR 0.00</span>
                                                </div>
                                            </div>
                                            <!-- <div class="col-12">
                                                <label for="abbroval_code" class="form-label fw-semibold mb-2">Approval Code (Optional)</label>
                                                <input type="text" class="form-control py-3" id="abbroval_code" name="abbroval_code"
                                                    placeholder="Enter approval code if applicable">
                                            </div> -->
                                        </div>
                                    </div>
                                </div>
                                <!-- Hidden inputs for form submission -->
                                <input type="hidden" id="grandtotal" name="gtotal" value="0.00">
                                <input type="hidden" id="total_" name="total_" value="0.00">
                                <input type="hidden" id="return" name="return" value="0.00">
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="col-12 text-end mt-4">
                        <a href="{{ route('patients.list') }}" class="btn btn-secondary btn-lg px-4">
                            <i class="fas fa-arrow-left me-2"></i> Back to Patients
                        </a>
                        <button type="submit" class="btn btn-success btn-lg px-4 ms-3">
                            <i class="fas fa-save me-2"></i> Save Bill
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script>
        function updateTotals() {
            let subtotal = 0;
            $('#selectedTests tbody tr[data-id]').each(function() {
                const priceInput = $(this).find('input[name="price[]"]');
                if (priceInput.length) {
                    subtotal += parseFloat(priceInput.val()) || 0;
                }
            });

            const discount = parseFloat($('#discount').val()) || 0;
            const total = subtotal - discount;
            const paid = parseFloat($('#pay_').val()) || 0;
            const due = total - paid;

            // Update hidden inputs for form submission
            $('#grandtotal').val(subtotal.toFixed(2));
            $('#total_').val(total.toFixed(2));
            $('#return').val(due.toFixed(2));

            // Update display elements with formatted currency
            $('#subtotal-display').text('PKR ' + subtotal.toFixed(2));
            $('#total-display').text('PKR ' + total.toFixed(2));
            $('#due-display').text('PKR ' + due.toFixed(2));
        }

        function renumberSelectedTests() {
            let counter = 1;
            $('#selectedTests tbody tr').each(function() {
                $(this).find('td:first').text(counter++);
            });
        }

        function ensureNoTestsMessage() {
            if ($('#selectedTests tbody tr').length === 0) {
                $('#selectedTests tbody').html(
                    '<tr class="no-tests-row"><td colspan="4" class="text-center text-muted">No tests in bill yet</td></tr>'
                );
            }
        }

        function syncAddButtons() {
            $('.add-test-btn').each(function() {
                const id = $(this).data('id');
                const exists = $(`#selectedTests tbody tr[data-id="${id}"]`).length > 0;
                $(this).prop('disabled', exists).html(exists ? 'Added' : '<i class="fas fa-plus"></i> Add');
            });
        }

        function appendRegisteredTest(test, counter) {
            $('.no-tests-row').remove();
            $('#selectedTests tbody').append(`
                <tr data-id="${test.id}" class="registered-test">
                    <td>${counter}</td>
                    <td>
                        ${test.name} <span class="badge bg-success">Registered</span>
                        <input type="hidden" name="id[]" value="${test.id}">
                        <input type="hidden" name="cat_name[]" value="${test.name}">
                    </td>
                    <td class="text-end">
                        ${parseFloat(test.price).toFixed(2)}
                        <input type="hidden" name="price[]" value="${parseFloat(test.price).toFixed(2)}">
                    </td>
                    <td>
                        <button type="button" class="btn btn-danger btn-sm remove-test">
                            <i class="fas fa-trash"></i> Remove
                        </button>
                    </td>
                </tr>
            `);
        }

        $(document).ready(function() {
            let testCounter = $('#selectedTests tbody tr').length + 1;

            // Automatically add registered tests to the bill on page load
            @if (isset($registeredTests) && $registeredTests->count() > 0)
                $('#registeredTestsList tr').each(function() {
                    const id = $(this).data('test-id');
                    const name = $(this).data('test-name');
                    const price = parseFloat($(this).data('test-price')).toFixed(2);

                    if (id && name) {
                        appendRegisteredTest({
                            id,
                            name,
                            price
                        }, testCounter++);
                    }
                });

                renumberSelectedTests();
                syncAddButtons();
                updateTotals();
            @endif

            $('#refreshRegisteredTests').on('click', function() {
                const url = $(this).data('fetch-url');
                const button = $(this);

                button.prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm me-1"></span> Refreshing');

                $.get(url)
                    .done(function(response) {
                        $('#registeredTestsList').empty();
                        $('#selectedTests tbody tr.registered-test').remove();

                        if (response.tests && response.tests.length) {
                            response.tests.forEach((test, index) => {
                                $('#registeredTestsList').append(`
                                    <tr data-test-id="${test.id}"
                                        data-test-name="${test.name}"
                                        data-test-price="${test.price}"
                                        data-test-dept="${test.department}">
                                        <td>${index + 1}</td>
                                        <td>${test.name}</td>
                                        <td>${test.department}</td>
                                        <td>${parseFloat(test.price).toFixed(2)}</td>
                                        <td><span class="badge bg-success">Registered</span></td>
                                    </tr>
                                `);

                                appendRegisteredTest(test, $('#selectedTests tbody tr').length +
                                    1);
                            });
                        } else {
                            $('#registeredTestsList').html(
                                '<tr><td colspan="5" class="text-center text-muted">No registered tests.</td></tr>'
                            );
                        }

                        renumberSelectedTests();
                        syncAddButtons();
                        updateTotals();
                    })
                    .fail(function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Refresh failed',
                            text: 'Unable to refresh registered tests right now.'
                        });
                    })
                    .always(function() {
                        button.prop('disabled', false).html(
                            '<i class="fas fa-sync-alt me-1"></i> Refresh registered tests');
                        ensureNoTestsMessage();
                    });
            });

            // Add additional test
            $('.add-test-btn').on('click', function() {
                const id = $(this).data('id');
                const name = $(this).data('name');
                const price = parseFloat($(this).data('price')).toFixed(2);
                const exists = $(`#selectedTests tbody tr[data-id="${id}"]`).length > 0;

                if (exists) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Already Added',
                        text: 'This test is already in the bill',
                        timer: 2000
                    });
                    return;
                }

                // Remove "no tests" message if it exists
                $('.no-tests-row').remove();

                $('#selectedTests tbody').append(`
                    <tr data-id="${id}">
                        <td>${testCounter++}</td>
                        <td>
                            ${name}
                            <input type="hidden" name="id[]" value="${id}">
                            <input type="hidden" name="cat_name[]" value="${name}">
                        </td>
                        <td class="text-end">
                            ${price}
                            <input type="hidden" name="price[]" value="${price}">
                        </td>
                        <td>
                            <button type="button" class="btn btn-danger btn-sm remove-test">
                                <i class="fas fa-trash"></i> Remove
                            </button>
                        </td>
                    </tr>
                `);

                updateTotals();

                // Disable the add button for this test
                $(this).prop('disabled', true).text('Added');
            });

            // Remove test
            $(document).on('click', '.remove-test', function() {
                const testId = $(this).closest('tr').data('id');

                // Re-enable the add button
                $(`.add-test-btn[data-id="${testId}"]`).prop('disabled', false).html(
                    '<i class="fas fa-plus"></i> Add');

                $(this).closest('tr').remove();

                // Show "no tests" message if table is empty
                if ($('#selectedTests tbody tr[data-id]').length === 0) {
                    $('#selectedTests tbody').html(
                        '<tr class="no-tests-row"><td colspan="4" class="text-center text-muted">No tests in bill yet</td></tr>'
                    );
                }

                updateTotals();
                renumberSelectedTests();
                syncAddButtons();
            });

            // Live total updates
            $('#discount, #pay_').on('keyup', updateTotals);

            // AJAX submission
            $('#CustomerBillForm').on('submit', function(e) {
                e.preventDefault();
                const $btn = $(this).find('button[type="submit"]');
                $btn.prop('disabled', true);

                // Validate that at least one test is selected
                const testCount = $('#selectedTests tbody tr[data-id]').length;
                if (testCount === 0) {
                    Swal.fire('Error', 'Please select at least one test');
                    $btn.prop('disabled', false);
                    return;
                }

                const formData = new FormData(this);
                console.log('Form Data:', formData);
                console.log('Tests selected:', testCount);

                $.ajax({
                    url: "{{ route('billing.add') }}",
                    type: "POST",
                    data: formData,
                    cache: false,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: response.message || 'Bill created successfully!',
                            timer: 1500
                        }).then(() => {
                            window.location.href = "{{ route('patients.list') }}";
                        });
                    },
                    error: function(xhr) {
                        console.error('Error Response:', xhr);
                        const errorMsg = xhr.responseJSON?.message || xhr.responseText ||
                            'Something went wrong';
                        Swal.fire('Error', errorMsg);
                        $btn.prop('disabled', false);
                    }
                });
            });

        });
    </script>
@endsection
