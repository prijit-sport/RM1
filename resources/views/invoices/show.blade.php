<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายละเอียดใบแจ้งหนี้</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI'; background: #f5f5f5; padding: 20px; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 40px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { margin-bottom: 30px; color: #333; }
        .detail-row { display: flex; border-bottom: 1px solid #eee; padding: 15px 0; }
        .detail-label { font-weight: 600; color: #555; width: 200px; }
        .detail-value { color: #333; flex: 1; }
        .status-badge { display: inline-block; padding: 5px 15px; border-radius: 20px; font-size: 14px; font-weight: 600; }
        .status-draft { background: #e2e3e5; color: #383d41; }
        .status-sent { background: #cce5ff; color: #004085; }
        .status-paid { background: #d4edda; color: #155724; }
        .status-overdue { background: #f8d7da; color: #721c24; }
        .status-cancelled { background: #f3c623; color: #333; }
        .btn-group { display: flex; gap: 10px; margin-top: 30px; }
        .btn { padding: 12px 20px; border: none; border-radius: 5px; cursor: pointer; font-weight: bold; text-decoration: none; display: inline-block; }
        .btn-edit { background: #ffc107; color: #333; }
        .btn-edit:hover { background: #e0a800; }
        .btn-delete { background: #dc3545; color: white; }
        .btn-delete:hover { background: #c82333; }
        .btn-back { background: #6c757d; color: white; }
        .btn-back:hover { background: #5a6268; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🧾 รายละเอียดใบแจ้งหนี้</h1>

        <div class="detail-row">
            <div class="detail-label">เลขที่ใบแจ้งหนี้:</div>
            <div class="detail-value">{{ $invoice->invoice_number }}</div>
        </div>

        <div class="detail-row">
            <div class="detail-label">การจอง:</div>
            <div class="detail-value">#{{ $invoice->booking_id }} - {{ $invoice->booking->room->room_number ?? '-' }}</div>
        </div>

        <div class="detail-row">
            <div class="detail-label">จำนวนเงิน:</div>
            <div class="detail-value">{{ number_format($invoice->amount, 2) }} บาท</div>
        </div>

        <div class="detail-row">
            <div class="detail-label">ภาษี:</div>
            <div class="detail-value">{{ number_format($invoice->tax, 2) }} บาท</div>
        </div>

        <div class="detail-row">
            <div class="detail-label">ยอดรวม:</div>
            <div class="detail-value"><strong>{{ number_format($invoice->total, 2) }} บาท</strong></div>
        </div>

        <div class="detail-row">
            <div class="detail-label">วันที่ออกใบแจ้งหนี้:</div>
            <div class="detail-value">{{ $invoice->issue_date->format('d/m/Y') }}</div>
        </div>

        <div class="detail-row">
            <div class="detail-label">วันที่ครบกำหนด:</div>
            <div class="detail-value">{{ $invoice->due_date->format('d/m/Y') }}</div>
        </div>

        <div class="detail-row">
            <div class="detail-label">สถานะ:</div>
            <div class="detail-value">
                @if($invoice->status === 'draft')
                    <span class="status-badge status-draft">ร่าง</span>
                @elseif($invoice->status === 'sent')
                    <span class="status-badge status-sent">ส่งแล้ว</span>
                @elseif($invoice->status === 'paid')
                    <span class="status-badge status-paid">ชำระแล้ว</span>
                @elseif($invoice->status === 'overdue')
                    <span class="status-badge status-overdue">เกินกำหนด</span>
                @elseif($invoice->status === 'cancelled')
                    <span class="status-badge status-cancelled">ยกเลิก</span>
                @endif
            </div>
        </div>

        <div class="detail-row">
            <div class="detail-label">หมายเหตุ:</div>
            <div class="detail-value">{{ $invoice->notes ?? '-' }}</div>
        </div>

        <div class="btn-group">
            <a href="{{ route('invoices.edit', $invoice->id) }}" class="btn btn-edit">✏️ แก้ไข</a>
            <form method="POST" action="{{ route('invoices.destroy', $invoice->id) }}" style="display:inline;" onsubmit="return confirm('คุณแน่ใจหรือไม่ที่จะลบใบแจ้งหนี้นี้?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-delete">🗑️ ลบ</button>
            </form>
            <a href="{{ route('invoices.index') }}" class="btn btn-back">⬅️ กลับ</a>
        </div>
    </div>
</body>
</html>
