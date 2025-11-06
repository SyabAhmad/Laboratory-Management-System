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

        <div class="card print-card" style="border-radius: 8px; overflow: hidden;">
            <div class="card-body p-4">
                <!-- Header Section with Logo and Lab Name -->
                <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom: 20px; border-bottom: 2px solid #8d2d36; padding-bottom: 15px;">
                    <tr>
                        <!-- Left: Logo -->
                        <td width="15%" valign="top" align="center" style="padding-right: 15px;">
                            <img src="{{ asset('assets/images/logo.png') }}" alt="Logo"
                                style="width: 80px; height: 80px; border-radius: 50%; border: 3px solid #8d2d36; display: block; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                        </td>
                        <!-- Center: Lab Name & Info -->
                        <td style="padding: 0 15px;">
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
                            style="font-size: 10px; color: #333; line-height: 1.4; padding-left: 15px; border-left: 2px solid #8d2d36;">
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

                <!-- Patient Information Section -->
                <div style="background: linear-gradient(135deg, rgba(248, 250, 252, 0.8), rgba(255, 255, 255, 0.8)); border: 1px solid #8d2d36; border-radius: 6px; padding: 15px; margin-bottom: 20px;">
                    <table width="100%" cellpadding="6" cellspacing="0" style="font-size: 11px; border-collapse: collapse;">
                        <tr>
                            <td width="25%" style="font-weight: bold; color: black; padding-bottom: 8px;">
                                <i class="fas fa-user" style="margin-right: 5px;"></i>Patient Name:
                            </td>
                            <td width="25%" style="padding-bottom: 8px; font-weight: 600;">{{ $patient->name }}</td>
                            <td width="25%" style="font-weight: bold; color: black; padding-bottom: 8px;">
                                <i class="fas fa-birthday-cake" style="margin-right: 5px;"></i>Age / Gender:
                            </td>
                            <td width="25%" style="padding-bottom: 8px; font-weight: 600;">{{ $patient->age }} / {{ $patient->gender }}</td>
                        </tr>
                        <tr>
                            <td style="font-weight: bold; color: black; padding-bottom: 8px;">
                                <i class="fas fa-id-card" style="margin-right: 5px;"></i>Patient ID:
                            </td>
                            <td style="padding-bottom: 8px; font-weight: 600;">{{ $patient->patient_id }}</td>
                            <td style="font-weight: bold; color: black; padding-bottom: 8px;">
                                <i class="fas fa-tint" style="margin-right: 5px;"></i>Blood Group:
                            </td>
                            <td style="padding-bottom: 8px; font-weight: 600;">{{ $patient->blood_group ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td style="font-weight: bold; color: black;">
                                <i class="fas fa-user-md" style="margin-right: 5px;"></i>Referred By:
                            </td>
                            <td style="font-weight: 600;">
                                @php $refName = optional($patient->referral)->name; @endphp
                                {{ empty($patient->referred_by) || $patient->referred_by === 'none' || $refName === null ? 'Self' : $refName }}
                            </td>
                            <td style="font-weight: bold; color: black;">
                                <i class="fas fa-phone" style="margin-right: 5px;"></i>Mobile:
                            </td>
                            <td style="font-weight: 600;">{{ $patient->mobile_phone }}</td>
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
                        <div style="background: #8d2d36; color: white; padding: 12px 15px; border-radius: 6px; margin: 20px 0; font-weight: bold; font-size: 14px; text-align: center; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                            <i class="fas fa-flask" style="margin-right: 8px;"></i>{{ $testName }}
                        </div>

                        <!-- Test Results Table -->
                        <div style="border: 1px solid #e0e0e0; border-radius: 6px; overflow: hidden; margin-bottom: 20px;">
                            <table width="100%" cellpadding="5" cellspacing="0" style="border-collapse: collapse; font-size: 11px;">
                                <thead>
                                    <tr style="background: linear-gradient(135deg, rgba(248, 250, 252, 0.9), rgba(255, 255, 255, 0.9)); border-bottom: 2px solid black;">
                                        <th style="text-align: left; padding: 12px; font-weight: bold; width: 40%; color: black;">
                                            <i class="fas fa-tag" style="margin-right: 5px;"></i>Test Name
                                        </th>
                                        <th style="text-align: left; padding: 12px; font-weight: bold; width: 20%; color: black;">
                                            <i class="fas fa-chart-line" style="margin-right: 5px;"></i>Results
                                        </th>
                                        <th style="text-align: left; padding: 12px; font-weight: bold; width: 15%; color: black;">
                                            <i class="fas fa-balance-scale" style="margin-right: 5px;"></i>Unit
                                        </th>
                                        <th style="text-align: left; padding: 12px; font-weight: bold; width: 25%; color: black;">
                                            <i class="fas fa-ruler" style="margin-right: 5px;"></i>Reference Ranges
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $rowCount = 0; @endphp
                                    @if ($hasHL7Data)
                                        @foreach ($analytes as $analyte)
                                            @php $rowCount++; @endphp
                                            <tr style="background: {{ $rowCount % 2 == 0 ? '#f9f9f9' : '#fff' }};">
                                                <td style="padding: 10px; border-bottom: 1px solid #e0e0e0; font-weight: 500;">
                                                    {{ $analyte['name'] ?? ($analyte['code'] ?? 'Unknown') }}
                                                </td>
                                                <td style="padding: 10px; border-bottom: 1px solid #e0e0e0; font-weight: 700; color: black;">
                                                    {{ $analyte['value'] ?? '' }}
                                                </td>
                                                <td style="padding: 10px; border-bottom: 1px solid #e0e0e0;">
                                                    {{ $analyte['units'] ?? '' }}
                                                </td>
                                                <td style="padding: 10px; border-bottom: 1px solid #e0e0e0;">
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
                                                    <td style="padding: 10px; border-bottom: 1px solid #e0e0e0; font-weight: 500;">{{ $label }}</td>
                                                    <td style="padding: 10px; border-bottom: 1px solid #e0e0e0; font-weight: 700; color: black;">{{ $value }}</td>
                                                    <td style="padding: 10px; border-bottom: 1px solid #e0e0e0;">{{ $unit }}</td>
                                                    <td style="padding: 10px; border-bottom: 1px solid #e0e0e0;">{{ $ref }}</td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="4" style="padding: 12px; text-align: center; color: var(--text-body); background: #f9f9f9;">
                                                <i class="fas fa-info-circle" style="margin-right: 5px;"></i>No test data recorded
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>

                        <!-- Individual Test PDF Button -->
                        <div class="no-print" style="margin-bottom: 15px; text-align: right;">
                            <a href="{{ route('patients.printTest', ['patient' => $patient->id, 'testName' => $testName]) }}"
                                target="_blank" class="btn btn-sm btn-outline-secondary">
                                <i class="mdi mdi-file-pdf"></i> Download Test PDF
                            </a>
                        </div>

                        <!-- Test Category Notes Section -->
                        @php
                            $labTestCategory = \App\Models\LabTestCat::where('cat_name', $testName)->first();
                            $categoryNotes = $labTestCategory ? $labTestCategory->notes : null;
                        @endphp
                        @if($categoryNotes)
                            <div style="border: 1px solid #8d2d36; border-radius: 6px; padding: 12px 15px; margin-bottom: 20px; border-left: 4px solid #8d2d36;">
                                <div style="font-weight: bold; color: #8d2d36; margin-bottom: 8px; font-size: 12px;">
                                    <i class="fas fa-sticky-note" style="margin-right: 6px;"></i>Test Notes & Remarks
                                </div>
                                <div style="font-size: 11px; color: #333; line-height: 1.5; white-space: pre-wrap;">
                                    {{ $categoryNotes }}
                                </div>
                            </div>
                        @endif
                    @empty
                    @endforelse
                @endif
            </div>

            <!-- Doctor Signature Section -->
            <div style="background: linear-gradient(135deg, rgba(248, 250, 252, 0.8), rgba(255, 255, 255, 0.8)); border: 1px solid #e0e0e0; border-radius: 6px; padding: 15px; margin-top: 25px; display: flex; justify-content: space-between; align-items: center; font-size: 10px; color: #333;">
                <div style="flex: 1; text-align: left;">
                    <div style="background: rgba(255, 255, 255, 0.8); padding: 10px; border-radius: 4px; border-left: 3px solid #8d2d36;">
                        <strong style="color: black;">Please Note:</strong><br>
                        <i class="fas fa-info-circle" style="margin-right: 5px; color: #8d2d36;"></i>Test(s) are performed on the state-of-the-art ARCHITECT MODULAR Ci4100 from Abbott Diagnostics, U.S.A.<br>
                        <i class="fas fa-signature" style="margin-right: 5px; color: #8d2d36;"></i>This is a digitally signed report and does not require manual signature.
                    </div>
                </div>
                <div style="text-align: right; white-space: nowrap;">
                    <div style="background: rgba(255, 255, 255, 0.8); padding: 10px; border-radius: 4px; border-right: 3px solid #8d2d36;">
                        <div style="font-weight: bold; color: black; margin-bottom: 5px;">
                            <i class="fas fa-user-md" style="margin-right: 5px;"></i>This is a digitally signed report by
                        </div>
                        <strong style="font-size: 11px;">Bacha Khan</strong>
                    </div>
                </div>
            </div>

            <!-- Footer Section -->
            <div
                style="background: black; color: var(--surface); padding: 10px 15px; margin: 15px -1.5rem -1.5rem -1.5rem; font-size: 10px; text-align: center;">
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
