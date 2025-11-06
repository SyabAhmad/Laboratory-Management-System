<style type="text/css">
    @media print {
        body {
            margin: 0;
            padding: 0;
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
        }
        
        /* Preserve row colors */
        tr[style*="background:"],
        tr[style*="background-color"] {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
    }
</style>

<div
    style="font-family: Arial, sans-serif; font-size: 11px; line-height: 1.4; padding: 20px; max-width: 800px; margin: auto; background: #fff; border: 1px solid #e0e0e0; border-radius: 8px;">

    <!-- Header: Logo Left + Lab Info Center + Contact Right -->
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
                style="font-size: 10px; color: #8d2d36; line-height: 1.4; padding-left: 15px; border-left: 2px solid #8d2d36;">
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

    <!-- Patient Info Section -->
    <div style="background-color: #f8fafb; border: 1px solid #8d2d36 !important; border-radius: 6px; padding: 15px; margin-bottom: 20px;">
        <table width="100%" cellpadding="6" cellspacing="0" style="font-size: 11px; border-collapse: collapse;">
            <tr>
                <td width="25%" style="font-weight: bold; color: black; padding-bottom: 8px;">Patient Name:</td>
                <td width="25%" style="padding-bottom: 8px; font-weight: 600;">{{ $patient->name }}</td>
                <td width="25%" style="font-weight: bold; color: black; padding-bottom: 8px;">Visit Date:</td>
                <td width="25%" style="padding-bottom: 8px;">
                    @php
                        $testDate = $testEntry['saved_data']['test_date'] ?? ($testEntry['saved_data']['reported_at'] ?? null);
                        echo $testDate ? date('d-M-Y', strtotime($testDate)) : 'N/A';
                    @endphp
                </td>
            </tr>
            <tr>
                <td style="font-weight: bold; color: black; padding-bottom: 8px;">Age / Gender:</td>
                <td style="padding-bottom: 8px; font-weight: 600;">{{ $patient->age }} yr(s) / {{ $patient->gender }}</td>
                <td style="font-weight: bold; color: black; padding-bottom: 8px;">Report Date:</td>
                <td style="padding-bottom: 8px;">
                    @php
                        $reportDate = $testEntry['saved_data']['reported_at'] ?? ($testEntry['saved_data']['test_date'] ?? null);
                        echo $reportDate ? date('d-M-Y', strtotime($reportDate)) : 'N/A';
                    @endphp
                </td>
            </tr>
            <tr>
                <td style="font-weight: bold; color: black;">Referred By:</td>
                <td colspan="3" style="font-weight: 600;">{{ $patient->referred_by ?? 'Self' }}</td>
            </tr>
        </table>
    </div>

    <!-- Test Title Section -->
    <div style="background-color: #8d2d36; color: white; padding: 12px 15px; border-radius: 6px; margin: 20px 0; font-weight: bold; font-size: 14px; text-align: center; box-shadow: 0 2px 4px rgba(0,0,0,0.1); page-break-inside: avoid;">
        <span style="color: white; font-weight: bold;">{{ $testEntry['name'] }}</span>
    </div>

    <!-- Main Results Table -->
    <div style="border: 1px solid #e0e0e0; border-radius: 6px; overflow: hidden; margin-bottom: 20px;">
        <table width="100%" cellpadding="5" cellspacing="0" style="border-collapse: collapse; font-size: 11px;">
            <thead>
                <tr style="background-color: #f8fafb !important; border-bottom: 2px solid #8d2d36 !important;">
                    <th style="text-align: left; padding: 12px; font-weight: bold; width: 40%; color: black;">
                        <i class="fas fa-tag" style="margin-right: 5px;"></i>Test Name
                    </th>
                    <th style="text-align: left; padding: 12px; font-weight: bold; width: 20%; color: black;">
                        <i class="fas fa-chart-line" style="margin-right: 5px;"></i>Results
                    </th>
                    <th style="text-align: left; padding: 12px; font-weight: bold; width: 15%; color: black;">
                        <i class="fas fa-ruler" style="margin-right: 5px;"></i>Reference Ranges
                    </th>
                    <th style="text-align: left; padding: 12px; font-weight: bold; width: 25%; color: black;">
                        <i class="fas fa-balance-scale" style="margin-right: 5px;"></i>Unit
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
                                {{ $analyte['ref_range'] ?? '' }}
                            </td>
                            <td style="padding: 10px; border-bottom: 1px solid #e0e0e0;">
                                {{ $analyte['units'] ?? '' }}
                            </td>
                        </tr>
                    @endforeach
                @else
                    <!-- Actual Test Parameters -->
                    @foreach ($testEntry['template']['fields'] as $field)
                        @php
                            $value = $testEntry['saved_data'][$field['name']] ?? '';
                            $label = $field['label'] ?? 'Unknown';
                            $unit = $field['unit'] ?? '';
                            $ref = $field['ref'] ?? '';
                            $rowCount++;

                            // Format date values to d-M-Y
                            if ($value && preg_match('/^\d{4}-\d{2}-\d{2}/', $value)) {
                                try {
                                    $dateTime = new DateTime($value);
                                    $value = $dateTime->format('d-M-Y');
                                } catch (Exception $e) {
                                    // Leave as-is if invalid
                                }
                            }
                        @endphp
                        <tr style="background: {{ $rowCount % 2 == 0 ? '#f9f9f9' : '#fff' }};">
                            <td style="padding: 10px; border-bottom: 1px solid #e0e0e0; font-weight: 500;">{{ $label }}</td>
                            <td style="padding: 10px; border-bottom: 1px solid #e0e0e0; font-weight: 700; color: black;">{{ $value }}</td>
                            <td style="padding: 10px; border-bottom: 1px solid #e0e0e0;">{{ $ref }}</td>
                            <td style="padding: 10px; border-bottom: 1px solid #e0e0e0;">{{ $unit }}</td>
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
            <div style="font-size: 11px; color: #333; line-height: 1.5; white-space: pre-wrap;">
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
                        <div style="margin-left: 15px; font-size: 10px; line-height: 1.4; margin-bottom: 6px; color: #333;">
                            <span style="font-weight: 600; color: #555;">{{ $note['test_name'] }}:</span> {{ $note['notes'] }}
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>
    @endif

    <!-- Footer -->
    <div style="background-color: #f8fafb !important; border: 1px solid #e0e0e0 !important; border-radius: 6px; padding: 15px; margin-top: 25px; display: flex; justify-content: space-between; align-items: center; font-size: 10px; color: #333;">
        <div style="flex: 1; text-align: left;">
            <div style="background-color: #ffffff !important; padding: 10px; border-radius: 4px; border-left: 3px solid #8d2d36 !important;">
                <strong style="color: black;">Please Note:</strong><br>
                <i class="fas fa-info-circle" style="margin-right: 5px; color: black;"></i>Test(s) are performed on the state-of-the-art ARCHITECT MODULAR Ci4100 from Abbott Diagnostics, U.S.A.<br>
                <i class="fas fa-signature" style="margin-right: 5px; color: black;"></i>This is a digitally signed report and does not require manual signature.
            </div>
        </div>
        <div style="text-align: right; white-space: nowrap;">
            <div style="background-color: #ffffff !important; padding: 10px; border-radius: 4px; border-right: 3px solid #8d2d36 !important;">
                <div style="font-weight: bold; color: black; margin-bottom: 5px;">
                    <i class="fas fa-user-md" style="margin-right: 5px;"></i>This is a digitally signed report by
                </div>
                <strong style="font-size: 11px;">Bacha Khan</strong>
            </div>
            {{-- <div style="display: inline-flex; gap: 8px; align-items: center; margin-top: 10px;">
                <img src="{{ asset('assets/images/neoapp.png') }}" alt="NEQAPP" style="height: 20px; opacity: 0.8;">
                <img src="{{ asset('assets/images/riqas.png') }}" alt="RIQAS" style="height: 20px; opacity: 0.8;">
                <img src="{{ asset('assets/images/pnac.png') }}" alt="PNAC" style="height: 20px; opacity: 0.8;">
                <img src="{{ asset('assets/images/synlab.png') }}" alt="SynLab" style="height: 20px; opacity: 0.8;">
                <img src="{{ asset('assets/images/iso.png') }}" alt="ISO" style="height: 20px; opacity: 0.8;">
            </div> --}}
        </div>
    </div>

</div>
