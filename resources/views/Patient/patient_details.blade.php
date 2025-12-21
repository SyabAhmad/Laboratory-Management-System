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

        <!-- Print Header (visible only in print view) -->
        @include('Patient.partials.print_header_footer')

        <div class="card print-card print-body" style="border-radius: 8px; overflow: hidden;">
            <div class="card-body p-4">
                <!-- Screen Header (hidden in print) -->
                <div class="screen-header no-print">
                    <!-- Header Section with Logo and Lab Name -->
                    <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom: 20px; border-bottom: 2px solid #8d2d36; padding-bottom: 15px;">
                        <tr>
                            <!-- Left: Logo -->
                            <td width="15%" valign="top" align="center" style="padding-right: 6px;"> 
                                <img src="{{ asset('assets/images/logo.png') }}" alt="Logo"
                                    style="width: 80px; height: 80px; border-radius: 50%; border: 3px solid #8d2d36; display: block; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                            </td>
                            <!-- Center: Lab Name & Info -->
                            <td style="padding: 0 6px;">
                                <div style="font-weight: bold; font-size: 20px; margin: 0; line-height: 1.1; color: #8d2d36; text-align: center;">
                                    NEW MODERN CLINICAL LABORATORY
                                </div>
                                <div style="font-size: 11px; margin: 5px 0 0 0; font-weight: 600; color: #8d2d36; text-align: center;">
                                    (KP HCC) REG: 03663 SWAT
                                </div>
                                <div style="font-size: 10px; color: #8d2d36; margin: 5px 0 0 0; line-height: 1.3; text-align: center;">
                                    Bacha Khan, BS Pathology (KMU)<br>
                                    DMLT KPK Peshawar
                                </div>
                            </td>
                            <!-- Right: Contact Info -->
                            <td width="30%" valign="top" align="right"
                                style="font-size: 10px; color: #333; line-height: 1.4; padding-left: 6px; border-left: 2px solid #8d2d36;"> 
                                <div style="font-weight: bold; color: #8d2d36; margin-bottom: 5px;">Contact Information</div>
                                <strong>Tel:</strong><br>
                                0302-8080191<br>
                                0313-9797790<br><br>
                                <strong>Address:</strong><br>
                                Kabal Road, Near Township Chowk<br>
                                Kanju Swat
                            </td>
                        </tr>
                    </table>
                </div>

                <!-- Patient Information Section -->
                <div style="background: linear-gradient(135deg, rgba(248, 250, 252, 0.8), rgba(255, 255, 255, 0.8)); border: 2px solid #8d2d36; border-radius: 8px; padding: 20px; margin-bottom: 25px;" class="patient-info-section">
                            <table width="100%" cellpadding="8" cellspacing="0" style="font-size: 14px; border-collapse: collapse;">
                        <tr>
                            <td width="25%" style="font-weight: bold; color: black; padding-bottom: 10px; font-size: 14px;">
                                <i class="fas fa-user" style="margin-right: 8px;"></i>Patient Name:
                            </td>
                            <td width="25%" style="padding-bottom: 10px; font-weight: 600; font-size: 14px;">{{ $patient->name }}</td>
                            <td width="25%" style="font-weight: bold; color: black; padding-bottom: 10px; font-size: 14px;">
                                <i class="fas fa-birthday-cake" style="margin-right: 8px;"></i>Age / Gender:
                            </td>
                            <td width="25%" style="padding-bottom: 10px; font-weight: 600; font-size: 14px;">
                                @php
                                    // Use individual age parts if available, otherwise use the combined string
                                    if (!empty($patient->age_years) || !empty($patient->age_months) || !empty($patient->age_days)) {
                                        $parts = [];
                                        if (!empty($patient->age_years)) $parts[] = $patient->age_years . 'Y';
                                        if (!empty($patient->age_months)) $parts[] = $patient->age_months . 'M';
                                        if (!empty($patient->age_days)) $parts[] = $patient->age_days . 'D';
                                        $ageDisplay = !empty($parts) ? implode(' ', $parts) : '0Y';
                                    } else {
                                        $ageDisplay = $patient->age ?: 'N/A';
                                    }
                                @endphp
                                {{ $ageDisplay }} / {{ $patient->gender }}
                            </td>
                        </tr>
                        <tr>
                            <td style="font-weight: bold; color: black; padding-bottom: 10px; font-size: 14px;">
                                <i class="fas fa-id-card" style="margin-right: 8px;"></i>Patient ID:
                            </td>
                            <td style="padding-bottom: 10px; font-weight: 600; font-size: 14px;">{{ $patient->patient_id }}</td>
                            <td style="font-weight: bold; color: black; padding-bottom: 10px; font-size: 14px;">
                                <i class="fas fa-tint" style="margin-right: 8px;"></i>Blood Group:
                            </td>
                            <td style="padding-bottom: 10px; font-weight: 600; font-size: 14px;">{{ $patient->blood_group ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td style="font-weight: bold; color: black; font-size: 14px;">
                                <i class="fas fa-user-md" style="margin-right: 8px;"></i>Referred By:
                            </td>
                            <td style="font-weight: 600; font-size: 14px;">
                                @php $refName = optional($patient->referral)->name; @endphp
                                {{ empty($patient->referred_by) || $patient->referred_by === 'none' || $refName === null ? 'Self' : $refName }}
                            </td>
                            <td style="font-weight: bold; color: black; font-size: 14px;">
                                <i class="fas fa-phone" style="margin-right: 8px;"></i>Mobile:
                            </td>
                            <td style="font-weight: 600; font-size: 14px;">{{ $patient->mobile_phone }}</td>
                        </tr>
                        <tr>
                            <td style="font-weight: bold; color: black; padding-bottom: 10px; font-size: 14px;">
                                <i class="fas fa-calendar-check" style="margin-right: 8px;"></i>Receiving Date:
                            </td>
                            <td style="padding-bottom: 10px; font-weight: 600; font-size: 14px;">
                                <strong>{{ $patient->receiving_date ? $patient->receiving_date->format('d-M-Y H:i') : '-' }}</strong>
                            </td>
                            <td style="font-weight: bold; color: black; padding-bottom: 10px; font-size: 14px;">
                                <i class="fas fa-calendar-alt" style="margin-right: 8px;"></i>Reporting Date:
                            </td>
                            <td style="padding-bottom: 10px; font-weight: 600; font-size: 14px;">
                                <strong>{{ $patient->reporting_date ? $patient->reporting_date->format('d-M-Y H:i') : '-' }}</strong>
                            </td>
                        </tr>
                    </table>
                </div>

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
                        <div style="background: #8d2d36; color: white; padding: 18px 20px; border-radius: 8px; margin: 25px 0; font-weight: bold; font-size: 18px; text-align: center; box-shadow: 0 2px 4px rgba(0,0,0,0.1);" class="test-title">
                            <i class="fas fa-flask" style="margin-right: 12px;"></i>{{ $testName }}
                        </div>

                        <!-- Test Results Table -->
                        <div style="border: 2px solid #e0e0e0; border-radius: 8px; overflow: hidden; margin-bottom: 25px;">
                            <table width="100%" cellpadding="8" cellspacing="0" style="border-collapse: collapse; font-size: 13px;" class="test-table">
                                <thead>
                                    <tr style="background: linear-gradient(135deg, rgba(248, 250, 252, 0.9), rgba(255, 255, 255, 0.9)); border-bottom: 3px solid black;">
                                        <th style="text-align: left; padding: 15px; font-weight: bold; width: 40%; color: black; font-size: 14px;">
                                            <i class="fas fa-tag" style="margin-right: 8px;"></i>Test Name
                                        </th>
                                        <th style="text-align: left; padding: 15px; font-weight: bold; width: 20%; color: black; font-size: 14px;">
                                            <i class="fas fa-chart-line" style="margin-right: 8px;"></i>Results
                                        </th>
                                        <th style="text-align: left; padding: 15px; font-weight: bold; width: 15%; color: black; font-size: 14px;">
                                            <i class="fas fa-balance-scale" style="margin-right: 8px;"></i>Unit
                                        </th>
                                        <th style="text-align: left; padding: 15px; font-weight: bold; width: 25%; color: black; font-size: 14px;">
                                            <i class="fas fa-ruler" style="margin-right: 8px;"></i>Reference Ranges
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $rowCount = 0; @endphp
                                    @if ($hasHL7Data)
                                        @foreach ($analytes as $analyte)
                                            @php $rowCount++; @endphp
                                            <tr style="background: {{ $rowCount % 2 == 0 ? '#f9f9f9' : '#fff' }};">
                                                <td style="padding: 12px; border-bottom: 1px solid #e0e0e0; font-weight: 500; font-size: 13px;">
                                                    {{ $analyte['name'] ?? ($analyte['code'] ?? 'Unknown') }}
                                                </td>
                                                <td style="padding: 12px; border-bottom: 1px solid #e0e0e0; font-weight: 700; color: black; font-size: 13px;">
                                                    {{ $analyte['value'] ?? '' }}
                                                </td>
                                                <td style="padding: 12px; border-bottom: 1px solid #e0e0e0; font-size: 13px;">
                                                    {{ $analyte['units'] ?? '' }}
                                                </td>
                                                <td style="padding: 12px; border-bottom: 1px solid #e0e0e0; font-size: 13px;">
                                                    {{ $analyte['ref_range'] ?? '' }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    @elseif($hasData && !empty($template['fields']))
                                        @foreach ($template['fields'] as $field)
                                            @php
                                                $value = $savedData[$field['name']] ?? '';
                                                $label = $field['label'] ?? 'Unknown';
                                                $unit = '';
                                                $ref = '';
                                                $rowCount++;

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
                                                <tr style="background: {{ $rowCount % 2 == 0 ? '#f9f9f9' : '#fff' }};">
                                                    <td style="padding: 12px; border-bottom: 1px solid #e0e0e0; font-weight: 500; font-size: 13px;">{{ $label }}</td>
                                                    <td style="padding: 12px; border-bottom: 1px solid #e0e0e0; font-weight: 700; color: black; font-size: 13px;">{{ $value }}</td>
                                                    <td style="padding: 12px; border-bottom: 1px solid #e0e0e0; font-size: 13px;">{{ $unit }}</td>
                                                    <td style="padding: 12px; border-bottom: 1px solid #e0e0e0; font-size: 13px;">{{ $ref }}</td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="4" style="padding: 15px; text-align: center; color: var(--text-body); background: #f9f9f9; font-size: 13px;">
                                                <i class="fas fa-info-circle" style="margin-right: 8px;"></i>No test data recorded
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>

                        <!-- Individual Test PDF Button -->
                        <div class="no-print" style="margin-bottom: 15px; text-align: right;">
                            <a href="#" onclick="printTest(event, '{{ route('patients.printTest', ['patient' => $patient->id, 'testName' => $testName]) }}')"
                                class="btn btn-sm btn-outline-secondary">
                                <i class="mdi mdi-file-pdf"></i> Download Test PDF
                            </a>
                            <!-- Save to System removed -->
                        </div>

                        <!-- Test Category Notes Section -->
                        @php
                            $labTestCategory = \App\Models\LabTestCat::where('cat_name', $testName)->first();
                            $categoryNotes = $labTestCategory ? $labTestCategory->notes : null;
                        @endphp
                        @if($categoryNotes)
                            <div style="border: 1px solid #8d2d36; border-radius: 6px; padding: 12px 15px; margin-bottom: 20px; border-left: 4px solid #8d2d36;">
                                <div style="font-weight: bold; color: #8d2d36; margin-bottom: 8px; font-size: 14px;">
                                    <i class="fas fa-sticky-note" style="margin-right: 6px;"></i>Test Notes & Remarks
                                </div>
                                <div style="font-size: 13px; color: #333; line-height: 1.5; white-space: pre-wrap;">
                                    {{ $categoryNotes }}
                                </div>
                            </div>
                        @endif
                    @empty
                    @endforelse
                @endif
            </div>

            <!-- Doctor Signature Section -->
            <div style="background: linear-gradient(135deg, rgba(248, 250, 252, 0.8), rgba(255, 255, 255, 0.8)); border: 2px solid #e0e0e0; border-radius: 8px; padding: 20px; margin-top: 30px; display: flex; justify-content: space-between; align-items: center; font-size: 13px; color: #333;">
                <div style="flex: 1; text-align: left;">
                    <div style="background: rgba(255, 255, 255, 0.8); padding: 15px; border-radius: 6px; border-left: 4px solid #8d2d36;">
                        <strong style="color: black;">Please Note:</strong><br>
                        <i class="fas fa-info-circle" style="margin-right: 5px; color: #8d2d36;"></i>if there is no correlation with your clinical findings then please do ask this lab to repeat the test on the same sample, as we preserve it till late evening.<br>
                        <i class="fas fa-signature" style="margin-right: 5px; color: #8d2d36;"></i>This is a digitally signed report and does not require manual signature.
                    </div>
                </div>
                <div style="text-align: right; white-space: nowrap;">
                    <div style="background: rgba(255, 255, 255, 0.8); padding: 15px; border-radius: 6px; border-right: 4px solid #8d2d36;">
                        <div style="font-weight: bold; color: black; margin-bottom: 5px;">
                            <i class="fas fa-user-md" style="margin-right: 5px;"></i>This is a digitally signed report by
                        </div>
                        <strong style="font-size: 11px;">Bacha Khan</strong>
                    </div>
                </div>
            <!-- Print Footer (now included via partial) -->

            <!-- Screen Footer (hidden in print) -->
            <div class="screen-footer no-print">
            </div>

            <!-- Footer Section -->
            <!-- <div
                style="background: black; color: var(--surface); padding: 10px 15px; margin: 15px -1.5rem -1.5rem -1.5rem; font-size: 10px; text-align: center;">
                <strong>�</strong> Asad Abad Road, Near Township Chowk Kamla Swat |
                <strong>📞</strong> 0302-9050191 - 03139796050 |
                <!-- <strong>🌐</strong> www.newmoderncliniclab.com -->
            </div> -->
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="row mt-5 no-print">
        <div class="col-md-12">
            <a href="{{ route('patients.list') }}" class="btn btn-primary">
                <i class="mdi mdi-arrow-left"></i> Back to List
            </a>
            <!-- <button type="button" onclick="window.print()" class="btn btn-info" style="margin-left: 5px;">
                <i class="mdi mdi-printer"></i> Print
            </button>
            <button type="button" onclick="downloadPDF(event)" class="btn btn-warning" style="margin-left: 5px;">
                <i class="mdi mdi-file-pdf"></i> Download PDF
            </button> -->
            <!-- Bulk save to system removed -->

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

    <!-- Saved Reports functionality removed -->

    <style>
        @media print {
                @page {
                        size: A4;
                        margin: 6mm 0mm 10mm 0mm; /* minimal left/right margins to reduce side white area */
                }
                :root {
                    --print-header-height: 36mm;
                    --print-footer-height: 32mm;
                    --print-footer-bottom-gap: 20mm;
                }

            .no-print {
                display: none !important;
            }

            .screen-header,
            .screen-footer {
                display: none !important;
            }

            .print-header,
            .print-footer {
                display: block !important;
                position: fixed;
                left: 0;
                transform: none;
                width: 100%;
                padding: 0 10mm;
                z-index: 9999;
                box-sizing: border-box;
            }

            .print-header {
                top: 6mm;
                height: var(--print-header-height);
            }

            .print-footer {
                bottom: var(--print-footer-bottom-gap);
                height: var(--print-footer-height);
            }

            /* Ensure card body leaves enough space for header/footer */
            .print-body {
                padding-top: calc(var(--print-header-height) + 12mm) !important;
                padding-bottom: calc(var(--print-footer-height) + var(--print-footer-bottom-gap) + 10mm) !important;
            }

            /* Do not add mini-header rows in this page to avoid duplication with the fixed print header */

            body {
                margin: 0;
                padding: 0;
                width: 100%;
                height: 100%;
                font-size: 14px !important; /* Increased base font size */
            }

            .container-fluid {
                padding: 0;
                width: 100% !important;
                max-width: none !important;
                margin: 0 !important;
            }

            .print-card {
                border: none !important;
                box-shadow: none !important;
                margin: 0 !important;
                width: 100% !important;
                max-width: 100% !important;
                padding: 0 !important;
            }

            .card-body {
                padding: 0 !important;
                margin: 0 !important;
                width: 100% !important;
                min-height: calc(297mm - 45mm); /* A4 height minus top/bottom margins */
            }

            .report-logo {
                width: 80px !important; /* Increased from 60px */
                height: auto !important;
            }

            table {
                page-break-inside: auto;
                width: 100% !important;
                font-size: 13px !important; /* Increased table font */
            }

            tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }

            /* Ensure proper print sizing */
            html, body {
                size: A4;
                width: var(--print-page-width);
                min-height: 297mm;
            }

            /* Center content on print page */
            .print-card {
                width: var(--print-inner-width-mm);
                margin: 0 auto;
                display: block;
            }

            /* Larger headers in print */
            .print-header div[style*="font-size: 16px"] {
                font-size: 18px !important;
            }

            .print-header div[style*="font-size: 10px"] {
                font-size: 12px !important;
            }

            .print-header div[style*="font-size: 9px"] {
                font-size: 11px !important;
            }

            .print-header td[style*="font-size: 9px"] {
                font-size: 11px !important;
            }

            /* Larger patient info section */
            .patient-info-section {
                font-size: 13px !important;
            }

            .patient-info-section td {
                padding: 8px !important;
                font-size: 13px !important;
            }

            /* Larger test results */
            .test-title {
                font-size: 16px !important;
                padding: 15px !important;
            }

            .test-table {
                font-size: 12px !important;
            }

            .test-table th {
                padding: 12px !important;
                font-size: 13px !important;
            }

            .test-table td {
                padding: 10px !important;
                font-size: 12px !important;
            }

            /* Larger signature section */
            .signature-section {
                font-size: 12px !important;
                padding: 20px !important;
            }

            /* Larger footer */
            .print-footer div {
                font-size: 11px !important;
                padding: 12px !important;
            }
        }

        @media screen {
            .print-header,
            .print-footer {
                display: none !important;
            }

            .print-card {
                max-width: var(--print-page-width);
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
        function downloadPDF(e) {
            if (e && e.preventDefault) {
                e.preventDefault();
            }
            // Get patient name and date for filename
            const patientName = @json($patient->name ?? '');
            const reportDate = @json($patient->reporting_date ? \Carbon\Carbon::parse($patient->reporting_date)->format('d-m-Y') : '');
            const safeName = (patientName || 'patient').toString().replace(/\s+/g, '_').replace(/[^a-zA-Z0-9_\-]/g, '');
            const fileName = safeName + (reportDate ? '_' + reportDate : '') + '.pdf';
            
            // Add a class to trigger print-specific styles
            document.body.classList.add('generating-pdf');
            
            // Trigger print dialog
            window.print();
            
            // Remove the class after a short delay
            setTimeout(() => {
                document.body.classList.remove('generating-pdf');
            }, 1000);
        }
        
        function printTest(e, url) {
            if (e && e.preventDefault) e.preventDefault();
            fetch(url, { credentials: 'include' })
                .then(resp => {
                    const type = (resp.headers.get('content-type') || '').toLowerCase();
                    if (type.indexOf('application/pdf') !== -1) {
                        return resp.blob().then(b => ({ pdf: b }));
                    }
                    return resp.text().then(t => ({ html: t }));
                })
                .then(result => {
                    if (result.pdf) {
                        const blobUrl = URL.createObjectURL(result.pdf);
                        const iframe = document.createElement('iframe');
                        iframe.style.position = 'fixed';
                        iframe.style.right = '0';
                        iframe.style.bottom = '0';
                        iframe.style.width = '0';
                        iframe.style.height = '0';
                        iframe.style.border = '0';
                        iframe.style.visibility = 'hidden';
                        iframe.src = blobUrl;
                        document.body.appendChild(iframe);
                        iframe.onload = function () {
                            try { iframe.contentWindow.focus(); iframe.contentWindow.print(); } catch (err) { console.error('Print failed', err); }
                            setTimeout(() => { URL.revokeObjectURL(blobUrl); document.body.removeChild(iframe); }, 1500);
                        };
                        return;
                    }

                    const sanitized = result.html.replace(/window\.print\s*\(\s*\)\s*;?/g, '');
                    const iframe = document.createElement('iframe');
                    iframe.style.position = 'fixed';
                    iframe.style.right = '0';
                    iframe.style.bottom = '0';
                    iframe.style.width = '0';
                    iframe.style.height = '0';
                    iframe.style.border = '0';
                    iframe.style.visibility = 'hidden';
                    document.body.appendChild(iframe);
                    const idoc = iframe.contentWindow.document;
                    idoc.open();
                    idoc.write(sanitized);
                    idoc.close();
                    iframe.onload = function () {
                        try { iframe.contentWindow.focus(); iframe.contentWindow.print(); } catch (err) { console.error('Print failed', err); }
                        setTimeout(() => { document.body.removeChild(iframe); }, 1500);
                    };
                }).catch(err => { console.error('Failed to load print view', err); });
        }

        // Save-to-system functionality removed
    </script>
    <script>
        // Saved reports listing removed
    </script>
@endsection
