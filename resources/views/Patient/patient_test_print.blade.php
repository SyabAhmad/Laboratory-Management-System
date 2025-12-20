
@extends('Layout.print')
@section('title', isset($testEntry['name']) ? $testEntry['name'] . ' - Test Report' : 'Test Report')
@section('content')
    <!-- Fixed Footer -->
    <div class="fixed-footer" style="position: fixed; bottom: 0; left: 20mm; right: 20mm; width: auto; z-index: 1000;">
        <div class="footer-container"
            style="
            padding: 4px;
            margin: 0;
            display: flex;
            gap: 6px;
            width: 100%;
            align-items: center;
            justify-content: space-between;
            background-color: #f8fafb;
            border: 1px solid #e0e0e0;
            border-radius: 6px;
        ">
            <div class="footer-item footer-note"
                style="
                background-color: #f8fafb;
                padding: 16px;
                border-left: 4px solid #8d2d36;
                font-size: 12px;
                width: 100%;
            ">
                <strong style="color: black; font-size: 18px;">Please Note:</strong><br />

                <div class="note-line"
                    style="padding-left: 20px; text-indent: -14px; display: block; margin-top: 4px; font-size: 16px;">
                    <i class="fas fa-info-circle" style="margin-right: 5px; color: black;"></i>
                    If there is no correlation with your clinical findings then please do ask this lab
                    to repeat the test on the same sample, as we preserve it till late evening.
                </div>

                <div style="margin-top: 4px; font-size: 16px">
                    <i class="fas fa-signature" style="margin-right: 5px; color: black;"></i>
                    This report is not meant for any medico-legal purpose.
                </div>

                <div style="margin-top: 4px;font-size: 16px">
                    <i class="fas fa-signature" style="margin-right: 5px; color: black;"></i>
                    This is a digitally signed report by
                    <strong style="color: black; font-size: 16px;">Bacha Khan</strong>
                    and does not require manual signature.
                </div>
            </div>
        </div>
    </div>

    <table class="report-table">
        <thead>
            <tr>
                <td>
                    <!-- Header Content -->
                    <div class="print-header-content" style="width: 100%; padding-bottom: 20px;">
                        <table width="100%" cellpadding="0" cellspacing="0"
                            style="border-bottom: 6px solid #8d2d36; padding-bottom: 8px; margin-top: 5px;">
                            <tr>
                                <td width="15%" valign="middle" align="left" class="logo-cell"
                                    style="padding: 0 6px 20px 6px; vertical-align: middle;">
                                    <img src="{{ asset('assets/images/logo.png') }}" class="header-logo" alt="Logo"
                                        style="width: 40mm; height: 40mm; border-radius: 50%; border: 3px solid #8d2d36; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);" />
                                </td>
                                <td class="center-cell" style="padding: 0 6px; text-align: left; vertical-align: middle;">
                                    <div class="lab-name"
                                        style="text-align: left; font-weight: bold; margin: 0; line-height: 1.1; color: #8d2d36; font-size: 24px; white-space: nowrap;">
                                        NEW MODERN CLINICAL LABORATORY
                                        <div class="lab-subtitle"
                                            style="text-align: left; margin: 2px 0 0 0; font-weight: 600; color: #8d2d36; font-size: 20px;">
                                            (KP HCC) REG: 03663 SWAT
                                        </div>
                                        <div class="lab-contact" style="font-size: 14px; padding-top: 4px;">
                                            <p>
                                                📞 0302-8080191 📞 0313-9797790 ✉ bachakhanacl@gmail.com <br> 📍 Kabal Road, Near
                                                Township Chowk, Kanju Swat
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td width="30%">
                                    <div class="lab-address"
                                        style="text-align: left; color: #8d2d36; margin: 2px 0 0 0; line-height: 1.2; font-size: 14px; padding-top: 10px;">
                                        <strong style="font-size: 18px">Bacha Khan</strong> <br />
                                        BS Pathology (KMU)<br>
                                        DMLT KPK Peshawar<br>
                                        CT Pathology Department<br>
                                        Saidu Medical College/ SGTH Swat
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </div>
                </td>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <div class="print-body">
                        @include('Patient.partials.test_report', [ 'patient' => $patient, 'testEntry' => $testEntry ])
                    </div>
                </td>
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <td style="height: 50mm;">
                    <!-- Spacer for Fixed Footer -->
                    &nbsp;
                </td>
            </tr>
        </tfoot>
    </table>
    <script>
        window.onload = function () {
            window.print();
        };
    </script>
@endsection
