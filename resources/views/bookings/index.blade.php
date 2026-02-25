<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการการจอง</title>
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
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header h1 {
            color: #333;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            border: none;
            cursor: pointer;
            transition: background 0.3s;
        }
        .btn:hover {
            background: #764ba2;
        }
        .btn-secondary {
            background: #6c757d;
        }
        .btn-secondary:hover {
            background: #5a6268;
        }
        .btn-danger {
            background: #dc3545;
        }
        .btn-danger:hover {
            background: #c82333;
        }
        .table-container {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th {
            background: #667eea;
            color: white;
            padding: 15px;
            text-align: left;
            font-weight: 600;
        }
        td {
            padding: 15px;
            border-bottom: 1px solid #eee;
        }
        tr:hover {
            background: #f9f9f9;
        }
        .status {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.9em;
            font-weight: 600;
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
        .actions {
            display: flex;
            gap: 10px;
        }
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📅 จัดการการจอง</h1>
            <a href="{{ route('bookings.create') }}" class="btn">➕ เพิ่มการจองใหม่</a>
        </div>

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ห้อง</th>
                        <th>แขก</th>
                        <th>เช็คอิน</th>
                        <th>เช็คเอาท์</th>
                        <th>ราคา</th>
                        <th>สถานะ</th>
                        <th>การกระทำ</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($bookings as $booking)
                        <tr>
                            <td><strong>#{{ $booking->room->room_number }}</strong></td>
                            <td>{{ $booking->guest->full_name }}</td>
                            <td>{{ $booking->check_in_date->format('d/m/Y') }}</td>
                            <td>{{ $booking->check_out_date->format('d/m/Y') }}</td>
                            <td>฿{{ number_format($booking->total_price, 2) }}</td>
                            <td>
                                <span class="status {{ $booking->status }}">
                                    @php
                                        $statusLabels = [
                                            'pending' => 'รอการยืนยัน',
                                            'confirmed' => 'ยืนยันแล้ว',
                                            'checked_in' => 'เช็คอินแล้ว',
                                            'checked_out' => 'เช็คเอาท์แล้ว',
                                            'cancelled' => 'ยกเลิก',
                                        ];
                                    @endphp
                                    {{ $statusLabels[$booking->status] ?? $booking->status }}
                                </span>
                            </td>
                            <td>
                                <div class="actions">
                                    <a href="{{ route('bookings.show', $booking) }}" class="btn btn-secondary">ดู</a>
                                    <a href="{{ route('bookings.edit', $booking) }}" class="btn btn-secondary">แก้ไข</a>
                                    <form action="{{ route('bookings.destroy', $booking) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger" onclick="return confirm('แน่ใจหรือ?')">ลบ</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 30px;">
                                ไม่มีข้อมูลการจอง - <a href="{{ route('bookings.create') }}">เพิ่มการจองใหม่</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div style="margin-top: 30px;">
            <a href="/" class="btn btn-secondary">← กลับไปแดชบอร์ด</a>
        </div>
    </div>
</body>
</html>
