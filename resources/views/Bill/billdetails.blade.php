@extends('Layout.master')
@section('title', 'Bill Details')
@section('content')

    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Billing System</a></li>
                            <li class="breadcrumb-item active">Billing Details</li>
                        </ol>
                    </div>
                    <h4 class="page-title">Patient Billing Details</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="card">
            <div class="card-body">
                <div id="printarea">
                    <div class="text-center mt-3">
                        @foreach (App\Models\MainCompanys::where('id', 1)->get() as $item)
                            <img src="{{ asset('/assets/HMS/lablogo/' . $item->lab_image) }}" alt="Lab Logo"
                                style="width: 120px; height: 120px" class="img-fluid"> <br />
                        @endforeach
                    </div>
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-sm-6">
                                <span class="h4">Invoice Number : {{ $bills->bill_no }}</span><br>
                                <span class="h6">Patient Id :
                                    {{ optional($bills->patient)->patient_id ?? 'N/A' }}</span><br>
                                <span class="h6">Patient Name :
                                    {{ optional($bills->patient)->name ?? 'N/A' }}</span><br>
                                <span class="h6">Mobile Number :
                                    {{ optional($bills->patient)->mobile_phone ?? 'N/A' }}</span><br>
                            </div>

                            <div class="col-sm-6">
                                <div class="text-right">
                                    @foreach (App\Models\MainCompanys::where('id', 1)->get() as $item)
                                        <span class="h4">{{ $item->lab_name }}</span><br>
                                        <span class="h6">{{ $item->lab_address }}</span><br>
                                        <span class="h6">{{ $item->lab_phone }}</span><br>
                                        <span class="h6">{{ $item->lab_email }}</span><br>
                                    @endforeach
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="container mt-5">
                        <table class="table table-sm">
                            <tbody>
                                <tr>
                                    <th>S/N</th>
                                    <th>Test Name</th>
                                    <th class="text-right">Price</th>
                                </tr>
                                @php $total = 0; @endphp
                                @if (isset($tests) && count($tests))
                                    @foreach ($tests as $test)
                                        <tr>
                                            <td style="width: 50px">{{ $loop->iteration }}</td>
                                            <td>{{ $test->cat_name }}</td>
                                            <td class="text-right">{{ number_format($test->price ?? 0, 2) }}</td>
                                        </tr>
                                        @php $total += $test->price ?? 0; @endphp
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="3" class="text-center">No test data available</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>

                        <div class="d-flex bd-highlight align-items-start">
                            <div class="p-2 flex-fill">
                                <h3>Total Amount :</h3>
                                <h4>In Words :</h4>
                                <h4>Discount :</h4>
                                <h3>Net Amount :</h3>
                                <h4>Payment Method :</h4>
                                <h4>Paid Amount :</h4>
                                <h4>Due/Return Amount :</h4>
                            </div>

                            <div class="p-2 text-right">
                                <h3 id="display_total">{{ number_format($total, 2) }}</h3>
                                <h4 id="display_in_words">{{ ucwords(\App\Helpers\NumberToWords::convert($total)) }} Rupees
                                    Only</h4>
                                <h4 id="display_discount">{{ number_format($bills->discount, 2) }}</h4>
                                <h3 id="display_net_amount">{{ number_format($bills->total_price, 2) }}</h3>
                                <h4 id="display_payment_type">{{ $bills->payment_type }}</h4>
                                <h4 id="display_paid_amount">{{ number_format($bills->paid_amount, 2) }}</h4>
                                <h4 id="display_due_amount">{{ number_format($bills->due_amount, 2) }}</h4>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-5">
                    <div class="col-md-12">
                        <button onclick="window.history.back()" class="btn btn-primary">Back</button>
                        <button type="button" class="btn btn-warning" data-toggle="modal"
                            data-target="#editBillModal">Payment</button>
                        @if (strtolower($bills->status ?? '') !== 'paid')
                            <!-- <button id="markPaidBtn" type="button" class="btn btn-success">Mark as Paid</button> -->
                            <span id="display_bill_status" class="badge badge-warning">Not Paid Yet</span>
                        @else
                            <span id="display_bill_status" class="badge badge-success">Paid</span>
                        @endif
                        <button onclick="myFunction('printarea')" class="btn btn-success float-right">Print</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Bill Modal -->
    <div class="modal fade" id="editBillModal" tabindex="-1" role="dialog" aria-labelledby="editBillModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title" id="editBillModalLabel">Edit Bill Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="editBillForm">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group row">
                            <label class="col-md-4 col-form-label"><strong>Total Amount:</strong></label>
                            <div class="col-md-8">
                                <input type="text" class="form-control" id="edit_total_amount" readonly>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-4 col-form-label"><strong>Discount (PKR):</strong></label>
                            <div class="col-md-8">
                                <input type="number" class="form-control" id="edit_discount" name="discount" step="0.01"
                                    min="0" required>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-4 col-form-label"><strong>Net Amount:</strong></label>
                            <div class="col-md-8">
                                <input type="text" class="form-control" id="edit_net_amount" readonly>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-4 col-form-label"><strong>Payment Method:</strong></label>
                            <div class="col-md-8">
                                <select class="form-control" id="edit_payment_type" name="payment_type" required>
                                    <option value="">-- Select Payment Method --</option>
                                    <option value="Cash">Cash</option>
                                    <option value="Card">Card</option>
                                    <option value="Bank Transfer">Bank Transfer</option>
                                    <option value="Check">Check</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-4 col-form-label"><strong>Paid Amount (PKR):</strong></label>
                            <div class="col-md-8">
                                <input type="number" class="form-control" id="edit_paid_amount" name="paid_amount"
                                    step="0.01" min="0" required>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-4 col-form-label"><strong>Due/Return Amount:</strong></label>
                            <div class="col-md-8">
                                <input type="text" class="form-control" id="edit_due_amount" readonly>
                            </div>
                        </div>

                        <div class="alert alert-info">
                            <strong>Note:</strong> Due Amount = Net Amount - Paid Amount. If positive, patient owes money.
                            If negative, return money to patient.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function myFunction(el) {
            var getFullContent = document.body.innerHTML;
            var printsection = document.getElementById(el).innerHTML;
            document.body.innerHTML = printsection;
            window.print();
            document.body.innerHTML = getFullContent;
        }

        $(document).ready(function() {
                    // Bill data
                    const billData = {
                        id: {{ $bills->id }},
                        total_amount: {{ $total }},
                        discount: {{ $bills->discount ?? 0 }},
                        total_price: {{ $bills->total_price ?? 0 }},
                        payment_type: '{{ $bills->payment_type ?? '' }}',
                        paid_amount: {{ $bills->paid_amount ?? 0 }},
                        due_amount: {{ $bills->due_amount ?? 0 }}
                    };

                    // Populate modal when opening
                    $('#editBillModal').on('show.bs.modal', function() {
                        $('#edit_total_amount').val(billData.total_amount.toFixed(2));
                        $('#edit_discount').val(billData.discount.toFixed(2));

                        // Compute net amount: prefer stored total_price if set, otherwise derive from total_amount - discount
                        let netAmount = 0;
                        if (billData.total_price && parseFloat(billData.total_price) > 0) {
                            netAmount = parseFloat(billData.total_price);
                        } else {
                            netAmount = parseFloat(billData.total_amount) - parseFloat(billData.discount || 0);
                        }
                        $('#edit_net_amount').val(netAmount.toFixed(2));

                        $('#edit_payment_type').val(billData.payment_type);
                        $('#edit_paid_amount').val(billData.paid_amount.toFixed(2));

                        // Update due amount on open
                        updateDueAmount();
                    });

                    // Calculate net amount when discount changes (use input event to catch paste/change)
                    $('#edit_discount').on('input', function() {
                        const discount = parseFloat($(this).val()) || 0;
                        const netAmount = parseFloat(billData.total_amount) - discount;
                        $('#edit_net_amount').val(netAmount.toFixed(2));
                        updateDueAmount();
                    });

                    // Calculate due amount when paid amount changes
                    $('#edit_paid_amount').on('input', function() {
                        updateDueAmount();
                    });

                    function updateDueAmount() {
                        const netAmount = parseFloat($('#edit_net_amount').val()) || 0;
                        const paidAmount = parseFloat($('#edit_paid_amount').val()) || 0;
                        const dueAmount = netAmount - paidAmount;
                        $('#edit_due_amount').val(dueAmount.toFixed(2));
                    }

                    // Handle form submission
                    $('#editBillForm').on('submit', function(e) {
                        e.preventDefault();

                        const formData = {
                            discount: parseFloat($('#edit_discount').val()),
                            total_price: parseFloat($('#edit_net_amount').val()),
                            payment_type: $('#edit_payment_type').val(),
                            paid_amount: parseFloat($('#edit_paid_amount').val()),
                            due_amount: parseFloat($('#edit_due_amount').val()),
                            _token: '{{ csrf_token() }}'
                        };

                        console.log('Submitting bill update:', formData);

                        $.ajax({
                            url: '{{ route('bills.update', $bills->id) }}',
                            method: 'PUT',
                            data: formData,
                            dataType: 'json',
                            success: function(response) {
                                console.log('Update success response:', response);

                                // Update the displayed values (values only, labels are static on the left)
                                $('#display_discount').text(formData.discount.toFixed(2));
                                $('#display_net_amount').text(formData.total_price.toFixed(2));
                                $('#display_payment_type').text(formData.payment_type);
                                $('#display_paid_amount').text(formData.paid_amount.toFixed(2));
                                $('#display_due_amount').text(formData.due_amount.toFixed(2));

                                // Also update in-words display if amount changed
                                try {
                                    $('#display_in_words').text((response.in_words) ? response
                                        .in_words :
                                        '{{ ucwords(\App\Helpers\NumberToWords::convert($total)) }} Rupees Only'
                                    );
                                } catch (e) {
                                    // ignore
                                }

                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success!',
                                    text: 'Bill details updated successfully!',
                                    confirmButtonText: 'OK'
                                }).then(function() {
                                    $('#editBillModal').modal('hide');
                                });
                            },
                            error: function(xhr) {
                                console.error('Update error response:', xhr);
                                console.error('Error status:', xhr.status);
                                console.error('Error text:', xhr.statusText);
                                console.error('Response text:', xhr.responseText);
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error!',
                                    text: xhr.responseJSON?.message ||
                                        'Failed to update bill details: ' + xhr.statusText,
                                    confirmButtonText: 'OK'
                                });
                            }
                        });
                    });

                    // Mark as Paid button handler
                    //     $('#markPaidBtn').on('click', function() {
                    //         Swal.fire({
                    //             title: 'Mark bill as paid?',
                    //             text: 'This will record the remaining payment and set the bill status to Paid.',
                    //             icon: 'warning',
                    //             showCancelButton: true,
                    //             confirmButtonText: 'Yes, mark as paid',
                    //             cancelButtonText: 'Cancel'
                    //         }).then((result) => {
                    //             if (!result.isConfirmed) return;

                    //             const url = '{{ route('bills.markPaid', $bills->id) }}';
                    //             $.ajax({
                    //                 url: url,
                    //                 method: 'POST',
                    //                 data: { _token: '{{ csrf_token() }}' },
                    //                 dataType: 'json',
                    //                 headers: {
                    //                     'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') || '{{ csrf_token() }}'
                    //                 },
                    //                 success: function(resp) {
                    //                     if (resp.success) {
                    //                         const updated = resp.bill;
                    //                         // Update visible values (no label prefixes)
                    //                         $('#display_paid_amount').text((parseFloat(updated.paid_amount || 0)).toFixed(2));
                    //                         $('#display_due_amount').text((parseFloat(updated.due_amount || 0)).toFixed(2));
                    //                         $('#display_net_amount').text((parseFloat(updated.total_price || 0)).toFixed(2));

                    //                         // Replace button with Paid badge
                    //                         $('#markPaidBtn').replaceWith('<span id="display_bill_status" class="badge badge-success">Paid</span>');

                    //                         Swal.fire('Updated', resp.message || 'Bill marked as paid.', 'success');
                    //                     } else {
                    //                         Swal.fire('Error', resp.message || 'Failed to mark paid', 'error');
                    //                     }
                    //                 },
                    //                 error: function(xhr) {
                    //                     console.error('Mark paid error:', xhr);
                    //                     console.error('Status:', xhr.status);
                    //                     console.error('Response text:', xhr.responseText);
                    //                     let serverMsg = 'Failed to mark as paid. ';
                    //                     try {
                    //                         const parsed = JSON.parse(xhr.responseText || '{}');
                    //                         serverMsg += parsed.message || parsed.error || JSON.stringify(parsed);
                    //                     } catch (e) {
                    //                         serverMsg += xhr.statusText || 'Unknown error';
                    //                     }

                    //                     Swal.fire('Error', serverMsg, 'error');
                    //                 }
                    //             });
                    //         });
                    //     });
                    // });
    </script>
@endsection
