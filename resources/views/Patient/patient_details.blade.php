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
                <!-- Header Section with Logo and Title -->
                <div class="report-header mb-1">
                    <div class="d-flex align-items-center mb-1">
                        <img src="{{ asset('assets/images/billlogo.png') }}" alt="Logo" class="report-logo" style="width: 100px; height: auto; margin-right: 20px;">
                        <div class="text-center flex-grow-1">
                            <h2 class="lab-name mb-1" style="color: #da920dff; font-weight: bold; font-size: 28px;">NEW MODERN CLINICAL LABORATORY</h2>
                            <p class="lab-subtitle mb-0" style="color: #7f8c8d; font-size: 14px;">
                                <strong>Bacha Khan</strong> - Lab Technologist
                            </p>
                            
                        </div>
                    </div>
                    <hr style="border-top: 3px solid #da920dff; margin: 20px 0;">
                </div>

                <!-- Patient Information Section -->
                <div class="patient-info mb-4">
                    <h5 class="section-title mb-3" style="color: #2c3e50; border-bottom: 2px solid #da920dff; padding-bottom: 5px;">
                        PATIENT INFORMATION
                    </h5>
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-sm table-borderless">
                                <tbody>
                                    <tr>
                                        <td style="width: 40%; font-weight: 600;">Patient Name:</td>
                                        <td>{{ $patient->name }}</td>
                                    </tr>
                                    <tr>
                                        <td style="font-weight: 600;">Patient ID:</td>
                                        <td>{{ $patient->patient_id }}</td>
                                    </tr>
                                    <tr>
                                        <td style="font-weight: 600;">Age / Gender:</td>
                                        <td>{{ $patient->age }} Years / {{ $patient->gender }}</td>
                                    </tr>
                                    <tr>
                                        <td style="font-weight: 600;">Blood Group:</td>
                                        <td>{{ $patient->blood_group ?? 'N/A' }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-sm table-borderless">
                                <tbody>
                                    <tr>
                                        <td style="width: 40%; font-weight: 600;">Mobile Phone:</td>
                                        <td>{{ $patient->mobile_phone }}</td>
                                    </tr>
                                    <tr>
                                        <td style="font-weight: 600;">Receiving Date:</td>
                                        <td>{{ $patient->receiving_date ? \Carbon\Carbon::parse($patient->receiving_date)->format('d-m-Y') : 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td style="font-weight: 600;">Reporting Date:</td>
                                        <td>{{ $patient->reporting_date ? \Carbon\Carbon::parse($patient->reporting_date)->format('d-m-Y') : 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td style="font-weight: 600;">Referred By:</td>
                                        <td>
                                            @php $refName = optional($patient->referral)->name; @endphp
                                            @if (empty($patient->referred_by) || $patient->referred_by === 'none' || $refName === null)
                                                None
                                            @else
                                                {{ $refName }}
                                            @endif
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <table class="table table-sm table-borderless">
                                <tbody>
                                    <tr>
                                        <td style="width: 12%; font-weight: 600;">Address:</td>
                                        <td>{{ $patient->address }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Test Results Section - Dynamic from Database -->
                @if(!empty($testsWithData))
                    <div class="test-results mb-4">
                        <h5 class="section-title mb-3" style="color: #2c3e50; border-bottom: 2px solid #da920dff; padding-bottom: 5px;">
                            <i class="fas fa-flask"></i> REGISTERED TESTS & RESULTS
                        </h5>

                        @forelse($testsWithData as $test)
                            @php
                                $testName = $test['name'];
                                $template = $test['template'];
                                $savedData = $test['saved_data'];
                                $hasData = $test['has_data'];
                                $isMllpData = $test['is_mllp_data'] ?? false;
                                $borderColor = $hasData ? '#28a745' : '#ffc107';
                            @endphp

                            <div class="test-card mb-4 p-3" style="background-color: #f8f9fa; border-left: 4px solid {{ $borderColor }}; border-radius: 4px;">
                                <div class="d-flex align-items-center mb-3">
                                    <i class="fas fa-{{ $isMllpData ? 'microchip' : 'vial' }}" style="color: #da920dff; font-size: 24px; margin-right: 12px;"></i>
                                    <div>
                                        <h6 class="mb-0" style="color: #2c3e50; font-weight: 600;">{{ $testName }}</h6>
                                        <small class="text-muted">
                                            @if($hasData)
                                                <span class="badge badge-success"><i class="fas fa-check-circle"></i> Completed</span>
                                                @if($isMllpData)
                                                    <span class="badge badge-info ml-2"><i class="fas fa-microchip"></i> From Analyzer</span>
                                                @endif
                                                @if(!empty($savedData['test_date']))
                                                    <span class="ml-2">Date: {{ $savedData['test_date'] }}</span>
                                                @elseif(!empty($savedData['reported_at']))
                                                    <span class="ml-2">Reported: {{ $savedData['reported_at'] }}</span>
                                                @endif
                                            @else
                                                <span class="badge badge-warning"><i class="fas fa-clock"></i> Pending</span>
                                            @endif
                                        </small>
                                    </div>
                                </div>

                                @if($hasData && !empty($template['fields']))
                                    <table class="table table-sm table-borderless mb-0">
                                        <tbody>
                                            @foreach($template['fields'] as $field)
                                                @php
                                                    $fieldValue = $savedData[$field['name']] ?? '';
                                                @endphp
                                                @if(!empty($fieldValue) || $isMllpData)
                                                    <tr>
                                                        <td style="width: 35%; font-weight: 500; color: #2c3e50;">
                                                            {{ $field['label'] }}:
                                                        </td>
                                                        <td style="color: #34495e;">
                                                            @if(!empty($fieldValue))
                                                                {{ $fieldValue }}
                                                            @elseif($isMllpData && in_array($field['name'], ['reported_at', 'instrument', 'accession_no']))
                                                                {{ $savedData[$field['name']] ?? '-' }}
                                                            @else
                                                                -
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                        </tbody>
                                    </table>
                                @else
                                    <div class="alert alert-info mb-0 py-2">
                                        <i class="fas fa-info-circle"></i> No test data recorded yet.
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> No tests registered for this patient.
                            </div>
                        @endforelse
                    </div>
                @endif

                    @if($patient->note)
                    <div class="clinical-notes mt-3 p-3" style="background-color: #f8f9fa; border-left: 4px solid #3498db;">
                        <strong>Clinical Notes:</strong>
                        <p class="mt-2 mb-0">{{ $patient->note }}</p>
                    </div>
                    @endif
                </div>

                <!-- Doctor Signature Section -->
                <div class="signature-section mt-5 mb-4">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="text-left">
                                <div style="border-top: 2px solid #000; width: 200px; margin-bottom: 10px;"></div>
                                <p class="mb-0" style="font-weight: 600;">Dr. Bacha Khan</p>
                                <p style="font-size: 12px; color: #7f8c8d;">Lab Technologist</p>
                                <p style="font-size: 11px; color: #95a5a6;">Signature & Stamp</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-center">
                                <p class="mb-1" style="font-size: 12px; color: #7f8c8d;">Report is System Generated</p>
                                <p style="font-size: 11px; color: #95a5a6;">{{ $patient->created_at ? $patient->created_at->format('d-m-Y H:i') : '' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer Section -->
                <div class="report-footer" style="background-color: #da920dff; color: white; padding: 10px 15px; margin: 0 -1.5rem -1.5rem -1.5rem; font-size: 11px;">
                    <div class="text-center">
                        <strong>🗺</strong> Asad Abad Road, Near Township Chowk Kamla Swat | 
                        <strong>📞</strong> 0302-9050191 - 03139796050 | 
                        <strong>📱</strong> +92 302 9050191 | 
                        <strong>🌐</strong> www.newmoderncliniclab.com | 
                        C17 Pariology Department Serial | BMT MRI Rastamua | Medical College SGRD Hoot
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="row mt-3 no-print">
            <div class="col-md-12">
                <a href="{{ route('patients.list') }}" class="btn btn-primary">
                    <i class="mdi mdi-arrow-left"></i> Back to List
                </a>
                <button onclick="window.print()" class="btn btn-success float-right ml-2">
                    <i class="mdi mdi-printer"></i> Print / Download PDF
                </button>
                @php
                    // Format phone number for WhatsApp (remove spaces, dashes, and add country code if needed)
                    $phoneNumber = preg_replace('/[^0-9]/', '', $patient->mobile_phone);
                    if (!str_starts_with($phoneNumber, '92')) {
                        if (str_starts_with($phoneNumber, '0')) {
                            $phoneNumber = '92' . substr($phoneNumber, 1);
                        } else {
                            $phoneNumber = '92' . $phoneNumber;
                        }
                    }
                    
                    // Get the current URL for the report
                    $reportUrl = url()->current();
                    
                    $message = "Hello " . $patient->name . ",\n\n";
                    $message .= "Your medical test report from NEW MODERN CLINICAL LABORATORY is ready.\n\n";
                    $message .= "Patient ID: " . $patient->patient_id . "\n";
                    $message .= "Report Date: " . ($patient->reporting_date ? \Carbon\Carbon::parse($patient->reporting_date)->format('d-m-Y') : 'N/A') . "\n\n";
                    $message .= "Please visit our laboratory to collect your report.\n\n";
                    $message .= "For any queries, contact us:\n";
                    $message .= "📞 +92 302 9050191\n";
                    $message .= "🌐 www.newmoderncliniclab.com\n\n";
                    $message .= "Thank you for choosing New Modern Clinical Laboratory.";
                    
                    $whatsappUrl = "https://wa.me/" . $phoneNumber . "?text=" . urlencode($message);
                @endphp
                <a href="{{ $whatsappUrl }}" target="_blank" class="btn btn-success float-right" style="background-color: #25D366; border-color: #25D366;">
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
                box-shadow: 0 0 10px rgba(0,0,0,0.1);
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

@endsection
