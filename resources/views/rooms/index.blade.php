<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการห้องพัก</title>
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
        .actions {
            display: flex;
            gap: 10px;
        }
        .actions a, .actions form {
            display: inline-block;
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
        .pagination {
            margin-top: 20px;
            text-align: center;
        }
        .pagination a, .pagination span {
            display: inline-block;
            padding: 10px 15px;
            margin: 0 5px;
            background: white;
            border: 1px solid #ddd;
            border-radius: 5px;
            text-decoration: none;
            color: #667eea;
        }
        .pagination .active {
            background: #667eea;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🛏️ จัดการห้องพัก</h1>
            <a href="{{ route('rooms.create') }}" class="btn">➕ เพิ่มห้องใหม่</a>
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
                        <th>หมายเลขห้อง</th>
                        <th>ประเภทห้อง</th>
                        <th>ราคา/คืน</th>
                        <th>ความจุ</th>
                        <th>สถานะ</th>
                        <th>การกระทำ</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($rooms as $room)
                        <tr>
                            <td><strong>{{ $room->room_number }}</strong></td>
                            <td>{{ $room->room_type }}</td>
                            <td>฿{{ number_format($room->price_per_night, 2) }}</td>
                            <td>{{ $room->capacity }} คน</td>
                            <td>
                                <span class="status {{ $room->status }}">
                                    @php
                                        $statusLabels = [
                                            'available' => 'ว่าง',
                                            'occupied' => 'ใช้งาน',
                                            'maintenance' => 'ซ่อมบำรุง',
                                        ];
                                    @endphp
                                    {{ $statusLabels[$room->status] ?? $room->status }}
                                </span>
                            </td>
                            <td>
                                <div class="actions">
                                    <a href="{{ route('rooms.show', $room) }}" class="btn btn-secondary">ดู</a>
                                    <a href="{{ route('rooms.edit', $room) }}" class="btn btn-secondary">แก้ไข</a>
                                    <form action="{{ route('rooms.destroy', $room) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger" onclick="return confirm('แน่ใจหรือ?')">ลบ</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 30px;">
                                ไม่มีข้อมูลห้อง - <a href="{{ route('rooms.create') }}">เพิ่มห้องใหม่</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($rooms->hasPages())
            <div class="pagination">
                {{ $rooms->links() }}
            </div>
        @endif

        <div style="margin-top: 30px;">
            <a href="/" class="btn btn-secondary">← กลับไปแดชบอร์ด</a>
        </div>
    </div>
</body>
</html>
