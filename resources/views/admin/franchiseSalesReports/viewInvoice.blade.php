@extends(_getConstant('dir_path.ADMIN_DIR_PATH') . '.admin_layout')
@section('admin_content')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lexend&display=swap" rel="stylesheet">
    <button onClick="printDiv('printThisDiv')" class="no-print"
        style="width: 188px; margin: auto; margin-top: 40px;
    font-family: 'Lexend', sans-serif; height: 44px;
    background: #6F44C9; border: none;
    font-size: 18px; color: #fff;
    border-radius: 8px; display: flex; align-items: center;
    justify-content: center; gap: 7px;">Print
        Invoice
    </button>
    <div class="" style="width:20cm;margin-left: auto;margin-right: auto;
margin-top: 30px; margin-bottom: 30px;"
        id="printThisDiv">
        <div class="invoice_print-bg" style="background:#fff; border-radius: 15px; padding: 22px 20px;">
            <div class="invoice_inner-box" style="border: 1px solid #EBEBEB; border-radius: 12px; padding: 26px 16px;">
                <div class="all_tables-mnf">
                    <table style="width: 100%;">
                        <caption class="d-none">&nbsp;&nbsp;</caption>
                        <thead>
                            <tr>
                                <th scope="col" colspan="3">
                                    <div class="logo_invoice"
                                        style="width: fit-content;
                                    margin: auto; padding: 19px 38px 17px 38px;
                                    margin-top: -28px; border-radius: 0px 0px 30px 30px;
                                    margin-bottom: 40px;">
                                        <img src="{{ _assetUrl('upload_path.LOGO_IMAGE_URL') . $configArr['upload_logo'] }}"
                                            alt="" style="width: 214px;">
                                    </div>
                                </th>
                            </tr>
                            <tr>
                                <th scope="col" colspan="3">
                                    <div class="h1"
                                        style="text-align: start;font-size: 42px;
                                    margin-bottom: 26px;
                                    font-family: 'Lexend', sans-serif;
                                    font-weight: 600;color:black;">
                                        Invoice</div>
                                </th>
                            </tr>
                            <tr>
                                <th scope="col" colspan="2">
                                    <div class="left_dateinv" style="margin-bottom: 30px;">
                                        <p
                                            style="text-align: start;
                                        font-weight: 400; margin-top: 7px; font-size: 14px;
                                        font-family: 'Lexend', sans-serif; color:black ">
                                            <span
                                                style="font-weight: 600;
                                            font-size: 15px; margin-right: 16px;
                                            font-family: 'Lexend', sans-serif;
                                            color:black">Invoice
                                                :</span>{{ $configArr['invoice_prefix'].$resultArr->id }}
                                        </p>
                                        <p
                                            style="text-align: start;
                                        font-weight: 400; margin-top: 7px;
                                        font-size: 14px; font-family: 'Lexend', sans-serif;
                                        color:black">
                                            <span
                                                style="font-weight: 600;
                                            font-size: 15px; margin-right: 16px;
                                            font-family: 'Lexend', sans-serif;
                                            color:black">Customer
                                                Id :</span>{{ $resultArr->member->matri_id }}
                                        </p>
                                        <p
                                            style="text-align: start;
                                        font-weight: 400; margin-top: 7px;
                                        font-size: 14px; font-family: 'Lexend', sans-serif;
                                        color:black">
                                            <span
                                                style="font-weight: 600; font-size: 15px;
                                            margin-right: 16px; font-family: 'Lexend', sans-serif;
                                            color:black">Payment
                                                Mode :</span>{{ $resultArr->payment_mode }}
                                        </p>
                                    </div>
                                </th>
                            </tr>
                            <tr>
                                <th style="width: 60%;" scope="col">
                                    <div class="left_adrrs" style="margin-bottom: 40px;">
                                        <p
                                            style="text-align: start;
                                        font-size: 18px; font-weight: 600;
                                        font-family: 'Lexend', sans-serif;
                                        color:black">
                                            From</p>
                                        <p
                                            style="text-align: start; font-weight: 400;
                                        margin-top: 7px; font-size: 14px;
                                        font-family: 'Lexend', sans-serif; color:black">
                                            {{ $configArr['web_frienly_name'] }}</p>
                                        <p
                                            style="text-align: start; font-weight: 400;
                                        margin-top: 7px; font-size: 14px;
                                        font-family: 'Lexend', sans-serif; color:black">
                                            <span
                                                style="font-weight: 500; font-size: 15px;
                                        margin-right: 16px;">Contact
                                                No
                                                :</span>
                                            @if (_getConstant('DISABLE_DEMO') == 'Enabled')
                                                {{ _getConstant('DISABLE_IN_DEMO_LABEL') }}
                                            @else
                                                {{ $configArr['contact_no'] }}
                                            @endif
                                        </p>
                                        <p
                                            style="text-align: start; font-weight: 400;
                                        margin-top: 7px; font-size: 14px;
                                        font-family: 'Lexend', sans-serif; color:black">
                                            <span
                                                style="font-weight: 500; font-size: 15px;
                                        margin-right: 16px;">Email
                                                :</span>
                                            @if (_getConstant('DISABLE_DEMO') == 'Enabled')
                                                {{ _getConstant('DISABLE_IN_DEMO_LABEL') }}
                                            @else
                                                {{ $configArr['from_email'] }}
                                            @endif
                                        </p>
                                    </div>
                                </th>
                                <th style="width: 40%;" scope="col">
                                    <div class="left_adrrs" style="margin-bottom: 40px;">
                                        <p
                                            style="text-align: start;
                                        font-size: 18px; font-weight: 600;
                                        font-family: 'Lexend', sans-serif;
                                        color:black">
                                            To</p>
                                        <p
                                            style="text-align: start; font-weight: 400;
                                        margin-top: 7px; font-size: 14px;
                                        font-family: 'Lexend', sans-serif;
                                        color:black">
                                            <span
                                                style="font-weight: 500; font-size: 15px;
                                        margin-right: 16px;">Name
                                                :</span>{{ $resultArr->member->fullname }}
                                        </p>
                                        <p
                                            style="text-align: start; font-weight: 400;
                                        margin-top: 7px; font-size: 14px; font-family: 'Lexend', sans-serif;
                                        color:black">
                                            <span
                                                style="font-weight: 500;
                                        font-size: 15px;
                                        margin-right: 16px;">Email
                                                :</span>
                                            @if (_getConstant('DISABLE_DEMO') == 'Enabled')
                                                {{ _getConstant('DISABLE_IN_DEMO_LABEL') }}
                                            @else
                                                {{ $resultArr->member->email }}
                                            @endif
                                        </p>
                                    </div>
                                </th>
                            </tr>
                        </thead>
                    </table>
                    <table style="width: 100%; border-spacing: 0; border-collapse: collapse;">
                        <caption class="d-none">&nbsp;&nbsp;</caption>
                        <thead>
                            <tr>
                                <th
                                    style="text-align: start;
                                font-weight: 400; font-size: 15px;
                                background: #7c7c7c;
                                color: #fff; font-family: 'Lexend', sans-serif;
                                padding: 10px 10px; border: solid #EBEBEB;border-width: 0px 1px 0px 0px;">
                                    QTY</th>
                                <th
                                    style="text-align: start;
                                font-weight: 400; font-size: 15px; background: #7c7c7c;
                                color: #fff; font-family: 'Lexend', sans-serif;
                                padding: 10px 10px;border: solid #EBEBEB;border-width: 0px 1px 0px 0px;">
                                    Product</th>
                                <th
                                    style="text-align: start; font-weight: 400;
                                font-size: 15px; background: #7c7c7c;
                                color: #fff; font-family: 'Lexend', sans-serif;
                                padding: 10px 10px;border: solid #EBEBEB;border-width: 0px 1px 0px 0px;">
                                    Activated On </th>
                                <th
                                    style="text-align: start; font-weight: 400;
                                font-size: 15px; background: #7c7c7c;
                                color: #fff; font-family: 'Lexend', sans-serif;
                                padding: 10px 10px;border: solid #EBEBEB;border-width: 0px 1px 0px 0px;">
                                    Expired On </th>
                                <th
                                    style="text-align: start; font-weight: 400;
                                font-size: 15px; background: #7c7c7c;
                                color: #fff; font-family: 'Lexend', sans-serif;
                                padding: 10px 10px;">
                                    Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td
                                    style="text-align: start; font-size: 14px; font-weight: 400; padding: 10px; border: solid #dddddd;border-width: 1px 1px 1px 1px; font-family: 'Lexend', sans-serif; color:black">
                                    1.</td>
                                <td
                                    style="text-align: start; font-size: 14px; font-weight: 400; padding: 10px; border: solid #dddddd;border-width: 1px 1px 1px 1px; font-family: 'Lexend', sans-serif; color:black">
                                    {{ $resultArr->plan_name }}
                                    @if (!empty($resultArr->plan_discount) && $resultArr->plan_discount > 0)
                                        <br><span style="font-size: 11px; color: #888;">(incl.
                                            {{ $resultArr->plan_discount }}% plan discount)</span>
                                    @endif
                                </td>
                                <td
                                    style="text-align: start; font-size: 14px; font-weight: 400; padding: 10px; border: solid #dddddd;border-width: 1px 1px 1px 1px; font-family: 'Lexend', sans-serif; color:black">
                                    {{ _displayDate($resultArr->plan_activate_date, 'd-m-Y') }} </td>
                                <td
                                    style="text-align: start; font-size: 14px; font-weight: 400; padding: 10px; border: solid #dddddd;border-width: 1px 1px 1px 1px; font-family: 'Lexend', sans-serif; color:black">
                                    {{ _displayDate($resultArr->plan_expiry_date, 'd-m-Y') }}</td>
                                <td
                                    style="text-align: start; font-size: 14px; font-weight: 400; padding: 10px; border: solid #dddddd;border-width: 1px 1px 1px 1px; font-family: 'Lexend', sans-serif; color:black">
                                    {{ $resultArr->currency_code }}
                                    {{ number_format((float) $resultArr->plan_amount, 2, '.', '') }}</td>
                            </tr>

                            @if ($addOnPlan->isNotEmpty())
                                @foreach ($addOnPlan as $key => $plan)
                                    <tr>
                                        <td
                                            style="text-align: start; font-size: 14px; font-weight: 400; padding: 10px; border: solid #dddddd; border-width: 1px 1px 1px 1px;; font-family: 'Lexend', sans-serif; color:black">
                                            {{ $key + 2 }}</td>
                                        <td
                                            style="text-align: start; font-size: 14px; font-weight: 400; padding: 10px; border: solid #dddddd; border-width: 1px 1px 1px 1px;; font-family: 'Lexend', sans-serif; color:black">
                                            {{ $plan->package_title }}</td>
                                        <td
                                            style="text-align: start; font-size: 14px; font-weight: 400; padding: 10px; border: solid #dddddd; border-width: 1px 1px 1px 1px;; font-family: 'Lexend', sans-serif; color:black">
                                            -</td>
                                        <td
                                            style="text-align: start; font-size: 14px; font-weight: 400; padding: 10px; border: solid #dddddd; border-width: 1px 1px 1px 1px;; font-family: 'Lexend', sans-serif; color:black">
                                            - </td>
                                        <td
                                            style="text-align: start; font-size: 14px; font-weight: 400; padding: 10px; border: solid #dddddd; border-width: 1px 1px 1px 1px;; font-family: 'Lexend', sans-serif; color:black">
                                            {{ $resultArr->currency_code }}
                                            {{ number_format((float) $plan->package_amount, 2, '.', '') }}</td>
                                    </tr>
                                @endforeach
                            @endif

                            @php
                                // Subtotal = plan amount (already net of any plan-level %discount) + all add-on amounts
                                $subtotal =
                                    (float) $resultArr->plan_amount +
                                    $addOnPlan->sum(fn($p) => (float) $p->package_amount);
                            @endphp

                            <tr>
                                <td colspan="4"
                                    style="text-align: end; font-size: 14px; font-weight: 600; padding: 10px; padding-top: 16px; font-family: 'Lexend', sans-serif; color: black; border: none;">
                                    Subtotal
                                </td>
                                <td
                                    style="text-align: start; font-size: 14px; font-weight: 600; padding: 10px; padding-top: 16px; font-family: 'Lexend', sans-serif; color: black; border: none;">
                                    {{ $resultArr->currency_code }} {{ number_format($subtotal, 2, '.', '') }}
                                </td>
                            </tr>

                            @if ($resultArr->discount_amount > 0)
                                <tr>
                                    <td colspan="4"
                                        style="text-align: end; font-size: 15px; font-weight: 600; padding: 10px; font-family: 'Lexend', sans-serif; color: black; border: none;">
                                        Coupon Discount
                                        @if (!empty($resultArr->discount_detail))
                                            <span
                                                style="font-size: 12px; font-weight: 400; color: #888;">({{ $resultArr->discount_detail }})</span>
                                        @endif
                                    </td>
                                    <td
                                        style="text-align: start; font-size: 15px; font-weight: 600; padding: 10px; font-family: 'Lexend', sans-serif; color: black; border: none;">
                                        - {{ $resultArr->currency_code }}
                                        {{ number_format((float) $resultArr->discount_amount, 2, '.', '') }}
                                    </td>
                                </tr>
                            @endif

                            @if (!blank($resultArr->tax_name) && !blank($resultArr->tax_percentage) && $resultArr->tax_percentage > 0)
                                <tr>
                                    <td colspan="4"
                                        style="text-align: end; font-size: 15px; font-weight: 600; padding: 10px; font-family: 'Lexend', sans-serif; color: black; border: none;">
                                        {{ $resultArr->tax_name }} ({{ $resultArr->tax_percentage }}%)
                                    </td>
                                    <td
                                        style="text-align: start; font-size: 15px; font-weight: 600; padding: 10px; font-family: 'Lexend', sans-serif; color: black; border: none;">
                                        {{ $resultArr->currency_code }}
                                        {{ number_format((float) $resultArr->tax_amount, 2, '.', '') }}
                                    </td>
                                </tr>
                            @endif

                            <tr>
                                <td colspan="4"
                                    style="text-align: end; font-size: 15px; font-weight: 600; padding: 10px; padding-bottom: 30px; font-family: 'Lexend', sans-serif; color: black; border: none;">
                                    Grand Total</td>
                                <td
                                    style="text-align: start; font-size: 15px; font-weight: 600; padding: 10px; padding-bottom: 30px; font-family: 'Lexend', sans-serif; color: black; border: none;">
                                    {{ $resultArr->currency_code }}
                                    {{ number_format((float) $resultArr->grand_total, 2, '.', '') }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <script>
        function printDiv(elem) {
            var content = document.getElementById(elem).innerHTML;
            var printWindow = window.open();
            printWindow.document.write('<html><head><title>Print Invoice</title>');
            printWindow.document.write('<body >');
            printWindow.document.write(content);
            printWindow.document.write('</body></html>');
            printWindow.document.close();
            printWindow.focus();
            printWindow.onload = function() {
                printWindow.print();
                printWindow.close();
            };
            return true;
        }
    </script>
@endsection
