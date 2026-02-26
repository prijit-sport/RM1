<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>มิเตอร์น้ำ/ไฟ</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; padding: 20px; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .header h1 { color: #333; }
        .btn { display: inline-block; padding: 10px 16px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; border: none; cursor: pointer; transition: background 0.2s; font-weight: 600; }
        .btn:hover { background: #764ba2; color: white; }
        .btn-secondary { background: #6c757d; }
        .btn-secondary:hover { background: #5a6268; }
        .btn-danger { background: #dc3545; }
        .btn-danger:hover { background: #c82333; }
        .btn-small { padding: 8px 12px; font-size: 0.9em; }
        .table-container { background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        table { width: 100%; border-collapse: collapse; }
        th { background: #667eea; color: white; padding: 12px 15px; text-align: left; font-weight: 600; }
        td { padding: 12px 15px; border-bottom: 1px solid #eee; vertical-align: top; }
        tr:hover { background: #f9f9f9; }
        .badge { display: inline-block; padding: 4px 10px; border-radius: 999px; font-size: 0.85em; font-weight: 700; }
        .badge-water { background: #d9f2ff; color: #0b5ed7; }
        .badge-electric { background: #fff3cd; color: #856404; }
        .badge-active { background: #d4edda; color: #155724; }
        .badge-inactive { background: #f8d7da; color: #721c24; }
        .actions { display: flex; flex-wrap: wrap; gap: 8px; }
        .alert { padding: 15px; margin-bottom: 15px; border-radius: 5px; }
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .pagination { margin-top: 20px; text-align: center; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>⚡ มิเตอร์น้ำ/ไฟ</h1>
            <a href="{{ route('meters.create') }}" class="btn">➕ เพิ่มมิเตอร์</a>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ห้อง</th>
                        <th>ประเภท</th>
                        <th>เลขมิเตอร์</th>
                        <th>หน่วย</th>
                        <th>สถานะ</th>
                        <th>การกระทำ</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($meters as $meter)
                        <tr>
                            <td><strong>{{ $meter->room->room_number ?? '-' }}</strong></td>
                            <td>
                                @if($meter->type === 'water')
                                    <span class="badge badge-water">น้ำ</span>
                                @else
                                    <span class="badge badge-electric">ไฟ</span>
                                @endif
                            </td>
                            <td>{{ $meter->meter_number }}</td>
                            <td>{{ $meter->unit ?? '-' }}</td>
                            <td>
                                @if($meter->is_active)
                                    <span class="badge badge-active">ใช้งาน</span>
                                @else
                                    <span class="badge badge-inactive">ปิดใช้งาน</span>
                                @endif
                            </td>
                            <td>
                                <div class="actions">
                                    <a class="btn btn-secondary btn-small" href="{{ route('meters.show', $meter) }}">ดู</a>
                                    <a class="btn btn-secondary btn-small" href="{{ route('meters.edit', $meter) }}">แก้ไข</a>
                                    <a class="btn btn-secondary btn-small" href="{{ route('meters.readings.index', $meter) }}">บันทึกเลข</a>
                                    <form action="{{ route('meters.destroy', $meter) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-small" onclick="return confirm('แน่ใจหรือ?')">ลบ</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align:center; padding: 30px;">
                                ยังไม่มีมิเตอร์ในระบบ - <a href="{{ route('meters.create') }}">เพิ่มมิเตอร์</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($meters->hasPages())
            <div class="pagination">{{ $meters->links() }}</div>
        @endif

        <div style="margin-top: 30px;">
            <a href="/" class="btn btn-secondary">← กลับไปแดชบอร์ด</a>
        </div>
    </div>
</body>
</html>

