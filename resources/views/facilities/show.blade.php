<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายละเอียดสิ่งอำนวยความสะดวก</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI'; background: #f5f5f5; padding: 20px; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 40px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { margin-bottom: 30px; color: #333; }
        .detail-row { display: flex; border-bottom: 1px solid #eee; padding: 15px 0; }
        .detail-label { font-weight: 600; color: #555; width: 200px; }
        .detail-value { color: #333; flex: 1; }
        .status-badge { display: inline-block; padding: 5px 15px; border-radius: 20px; font-size: 14px; font-weight: 600; }
        .status-active { background: #d4edda; color: #155724; }
        .status-inactive { background: #e2e3e5; color: #383d41; }
        .status-maintenance { background: #fff3cd; color: #856404; }
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
        <h1>🏢 รายละเอียดสิ่งอำนวยความสะดวก</h1>

        <div class="detail-row">
            <div class="detail-label">ชื่อ:</div>
            <div class="detail-value">{{ $facility->name }}</div>
        </div>

        <div class="detail-row">
            <div class="detail-label">ประเภท:</div>
            <div class="detail-value">{{ $facility->type }}</div>
        </div>

        <div class="detail-row">
            <div class="detail-label">ที่ตั้ง:</div>
            <div class="detail-value">{{ $facility->location }}</div>
        </div>

        <div class="detail-row">
            <div class="detail-label">คำอธิบาย:</div>
            <div class="detail-value">{{ $facility->description ?? '-' }}</div>
        </div>

        <div class="detail-row">
            <div class="detail-label">สถานะ:</div>
            <div class="detail-value">
                @if($facility->status === 'active')
                    <span class="status-badge status-active">ใช้งาน</span>
                @elseif($facility->status === 'inactive')
                    <span class="status-badge status-inactive">ไม่ใช้งาน</span>
                @elseif($facility->status === 'maintenance')
                    <span class="status-badge status-maintenance">บำรุงรักษา</span>
                @endif
            </div>
        </div>

        <div class="detail-row">
            <div class="detail-label">ตารางบำรุงรักษา:</div>
            <div class="detail-value">{{ $facility->maintenance_schedule ?? '-' }}</div>
        </div>

        <div class="detail-row">
            <div class="detail-label">วันที่บำรุงรักษาล่าสุด:</div>
            <div class="detail-value">{{ $facility->last_maintenance_date ? $facility->last_maintenance_date->format('d/m/Y') : '-' }}</div>
        </div>

        <div class="detail-row">
            <div class="detail-label">วันที่บำรุงรักษาครั้งถัดไป:</div>
            <div class="detail-value">{{ $facility->next_maintenance_date ? $facility->next_maintenance_date->format('d/m/Y') : '-' }}</div>
        </div>

        <div class="btn-group">
            <a href="{{ route('facilities.edit', $facility->id) }}" class="btn btn-edit">✏️ แก้ไข</a>
            <form method="POST" action="{{ route('facilities.destroy', $facility->id) }}" style="display:inline;" onsubmit="return confirm('คุณแน่ใจหรือไม่ที่จะลบสิ่งอำนวยความสะดวกนี้?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-delete">🗑️ ลบ</button>
            </form>
            <a href="{{ route('facilities.index') }}" class="btn btn-back">⬅️ กลับ</a>
        </div>
    </div>
</body>
</html>
