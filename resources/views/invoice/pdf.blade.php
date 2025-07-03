<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $invoice_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 14px;
            line-height: 1.6;
            color: #333;
            padding: 40px;
        }
        
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 40px;
            border-bottom: 3px solid #2563eb;
            padding-bottom: 20px;
        }
        
        .invoice-title {
            font-size: 48px;
            font-weight: bold;
            color: #2563eb;
            margin-bottom: 5px;
        }
        
        .invoice-number {
            font-size: 16px;
            color: #666;
        }
        
        .invoice-dates {
            text-align: right;
            font-size: 14px;
            color: #666;
        }
        
        .invoice-dates div {
            margin-bottom: 5px;
        }
        
        .company-details {
            display: flex;
            justify-content: space-between;
            margin-bottom: 40px;
        }
        
        .from-section, .to-section {
            width: 48%;
        }
        
        .section-title {
            font-size: 16px;
            font-weight: bold;
            color: #333;
            margin-bottom: 10px;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 5px;
        }
        
        .company-name {
            font-weight: bold;
            font-size: 16px;
            margin-bottom: 5px;
        }
        
        .company-info {
            color: #666;
            font-size: 14px;
        }
        
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        
        .items-table th {
            background-color: #eff6ff;
            color: #1e3a8a;
            padding: 15px 10px;
            text-align: left;
            font-weight: bold;
            border-bottom: 2px solid #2563eb;
        }
        
        .items-table th:nth-child(2),
        .items-table th:nth-child(3),
        .items-table th:nth-child(4) {
            text-align: right;
        }
        
        .items-table td {
            padding: 12px 10px;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .items-table td:nth-child(2),
        .items-table td:nth-child(3),
        .items-table td:nth-child(4) {
            text-align: right;
        }
        
        .items-table tr:nth-child(even) {
            background-color: #f9fafb;
        }
        
        .totals-section {
            margin-left: auto;
            width: 300px;
            margin-bottom: 30px;
        }
        
        .total-line {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .total-line.final {
            border-top: 2px solid #2563eb;
            border-bottom: 3px double #2563eb;
            font-weight: bold;
            font-size: 18px;
            color: #2563eb;
            margin-top: 10px;
            padding-top: 15px;
        }
        
        .discount {
            color: #dc2626;
        }
        
        .notes-section {
            margin-top: 40px;
            border-top: 1px solid #e5e7eb;
            padding-top: 30px;
        }
        
        .notes-grid {
            display: flex;
            justify-content: space-between;
        }
        
        .notes-column {
            width: 48%;
        }
        
        .notes-title {
            font-weight: bold;
            margin-bottom: 10px;
            color: #333;
        }
        
        .notes-content {
            color: #666;
            white-space: pre-line;
        }
        
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            color: #9ca3af;
            font-size: 10px;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div>
            <div class="invoice-title">INVOICE</div>
            <div class="invoice-number"># {{ $invoice_number }}</div>
        </div>
        <div class="invoice-dates">
            <div><strong>Date:</strong> {{ date('F j, Y', strtotime($date)) }}</div>
            <div><strong>Due Date:</strong> {{ date('F j, Y', strtotime($due_date)) }}</div>
        </div>
    </div>

    <!-- Company Details -->
    <div class="company-details">
        <div class="from-section">
            <div class="section-title">From:</div>
            <div class="company-name">{{ $from['name'] ?: 'Your Company' }}</div>
            @if($from['email'])
                <div class="company-info">{{ $from['email'] }}</div>
            @endif
            @if($from['address'])
                <div class="company-info">{{ nl2br($from['address']) }}</div>
            @endif
        </div>
        
        <div class="to-section">
            <div class="section-title">Bill To:</div>
            <div class="company-name">{{ $to['name'] ?: 'Client Name' }}</div>
            @if($to['email'])
                <div class="company-info">{{ $to['email'] }}</div>
            @endif
            @if($to['address'])
                <div class="company-info">{{ nl2br($to['address']) }}</div>
            @endif
        </div>
    </div>

    <!-- Items Table -->
    <table class="items-table">
        <thead>
            <tr>
                <th>Item</th>
                <th class="text-center">Quantity</th>
                <th class="text-center">Rate</th>
                <th class="text-right">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $item)
                <tr>
                    <td>{{ $item['description'] }}</td>
                    <td class="text-center">{{ number_format($item['quantity'], 0) }}</td>
                    <td class="text-center">{{ $currency_symbol }}{{ number_format($item['rate'], 2) }}</td>
                    <td class="text-right">{{ $currency_symbol }}{{ number_format($item['amount'], 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Totals -->
    <div class="totals-section">
        <div class="total-line">
            <span>Subtotal:</span>
            <span>{{ $currency_symbol }}{{ number_format($subtotal, 2) }}</span>
        </div>
        
        @if($tax_rate > 0)
            <div class="total-line">
                <span>Tax ({{ number_format($tax_rate, 1) }}%):</span>
                <span>{{ $currency_symbol }}{{ number_format($tax_amount, 2) }}</span>
            </div>
        @endif
        
        @if($discount > 0)
            <div class="total-line discount">
                <span>Discount:</span>
                <span>-{{ $currency_symbol }}{{ number_format($discount, 2) }}</span>
            </div>
        @endif
        
        @if($shipping > 0)
            <div class="total-line">
                <span>Shipping:</span>
                <span>{{ $currency_symbol }}{{ number_format($shipping, 2) }}</span>
            </div>
        @endif
        
        <div class="total-line final">
            <span>Total:</span>
            <span>{{ $currency_symbol }}{{ number_format($total, 2) }}</span>
        </div>
        
        @if($amount_paid > 0)
            <div class="total-line">
                <span>Amount Paid:</span>
                <span>{{ $currency_symbol }}{{ number_format($amount_paid, 2) }}</span>
            </div>
        @endif
        
        <div class="total-line final" style="color: #2563eb; border-color: #2563eb;">
            <span>Balance Due:</span>
            <span>{{ $currency_symbol }}{{ number_format($balance_due, 2) }}</span>
        </div>
    </div>

    <!-- Notes and Terms -->
    @if($notes || $terms)
        <div class="notes-section">
            <div class="notes-grid">
                @if($notes)
                    <div class="notes-column">
                        <div class="notes-title">Notes:</div>
                        <div class="notes-content">{{ nl2br($notes) }}</div>
                    </div>
                @endif
                
                @if($terms)
                    <div class="notes-column">
                        <div class="notes-title">Terms & Conditions:</div>
                        <div class="notes-content">{{ nl2br($terms) }}</div>
                    </div>
                @endif
            </div>
        </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        Invoice generated on {{ date('F j, Y \a\t g:i A') }} | Thank you for your business!
    </div>
</body>
</html> 