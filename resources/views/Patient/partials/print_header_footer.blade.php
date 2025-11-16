<style>
    @media print {
        :root {
            --print-header-height: 36mm; /* header height in print mode */
            --print-footer-height: 32mm;
        }

        .print-header,
        .print-footer {
            position: fixed;
            left: 0; /* anchor to page left edge */
            right: 0; /* anchor to page right edge, ensures full width */
            width: auto; /* allow full width to be derived from left/right */
            padding: 0; /* no extra padding, inner alignment is handled by .print-inner */
            z-index: 9999;
            display: block !important;
            box-sizing: border-box;
            max-width: none; /* ensure no max width applies */
        }
        .print-header-details { display: block !important; width: auto; padding: 0; }

        .print-header {
            top: 6mm; /* align with page margins */
            height: var(--print-header-height);
        }

        .print-footer {
            bottom: 10mm; /* align with page margins */
            height: var(--print-footer-height);
        }

        /* Make sure header/footer are not split */
        .print-header, .print-footer { page-break-inside: avoid; }
    }
</style>

<!-- Reusable Print Header -->
    <div class="print-header" style="display:none; padding: 0;">
        <div class="print-inner" style="width: var(--print-inner-width-mm); margin: 0 auto; padding: 0; box-sizing: border-box;">
            <table width="100%" cellpadding="0" cellspacing="0" style="border-bottom: 2px solid #8d2d36; padding-bottom: 15px;">
        <tr>
            <!-- Left: Logo -->
            <td width="15%" valign="top" align="center" style="padding-right: 6px;">
                <img src="{{ asset('assets/images/logo.png') }}" alt="Logo"
                    style="width: 80px; height: 80px; border-radius: 50%; border: 3px solid #8d2d36; display: block; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            </td>
            <!-- Center: Lab Name & Info -->
            <td style="padding: 0 6px; text-align: center;">
                <div style="font-weight: bold; font-size: 20px; margin: 0; line-height: 1.1; color: #8d2d36;">
                    NEW MODERN CLINICAL LABORATORY
                </div>
                <div style="font-size: 11px; margin: 5px 0 0 0; font-weight: 600; color: #8d2d36;">
                    (KP HCC) REG: 03663 SWAT
                </div>
                <div style="font-size: 10px; color: #8d2d36; margin: 5px 0 0 0; line-height: 1.3;">
                    Bacha Khan, BS Pathology (KMU)<br>
                    DMLT KPK Peshawar
                </div>
            </td>
            <!-- Right: Contact Info -->
            <td width="30%" valign="top" align="right"
                style="font-size: 10px; color: #8d2d36; line-height: 1.4; padding-left: 6px; border-left: 2px solid #8d2d36;">
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
    </div>
<!-- Optional compact patient/test detail line (print only) -->
<div class="print-header-details" style="display:none; width: var(--print-inner-width-mm); margin: 0 auto; padding: 0;">
    <table width="100%" cellpadding="0" cellspacing="0" style="font-size: 11px;">
        <tr>
            <!-- Removed patient summary from the compact print header to avoid duplication with the main header -->
            <td align="left" style="padding: 6px 0; color: #444; display:none;">
                {{-- patient info intentionally removed for print header-details --}}
            </td>
            <td align="right" style="padding: 6px 0; color: #444;">
                <!-- @if(isset($testEntry))
                    <strong>Test:</strong> {{ $testEntry['name'] ?? '-' }}
                    @php $td = $testEntry['saved_data']['test_date'] ?? null; @endphp
                    @if($td) &nbsp;&middot;&nbsp; <strong>Date:</strong> {{ (is_string($td) ? (new \DateTime($td))->format('d-M-Y') : (method_exists($td,'format') ? $td->format('d-M-Y') : $td)) }} @endif
                @endif -->
            </td>
        </tr>
    </table>
</div>

<!-- Reusable Print Footer -->
    <div class="print-footer" style="display:none; padding: 0;">
        <div class="print-inner" style="width: var(--print-inner-width-mm); margin: 0 auto; padding: 0; box-sizing: border-box;">
            <div class="footer-container" style="padding: 8px; margin: 0; display: grid; grid-template-columns: 57% 21.5% 21.5%; gap: 8px; width: 100%; align-items: start; justify-items: stretch;">
        <div class="footer-item footer-note" style="background-color: #f8fafb; padding: 8px; border-left: 3px solid #8d2d36;">
            <strong style="color: black;">Please Note:</strong><br>
            <i class="fas fa-info-circle" style="margin-right: 5px; color: black;"></i>Test(s) are performed on the state-of-the-art ARCHITECT MODULAR Ci4100 from Abbott Diagnostics, U.S.A.<br>
            <i class="fas fa-signature" style="margin-right: 5px; color: black;"></i>This is a digitally signed report and does not require manual signature.
        </div>
        <div class="footer-item footer-signature" style="text-align:center; background: #fff; padding: 8px;">
            <div style="font-weight: bold; color: black; margin-bottom: 5px;">
                <i class="fas fa-user-md" style="margin-right: 5px;"></i>This is a digitally signed report by
            </div>
            <strong style="font-size: 11px;">Bacha Khan</strong>
        </div>
        <div class="footer-item footer-contact" style="text-align: right; background: #fff; padding: 8px; border-left: 3px solid #8d2d36;">
            <div style="margin-bottom: 5px;"> <i class="fas fa-map-marker-alt" style="margin-right: 6px; color: #8d2d36;"></i>Asad Abad Road, Near Township Chowk, Kanju Swat</div>
            <div><i class="fas fa-phone" style="margin-right: 6px; color: #8d2d36;"></i>0302-8080191  ·  0313-9797790</div>
        </div>
            </div>
        </div>
    </div>