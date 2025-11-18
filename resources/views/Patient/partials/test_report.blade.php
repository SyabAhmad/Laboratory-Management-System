    <style type="text/css">
    .print-body {
        margin-top: 20px;
        margin-bottom: 25px;
    }

    /* hide print header/footer on screen; show only in print */
    .print-header, .print-footer { display: none; }
    /* Hide the repeating mini-header on screen; show only during print */
    thead .mini-header { display: none; }
    .print-footer {
        margin-top: 25px;
    }

    .footer-container {
        background-color: #f8fafb;
        border: 1px solid #e0e0e0 !important;
        border-radius: 6px;
        padding: 15px;
        margin-top: 25px;
        font-size: 10px;
        color: #333;
            display: flex;
            flex-direction: column; /* stack on screen */
            gap: 12px;
    }

    .footer-container table td {
        width: 100%;
        vertical-align: top;
    }

    .footer-row {
        background-color: #ffffff !important;
        padding: 12px;
        border-radius: 4px;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        line-height: 1.6;
        word-wrap: break-word;
        white-space: normal;
    }

    .footer-note {
        border-left: 3px solid #8d2d36 !important;
        padding-left: 8px;
    }

    .footer-signature {
        border-left: 3px solid #8d2d36 !important;
        text-align: center;
        padding: 15px 12px;
    }

        .footer-contact {
        border-left: 3px solid #8d2d36 !important;
    }

        /* Stack on mobile or narrow pages */
        @media (max-width: 700px) {
            .print-footer .footer-container, .footer-container { flex-direction: column; }
            .print-footer .footer-item, .footer-item { flex-basis: auto; }
        }
    
    .footer-contact div {
        margin-bottom: 5px;
        line-height: 1.6;
    }
    
    .footer-contact div:last-child {
        margin-bottom: 0;
    }
        .print-footer .footer-contact { text-align: right; }

    @media print {
            :root {
                --print-header-height: 36mm; /* slightly smaller header to allow more body space */
                --print-footer-height: 32mm; /* slightly smaller footer for more body space */
            }
        @page {
                    size: A4;
                    margin: 6mm 0mm 10mm 0mm; /* minimal left/right to reduce side white area */
        }
        
        body {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
        }
        
        /* Ensure proper A4 sizing */
        html, body {
            size: A4;
            width: var(--print-page-width);
            min-height: 297mm;
        }
        
        /* Layout calibration */
            .report-container {
            /* use relative widths so different printers don't scale unexpectedly */
            width: calc(100% - 12mm) !important;
            max-width: var(--print-inner-width-mm) !important;
            margin: 0 auto !important;
            padding: 0 !important;
            border: none !important;
            background: transparent !important;
        }

        /* Header/Footer positioning handled by the shared partial (print_header_footer.blade.php) */

        .print-body {
            padding-top: calc(var(--print-header-height) + 12mm) !important; /* ensure content clears header */
            /* ensure body-bottom keeps clear space equal to footer height + bottom anchor */
            padding-bottom: calc(var(--print-footer-height) + 10mm) !important;
            /* rely on report-inner for horizontal spacing so inner content matches header/footer */
            padding-left: 0 !important; padding-right: 0 !important;
            box-sizing: border-box;
        }

        .footer-container {
            padding: 10px 0;
            margin: 0;
            display: flex;
            gap: 12px;
            width: 100%;
            align-items: center;
            justify-content: space-between;
        }
        
        .footer-row {
            margin-bottom: 8px;
            page-break-inside: avoid;
            display: block;
        }
        /* Per column widths are enforced by the grid above */
        .print-footer .footer-item { min-width: 0; box-sizing: border-box; page-break-inside: avoid; widows: 1; orphans: 1; }
        .footer-container table td {
            width: 100%;
            vertical-align: top;
        }
        
        /* Preserve all colors in print */
        * {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            color-adjust: exact !important;
        }
        
        /* Burgundy backgrounds and borders must print */
        div[style*="background-color: #8d2d36"],
        div[style*="background-color:#8d2d36"],
        table[style*="border-bottom: 2px solid #8d2d36"],
        td[style*="border-left: 3px solid #8d2d36"],
        td[style*="border-right: 3px solid #8d2d36"],
        tr[style*="border-bottom: 2px solid #8d2d36"] {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            color-adjust: exact !important;
        }
        
        /* Preserve table styling */
        table {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            border-collapse: collapse !important;
            width: 100% !important;
        }
        
        /* Preserve row colors */
        tr[style*="background:"],
        tr[style*="background-color"] {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        /* Ensure proper page breaks */
        .test-title-section {
            page-break-inside: avoid;
        }
        
        .results-table {
            page-break-inside: auto;
        }

        /* Repeat table headers on each page */
        thead { display: table-header-group; }
        tfoot { display: table-footer-group; }
        /* Keep mini-header hidden by default to avoid duplication with fixed header */
        thead .mini-header { display: none; }
        thead .mini-header th { background-color: #8d2d36 !important; color: #fff !important; padding: 8px 12px !important; font-weight: 700; }

        /* Small repeating lab heading for browsers that don't repeat fixed elements */

        /* Debug outlines to help visually align header/body/footer (remove later if OK) */
        /* Debug outlines were used for layout validation and are now removed */

        /* Prevent header/footer from splitting across pages */
        .print-header, .print-footer { page-break-inside: avoid; }
        
        .notes-section {
            page-break-inside: avoid;
        }
    }
</style>

{{-- Header and footer are included by Layout.print now to avoid duplication --}}
<!-- end include print header/footer -->
    {{-- Personal information block (table format) --}}
    <style>
        .personal-info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            font-size: 20px; /* increase baseline personal info font */
            border: 1px solid #e7e7e7;
                border-radius: 6px;
                background: #fff;
            box-sizing: border-box;
        }
        .personal-info-table td, .personal-info-table th {
            padding: 6px 8px;
            vertical-align: top;
            border: none;
        }
        .personal-info-table th {
            text-align: left;
            color: #8d2d36;
            font-weight: 600;
            width: 14rem;
            white-space: nowrap;
        }
        .personal-info-table td.value {
            padding-left: 4px;
        }
        .personal-info-table .value { color: #333; }
        .personal-info-row { background: transparent; }
        @media print {
            .personal-info-table { font-size: 18px !important; }
        }
    </style>
    <style>
        /* Personal card grid styles (screen and print friendly) */
        .personal-card { border-radius: 8px; background: #fff; border: 1px solid #e7e7e7; padding: 8px; margin-bottom: 12px; width: var(--print-inner-width-mm) !important; max-width: var(--print-inner-width-mm) !important; margin-left: auto; margin-right: auto; box-sizing: border-box; }
        .personal-card-inner { display: grid; grid-template-columns: repeat(2, 1fr); gap: 6px 18px; }
        .pi-cell { display:flex; gap: 10px; align-items: center; }
        .pi-cell i { color: #8d2d36; width: 30px; text-align: center; }
        .pi-meta { display:flex; flex-direction:column; }
        .pi-label { font-size: 12px; color: #8d2d36; font-weight: 700; }
        .pi-value { font-size: 12px; color: #333; font-weight: 600; }
        .section-title { background: #8d2d36; color: #fff; border-radius: 6px; padding: 10px 12px; text-align:center; font-weight:700; margin-bottom: 12px; display:block; }
        .section-title i { margin-right: 8px; }

        /* Results table styling */
        .results-table th { background: #fff; color: #333; font-weight: 700; }
        .results-table thead tr { border-bottom: 3px solid #8d2d36; }
        .results-table tbody tr td { padding: 12px; }
        .results-table { border-radius: 6px; overflow: hidden; border: 1px solid #e6e6e6; width: var(--print-inner-width-mm) !important; max-width: var(--print-inner-width-mm) !important; box-sizing: border-box; font-size: 13px; }
        @media print {
            .personal-card { border: 1px solid #e7e7e7 !important; }
            .section-title { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            /* Make table text larger for print so it's readable on paper */
            .results-table { font-size: 18px !important; }
            .results-table th { font-size: 18px !important; }
            .results-table td { font-size: 18px !important; }
            .pi-value { font-size: 16px !important; }
            .pi-label { font-size: 16px !important; }
            .section-title { font-size: 22px !important; padding: 12px 14px !important; }
        }
    </style>

    <div class="personal-card" role="region" aria-label="Patient information">
        <div class="personal-card-inner">
            <div class="pi-cell">
                <i class="fas fa-user-circle fa-lg" aria-hidden="true"></i>
                <div class="pi-meta"><div class="pi-label">Patient Name</div><div class="pi-value">{{ $patient->name ?? '-' }}</div></div>
            </div>
            <div class="pi-cell">
                <i class="fas fa-birthday-cake fa-lg" aria-hidden="true"></i>
                <div class="pi-meta">
                    <div class="pi-label">Age / Gender</div>
                    <div class="pi-value">
                        @php
                            // Use individual age parts if available, otherwise use the combined string
                            if (!empty($patient->age_years) || !empty($patient->age_months) || !empty($patient->age_days)) {
                                $parts = [];
                                if (!empty($patient->age_years)) $parts[] = $patient->age_years . 'Y';
                                if (!empty($patient->age_months)) $parts[] = $patient->age_months . 'M';
                                if (!empty($patient->age_days)) $parts[] = $patient->age_days . 'D';
                                $ageDisplay = !empty($parts) ? implode(' ', $parts) : '0Y';
                            } else {
                                $ageDisplay = $patient->age ?: '-';
                            }
                        @endphp
                        {{ $ageDisplay }} / {{ ucfirst($patient->gender ?? '-') }}
                    </div>
                </div>
            </div>
            <div class="pi-cell">
                <i class="fas fa-id-card fa-lg" aria-hidden="true"></i>
                <div class="pi-meta"><div class="pi-label">Patient ID</div><div class="pi-value">{{ $patient->patient_id ?? '-' }}</div></div>
            </div>
            <div class="pi-cell">
                <i class="fas fa-phone fa-lg" aria-hidden="true"></i>
                <div class="pi-meta"><div class="pi-label">Mobile</div><div class="pi-value">{{ $patient->mobile_phone ?? '-' }}</div></div>
            </div>
            <div class="pi-cell">
                <i class="fas fa-map-marker-alt fa-lg" aria-hidden="true"></i>
                <div class="pi-meta"><div class="pi-label">Address</div><div class="pi-value">{{ $patient->address ?? '-' }}</div></div>
            </div>
            <div class="pi-cell">
                <i class="fas fa-user-tie fa-lg" aria-hidden="true"></i>
                <div class="pi-meta"><div class="pi-label">Referred By</div><div class="pi-value">{{ $patient->referred_by ?? '-' }}</div></div>
            </div>
        </div>
    </div>
    <hr style="border: 0; border-top: 1px solid #e7e7e7; margin: 8px 0 12px 0;">

    {{-- Title of the test category --}}
    <div class="section-title">
        <i class="fas fa-flask"></i>
        <span>{{ strtoupper($testEntry['name'] ?? 'TEST') }}</span>
    </div>
    <div style="border: 1px solid #e0e0e0; border-radius: 6px; overflow: hidden; margin-bottom: 20px; width: var(--print-inner-width-mm); max-width: var(--print-inner-width-mm); margin-left:auto; margin-right:auto; box-sizing: border-box;">
        <table class="results-table" width="100%" cellpadding="5" cellspacing="0" style="border-collapse: collapse;">
            <thead>
                <!-- mini-header fallback removed to avoid duplication; use fixed header from partial -->
                <tr style="background-color: #fff !important; border-bottom: 3px solid #8d2d36 !important;">
                    <th style="text-align: left; padding: 12px; font-weight: bold; width: 50%; color: black;">
                        <i class="fas fa-tag" style="margin-right: 5px;"></i>Test Name
                    </th>
                    <th style="text-align: left; padding: 12px; font-weight: bold; width: 10%; color: black;">
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
                @php
                    $analytes = $testEntry['saved_data']['analytes'] ?? [];
                    $hasHL7Data = !empty($analytes) && is_array($analytes);
                    $rowCount = 0;
                @endphp

                @if ($hasHL7Data)
                    <!-- HL7 Data -->
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
                @else
                    <!-- Actual Test Parameters -->
                    @foreach ($testEntry['template']['fields'] ?? [] as $field)
                        @php
                            $value = $testEntry['saved_data'][$field['name']] ?? '';
                            $label = $field['label'] ?? 'Unknown';
                            $unit = $field['unit'] ?? '';
                            $ref = $field['ref'] ?? '';
                            $rowCount++;

                            // Format date values to d-M-Y
                            if ($value && preg_match('/^\d{4}-\d{2}-\d{2}/', $value)) {
                                try {
                                    $dateTime = new \DateTime($value);
                                    $value = $dateTime->format('d-M-Y');
                                } catch (\Exception $e) {
                                    // Leave as-is if invalid
                                }
                            }
                        @endphp
                        <tr style="background: {{ $rowCount % 2 == 0 ? '#f9f9f9' : '#fff' }};">
                            <td style="padding: 10px; border-bottom: 1px solid #e0e0e0; font-weight: 500;">{{ $label }}</td>
                            <td style="padding: 10px; border-bottom: 1px solid #e0e0e0; font-weight: 700; color: black;">{{ $value }}</td>
                            <td style="padding: 10px; border-bottom: 1px solid #e0e0e0;">{{ $unit }}</td>
                            <td style="padding: 10px; border-bottom: 1px solid #e0e0e0;">{{ $ref }}</td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>

    <!-- Test Category Notes Section (Lab Test Category Notes) -->
    @php
        $categoryNotes = null;
        // Fetch notes from LabTestCat based on test name
        try {
            $labTestCategory = \App\Models\LabTestCat::where('cat_name', $testEntry['name'])->first();
            $categoryNotes = $labTestCategory ? $labTestCategory->notes : null;
        } catch (\Exception $e) {
            $categoryNotes = null;
        }
    @endphp
    @if($categoryNotes)
        <div style="padding: 10px; margin-bottom: 20px;">
            <div style="font-weight: bold; color: #8d2d36; margin-bottom: 8px; font-size: 12px; border-bottom: 1px solid #8d2d36; padding-bottom: 8px;">
                <i class="fas fa-sticky-note" style="margin-right: 8px;"></i>Notes
            </div>
            <div style="font-size: 20px; color: #333; line-height: 1.5; white-space: pre-wrap;">
                {{ $categoryNotes }}
            </div>
        </div>
    @endif

    <!-- Test Notes Section (if any notes exist) -->
    @php
        $billNotes = [];
        // Collect notes from the bill's all_test JSON if available
        if (isset($bill) && $bill->all_test) {
            $allTests = json_decode($bill->all_test, true);
            if (is_array($allTests)) {
                foreach ($allTests as $test) {
                    if (!empty($test['notes'])) {
                        $dept = $test['department'] ?? 'General';
                        if (!isset($billNotes[$dept])) {
                            $billNotes[$dept] = [];
                        }
                        $billNotes[$dept][] = [
                            'test_name' => $test['test_name'] ?? '',
                            'notes' => $test['notes']
                        ];
                    }
                }
            }
        }
    @endphp

    @if (!empty($billNotes))
        <div style="background-color: #fffacd !important; border: 2px solid #f39c12 !important; border-radius: 6px; padding: 15px; margin-bottom: 20px;">
            <div style="font-weight: bold; color: #d68910; font-size: 12px; margin-bottom: 10px; border-bottom: 1px solid #f39c12; padding-bottom: 8px;">
                <i class="fas fa-sticky-note" style="margin-right: 8px;"></i>Clinical Notes & Remarks
            </div>
            @foreach ($billNotes as $department => $notesList)
                <div style="margin-bottom: 12px; padding-bottom: 10px; border-bottom: 1px dashed #f39c12;">
                    <div style="font-weight: bold; color: #d68910; font-size: 11px; margin-bottom: 6px;">
                        <i class="fas fa-building" style="margin-right: 5px;"></i>{{ $department }}
                    </div>
                    @foreach ($notesList as $note)
                        <div style="margin-left: 15px; font-size: 13px; line-height: 1.4; margin-bottom: 6px; color: #333;">
                            <span style="font-weight: 600; color: #555;">{{ $note['test_name'] }}:</span> {{ $note['notes'] }}
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>
    @endif

    <!-- Footer will be provided by print_header_footer partial (already included above) -->
