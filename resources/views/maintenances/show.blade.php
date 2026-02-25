<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายละเอียดการซ่อมบำรุง</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI'; background: #f5f5f5; padding: 20px; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 40px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { margin-bottom: 30px; color: #333; }
        .detail-row { display: flex; border-bottom: 1px solid #eee; padding: 15px 0; }
        .detail-label { font-weight: 600; color: #555; width: 200px; }
        .detail-value { color: #333; flex: 1; }
        .status-badge { display: inline-block; padding: 5px 15px; border-radius: 20px; font-size: 14px; font-weight: 600; }
        .status-pending { background: #fff3cd; color: #856404; }
        .status-in_progress { background: #cce5ff; color: #004085; }
        .status-completed { background: #d4edda; color: #155724; }
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
        <h1>🔧 รายละเอียดการซ่อมบำรุง</h1>

        <div class="detail-row">
            <div class="detail-label">ห้อง:</div>
            <div class="detail-value">{{ $maintenance->room->room_number }}</div>
        </div>

        <div class="detail-row">
            <div class="detail-label">ประเภทปัญหา:</div>
            <div class="detail-value">{{ $maintenance->issue_type }}</div>
        </div>

        <div class="detail-row">
            <div class="detail-label">คำอธิบาย:</div>
            <div class="detail-value">{{ $maintenance->description ?? '-' }}</div>
        </div>

        <div class="detail-row">
            <div class="detail-label">วันที่แจ้งซ่อม:</div>
            <div class="detail-value">{{ $maintenance->reported_date->format('d/m/Y') }}</div>
        </div>

        <div class="detail-row">
            <div class="detail-label">วันที่เสร็จสิ้น:</div>
            <div class="detail-value">{{ $maintenance->completed_date ? $maintenance->completed_date->format('d/m/Y') : '-' }}</div>
        </div>

        <div class="detail-row">
            <div class="detail-label">สถานะ:</div>
            <div class="detail-value">
                @if($maintenance->status === 'pending')
                    <span class="status-badge status-pending">รอดำเนินการ</span>
                @elseif($maintenance->status === 'in_progress')
                    <span class="status-badge status-in_progress">กำลังดำเนินการ</span>
                @elseif($maintenance->status === 'completed')
                    <span class="status-badge status-completed">เสร็จสิ้น</span>
                @elseif($maintenance->status === 'cancelled')
                    <span class="status-badge status-cancelled">ยกเลิก</span>
                @endif
            </div>
        </div>

        <div class="detail-row">
            <div class="detail-label">ผู้รับผิดชอบ:</div>
            <div class="detail-value">{{ $maintenance->assigned_to ?? '-' }}</div>
        </div>

        <div class="detail-row">
            <div class="detail-label">ค่าใช้จ่าย:</div>
            <div class="detail-value">{{ $maintenance->cost ? number_format($maintenance->cost, 2) . ' บาท' : '-' }}</div>
        </div>

        <div class="detail-row">
            <div class="detail-label">หมายเหตุ:</div>
            <div class="detail-value">{{ $maintenance->notes ?? '-' }}</div>
        </div>

        <div class="btn-group">
            <a href="{{ route('maintenances.edit', $maintenance->id) }}" class="btn btn-edit">✏️ แก้ไข</a>
            <form method="POST" action="{{ route('maintenances.destroy', $maintenance->id) }}" style="display:inline;" onsubmit="return confirm('คุณแน่ใจหรือไม่ที่จะลบการซ่อมบำรุงนี้?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-delete">🗑️ ลบ</button>
            </form>
            <a href="{{ route('maintenances.index') }}" class="btn btn-back">⬅️ กลับ</a>
        </div>
    </div>
</body>
</html>
