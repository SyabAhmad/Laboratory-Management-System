<style>
    @media print {
        :root {
            --print-header-height: 20mm; /* smaller header for thermal */
            --print-footer-height: 15mm; /* smaller footer for thermal */
        }

        .print-header,
        .print-footer {
            position: fixed;
            left: 0; /* anchor to page left edge */
            right: 0; /* anchor to page right edge, ensures full width */
            width: auto; /* allow full width to be derived from left/right */
            padding: 0; /* no extra padding, inner alignment is handled by .print-inner */
            z-index: 200000;
            display: block !important;
            box-sizing: border-box;
            max-width: none; /* ensure no max width applies */
        }
        /* Adjust header and footer fonts for thermal printing */
        .print-header .lab-name { font-size: 16px !important; }
        .print-header .lab-subtitle { font-size: 10px !important; }
        .print-header .lab-address { font-size: 9px !important; }
        .print-header .contact-info { font-size: 9px !important; }
        .print-header-details { font-size: 9px !important; }
        .print-footer .footer-item { font-size: 9px !important; }
        .print-footer .footer-signature strong { font-size: 10px !important; }
        .print-header-details { display: block !important; width: auto; padding: 0; }
        /* Ensure header table cells vertically center their content for print */
        .print-header .print-inner table td { vertical-align: middle !important; }
        /* Adjust logo size for thermal printing */
            .print-header .print-inner table td.header-logo-cell { display: none; }
            /* The floating logo is absolutely positioned, so the table doesn't need a dedicated left cell */

        .print-header .print-inner table td img { width: 20mm !important; height: 20mm !important; }
        /* Make header logo smaller for thermal */
        .print-header .print-inner table td img.header-logo {
            position: absolute !important;
            left: 2mm !important; /* anchor to cell left */
            top: 50% !important;
            transform: translateY(-50%) !important; /* only translate vertically */
            width: 16mm !important;
            height: 16mm !important;
            z-index: 999999 !important; /* very high to ensure it sits on top */
            display: block !important;
        }
            .print-header .print-inner .print-logo img.header-logo {
                width: 18mm !important;
                height: 18mm !important;
                border-radius: 50% !important;
                border: 2px solid #8d2d36 !important;
                box-shadow: 0 1px 2px rgba(0,0,0,0.1) !important;
            }
        /* Prevent the logo from overlapping center text by adding left padding to center cell */
        .print-header .print-inner table td.center-cell { padding-left: 20mm !important; }

        .print-header {
            top: 2mm; /* smaller top margin for thermal */
            height: var(--print-header-height);
        }

        .print-footer {
            bottom: 2mm; /* smaller bottom margin for thermal */
            height: var(--print-footer-height);
        }

        /* Make sure header/footer are not split */
        .print-header, .print-footer { page-break-inside: avoid; }
    }
</style>

<!-- Reusable Print Header -->
    <div class="print-header" style="display:none; padding: 0;">
        <div class="print-inner" style="width: var(--print-inner-width-mm); margin: 0 auto; padding: 0; box-sizing: border-box; position: relative;">
            <!-- Floating logo (absolutely positioned so it doesn't affect table layout) -->
            <div class="print-logo" style="position: absolute; left: 1mm; top: 1mm; z-index: 999999;">
                <img src="{{ asset('assets/images/logo.png') }}" class="header-logo" alt="Logo" style="width: 28mm; height: 28mm; border-radius: 50%; border: 3px solid #8d2d36; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            </div>
            <table width="100%" cellpadding="0" cellspacing="0" style="border-bottom: 2px solid #8d2d36; padding-bottom: 8px;">
        <tr>
            <!-- Center: Lab Name & Info -->
            <td class="center-cell" style="padding: 0 6px; text-align: center; vertical-align: middle;">
                <div class="lab-name" style="font-weight: bold; font-size: 14px; margin: 0; line-height: 1.1; color: #8d2d36;">
                    NEW MODERN CLINICAL LABORATORY
                </div>
                <div class="lab-subtitle" style="font-size: 8px; margin: 2px 0 0 0; font-weight: 600; color: #8d2d36;">
                    (KP HCC) REG: 03663 SWAT
                </div>
                <div class="lab-address" style="font-size: 7px; color: #8d2d36; margin: 2px 0 0 0; line-height: 1.2;">
                    Bacha Khan, BS Pathology (KMU)<br>
                    DMLT KPK Peshawar
                    CT Pathology Department Saidu Medical College/ SGTH Swat
                </div>
            </td>
            <!-- Right: Contact Info -->
            <td width="30%" valign="middle" align="right"
                style="font-size: 8px; color: #8d2d36; line-height: 1.3; padding-left: 4px; border-left: 2px solid #8d2d36; vertical-align: middle;"
                class="contact-info">
                <div style="font-weight: bold; color: #8d2d36; margin-bottom: 2px;">Contact Information</div>
                <!-- <strong>Tel:</strong><br> -->
                
                0302-8080191  <br>   0313-9797790
                <br>
                Email: bachakhanacl@gmail.com <br>
                <!-- <strong>Address:</strong><br> -->
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
                <div class="footer-container" style="padding: 4px; margin: 0; display: flex; gap: 6px; width: 100%; align-items: center; justify-content: space-between;">
            <div class="footer-item footer-note" style="background-color: #f8fafb; padding: 4px; border-left: 2px solid #8d2d36; font-size: 9px; flex: 1 1 62%;">
            <strong style="color: black;">Please Note:</strong><br>
            <i class="fas fa-info-circle" style="margin-right: 5px; color: black;"></i>If there is no correlation with your clinical findings then please do ask this lab to repeat the test on the same sample, as we preserve it till late evening.<br>
            <i class="fas fa-signature" style="margin-right: 5px; color: black;"></i>This report is not meant for any sort of medico legal purpose.<br>
            <i class="fas fa-signature" style="margin-right: 5px; color: black;"></i>This is a digitally signed report By <strong style=" color: black; font-size: 15px;">Bacha Khan</strong> and does not require manual signature. <br>
            <!-- This is a digitally signed report by <strong style=" color: black; font-size: 12px;">Bacha Khan</strong> -->
        </div>
        <!-- <div class="footer-item footer-signature" style="text-align:center; background: #fff; padding: 8px; font-size: 14px; display: flex; align-items: center; justify-content: center; flex: 0 0 30%;">
            <div style="font-weight: bold; color: black; margin-bottom: 5px;">
                <i class="fas fa-user-md" style="margin-right: 5px;"></i>This is a digitally signed report by
            </div>
            <strong style="font-size: 12px;">Bacha Khan</strong>
        </div> -->
        <!-- <div class="footer-item footer-contact" style="text-align: right; background: #fff; padding: 8px; border-left: 3px solid #8d2d36; padding-left: 12px;">
            <div style="margin-bottom: 5px;"> <i class="fas fa-map-marker-alt" style="margin-right: 6px; color: #8d2d36;"></i>Asad Abad Road, Near Township Chowk, Kanju Swat</div>
            <div><i class="fas fa-phone" style="margin-right: 6px; color: #8d2d36;"></i>0302-8080191  ·  0313-9797790</div>
        </div> -->
            </div>
        </div>
    </div>
