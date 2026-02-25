<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายละเอียดแขก</title>
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
            <h1>👥 {{ $guest->first_name }} {{ $guest->last_name }}</h1>

            <div class="info-group">
                <div class="info-item">
                    <div class="info-label">ชื่อแรก</div>
                    <div class="info-value">{{ $guest->first_name }}</div>
                </div>

                <div class="info-item">
                    <div class="info-label">นามสกุล</div>
                    <div class="info-value">{{ $guest->last_name }}</div>
                </div>

                <div class="info-item">
                    <div class="info-label">อีเมล</div>
                    <div class="info-value">{{ $guest->email }}</div>
                </div>

                <div class="info-item">
                    <div class="info-label">เบอร์โทร</div>
                    <div class="info-value">{{ $guest->phone }}</div>
                </div>

                <div class="info-item">
                    <div class="info-label">หมายเลขประจำตัว</div>
                    <div class="info-value">{{ $guest->id_number }}</div>
                </div>

                <div class="info-item">
                    <div class="info-label">ที่อยู่</div>
                    <div class="info-value">{{ $guest->address ?? '-' }}</div>
                </div>

                @if ($guest->city || $guest->country)
                    <div class="info-item">
                        <div class="info-label">สถานที่</div>
                        <div class="info-value">{{ $guest->city }}, {{ $guest->country }}</div>
                    </div>
                @endif

                <div class="info-item">
                    <div class="info-label">สร้างเมื่อ</div>
                    <div class="info-value">{{ $guest->created_at->format('d/m/Y H:i') }}</div>
                </div>
            </div>

            <div class="btn-group">
                <a href="{{ route('guests.edit', $guest) }}" class="btn btn-primary">✏️ แก้ไข</a>
                <a href="{{ route('guests.index') }}" class="btn btn-secondary">← กลับไป</a>
                <form action="{{ route('guests.destroy', $guest) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" onclick="return confirm('แน่ใจหรือ?')">🗑️ ลบ</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
