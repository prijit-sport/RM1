<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการสัญญา</title>
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
        .status.pending { background: #fff3cd; color: #856404; }
        .status.expired { background: #f8d7da; color: #721c24; }
        .actions { display: flex; gap: 10px; }
        .btn-link { background: transparent; color: #667eea; padding: 5px 10px; text-decoration: underline; cursor: pointer; }
        .btn-link:hover { color: #764ba2; }
        .btn-danger { background: #dc3545; color: white; padding: 5px 10px; }
        .btn-danger:hover { background: #c82333; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📄 จัดการสัญญา</h1>
            <a href="{{ route('contracts.create') }}" class="btn">➕ เพิ่มสัญญา</a>
        </div>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>หมายเลขสัญญา</th>
                        <th>ผู้รับเหมา</th>
                        <th>วันที่เริ่ม</th>
                        <th>วันที่สิ้นสุด</th>
                        <th>จำนวนเงิน</th>
                        <th>สถานะ</th>
                        <th>การกระทำ</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($contracts as $contract)
                    <tr>
                        <td>{{ $contract->contract_number }}</td>
                        <td>{{ $contract->contractor_name }}</td>
                        <td>{{ $contract->start_date->format('d/m/Y') }}</td>
                        <td>{{ $contract->end_date->format('d/m/Y') }}</td>
                        <td>฿ {{ number_format($contract->amount, 2) }}</td>
                        <td><span class="status {{ strtolower($contract->status) }}">{{ ucfirst($contract->status) }}</span></td>
                        <td>
                            <div class="actions">
                                <a href="{{ route('contracts.show', $contract->id) }}" class="btn-link">👁️ ดู</a>
                                <a href="{{ route('contracts.edit', $contract->id) }}" class="btn-link">✏️ แก้ไข</a>
                                <form method="POST" action="{{ route('contracts.destroy', $contract->id) }}" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-danger" onclick="return confirm('ยืนยันการลบ?')">🗑️ ลบ</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 40px;">ไม่มีสัญญา</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
