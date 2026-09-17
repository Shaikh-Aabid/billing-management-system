<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>E-Way Bill - {{ $ewayBill->eway_bill_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 11px;
            line-height: 1.3;
            color: #000;
        }
        .container {
            padding: 10px;
            border: 2px solid #000;
        }
        .title {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            padding: 8px 0;
            border-bottom: 1px solid #000;
        }
        .header-section {
            display: table;
            width: 100%;
            border-bottom: 1px solid #000;
        }
        .company-info {
            display: table-cell;
            width: 50%;
            padding: 8px;
            vertical-align: top;
            border-right: 1px solid #000;
        }
        .invoice-details {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }
        .company-name {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 3px;
        }
        .company-info p {
            margin-bottom: 2px;
        }
        .buyer-section {
            margin-top: 8px;
            padding-top: 8px;
            border-top: 1px solid #000;
        }
        .buyer-label {
            font-weight: normal;
            margin-bottom: 3px;
        }
        .buyer-name {
            font-weight: bold;
        }
        .invoice-grid {
            width: 100%;
            border-collapse: collapse;
        }
        .invoice-grid td {
            padding: 4px 6px;
            border-bottom: 1px solid #000;
            vertical-align: top;
        }
        .invoice-grid .label {
            width: 45%;
            border-right: 1px solid #000;
        }
        .invoice-grid .value {
            width: 55%;
            font-weight: bold;
        }
        .invoice-grid tr:last-child td {
            border-bottom: none;
        }
        .eway-highlight {
            background: #e8f5e9;
            font-size: 12px;
        }

        /* Items Table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 0;
        }
        .items-table th {
            background: #f5f5f5;
            padding: 6px 4px;
            text-align: center;
            border: 1px solid #000;
            font-size: 10px;
            font-weight: bold;
        }
        .items-table td {
            padding: 5px 4px;
            border: 1px solid #000;
            vertical-align: top;
        }
        .items-table .sl { width: 5%; text-align: center; }
        .items-table .desc { width: 30%; }
        .items-table .hsn { width: 10%; text-align: center; }
        .items-table .qty { width: 12%; text-align: right; }
        .items-table .rate { width: 10%; text-align: right; }
        .items-table .per { width: 8%; text-align: center; }
        .items-table .disc { width: 8%; text-align: center; }
        .items-table .amount { width: 17%; text-align: right; }

        .items-table .gst-row td {
            border-top: none;
            padding-top: 2px;
        }
        .items-table .gst-row .gst-label {
            font-style: italic;
            font-weight: bold;
        }

        .items-table .empty-row td {
            height: 25px;
            border-top: none;
        }

        .total-row td {
            font-weight: bold;
            background: #f9f9f9;
        }

        /* Amount Section */
        .amount-section {
            display: table;
            width: 100%;
            border-top: 1px solid #000;
        }
        .amount-words {
            display: table-cell;
            width: 70%;
            padding: 6px;
            border-right: 1px solid #000;
            vertical-align: top;
        }
        .amount-total {
            display: table-cell;
            width: 30%;
            padding: 6px;
            text-align: right;
            vertical-align: top;
        }
        .eoe {
            font-style: italic;
            font-size: 10px;
        }

        /* HSN Summary Table */
        .hsn-summary {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }
        .hsn-summary th, .hsn-summary td {
            padding: 5px;
            border: 1px solid #000;
            text-align: center;
            font-size: 10px;
        }
        .hsn-summary th {
            background: #f5f5f5;
            font-weight: bold;
        }
        .hsn-summary .left {
            text-align: left;
        }
        .hsn-summary .right {
            text-align: right;
        }

        /* Tax Amount in Words */
        .tax-words {
            padding: 6px;
            border: 1px solid #000;
            border-top: none;
        }

        /* Footer Section */
        .footer-section {
            display: table;
            width: 100%;
            border: 1px solid #000;
            border-top: none;
        }
        .declaration {
            display: table-cell;
            width: 60%;
            padding: 8px;
            border-right: 1px solid #000;
            vertical-align: top;
            font-size: 10px;
        }
        .declaration-title {
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 3px;
        }
        .signature-section {
            display: table-cell;
            width: 40%;
            padding: 8px;
            text-align: right;
            vertical-align: top;
        }
        .signature-company {
            font-weight: bold;
            margin-bottom: 40px;
        }
        .signature-line {
            border-top: 1px solid #000;
            padding-top: 3px;
            display: inline-block;
        }

        .computer-generated {
            text-align: center;
            padding: 8px;
            font-size: 10px;
            border: 1px solid #000;
            border-top: none;
        }

        /* E-Way Bill specific */
        .eway-validity {
            background: #fff3e0;
            padding: 8px;
            margin: 8px 0;
            border: 1px solid #ff9800;
            text-align: center;
        }
        .eway-validity .valid-label {
            font-size: 10px;
            color: #666;
        }
        .eway-validity .valid-date {
            font-size: 14px;
            font-weight: bold;
            color: #e65100;
        }
        .status-active {
            color: #2e7d32;
            font-weight: bold;
        }
        .status-expired {
            color: #c62828;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Title -->
        <div class="title">Tax Invoice / E-Way Bill</div>

        <!-- Header Section -->
        <div class="header-section">
            <!-- Left: Company & Buyer Info -->
            <div class="company-info">
                @if($user->getSetting('business_logo') && file_exists(storage_path('app/public/' . $user->getSetting('business_logo'))))
                <div style="margin-bottom: 10px;">
                    <img src="{{ storage_path('app/public/' . $user->getSetting('business_logo')) }}" alt="Logo" style="max-height: 60px; max-width: 200px;">
                </div>
                @endif
                <div class="company-name">{{ $user->business_name ?? $user->name }}</div>
                @if($user->address)
                <p>{{ $user->address }}</p>
                @endif
                @if($user->city || $user->state || $user->pincode)
                <p>{{ implode(', ', array_filter([$user->city, $user->state])) }}{{ $user->pincode ? ' - '.$user->pincode : '' }}</p>
                @endif
                @if($user->gstin)
                <p>GSTIN/UIN: {{ $user->gstin }}</p>
                @endif
                @if($user->state)
                <p>State Name : {{ $user->state }}{{ $user->state_code ? ', Code : '.$user->state_code : '' }}</p>
                @endif

                <!-- Buyer Section -->
                <div class="buyer-section">
                    <p class="buyer-label">Buyer (Bill to)</p>
                    <p class="buyer-name">{{ $bill->party->name }}</p>
                    @if($bill->party->address)
                    <p>{{ $bill->party->address }}</p>
                    @endif
                    @if($bill->party->city || $bill->party->state || $bill->party->pincode)
                    <p>{{ implode(', ', array_filter([$bill->party->city, $bill->party->state])) }}{{ $bill->party->pincode ? ' - '.$bill->party->pincode : '' }}</p>
                    @endif
                    @if($bill->party->gstin)
                    <p>GSTIN/UIN&nbsp;&nbsp;&nbsp;: {{ $bill->party->gstin }}</p>
                    @endif
                    @if($bill->party->state)
                    <p>State Name&nbsp;&nbsp;: {{ $bill->party->state }}{{ $bill->party->state_code ? ', Code : '.$bill->party->state_code : '' }}</p>
                    @endif
                </div>
            </div>

            <!-- Right: Invoice & E-Way Bill Details Grid -->
            <div class="invoice-details">
                <table class="invoice-grid">
                    <tr class="eway-highlight">
                        <td class="label">e-Way Bill No.</td>
                        <td class="value" colspan="3">{{ $ewayBill->eway_bill_number }}</td>
                    </tr>
                    <tr class="eway-highlight">
                        <td class="label">Status</td>
                        <td class="value {{ $ewayBill->isActive() ? 'status-active' : 'status-expired' }}" colspan="3">
                            {{ $ewayBill->isActive() ? 'ACTIVE' : 'EXPIRED' }}
                        </td>
                    </tr>
                    <tr>
                        <td class="label">Invoice No.</td>
                        <td class="value" style="width: 25%; border-right: 1px solid #000;">{{ $bill->bill_number }}</td>
                        <td class="label" style="width: 15%;">Dated</td>
                        <td class="value">{{ $bill->bill_date->format('d-M-y') }}</td>
                    </tr>
                    <tr>
                        <td class="label">Transport Mode</td>
                        <td class="value" style="border-right: 1px solid #000;">{{ $ewayBill->transport_mode }}</td>
                        <td class="label">Vehicle No.</td>
                        <td class="value">{{ $ewayBill->vehicle_number }}</td>
                    </tr>
                    <tr>
                        <td class="label">Distance (KM)</td>
                        <td class="value" style="border-right: 1px solid #000;">{{ $ewayBill->distance }}</td>
                        <td class="label">Vehicle Type</td>
                        <td class="value">{{ $ewayBill->vehicle_type ?? 'Regular' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Transporter Name</td>
                        <td class="value" style="border-right: 1px solid #000;">{{ $ewayBill->transporter_name ?? '' }}</td>
                        <td class="label">Transporter ID</td>
                        <td class="value">{{ $ewayBill->transporter_id ?? '' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Generated At</td>
                        <td class="value" style="border-right: 1px solid #000;">{{ $ewayBill->generated_at->format('d-M-y H:i') }}</td>
                        <td class="label">Valid Until</td>
                        <td class="value" style="color: #e65100;">{{ $ewayBill->valid_until->format('d-M-y H:i') }}</td>
                    </tr>
                    <tr>
                        <td class="label" colspan="2" style="border-right: 1px solid #000;">Supply Type: {{ $bill->is_inter_state ? 'Inter-State' : 'Intra-State' }}</td>
                        <td class="label" colspan="2">Place of Supply: {{ $bill->party->state ?? 'N/A' }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- From/To Address Section -->
        <div style="display: table; width: 100%; border-bottom: 1px solid #000;">
            <div style="display: table-cell; width: 50%; padding: 8px; border-right: 1px solid #000;">
                <strong>Dispatch From:</strong><br>
                {{ $ewayBill->from_address }}
                @if($ewayBill->from_pincode)
                <br>Pincode: {{ $ewayBill->from_pincode }}
                @endif
            </div>
            <div style="display: table-cell; width: 50%; padding: 8px;">
                <strong>Ship To:</strong><br>
                {{ $ewayBill->to_address }}
                @if($ewayBill->to_pincode)
                <br>Pincode: {{ $ewayBill->to_pincode }}
                @endif
            </div>
        </div>

        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th class="sl">SI<br>No.</th>
                    <th class="desc">Description of Goods</th>
                    <th class="hsn">HSN/SAC</th>
                    <th class="qty">Quantity</th>
                    <th class="rate">Rate</th>
                    <th class="per">per</th>
                    <th class="disc">Disc. %</th>
                    <th class="amount">Amount</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $totalQty = 0;
                    $hsnSummary = [];
                @endphp
                @foreach($bill->items as $index => $item)
                @php
                    $totalQty += $item->quantity;
                    $itemSubtotal = $item->quantity * $item->price;
                    $itemGst = $item->cgst + $item->sgst + $item->igst;

                    // Build HSN summary
                    $hsnKey = $item->hsn_code;
                    if (!isset($hsnSummary[$hsnKey])) {
                        $hsnSummary[$hsnKey] = [
                            'hsn' => $item->hsn_code,
                            'taxable' => 0,
                            'cgst_rate' => $bill->is_inter_state ? 0 : $item->gst_rate / 2,
                            'cgst_amt' => 0,
                            'sgst_rate' => $bill->is_inter_state ? 0 : $item->gst_rate / 2,
                            'sgst_amt' => 0,
                            'igst_rate' => $bill->is_inter_state ? $item->gst_rate : 0,
                            'igst_amt' => 0,
                            'total' => 0,
                        ];
                    }
                    $hsnSummary[$hsnKey]['taxable'] += $itemSubtotal;
                    $hsnSummary[$hsnKey]['cgst_amt'] += $item->cgst;
                    $hsnSummary[$hsnKey]['sgst_amt'] += $item->sgst;
                    $hsnSummary[$hsnKey]['igst_amt'] += $item->igst;
                    $hsnSummary[$hsnKey]['total'] += $itemGst;
                @endphp
                <tr>
                    <td class="sl">{{ $index + 1 }}</td>
                    <td class="desc"><strong>{{ $item->product->name }}</strong></td>
                    <td class="hsn">{{ $item->hsn_code }}</td>
                    <td class="qty">{{ number_format($item->quantity, 2) }} {{ $item->unit }}</td>
                    <td class="rate">{{ number_format($item->price, 2) }}</td>
                    <td class="per">{{ $item->unit }}</td>
                    <td class="disc"></td>
                    <td class="amount">{{ number_format($itemSubtotal, 2) }}</td>
                </tr>
                <tr class="gst-row">
                    <td class="sl"></td>
                    <td class="desc gst-label">
                        @if($bill->is_inter_state)
                            IGST@{{ number_format($item->gst_rate, 0) }}%
                        @else
                            CGST@{{ number_format($item->gst_rate / 2, 1) }}%<br>
                            SGST@{{ number_format($item->gst_rate / 2, 1) }}%
                        @endif
                    </td>
                    <td class="hsn"></td>
                    <td class="qty"></td>
                    <td class="rate"></td>
                    <td class="per"></td>
                    <td class="disc">
                        @if($bill->is_inter_state)
                            {{ number_format($item->gst_rate, 0) }} %
                        @else
                            {{ number_format($item->gst_rate / 2, 1) }} %<br>
                            {{ number_format($item->gst_rate / 2, 1) }} %
                        @endif
                    </td>
                    <td class="amount">
                        @if($bill->is_inter_state)
                            {{ number_format($item->igst, 2) }}
                        @else
                            {{ number_format($item->cgst, 2) }}<br>
                            {{ number_format($item->sgst, 2) }}
                        @endif
                    </td>
                </tr>
                @endforeach

                <!-- Empty rows for spacing -->
                @for($i = 0; $i < max(0, 3 - count($bill->items)); $i++)
                <tr class="empty-row">
                    <td class="sl"></td>
                    <td class="desc"></td>
                    <td class="hsn"></td>
                    <td class="qty"></td>
                    <td class="rate"></td>
                    <td class="per"></td>
                    <td class="disc"></td>
                    <td class="amount"></td>
                </tr>
                @endfor

                <!-- Total Row -->
                <tr class="total-row">
                    <td class="sl"></td>
                    <td class="desc" style="text-align: right;">Total</td>
                    <td class="hsn"></td>
                    <td class="qty">{{ number_format($totalQty, 2) }} {{ $bill->items->first()->unit ?? '' }}</td>
                    <td class="rate"></td>
                    <td class="per"></td>
                    <td class="disc"></td>
                    <td class="amount">&#8377; {{ number_format($bill->total_amount, 2) }}</td>
                </tr>
            </tbody>
        </table>

        <!-- Amount in Words Section -->
        <div class="amount-section">
            <div class="amount-words">
                <strong>Amount Chargeable (in words)</strong><br>
                @php
                    $amount = floor($bill->total_amount);
                    $words = '';
                    if (class_exists('NumberFormatter')) {
                        try {
                            $formatter = new \NumberFormatter('en_IN', \NumberFormatter::SPELLOUT);
                            $words = ucwords($formatter->format($amount));
                        } catch (\Exception $e) {
                            $words = number_format($amount, 0);
                        }
                    } else {
                        $words = number_format($amount, 0);
                    }
                @endphp
                <strong>INR {{ $words }} Only</strong>
            </div>
            <div class="amount-total">
                <span class="eoe">E. & O.E</span>
            </div>
        </div>

        <!-- HSN/SAC Summary Table -->
        <table class="hsn-summary">
            <thead>
                <tr>
                    <th rowspan="2" style="width: 15%;">HSN/SAC</th>
                    <th rowspan="2" style="width: 20%;">Taxable<br>Value</th>
                    @if($bill->is_inter_state)
                    <th colspan="2" style="width: 25%;">IGST</th>
                    @else
                    <th colspan="2" style="width: 20%;">Central Tax</th>
                    <th colspan="2" style="width: 20%;">State Tax</th>
                    @endif
                    <th rowspan="2" style="width: 15%;">Total<br>Tax Amount</th>
                </tr>
                <tr>
                    @if($bill->is_inter_state)
                    <th>Rate</th>
                    <th>Amount</th>
                    @else
                    <th>Rate</th>
                    <th>Amount</th>
                    <th>Rate</th>
                    <th>Amount</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @foreach($hsnSummary as $hsn)
                <tr>
                    <td class="left">{{ $hsn['hsn'] }}</td>
                    <td class="right">{{ number_format($hsn['taxable'], 2) }}</td>
                    @if($bill->is_inter_state)
                    <td>{{ number_format($hsn['igst_rate'], 0) }}%</td>
                    <td class="right">{{ number_format($hsn['igst_amt'], 2) }}</td>
                    @else
                    <td>{{ number_format($hsn['cgst_rate'], 1) }}%</td>
                    <td class="right">{{ number_format($hsn['cgst_amt'], 2) }}</td>
                    <td>{{ number_format($hsn['sgst_rate'], 1) }}%</td>
                    <td class="right">{{ number_format($hsn['sgst_amt'], 2) }}</td>
                    @endif
                    <td class="right">{{ number_format($hsn['total'], 2) }}</td>
                </tr>
                @endforeach
                <tr style="font-weight: bold;">
                    <td class="right">Total</td>
                    <td class="right">{{ number_format($bill->subtotal, 2) }}</td>
                    @if($bill->is_inter_state)
                    <td></td>
                    <td class="right">{{ number_format($bill->igst, 2) }}</td>
                    @else
                    <td></td>
                    <td class="right">{{ number_format($bill->cgst, 2) }}</td>
                    <td></td>
                    <td class="right">{{ number_format($bill->sgst, 2) }}</td>
                    @endif
                    <td class="right">{{ number_format($bill->gst_amount, 2) }}</td>
                </tr>
            </tbody>
        </table>

        <!-- Tax Amount in Words -->
        <div class="tax-words">
            <strong>Tax Amount (in words) :</strong>
            @php
                $taxAmount = floor($bill->gst_amount);
                $taxWords = '';
                if (class_exists('NumberFormatter')) {
                    try {
                        $formatter = new \NumberFormatter('en_IN', \NumberFormatter::SPELLOUT);
                        $taxWords = ucwords($formatter->format($taxAmount));
                    } catch (\Exception $e) {
                        $taxWords = number_format($taxAmount, 0);
                    }
                } else {
                    $taxWords = number_format($taxAmount, 0);
                }
            @endphp
            <strong>INR {{ $taxWords }} Only</strong>
        </div>

        <!-- Footer Section -->
        <div class="footer-section">
            <div class="declaration">
                <p class="declaration-title">Declaration</p>
                <p>We declare that this invoice shows the actual price of the goods described and that all particulars are true and correct.</p>
                <p style="margin-top: 8px;"><strong>E-Way Bill Details:</strong><br>
                E-Way Bill No: {{ $ewayBill->eway_bill_number }}<br>
                Valid Until: {{ $ewayBill->valid_until->format('d-M-Y H:i') }}</p>
            </div>
            <div class="signature-section">
                <p class="signature-company">for {{ $user->business_name ?? $user->name }}</p>
                @if($user->getSetting('business_signature') && file_exists(storage_path('app/public/' . $user->getSetting('business_signature'))))
                <div style="margin: 10px 0;">
                    <img src="{{ storage_path('app/public/' . $user->getSetting('business_signature')) }}" alt="Signature" style="max-height: 50px; max-width: 150px;">
                </div>
                @else
                <div style="height: 50px;"></div>
                @endif
                <p class="signature-line">Authorised Signatory</p>
            </div>
        </div>

        <!-- Computer Generated Notice -->
        <div class="computer-generated">
            This is a Computer Generated Invoice with E-Way Bill
        </div>
    </div>
</body>
</html>
