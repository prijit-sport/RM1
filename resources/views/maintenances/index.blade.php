<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการการบำรุงรักษา</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI'; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; padding: 20px; }
        .header { display: flex; justify-content: space-between; margin-bottom: 30px; background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .btn { display: inline-block; padding: 10px 20px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; border: none; cursor: pointer; }
        .btn:hover { background: #764ba2; }
        .table-container { background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        table { width: 100%; border-collapse: collapse; }
        th { background: #667eea; color: white; padding: 15px; text-align: left; }
        td { padding: 15px; border-bottom: 1px solid #eee; }
        tr:hover { background: #f9f9f9; }
        .status { padding: 5px 10px; border-radius: 20px; font-size: 0.9em; font-weight: 600; }
        .status.active { background: #d4edda; color: #155724; }
        .status.inactive { background: #f8d7da; color: #721c24; }
        .actions { display: flex; gap: 10px; }
        .btn-secondary { background: #6c757d; }
        .btn-secondary:hover { background: #5a6268; }
        .btn-danger { background: #dc3545; }
        .btn-danger:hover { background: #c82333; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔧 จัดการการบำรุงรักษา</h1>
            <a href="{{ route('maintenances.create') }}" class="btn">➕ เพิ่มการบำรุงรักษา</a>
        </div>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ห้อง</th>
                        <th>ประเภทปัญหา</th>
                        <th>วันที่แจ้ง</th>
                        <th>ผู้รับผิดชอบ</th>
                        <th>สถานะ</th>
                        <th>ค่าใช้จ่าย</th>
                        <th>การกระทำ</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($maintenances as $maintenance)
                        <tr>
                            <td>{{ optional($maintenance->room)->room_number ?? '-' }}</td>
                            <td>{{ $maintenance->issue_type }}</td>
                            <td>{{ optional($maintenance->reported_date)->format('d/m/Y') }}</td>
                            <td>{{ $maintenance->assigned_to ?? '-' }}</td>
                            <td><span class="status {{ $maintenance->status }}">{{ ucfirst(str_replace('_', ' ', $maintenance->status)) }}</span></td>
                            <td>{{ $maintenance->cost ? number_format($maintenance->cost, 2) : '-' }}</td>
                            <td>
                                <div class="actions">
                                    <a href="{{ route('maintenances.show', $maintenance) }}" class="btn btn-secondary">ดู</a>
                                    <a href="{{ route('maintenances.edit', $maintenance) }}" class="btn btn-secondary">แก้ไข</a>
                                    <form action="{{ route('maintenances.destroy', $maintenance) }}" method="POST" style="display:inline;">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-danger" onclick="return confirm('ลบ?')">ลบ</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" style="text-align: center; padding: 30px;">ไม่มีข้อมูล</td></tr>
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
