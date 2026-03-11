<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', 'Thai', sans-serif;
            font-size: 14px;
            line-height: 1.6;
            color: #333;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            color: #2c3e50;
        }
        .invoice-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            gap: 20px;
        }
        .invoice-info .left,
        .invoice-info .right {
            width: 48%;
        }
        .info-box {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 15px;
        }
        .info-box h3 {
            margin-top: 0;
            color: #495057;
            font-size: 16px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background: #343a40;
            color: white;
        }
        .total-section {
            text-align: right;
            margin-top: 20px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 5px;
        }
        .total-row {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 10px;
        }
        .total-label {
            width: 150px;
            font-weight: bold;
        }
        .total-value {
            width: 120px;
            text-align: right;
        }
        .grand-total {
            font-size: 18px;
            color: #e74c3c;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 12px;
            color: #6c757d;
        }
        .status {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 3px;
            font-weight: bold;
        }
        .status-paid { background: #28a745; color: white; }
        .status-sent { background: #007bff; color: white; }
        .status-overdue { background: #dc3545; color: white; }
        .status-draft { background: #6c757d; color: white; }
    </style>
</head>
<body>
    <div class="header">
        <h1>ใบแจ้งหนี้ / INVOICE</h1>
        <p>เลขที่ Invoice Number: <strong>{{ $invoice->invoice_number }}</strong></p>
    </div>

    <div class="invoice-info">
        <div class="left">
            <div class="info-box">
                <h3>รายละเอียดผู้เช่า / Tenant Details</h3>
                <p><strong>ชื่อ:</strong> {{ trim((($guest->first_name ?? '') . ' ' . ($guest->last_name ?? ''))) ?: '-' }}</p>
                <p><strong>อีเมล:</strong> {{ optional($guest)->email ?? '-' }}</p>
                <p><strong>เบอร์โทร:</strong> {{ optional($guest)->phone ?? '-' }}</p>
                <p><strong>ที่อยู่:</strong> {{ optional($guest)->address ?? '-' }}</p>
            </div>
        </div>
        <div class="right">
            <div class="info-box">
                <h3>รายละเอียดห้อง / Room Details</h3>
                <p><strong>ห้อง:</strong> {{ optional($room)->room_number ?? '-' }}</p>
                <p><strong>ประเภท:</strong> {{ optional($room)->room_type ?? '-' }}</p>
            </div>
            <div class="info-box">
                <h3>วันที่ / Dates</h3>
                <p><strong>วันที่ออก:</strong> {{ $invoice->issue_date ? \Carbon\Carbon::parse($invoice->issue_date)->format('d/m/Y') : '-' }}</p>
                <p><strong>วันที่ครบกำหนด:</strong> {{ $invoice->due_date ? \Carbon\Carbon::parse($invoice->due_date)->format('d/m/Y') : '-' }}</p>
                <p><strong>สถานะ:</strong>
                    <span class="status status-{{ $invoice->status }}">
                        @switch($invoice->status)
                            @case('paid') ชำระแล้ว @break
                            @case('sent') ส่งแล้ว @break
                            @case('overdue') เกินกำหนด @break
                            @case('draft') ฉบับร่าง @break
                            @default {{ $invoice->status }}
                        @endswitch
                    </span>
                </p>
            </div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>รายการ</th>
                <th style="text-align: right;">จำนวนเงิน</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>ค่าเช่าห้อง {{ optional($room)->room_number ?? '-' }}
                    ({{ $invoice->issue_date ? \Carbon\Carbon::parse($invoice->issue_date)->format('F Y') : '-' }})</td>
                <td style="text-align: right;">{{ number_format($invoice->amount ?? 0, 2) }} ฿</td>
            </tr>
            @if($invoice->tax > 0)
            <tr>
                <td>ภาษีมูลค่าเพิ่ม (VAT 7%)</td>
                <td style="text-align: right;">{{ number_format($invoice->tax ?? 0, 2) }} ฿</td>
            </tr>
            @endif
            @if($invoice->late_fee > 0)
            <tr>
                <td>ค่าปรับล่าช้า</td>
                <td style="text-align: right;">{{ number_format($invoice->late_fee ?? 0, 2) }} ฿</td>
            </tr>
            @endif
        </tbody>
    </table>

    <div class="total-section">
        <div class="total-row">
            <div class="total-label">รวมค่าเช่า:</div>
            <div class="total-value">{{ number_format($invoice->amount ?? 0, 2) }} ฿</div>
        </div>
        @if($invoice->tax > 0)
        <div class="total-row">
            <div class="total-label">ภาษี VAT:</div>
            <div class="total-value">{{ number_format($invoice->tax ?? 0, 2) }} ฿</div>
        </div>
        @endif
        @if($invoice->late_fee > 0)
        <div class="total-row">
            <div class="total-label">ค่าปรับ:</div>
            <div class="total-value">{{ number_format($invoice->late_fee ?? 0, 2) }} ฿</div>
        </div>
        @endif
        <div class="total-row grand-total">
            <div class="total-label">รวมทั้งสิ้น:</div>
            <div class="total-value">{{ number_format($invoice->total ?? ($invoice->amount + $invoice->tax + ($invoice->late_fee ?? 0)), 2) }} ฿</div>
        </div>
    </div>

    @if($invoice->notes)
    <div class="info-box" style="margin-top: 20px;">
        <h3>หมายเหตุ / Notes</h3>
        <p>{{ $invoice->notes }}</p>
    </div>
    @endif

    <div class="footer">
        <p>ขอบคุณที่ใช้บริการ / Thank you for your business</p>
        <p>ระบบจัดการหอพัก / Room Management System</p>
    </div>
</body>
</html>
