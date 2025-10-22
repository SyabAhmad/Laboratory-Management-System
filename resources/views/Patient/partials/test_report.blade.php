<div style="font-family: Arial, sans-serif; font-size: 11px; line-height: 1.4; padding: 20px; max-width: 800px; margin: auto;">

    <!-- Header: Logo Left + Lab Info Center + Contact Right -->
    <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom: 15px;">
        <tr>
            <!-- Left: Logo -->
            <td width="15%" valign="top" align="center" style="padding-right: 15px;">
                <img src="{{ asset('assets/images/logo.png') }}" alt="Logo" style="width: 75px; height: 75px; border-radius: 50%; border: 2px solid #da920d; display: block;">
            </td>
            <!-- Center: Lab Name & Info -->
            <td>
                <div style="color: #da920d; font-weight: bold; font-size: 18px; margin: 0; line-height: 1.1;">NEW MODERN CLINICAL<br>LABORATORY</div>
                <div style="font-size: 10px; color: #da920d; margin: 4px 0 0 0; font-weight: 600;">(KP HCC) REG: 03663 SWAT</div>
                <div style="font-size: 9px; color: #333; margin: 3px 0 0 0; line-height: 1.3;">
                    Bacha Khan, BS Pathology (KMU)<br>
                    DMLT KPK Peshawar
                </div>
            </td>
            <!-- Right: Contact Info -->
            <td width="30%" valign="top" align="right" style="font-size: 9px; color: #333; line-height: 1.4; padding-left: 10px; border-left: 2px solid #da920d;">
                <strong style="color: #da920d;">Tel:</strong><br>
                0302-8080191<br>
                0313-9797790<br><br>
                <strong style="color: #da920d;">Address:</strong><br>
                Kabal Road, Near Township Chowk Kanju Swat
            </td>
        </tr>
    </table>

    <div style="border-top: 2px solid #da920d; margin-bottom: 12px;"></div>

    <!-- Patient Info Table -->
    <table width="100%" cellpadding="4" cellspacing="0" style="border: 1px solid #ccc; margin-bottom: 15px; font-size: 10px;">
        <tr>
            <td width="20%"><strong>Name:</strong></td>
            <td width="30%">{{ $patient->name }}</td>
            <td width="20%"><strong>Visit Date:</strong></td>
            <td width="30%">
                @php
                    $testDate = $testEntry['saved_data']['test_date'] ?? $testEntry['saved_data']['reported_at'] ?? null;
                    echo $testDate ? date('d-M-Y', strtotime($testDate)) : 'N/A';
                @endphp
            </td>
        </tr>
        <tr>
            <td><strong>Age / Gender:</strong></td>
            <td>{{ $patient->age }} yr(s) / {{ $patient->gender }}</td>
            <td><strong>Report Date:</strong></td>
            <td>
                @php
                    $reportDate = $testEntry['saved_data']['reported_at'] ?? $testEntry['saved_data']['test_date'] ?? null;
                    echo $reportDate ? date('d-M-Y', strtotime($reportDate)) : 'N/A';
                @endphp
            </td>
        </tr>
        <tr>
            <td><strong>Referred By:</strong></td>
            <td>{{ $patient->referred_by ?? 'Self' }}</td>
            <td></td>
            <td></td>
        </tr>
    </table>

    <!-- Test Title Section -->
    <div style="margin: 15px 0; padding: 5px 0; border-bottom: 2px solid #da920d; font-weight: bold; color: #da920d; font-size: 13px;">
        {{ $testEntry['name'] }}
    </div>

    <!-- Main Results Table -->
    <table width="100%" cellpadding="5" cellspacing="0" style="border-collapse: collapse; font-size: 11px; margin-bottom: 20px;">
        <thead>
            <tr style="background-color: white; border-bottom: 2px solid #da920d;">
                <th style="text-align: left; padding: 8px; font-weight: bold; width: 40%; color: #da920d;">Test Name</th>
                <th style="text-align: left; padding: 8px; font-weight: bold; width: 20%; color: #da920d;">Results</th>
                <th style="text-align: left; padding: 8px; font-weight: bold; width: 15%; color: #da920d;">Reference Ranges</th>
                <th style="text-align: left; padding: 8px; font-weight: bold; width: 25%; color: #da920d;">Unit </th>
            </tr>
        </thead>
        <tbody>
            @php
                $analytes = $testEntry['saved_data']['analytes'] ?? [];
                $hasHL7Data = !empty($analytes) && is_array($analytes);
            @endphp

            @if($hasHL7Data)
                <!-- HL7 Data -->
                @foreach($analytes as $analyte)
                    <tr>
                        <td style="padding: 8px; border: 1px solid #ddd;">{{ $analyte['name'] ?? $analyte['code'] ?? 'Unknown' }}</td>
                        <td style="padding: 8px; border: 1px solid #ddd; font-weight: 600;">{{ $analyte['value'] ?? '' }}</td>
                        <td style="padding: 8px; border: 1px solid #ddd;">{{ $analyte['ref_range'] ?? '' }}</td>
                        <td style="padding: 8px; border: 1px solid #ddd;">{{ $analyte['units'] ?? '' }}</td>
                    </tr>
                @endforeach
            @else
                
                <!-- Actual Test Parameters -->
                @foreach($testEntry['template']['fields'] as $field)
                    @php
                        $value = $testEntry['saved_data'][$field['name']] ?? '';
                        $label = $field['label'] ?? 'Unknown';

                        // Extract Unit
                        $unit = '';
                        if (preg_match('/\(([^)]+)\)/', $label, $matches)) {
                            $unit = trim($matches[1]);
                            $label = preg_replace('/\s*\([^)]+\)\s*/', '', $label);
                        }

                        // Extract Reference Range
                        $ref = '';
                        if (preg_match('/Ref:\s*([^\n]+)$/i', $label, $matches)) {
                            $ref = trim($matches[1]);
                            $label = preg_replace('/\s*[-–—]\s*Ref:\s*[^\n]+$/i', '', $label);
                        }

                        $label = trim($label);

                        // ⚠️ Format date values to d-M-Y
                        if ($value && preg_match('/^\d{4}-\d{2}-\d{2}/', $value)) {
                            try {
                                $dateTime = new DateTime($value);
                                $value = $dateTime->format('d-M-Y');
                            } catch (Exception $e) {
                                // Leave as-is if invalid
                            }
                        }
                    @endphp
                    <tr>
                        <td style="padding: 8px; border: 1px solid #ddd;">{{ $label }}</td>
                        <td style="padding: 8px; border: 1px solid #ddd; font-weight: 600;">{{ $value }}</td>
                        <td style="padding: 8px; border: 1px solid #ddd;">{{ $ref }}</td>
                        <td style="padding: 8px; border: 1px solid #ddd;">{{ $unit }}</td>
                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>

    <!-- Footer -->
    <div style="margin-top: 25px; padding-top: 10px; border-top: 1px solid #aaa; display: flex; justify-content: space-between; align-items: center; font-size: 9px; color: #333;">
        <div style="flex: 1; text-align: left;">
            <strong>Please Note:</strong><br>
            Test(s) are performed on the state-of-the-art ARCHITECT MODULAR Ci4100 from Abbott Diagnostics, U.S.A.<br>
            This is a digitally signed report and does not require manual signature.
        </div>
        <div style="text-align: right; white-space: nowrap;">
            <div style="font-weight: bold; color: #da920d; margin-bottom: 5px;">
                This is a digitally signed report by<br>
                <strong>Bacha Khan</strong>
            </div>
            <!-- <div style="display: inline-flex; gap: 8px; align-items: center; margin-top: 5px;">
                <img src="{{ asset('assets/images/neoapp.png') }}" alt="NEQAPP" style="height: 20px; opacity: 0.8;">
                <img src="{{ asset('assets/images/riqas.png') }}" alt="RIQAS" style="height: 20px; opacity: 0.8;">
                <img src="{{ asset('assets/images/pnac.png') }}" alt="PNAC" style="height: 20px; opacity: 0.8;">
                <img src="{{ asset('assets/images/synlab.png') }}" alt="SynLab" style="height: 20px; opacity: 0.8;">
                <img src="{{ asset('assets/images/iso.png') }}" alt="ISO" style="height: 20px; opacity: 0.8;">
            </div> -->
        </div>
    </div>

</div>