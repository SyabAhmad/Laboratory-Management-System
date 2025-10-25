@extends('Layout.master')
@section('title', 'Patient Details')
@section('content')

    <div class="container-fluid">
        <!-- start page title -->
        <div class="row no-print">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('patients.list') }}">Patients</a></li>
                            <li class="breadcrumb-item active">Details</li>
                        </ol>
                    </div>
                    <h4 class="page-title">Patient Details</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="card print-card">
            <div class="card-body p-4">
                <!-- Header Section with Logo and Lab Name -->
                <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom: 15px;">
                    <tr>
                        <!-- Left: Logo -->
                        <td width="15%" valign="top" align="center" style="padding-right: 15px;">
                            <img src="{{ asset('assets/images/logo.png') }}" alt="Logo"
                                style="width: 75px; height: 75px; border-radius: 50%; border: 2px solid var(--primary); display: block;">
                        </td>
                        <!-- Center: Lab Name & Info -->
                        <td>
                            <div style="font-weight: bold; font-size: 18px; margin: 0; line-height: 1.1;"
                                class="text-primary-custom">NEW MODERN CLINICAL<br>LABORATORY</div>
                            <div style="font-size: 10px; margin: 4px 0 0 0; font-weight: 600;" class="text-primary-custom">
                                (KP HCC) REG: 03663 SWAT</div>
                            <div style="font-size: 9px; color: #333; margin: 3px 0 0 0; line-height: 1.3;">
                                Bacha Khan <br>
                                BS Pathology (KMU)<br>
                                DMLT KPK Peshawar<br>

                            </div>
                        </td>
                        <!-- Right: Contact Info -->
                        <td width="30%" valign="top" align="right"
                            style="font-size: 9px; color: #333; line-height: 1.4; padding-left: 10px; border-left: 2px solid var(--primary);">
                            <strong class="text-primary-custom">Tel:</strong><br>
                            0302-8080191<br>
                            0313-9797790<br><br>
                            <strong class="text-primary-custom">Address:</strong><br>
                            Kabal Road, Near Township Chowk Kanju Swat
                        </td>
                    </tr>
                </table>

                <div style="border-top: 2px solid var(--primary); margin-bottom: 12px;"></div>

                <!-- Patient Information Section -->
                <table width="100%" cellpadding="4" cellspacing="0"
                    style="border: 1px solid #ccc; margin-bottom: 15px; font-size: 10px;">
                    <tr>
                        <td width="20%"><strong>Name:</strong></td>
                        <td width="30%">{{ $patient->name }}</td>
                        <td width="20%"><strong>Age / Gender:</strong></td>
                        <td width="30%">{{ $patient->age }} yr(s) / {{ $patient->gender }}</td>
                    </tr>
                    <tr>
                        <td><strong>Patient ID:</strong></td>
                        <td>{{ $patient->patient_id }}</td>
                        <td><strong>Blood Group:</strong></td>
                        <td>{{ $patient->blood_group ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Referred By:</strong></td>
                        <td>
                            @php $refName = optional($patient->referral)->name; @endphp
                            {{ empty($patient->referred_by) || $patient->referred_by === 'none' || $refName === null ? 'Self' : $refName }}
                        </td>
                        <td><strong>Mobile:</strong></td>
                        <td>{{ $patient->mobile_phone }}</td>
                    </tr>
                </table>

                <!-- Test Results Section -->
                @if (!empty($testsWithData))
                    @forelse($testsWithData as $test)
                        @php
                            $testName = $test['name'];
                            $template = $test['template'];
                            $savedData = $test['saved_data'];
                            $hasData = $test['has_data'];
                            $isMllpData = $test['is_mllp_data'] ?? false;
                            $analytes = $savedData['analytes'] ?? [];
                            $hasHL7Data = !empty($analytes) && is_array($analytes);
                        @endphp

                        <!-- Test Title -->
                        <div
                            style="margin: 20px 0 10px 0; padding: 8px 0; border-bottom: 2px solid var(--primary); font-weight: bold; color: var(--primary); font-size: 14px;">
                            {{ $testName }}
                        </div>

                        <!-- Test Results Table -->
                        <table width="100%" cellpadding="5" cellspacing="0"
                            style="border-collapse: collapse; font-size: 11px; margin-bottom: 20px;">
                            <thead>
                                <tr style="background: var(--surface); border-bottom: 2px solid var(--primary);">
                                    <th
                                        style="text-align: left; padding: 8px; font-weight: bold; width: 40%; color: var(--primary);">
                                        Test Name</th>
                                    <th
                                        style="text-align: left; padding: 8px; font-weight: bold; width: 20%; color: var(--primary);">
                                        Results</th>
                                    <th
                                        style="text-align: left; padding: 8px; font-weight: bold; width: 15%; color: var(--primary);">
                                        Unit</th>
                                    <th
                                        style="text-align: left; padding: 8px; font-weight: bold; width: 25%; color: var(--primary);">
                                        Reference Ranges</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if ($hasHL7Data)
                                    @foreach ($analytes as $analyte)
                                        <tr>
                                            <td style="padding: 8px; border: 1px solid #ddd;">
                                                {{ $analyte['name'] ?? ($analyte['code'] ?? 'Unknown') }}</td>
                                            <td style="padding: 8px; border: 1px solid #ddd; font-weight: 600;">
                                                {{ $analyte['value'] ?? '' }}</td>
                                            <td style="padding: 8px; border: 1px solid #ddd;">{{ $analyte['units'] ?? '' }}
                                            </td>
                                            <td style="padding: 8px; border: 1px solid #ddd;">
                                                {{ $analyte['ref_range'] ?? '' }}</td>
                                        </tr>
                                    @endforeach
                                @elseif($hasData && !empty($template['fields']))
                                    @foreach ($template['fields'] as $field)
                                        @php
                                            $value = $savedData[$field['name']] ?? '';
                                            $label = $field['label'] ?? 'Unknown';
                                            $unit = '';
                                            $ref = '';

                                            if (preg_match('/\(([^)]+)\)/', $label, $matches)) {
                                                $unit = trim($matches[1]);
                                                $label = preg_replace('/\s*\([^)]+\)\s*/', '', $label);
                                            }

                                            if (preg_match('/Ref:\s*([^\n]+)$/i', $label, $matches)) {
                                                $ref = trim($matches[1]);
                                                $label = preg_replace('/\s*[-–—]\s*Ref:\s*[^\n]+$/i', '', $label);
                                            }

                                            $label = trim($label);
                                        @endphp
                                        @if (!empty($value))
                                            <tr>
                                                <td style="padding: 8px; border: 1px solid #ddd;">{{ $label }}</td>
                                                <td style="padding: 8px; border: 1px solid #ddd; font-weight: 600;">
                                                    {{ $value }}</td>
                                                <td style="padding: 8px; border: 1px solid #ddd;">{{ $unit }}</td>
                                                <td style="padding: 8px; border: 1px solid #ddd;">{{ $ref }}</td>
                                            </tr>
                                        @endif
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="4"
                                            style="padding: 12px; text-align: center; color: var(--text-body);">No test data
                                            recorded</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>

                        <!-- Individual Test PDF Button -->
                        <div class="no-print" style="margin-bottom: 15px; text-align: right;">
                            <a href="{{ route('patients.printTest', ['patient' => $patient->id, 'testName' => $testName]) }}"
                                target="_blank" class="btn btn-sm btn-outline-secondary">
                                <i class="mdi mdi-file-pdf"></i> Download Test PDF
                            </a>
                        </div>
                    @empty
                    @endforelse
                @endif
            </div>

            <!-- Doctor Signature Section -->
            <div
                style="margin-top: 25px; padding-top: 10px; border-top: 1px solid #aaa; display: flex; justify-content: space-between; align-items: center; font-size: 9px; color: #333;">
                <div style="flex: 1; text-align: left;">
                    <strong>Please Note:</strong><br>
                    Test(s) are performed on the state-of-the-art ARCHITECT MODULAR Ci4100 from Abbott Diagnostics,
                    U.S.A.<br>
                    This is a digitally signed report and does not require manual signature.
                </div>
                <div style="text-align: right; white-space: nowrap;">
                    <div style="font-weight: bold; color: var(--primary); margin-bottom: 5px;">
                        This is a digitally signed report by<br>
                        <strong>Bacha Khan</strong>
                    </div>
                </div>
            </div>

            <!-- Footer Section -->
            <div
                style="background: var(--primary); color: var(--surface); padding: 10px 15px; margin: 15px -1.5rem -1.5rem -1.5rem; font-size: 10px; text-align: center;">
                <strong>�</strong> Asad Abad Road, Near Township Chowk Kamla Swat |
                <strong>📞</strong> 0302-9050191 - 03139796050 |
                <!-- <strong>🌐</strong> www.newmoderncliniclab.com -->
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="row mt-5 no-print">
        <div class="col-md-12">
            <a href="{{ route('patients.list') }}" class="btn btn-primary">
                <i class="mdi mdi-arrow-left"></i> Back to List
            </a>
            <button onclick="window.print()" class="btn btn-info" style="margin-left: 5px;">
                <i class="mdi mdi-printer"></i> Print
            </button>
            <button onclick="downloadPDF()" class="btn btn-warning" style="margin-left: 5px;">
                <i class="mdi mdi-file-pdf"></i> Download PDF
            </button>

            @php
                // Format phone number for WhatsApp
                $phoneNumber = preg_replace('/[^0-9]/', '', $patient->mobile_phone);
                if (!str_starts_with($phoneNumber, '92')) {
                    if (str_starts_with($phoneNumber, '0')) {
                        $phoneNumber = '92' . substr($phoneNumber, 1);
                    } else {
                        $phoneNumber = '92' . $phoneNumber;
                    }
                }

                $message = 'Hello ' . $patient->name . ",\n\n";
                $message .= "Your medical test report from NEW MODERN CLINICAL LABORATORY is ready.\n\n";
                $message .= 'Patient ID: ' . $patient->patient_id . "\n";
                $message .=
                    'Report Date: ' .
                    ($patient->reporting_date
                        ? \Carbon\Carbon::parse($patient->reporting_date)->format('d-m-Y')
                        : 'N/A') .
                    "\n\n";
                $message .= "Please visit our laboratory to collect your report.\n\n";
                $message .= "For any queries, contact us:\n";
                $message .= "📞 +92 302 8080191\n";
                $message .= 'Thank you for choosing New Modern Clinical Laboratory.';

                $whatsappUrl = 'https://wa.me/' . $phoneNumber . '?text=' . urlencode($message);
            @endphp

            <a href="{{ $whatsappUrl }}" target="_blank" class="btn btn-success"
                style="background-color: var(--success); border-color: var(--success); margin-left: 5px;">
                <i class="mdi mdi-whatsapp"></i> Send via WhatsApp
            </a>
        </div>
    </div>
    </div>

    <style>
        @media print {
            .no-print {
                display: none !important;
            }

            body {
                margin: 0;
                padding: 0;
            }

            .container-fluid {
                padding: 0;
            }

            .print-card {
                border: none !important;
                box-shadow: none !important;
                margin: 0 !important;
            }

            .card-body {
                padding: 20px !important;
            }

            .report-logo {
                width: 80px !important;
                height: auto !important;
            }

            .report-footer {
                margin: 0 !important;
            }

            table {
                page-break-inside: auto;
            }

            tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }
        }

        @media screen {
            .print-card {
                max-width: 210mm;
                margin: 0 auto;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            }
        }

        .table-borderless td {
            padding: 0.3rem 0.5rem;
        }

        .section-title {
            font-size: 16px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .btn.ml-2 {
            margin-left: 0.5rem;
        }

        .flex-grow-1 {
            flex-grow: 1;
        }
    </style>

    <script>
        function downloadPDF() {
            // Get patient name and date for filename
            const patientName = @json($patient->name ?? '');
            const reportDate = @json($patient->reporting_date ? \Carbon\Carbon::parse($patient->reporting_date)->format('d-m-Y') : '');
            const safeName = (patientName || 'patient').toString().replace(/\s+/g, '_').replace(/[^a-zA-Z0-9_\-]/g, '');
            const fileName = safeName + (reportDate ? '_' + reportDate : '') + '.pdf';
            // Use the browser print dialog to save as PDF; advanced server-side PDF generation can be added if needed
            window.print();
        }
    </script>
@endsection
