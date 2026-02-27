<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายละเอียดการจอง</title>
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
        .status.pending {
            background: #fff3cd;
            color: #856404;
        }
        .status.confirmed {
            background: #d1ecf1;
            color: #0c5460;
        }
        .status.checked_in {
            background: #d4edda;
            color: #155724;
        }
        .status.checked_out {
            background: #e2e3e5;
            color: #383d41;
        }
        .status.cancelled {
            background: #f8d7da;
            color: #721c24;
        }
        .notes {
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
            <h1>📅 รายละเอียดการจอง</h1>

            <div class="info-group">
                <div class="info-item">
                    <div class="info-label">ห้อม</div>
                                        <div class="info-value">#{{ $booking->room->room_number }} ({{ enum_bi('room_type', $booking->room->room_type) }})</div>
                </div>

                <div class="info-item">
                    <div class="info-label">แขก</div>
                    <div class="info-value">{{ $booking->guest->first_name }} {{ $booking->guest->last_name }}</div>
                </div>

                <div class="info-item">
                    <div class="info-label">วันเช็คอิน</div>
                    <div class="info-value">{{ $booking->check_in_date->format('d/m/Y') }}</div>
                </div>

                <div class="info-item">
                    <div class="info-label">วันเช็คเอาท์</div>
                    <div class="info-value">{{ $booking->check_out_date->format('d/m/Y') }}</div>
                </div>

                <div class="info-item">
                    <div class="info-label">จำนวนคืน</div>
                    <div class="info-value">{{ $booking->check_out_date->diff($booking->check_in_date)->days }} คืน</div>
                </div>

                <div class="info-item">
                    <div class="info-label">ราคารวม</div>
                    <div class="info-value">฿{{ number_format($booking->total_price, 2) }}</div>
                </div>

                <div class="info-item">
                    <div class="info-label">สถานะ</div>
                    <span class="status {{ $booking->status }}">
                        @php
                        @endphp
                        {{ enum_th('booking_status', $booking->status) }}
                    </span>
                </div>

                <div class="info-item">
                    <div class="info-label">สร้างเมื่อ</div>
                    <div class="info-value">{{ $booking->created_at->format('d/m/Y H:i') }}</div>
                </div>
            </div>

            @if ($booking->notes)
                <div class="notes">
                    <strong>หมายเหตุ:</strong><br>
                    {{ nl2br($booking->notes) }}
                </div>
            @endif

            <div class="btn-group">
                <a href="{{ route('bookings.edit', $booking) }}" class="btn btn-primary">✏️ แก้ไข</a>
                <a href="{{ route('bookings.index') }}" class="btn btn-secondary">← กลับไป</a>
                <form action="{{ route('bookings.destroy', $booking) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" onclick="return confirm('แน่ใจหรือ?')">🗑️ ลบ</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>



