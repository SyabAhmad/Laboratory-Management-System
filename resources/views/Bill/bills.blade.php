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
                <strong>New Bill Entry</strong>
            </div>

            <div class="card-body">
                <form id="CustomerBillForm" method="POST" class="row g-3">
                    @csrf
                    <input type="hidden" name="bill_no" value="{{ $nextInvoiceNumber }}">

                    <!-- Patient Info -->
                    <div class="col-md-6">
                        <label for="patient_name" class="form-label fw-semibold">Patient Name</label>

                        @if (isset($patient))
                            <!-- When coming from "Bill" icon (patient already selected) -->
                            <input type="hidden" name="patient_id" value="{{ $patient->id }}">
                            <input type="text" class="form-control" id="patient_name"
                                value="{{ $patient->name }} ({{ $patient->patient_id }})" readonly>
                        @else
                            <!-- When no patient selected yet (manual search) -->
                            <input type="text" class="form-control" id="patient_search"
                                placeholder="Search Patient by Name or ID...">
                            <input type="hidden" name="patient_id" id="patient_id">
                            <div id="patientResults" class="list-group position-absolute w-100" style="z-index:1000;"></div>
                        @endif
                    </div>


                    <!-- Selected Tests Table -->
                    <div id="testList" class="mt-4" style="display:none;">
                        <h5>Selected Tests</h5>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Test Name</th>
                                    <th>Price (PKR)</th>
                                </tr>
                            </thead>
                            <tbody id="testRows"></tbody>
                            <tfoot>
                                <tr>
                                    <th class="text-end">Subtotal:</th>
                                    <th id="subtotal">0</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>



                    <!-- Payment Type -->
                    <div class="col-md-6">
                        <label for="paidby" class="form-label fw-semibold">Payment Type</label>
                        <select class="form-select" id="paidby" name="paidby">
                            <option value="Cash">Cash</option>
                            <option value="Card">Card</option>
                            <option value="Mobile Banking">Mobile Banking</option>
                        </select>
                    </div>

                    <!-- Test List -->
                    <div class="col-12 mt-4">
                        <h5 class="text-primary fw-semibold mb-2">🧪 Available Tests</h5>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover align-middle">
                                <thead class="table-primary">
                                    <tr>
                                        <th>#</th>
                                        <th>Test Name</th>
                                        <th>Department</th>
                                        <th>Price</th>
                                        <th>Add</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($tests as $test)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $test->cat_name }}</td>
                                            <td>{{ $test->department }}</td>
                                            <td>₦{{ number_format($test->price, 2) }}</td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-outline-primary add-test-btn"
                                                    data-id="{{ $test->id }}" data-name="{{ $test->cat_name }}"
                                                    data-price="{{ $test->price }}" data-dept="{{ $test->department }}">
                                                    Add
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Selected Tests -->
                    <div class="col-12 mt-3">
                        <h5 class="text-success fw-semibold">🧾 Selected Tests</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered" id="selectedTests">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Test Name</th>
                                        <th>Price</th>
                                        <th>Remove</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Summary Section -->
                    <div class="col-md-4 offset-md-8">
                        <table class="table table-borderless">
                            <tr>
                                <td>Subtotal:</td>
                                <td><input type="text" class="form-control" id="grandtotal" name="gtotal" readonly></td>
                            </tr>
                            <tr>
                                <td>Discount:</td>
                                <td><input type="text" class="form-control" id="discount" name="discount"
                                        value="0"></td>
                            </tr>
                            <tr>
                                <td>Total:</td>
                                <td><input type="text" class="form-control" id="total_" name="total_" readonly></td>
                            </tr>
                            <tr>
                                <td>Amount Paid:</td>
                                <td><input type="text" class="form-control" id="pay_" name="pay"></td>
                            </tr>
                            <tr>
                                <td>Due / Return:</td>
                                <td><input type="text" class="form-control" id="return" name="return" readonly></td>
                            </tr>
                            <tr>
                                <td>Approval Code:</td>
                                <td><input type="text" class="form-control" id="abbroval_code" name="abbroval_code"></td>
                            </tr>
                        </table>
                    </div>

                    <!-- Submit Button -->
                    <div class="col-12 text-end">
                        <button type="submit" class="btn btn-success">💾 Save Bill</button>
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
            $('#selectedTests tbody tr').each(function() {
                subtotal += parseFloat($(this).find('input[name="price[]"]').val());
            });
            const discount = parseFloat($('#discount').val()) || 0;
            const total = subtotal - discount;
            const paid = parseFloat($('#pay_').val()) || 0;
            const due = total - paid;

            $('#grandtotal').val(subtotal.toFixed(2));
            $('#total_').val(total.toFixed(2));
            $('#return').val(due.toFixed(2));
        }

        $(document).ready(function() {
            let testCounter = 1;

            // Add test
            $('.add-test-btn').on('click', function() {
                const id = $(this).data('id');
                const name = $(this).data('name');
                const price = parseFloat($(this).data('price')).toFixed(2);
                const exists = $(`#selectedTests tbody tr[data-id="${id}"]`).length > 0;

                if (exists) {
                    Swal.fire('Warning', 'This test is already added', 'warning');
                    return;
                }

                $('#selectedTests tbody').append(`
            <tr data-id="${id}">
                <td>${testCounter++}</td>
                <td>${name}<input type="hidden" name="id[]" value="${id}"><input type="hidden" name="cat_name[]" value="${name}"></td>
                <td>${price}<input type="hidden" name="price[]" value="${price}"></td>
                <td><button type="button" class="btn btn-danger btn-sm remove-test">Remove</button></td>
            </tr>
        `);

                updateTotals();
            });

            // Remove test
            $(document).on('click', '.remove-test', function() {
                $(this).closest('tr').remove();
                updateTotals();
            });

            // Live total updates
            $('#discount, #pay_').on('keyup', updateTotals);



            // AJAX submission
            $('#CustomerBillForm').on('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                $.ajax({
                    url: "{{ route('billing.add') }}",
                    type: "POST",
                    data: formData,
                    cache: false,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        Swal.fire('Success', 'Bill created successfully!', 'success');
                        $('#CustomerBillForm')[0].reset();
                        $('#selectedTests tbody').empty();
                    },
                    error: function(xhr) {
                        Swal.fire('Error', xhr.responseJSON.message || 'Something went wrong',
                            'error');
                    }
                });
            });
        });
    </script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#patient_search').on('keyup', function() {
                let query = $(this).val();

                if (query.length > 1) {
                    $.ajax({
                        url: "{{ route('patients.search') }}",
                        type: "GET",
                        data: {
                            query: query
                        },
                        success: function(data) {
                            let results = '';
                            data.forEach(function(patient) {
                                results += `
                            <a href="#" class="list-group-item list-group-item-action patient-item" data-id="${patient.id}" data-name="${patient.name}" data-patientid="${patient.patient_id}">
                                ${patient.name} (${patient.patient_id})
                            </a>
                        `;
                            });
                            $('#patientResults').html(results).show();
                        }
                    });
                } else {
                    $('#patientResults').hide();
                }
            });
            $(document).on('click', '.patient-item', function(e) {
                e.preventDefault();

                const id = $(this).data('id');
                const name = $(this).data('name');
                const patientId = $(this).data('patientid');

                // set hidden patient_id and display
                $('#patient_id').val(id);
                $('#patient_search').val(`${name} (${patientId})`);
                $('#patientResults').hide();

                // clear existing selected tests
                $('#selectedTests tbody').empty();

                // Build route URL (Blade will render base with :id placeholder)
                var routeUrl = "{{ route('patients.registered_tests', ':id') }}";
                routeUrl = routeUrl.replace(':id', id);

                $.get(routeUrl, function(res) {
                    if (!res || !res.tests || res.tests.length === 0) {
                        // no registered tests: keep form empty and update totals
                        updateTotals();
                        Swal.fire({
                            position: 'top-end',
                            icon: 'info',
                            title: 'No registered tests found for this patient',
                            showConfirmButton: false,
                            timer: 1500
                        });
                        return;
                    }

                    // Populate selectedTests table with returned tests
                    let counter = 1;
                    res.tests.forEach(function(test) {
                        // avoid duplicates
                        if ($('#selectedTests tbody tr[data-id="' + test.id + '"]').length)
                            return;

                        const price = Number(test.price || 0).toFixed(2);
                        $('#selectedTests tbody').append(`
                <tr data-id="${test.id}">
                    <td>${counter}</td>
                    <td>${test.cat_name}
                        <input type="hidden" name="id[]" value="${test.id}">
                        <input type="hidden" name="cat_name[]" value="${test.cat_name}">
                    </td>
                    <td>${price}
                        <input type="hidden" name="price[]" value="${price}">
                    </td>
                    <td><button type="button" class="btn btn-danger btn-sm remove-test">Remove</button></td>
                </tr>
            `);
                        counter++;
                    });

                    // recalc totals using your existing function
                    updateTotals();

                }).fail(function() {
                    Swal.fire({
                        title: 'Error',
                        text: 'Failed to load registered tests for selected patient.',
                        icon: 'error'
                    });
                });
            });


        });
    </script>



@endsection
