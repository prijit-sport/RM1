<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายละเอียดห้อง</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f5f5;
        }
        .container {
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
        }
        .card {
            background: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            margin-bottom: 30px;
        }
        .info-group {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-bottom: 20px;
        }
        .info-item {
            padding: 15px;
            background: #f9f9f9;
            border-radius: 5px;
        }
        .info-label {
            color: #666;
            font-size: 0.9em;
            margin-bottom: 5px;
        }
        .info-value {
            color: #333;
            font-size: 1.1em;
            font-weight: 600;
        }
        .status {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.9em;
            font-weight: 600;
            display: inline-block;
        }
        .status.available {
            background: #d4edda;
            color: #155724;
        }
        .status.occupied {
            background: #f8d7da;
            color: #721c24;
        }
        .status.maintenance {
            background: #fff3cd;
            color: #856404;
        }
        .description {
            padding: 20px;
            background: #f9f9f9;
            border-radius: 5px;
            margin-bottom: 20px;
            line-height: 1.6;
        }
        .btn-group {
            display: flex;
            gap: 10px;
            margin-top: 30px;
        }
        .btn {
            padding: 12px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1em;
            font-weight: 600;
            transition: background 0.3s;
            text-decoration: none;
            display: inline-block;
        }
        .btn-primary {
            background: #667eea;
            color: white;
        }
        .btn-primary:hover {
            background: #764ba2;
        }
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        .btn-secondary:hover {
            background: #5a6268;
        }
        .btn-danger {
            background: #dc3545;
            color: white;
        }
        .btn-danger:hover {
            background: #c82333;
        }
        @media (max-width: 600px) {
            .info-group {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <h1>🛏️ รายละเอียดห้อง #{{ $room->room_number }}</h1>

            <div class="info-group">
                <div class="info-item">
                    <div class="info-label">หมายเลขห้อง</div>
                    <div class="info-value">{{ $room->room_number }}</div>
                </div>

                <div class="info-item">
                    <div class="info-label">ประเภทห้อง</div>
                                        <div class="info-value">{{ enum_bi('room_type', $room->room_type) }}</div>
                </div>

                <div class="info-item">
                    <div class="info-label">ราคา/คืน</div>
                    <div class="info-value">฿{{ number_format($room->price_per_night, 2) }}</div>
                </div>

                <div class="info-item">
                    <div class="info-label">ความจุ</div>
                    <div class="info-value">{{ $room->capacity }} คน</div>
                </div>

                <div class="info-item">
                    <div class="info-label">สถานะ</div>
                    <span class="status {{ $room->status }}">
                        @php
                        @endphp
                        {{ enum_th('room_status', $room->status) }}
                    </span>
                </div>

                <div class="info-item">
                    <div class="info-label">สร้างเมื่อ</div>
                    <div class="info-value">{{ $room->created_at->format('d/m/Y H:i') }}</div>
                </div>
            </div>

            @if ($room->description)
                <div class="description">
                    <strong>คำอธิบาย:</strong><br>
                    {{ nl2br($room->description) }}
                </div>
            @endif

            <div class="btn-group">
                <a href="{{ route('rooms.edit', $room) }}" class="btn btn-primary">✏️ แก้ไข</a>
                <a href="{{ route('rooms.index') }}" class="btn btn-secondary">← กลับไป</a>
                <form action="{{ route('rooms.destroy', $room) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" onclick="return confirm('แน่ใจหรือ?')">🗑️ ลบ</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>



