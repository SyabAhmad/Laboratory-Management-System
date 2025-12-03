<style>
    @media print {
        :root {
            --print-header-height: 20mm;
            --print-footer-height: 15mm;
        }

        .print-header,
        .print-footer {
            position: fixed;
            left: 0;
            right: 0;
            width: auto;
            padding: 0;
            z-index: 200000;
            display: block !important;
            box-sizing: border-box;
            max-width: none;
        }

        .print-header .lab-name {
            font-size: 16px !important;
        }

        .print-header .lab-subtitle {
            font-size: 10px !important;
        }

        .print-header .lab-address {
            font-size: 9px !important;
        }

        .print-header .contact-info {
            font-size: 9px !important;
        }

        .print-header-details {
            font-size: 9px !important;
        }

        .print-footer .footer-item {
            font-size: 9px !important;
        }

        .print-footer .footer-signature strong {
            font-size: 10px !important;
        }

        .print-header-details {
            display: block !important;
            width: auto;
            padding: 0;
        }

        .print-header .print-inner table td {
            vertical-align: middle !important;
        }

        .print-header .print-inner table td.header-logo-cell {
            display: none;
        }

        .print-header .print-inner table td img {
            width: 20mm !important;
            height: 20mm !important;
        }

        .print-header .print-inner table td img.header-logo {
            position: absolute !important;
            left: 2mm !important;
            top: 50% !important;
            transform: translateY(-50%) !important;
            width: 24mm !important;
            height: 24mm !important;
            z-index: 999999 !important;
            display: block !important;
        }

        .print-header .print-inner .print-logo img.header-logo {
            width: 24mm !important;
            height: 24mm !important;
            border-radius: 50% !important;
            border: 2px solid #8d2d36 !important;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1) !important;
        }

        .print-header .print-inner table td.center-cell {
            padding-left: 20mm !important;
        }

        .print-header {
            top: 2mm;
            height: var(--print-header-height);
        }

        .print-footer {
            bottom: 2mm;
            height: var(--print-footer-height);
        }

        .print-header,
        .print-footer {
            page-break-inside: avoid;
        }

        .footer-note .note-line {
            padding-left: 27px;
            /* move second line right (like mr-2) */
            text-indent: -14px;
            /* keeps first line aligned with icon */
            display: block;
            /* ensures line behaves correctly */
        }

    }
</style>

<!-- Print Header -->
<div class="print-header" style="display: none; padding: 0;">
    <div class="print-inner"
        style="
            width: var(--print-inner-width-mm);
            margin: 0 auto;
            padding: 0;
            box-sizing: border-box;
            position: relative;
        ">
        <!-- Floating Logo -->
        <div class="print-logo" style="position: absolute; left: 1mm; top: 1mm; z-index: 999999;">
            <img src="{{ asset('assets/images/logo.png') }}" class="header-logo" alt="Logo"
                style="
                    width: 28mm;
                    height: 28mm;
                    border-radius: 50%;
                    border: 3px solid #8d2d36;
                    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
                " />
        </div>

        <table width="100%" cellpadding="0" cellspacing="0"
            style="border-bottom: 2px solid #8d2d36; padding-bottom: 8px;">
            <tr>
                <td class="center-cell" style="padding: 0 6px; text-align: center; vertical-align: middle;">
                    <div class="lab-name"
                        style="
                            font-weight: bold;
                            font-size: 14px;
                            margin: 0;
                            line-height: 1.1;
                            color: #8d2d36;
                        ">
                        NEW MODERN CLINICAL LABORATORY
                    </div>

                    <div class="lab-subtitle"
                        style="
                            font-size: 8px;
                            margin: 2px 0 0 0;
                            font-weight: 600;
                            color: #8d2d36;
                        ">
                        (KP HCC) REG: 03663 SWAT
                    </div>

                    <div class="lab-address"
                        style="
                            font-size: 7px;
                            color: #8d2d36;
                            margin: 2px 0 0 0;
                            line-height: 1.2;
                        ">
                        Bacha Khan, BS Pathology (KMU)<br />
                        DMLT KPK Peshawar CT Pathology Department Saidu Medical
                        College/ SGTH Swat
                    </div>
                </td>

                <td width="30%" valign="middle" align="right" class="contact-info"
                    style="
                        font-size: 8px;
                        color: #8d2d36;
                        line-height: 1.3;
                        padding-left: 4px;
                        border-left: 2px solid #8d2d36;
                        vertical-align: middle;
                    ">
                    <div style="font-weight: bold; color: #8d2d36; margin-bottom: 2px;">
                        Contact Information
                    </div>

                    0302-8080191 <br />
                    0313-9797790 <br />
                    Email: bachakhanacl@gmail.com <br />
                    Kabal Road, Near Township Chowk<br />
                    Kanju Swat
                </td>
            </tr>
        </table>
    </div>
</div>

<!-- Print Header Details -->
<div class="print-header-details"
    style="display: none; width: var(--print-inner-width-mm); margin: 0 auto; padding: 0;">
    <table width="100%" cellpadding="0" cellspacing="0" style="font-size: 11px;">
        <tr>
            <td align="left" style="padding: 6px 0; color: #444; display: none;">
                {{-- patient info intentionally removed --}}
            </td>
            <td align="right" style="padding: 6px 0; color: #444;">
                <!-- optional test info removed -->
            </td>
        </tr>
    </table>
</div>

<!-- Print Footer -->
<div class="print-footer" style="display: none; padding: 0;">
    <div class="print-inner"
        style="
            width: var(--print-inner-width-mm);
            margin: 0 auto;
            padding: 0;
            box-sizing: border-box;
        ">
        <div class="footer-container"
            style="
                padding: 4px;
                margin: 0;
                display: flex;
                gap: 6px;
                width: 100%;
                align-items: center;
                justify-content: space-between;
            ">
            <div class="footer-item footer-note"
                style="
                    background-color: #f8fafb;
                    padding: 4px;
                    border-left: 2px solid #8d2d36;
                    font-size: 9px;
                    flex: 1 1 62%;
                ">
                <strong style="color: black;">Please Note:</strong><br />

                <div class="note-line">
                    <i class="fas fa-info-circle" style="margin-right: 5px; color: black;"></i>
                    If there is no correlation with your clinical findings then please do ask this lab
                    to repeat the test on the same sample, as we preserve it till late evening.
                </div>

                <i class="fas fa-signature" style="margin-right: 5px; color: black;"></i>
                This report is not meant for any medico-legal purpose.<br />

                <i class="fas fa-signature" style="margin-right: 5px; color: black;"></i>
                This is a digitally signed report by
                <strong style="color: black; font-size: 15px;">Bacha Khan</strong>
                and does not require manual signature.
            </div>
        </div>
    </div>
</div>
