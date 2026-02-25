<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายละเอียดสินค้า</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI'; background: #f5f5f5; padding: 20px; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 40px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { margin-bottom: 30px; color: #333; }
        .info-group { margin-bottom: 25px; }
        .info-label { font-weight: 600; color: #667eea; font-size: 0.9em; text-transform: uppercase; margin-bottom: 5px; }
        .info-value { color: #333; font-size: 18px; padding: 10px 0; }
        .status { display: inline-block; padding: 5px 15px; border-radius: 20px; font-weight: 600; }
        .status.active { background: #d4edda; color: #155724; }
        .status.inactive { background: #f8d7da; color: #721c24; }
        .btn-group { display: flex; gap: 10px; margin-top: 30px; }
        .btn { padding: 12px 20px; border: none; border-radius: 5px; cursor: pointer; font-weight: bold; text-decoration: none; display: inline-block; }
        .btn-edit { background: #667eea; color: white; }
        .btn-edit:hover { background: #764ba2; }
        .btn-back { background: #6c757d; color: white; }
        .btn-back:hover { background: #5a6268; }
        .btn-delete { background: #dc3545; color: white; }
        .btn-delete:hover { background: #c82333; }
        .divider { border-top: 1px solid #eee; margin: 20px 0; }
    </style>
</head>
<body>
    <div class="container">
        <h1>👁️ รายละเอียดสินค้า</h1>

        <div class="info-group">
            <div class="info-label">ชื่อสินค้า</div>
            <div class="info-value">{{ $item->name }}</div>
        </div>

        <div class="info-group">
            <div class="info-label">หมวดหมู่</div>
            <div class="info-value">{{ $item->category }}</div>
        </div>

        <div class="info-group">
            <div class="info-label">ราคา</div>
            <div class="info-value">฿ {{ number_format($item->price, 2) }}</div>
        </div>

        <div class="info-group">
            <div class="info-label">จำนวน</div>
            <div class="info-value">{{ $item->quantity }} หน่วย</div>
        </div>

        <div class="info-group">
            <div class="info-label">สถานะ</div>
            <div class="info-value">
                <span class="status {{ $item->status }}">
                    {{ $item->status === 'active' ? 'ใช้งาน' : 'ไม่ใช้งาน' }}
                </span>
            </div>
        </div>

        @if($item->description)
        <div class="divider"></div>

        <div class="info-group">
            <div class="info-label">คำอธิบาย</div>
            <div class="info-value">{{ $item->description }}</div>
        </div>
        @endif

        <div class="divider"></div>

        <div class="info-group">
            <div class="info-label">วันที่สร้าง</div>
            <div class="info-value">{{ $item->created_at->format('d/m/Y H:i') }}</div>
        </div>

        <div class="info-group">
            <div class="info-label">วันที่แก้ไขล่าสุด</div>
            <div class="info-value">{{ $item->updated_at->format('d/m/Y H:i') }}</div>
        </div>

        <div class="btn-group">
            <a href="{{ route('items.edit', $item->id) }}" class="btn btn-edit">✏️ แก้ไข</a>
            <a href="{{ route('items.index') }}" class="btn btn-back">⬅️ กลับ</a>
            <form method="POST" action="{{ route('items.destroy', $item->id) }}" style="display: inline;" onsubmit="return confirm('คุณแน่ใจหรือว่าต้องการลบข้อมูลนี้?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-delete">🗑️ ลบ</button>
            </form>
        </div>
    </div>
</body>
</html>
