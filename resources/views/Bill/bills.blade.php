@extends('Layout.master')
@section('title', 'Patient Billing')
@section('content')

    <div class="container-fluid">
        <!-- PAGE HEADER -->
        <div class="row mb-3">
            <div class="col">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">🧾 Patient Billing System</h4>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('patients.list') }}">Patients</a></li>
                            <li class="breadcrumb-item active">Billing</li>
                        </ol>
                    </nav>
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
                    <div class="col-12">
                        <div class="alert alert-info">
                            <div class="row">
                                <div class="col-md-3">
                                    <strong>Patient ID:</strong> {{ $patient->patient_id }}
                                </div>
                                <div class="col-md-3">
                                    <strong>Name:</strong> {{ $patient->name }}
                                </div>
                                <div class="col-md-3">
                                    <strong>Age/Gender:</strong> {{ $patient->age }} / {{ $patient->gender }}
                                </div>
                                <div class="col-md-3">
                                    <strong>Phone:</strong> {{ $patient->mobile_phone }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Invoice Number -->
                    <div class="col-md-6">
                        <label for="bill_display" class="form-label fw-semibold">Invoice Number</label>
                        <input type="text" class="form-control" id="bill_display" value="{{ $nextInvoiceNumber }}" readonly>
                    </div>

                    <!-- Payment Type -->
                    <div class="col-md-6">
                        <label for="paidby" class="form-label fw-semibold">Payment Type</label>
                        <select class="form-select" id="paidby" name="paidby" required>
                            <option value="Cash">Cash</option>
                            <option value="Card">Card</option>
                            <option value="Mobile Banking">Mobile Banking</option>
                        </select>
                    </div>

                    <!-- Registered Tests Section -->
                    @if(isset($registeredTests) && $registeredTests->count() > 0)
                    <div class="col-12 mt-4">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <div>
                                <h5 class="text-success fw-semibold mb-2">✅ Registered Tests for This Patient</h5>
                                <p class="mb-0">The following tests have been registered for this patient and will be automatically added to the bill.</p>
                            </div>
                            <button type="button"
                                id="refreshRegisteredTests"
                                class="btn btn-outline-success btn-sm"
                                data-fetch-url="{{ route('billing.registeredTests', $patient->id) }}">
                                <i class="fas fa-sync-alt me-1"></i> Refresh registered tests
                            </button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover align-middle">
                                <thead class="table-success">
                                    <tr>
                                        <th>#</th>
                                        <th>Test Name</th>
                                        <th>Department</th>
                                        <th>Price (PKR)</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody id="registeredTestsList">
                                    @foreach ($registeredTests as $test)
                                        <tr data-test-id="{{ $test->id }}" 
                                            data-test-name="{{ $test->cat_name }}"
                                            data-test-price="{{ $test->price }}"
                                            data-test-dept="{{ $test->department ?? 'N/A' }}">
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $test->cat_name }}</td>
                                            <td>{{ $test->department ?? 'N/A' }}</td>
                                            <td>{{ number_format($test->price, 2) }}</td>
                                            <td><span class="badge bg-success">Registered</span></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif

                    <!-- Additional Tests Section -->
                    <div class="col-12 mt-4">
                        <h5 class="text-primary fw-semibold mb-2">🧪 Add Additional Tests (Optional)</h5>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover align-middle">
                                <thead class="table-primary">
                                    <tr>
                                        <th>#</th>
                                        <th>Test Name</th>
                                        <th>Department</th>
                                        <th>Price (PKR)</th>
                                        <th>Add</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($tests as $test)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $test->cat_name }}</td>
                                            <td>{{ $test->department }}</td>
                                            <td>{{ number_format($test->price, 2) }}</td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-outline-primary add-test-btn"
                                                    data-id="{{ $test->id }}" 
                                                    data-name="{{ $test->cat_name }}"
                                                    data-price="{{ $test->price }}" 
                                                    data-dept="{{ $test->department }}">
                                                    <i class="fas fa-plus"></i> Add
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Selected Tests for Bill -->
                    <div class="col-12 mt-3">
                        <h5 class="text-success fw-semibold">🧾 Tests in Current Bill</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered" id="selectedTests">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Test Name</th>
                                        <th>Price (PKR)</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(!isset($registeredTests) || $registeredTests->count() === 0)
                                        <tr class="no-tests-row">
                                            <td colspan="4" class="text-center text-muted">No tests in bill yet</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Summary Section -->
                    <div class="col-md-6 offset-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Subtotal:</strong></td>
                                <td><input type="text" class="form-control text-end" id="grandtotal" name="gtotal" value="0.00" readonly></td>
                            </tr>
                            <tr>
                                <td><strong>Discount:</strong></td>
                                <td><input type="number" step="0.01" class="form-control text-end" id="discount" name="discount" value="0"></td>
                            </tr>
                            <tr>
                                <td><strong>Total:</strong></td>
                                <td><input type="text" class="form-control text-end" id="total_" name="total_" value="0.00" readonly></td>
                            </tr>
                            <tr>
                                <td><strong>Amount Paid:</strong></td>
                                <td><input type="number" step="0.01" class="form-control text-end" id="pay_" name="pay" value="0"></td>
                            </tr>
                            <tr>
                                <td><strong>Due / Return:</strong></td>
                                <td><input type="text" class="form-control text-end" id="return" name="return" value="0.00" readonly></td>
                            </tr>
                            <tr>
                                <td><strong>Approval Code:</strong></td>
                                <td><input type="text" class="form-control" id="abbroval_code" name="abbroval_code" placeholder="Optional"></td>
                            </tr>
                        </table>
                    </div>

                    <!-- Submit Button -->
                    <div class="col-12 text-end mt-3">
                        <a href="{{ route('patients.list') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Patients
                        </a>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> Save Bill
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

            $('#grandtotal').val(subtotal.toFixed(2));
            $('#total_').val(total.toFixed(2));
            $('#return').val(due.toFixed(2));
        }

        function renumberSelectedTests() {
            let counter = 1;
            $('#selectedTests tbody tr').each(function () {
                $(this).find('td:first').text(counter++);
            });
        }

        function ensureNoTestsMessage() {
            if ($('#selectedTests tbody tr').length === 0) {
                $('#selectedTests tbody').html('<tr class="no-tests-row"><td colspan="4" class="text-center text-muted">No tests in bill yet</td></tr>');
            }
        }

        function syncAddButtons() {
            $('.add-test-btn').each(function () {
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
            @if(isset($registeredTests) && $registeredTests->count() > 0)
                $('#registeredTestsList tr').each(function() {
                    const id = $(this).data('test-id');
                    const name = $(this).data('test-name');
                    const price = parseFloat($(this).data('test-price')).toFixed(2);
                    
                    if (id && name) {
                        appendRegisteredTest({ id, name, price }, testCounter++);
                    }
                });

                renumberSelectedTests();
                syncAddButtons();
                updateTotals();
            @endif

            $('#refreshRegisteredTests').on('click', function () {
                const url = $(this).data('fetch-url');
                const button = $(this);

                button.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Refreshing');

                $.get(url)
                    .done(function (response) {
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

                                appendRegisteredTest(test, $('#selectedTests tbody tr').length + 1);
                            });
                        } else {
                            $('#registeredTestsList').html('<tr><td colspan="5" class="text-center text-muted">No registered tests.</td></tr>');
                        }

                        renumberSelectedTests();
                        syncAddButtons();
                        updateTotals();
                    })
                    .fail(function () {
                        Swal.fire({
                            icon: 'error',
                            title: 'Refresh failed',
                            text: 'Unable to refresh registered tests right now.'
                        });
                    })
                    .always(function () {
                        button.prop('disabled', false).html('<i class="fas fa-sync-alt me-1"></i> Refresh registered tests');
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
                $(`.add-test-btn[data-id="${testId}"]`).prop('disabled', false).html('<i class="fas fa-plus"></i> Add');
                
                $(this).closest('tr').remove();
                
                // Show "no tests" message if table is empty
                if ($('#selectedTests tbody tr[data-id]').length === 0) {
                    $('#selectedTests tbody').html('<tr class="no-tests-row"><td colspan="4" class="text-center text-muted">No tests in bill yet</td></tr>');
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
                
                // Validate that at least one test is selected
                if ($('#selectedTests tbody tr[data-id]').length === 0) {
                    Swal.fire({
                        icon: 'error',
                        title: 'No Tests Selected',
                        text: 'Please select at least one test before saving the bill'
                    });
                    return;
                }
                
                const formData = new FormData(this);
                
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
                            text: 'Bill created successfully!',
                            timer: 2000
                        }).then(() => {
                            window.location.href = "{{ route('patients.list') }}";
                        });
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: xhr.responseJSON?.message || 'Something went wrong. Please try again.'
                        });
                    }
                });
            });
        });
    </script>
@endsection
