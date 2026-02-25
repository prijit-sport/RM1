<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายละเอียดสัญญา</title>
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
        .status-active { background: #d4edda; color: #155724; }
        .status-completed { background: #cce5ff; color: #004085; }
        .status-cancelled { background: #f8d7da; color: #721c24; }
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
        <h1>📄 รายละเอียดสัญญา</h1>

        <div class="detail-row">
            <div class="detail-label">ชื่อผู้รับเหมา:</div>
            <div class="detail-value">{{ $contract->contractor_name }}</div>
        </div>

        <div class="detail-row">
            <div class="detail-label">เลขที่สัญญา:</div>
            <div class="detail-value">{{ $contract->contract_number }}</div>
        </div>

        <div class="detail-row">
            <div class="detail-label">คำอธิบาย:</div>
            <div class="detail-value">{{ $contract->description ?? '-' }}</div>
        </div>

        <div class="detail-row">
            <div class="detail-label">วันที่เริ่มต้น:</div>
            <div class="detail-value">{{ $contract->start_date->format('d/m/Y') }}</div>
        </div>

        <div class="detail-row">
            <div class="detail-label">วันที่สิ้นสุด:</div>
            <div class="detail-value">{{ $contract->end_date->format('d/m/Y') }}</div>
        </div>

        <div class="detail-row">
            <div class="detail-label">จำนวนเงิน:</div>
            <div class="detail-value">{{ number_format($contract->amount, 2) }} บาท</div>
        </div>

        <div class="detail-row">
            <div class="detail-label">สถานะ:</div>
            <div class="detail-value">
                @if($contract->status === 'draft')
                    <span class="status-badge status-draft">ร่าง</span>
                @elseif($contract->status === 'active')
                    <span class="status-badge status-active">ใช้งาน</span>
                @elseif($contract->status === 'completed')
                    <span class="status-badge status-completed">เสร็จสิ้น</span>
                @elseif($contract->status === 'cancelled')
                    <span class="status-badge status-cancelled">ยกเลิก</span>
                @endif
            </div>
        </div>

        <div class="detail-row">
            <div class="detail-label">หมายเหตุ:</div>
            <div class="detail-value">{{ $contract->notes ?? '-' }}</div>
        </div>

        <div class="btn-group">
            <a href="{{ route('contracts.edit', $contract->id) }}" class="btn btn-edit">✏️ แก้ไข</a>
            <form method="POST" action="{{ route('contracts.destroy', $contract->id) }}" style="display:inline;" onsubmit="return confirm('คุณแน่ใจหรือไม่ที่จะลบสัญญานี้?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-delete">🗑️ ลบ</button>
            </form>
            <a href="{{ route('contracts.index') }}" class="btn btn-back">⬅️ กลับ</a>
        </div>
    </div>
</body>
</html>
