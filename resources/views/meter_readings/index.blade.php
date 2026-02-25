<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>บันทึกเลขมิเตอร์</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f5f5f5; }
        .container { max-width: 1100px; margin: 0 auto; padding: 20px; }
        .header { display:flex; justify-content: space-between; align-items: center; margin-bottom: 15px; background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); gap: 12px; }
        .header h1 { color: #333; }
        .subtitle { color:#666; font-size: 0.95em; margin-top: 6px; }
        .btn { display: inline-block; padding: 10px 16px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; border: none; cursor: pointer; transition: background 0.2s; font-weight: 700; }
        .btn:hover { background: #764ba2; color:white; }
        .btn-secondary { background: #6c757d; }
        .btn-secondary:hover { background: #5a6268; }
        .btn-danger { background: #dc3545; }
        .btn-danger:hover { background: #c82333; }
        .btn-small { padding: 8px 12px; font-size: 0.9em; }
        .table-container { background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        table { width: 100%; border-collapse: collapse; }
        th { background: #667eea; color: white; padding: 12px 15px; text-align:left; font-weight: 700; }
        td { padding: 12px 15px; border-bottom: 1px solid #eee; vertical-align: top; }
        tr:hover { background: #f9f9f9; }
        .actions { display:flex; flex-wrap: wrap; gap: 8px; }
        .alert { padding: 15px; margin-bottom: 15px; border-radius: 5px; }
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .pagination { margin-top: 20px; text-align: center; }
        .badge { display:inline-block; padding: 4px 10px; border-radius: 999px; font-size: 0.85em; font-weight: 800; }
        .badge-water { background: #d9f2ff; color: #0b5ed7; }
        .badge-electric { background: #fff3cd; color: #856404; }
    </style>
</head>
<body>
    <div class="container">
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="header">
            <div>
                <h1>บันทึกเลขมิเตอร์</h1>
                <div class="subtitle">
                    ห้อง <strong>{{ $meter->room->room_number ?? '-' }}</strong>
                    |
                    @if($meter->type === 'water')
                        <span class="badge badge-water">น้ำ</span>
                    @else
                        <span class="badge badge-electric">ไฟ</span>
                    @endif
                    |
                    เลขมิเตอร์: <strong>{{ $meter->meter_number }}</strong>
                </div>
            </div>
            <div class="actions">
                <a class="btn btn-secondary btn-small" href="{{ route('meters.show', $meter) }}">← กลับ</a>
                <a class="btn btn-small" href="{{ route('meters.readings.create', $meter) }}">➕ เพิ่มเลข</a>
            </div>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>วันที่</th>
                        <th>เลขมิเตอร์</th>
                        <th>ผู้บันทึก</th>
                        <th>หมายเหตุ</th>
                        <th>การกระทำ</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($readings as $reading)
                        <tr>
                            <td>{{ $reading->reading_date?->format('d/m/Y') ?? '-' }}</td>
                            <td><strong>{{ number_format((float)$reading->reading_value, 2) }}</strong></td>
                            <td>{{ $reading->recordedBy->name ?? '-' }}</td>
                            <td>{{ $reading->notes ?? '-' }}</td>
                            <td>
                                <div class="actions">
                                    <a class="btn btn-secondary btn-small" href="{{ route('meters.readings.edit', [$meter, $reading]) }}">แก้ไข</a>
                                    <form action="{{ route('meters.readings.destroy', [$meter, $reading]) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-small" onclick="return confirm('แน่ใจหรือ?')">ลบ</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align:center; padding: 30px;">
                                ยังไม่มีรายการเลขมิเตอร์
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($readings->hasPages())
            <div class="pagination">{{ $readings->links() }}</div>
        @endif
    </div>
</body>
</html>

