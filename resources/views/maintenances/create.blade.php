<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เพิ่มการซ่อมบำรุงใหม่</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI'; background: #f5f5f5; padding: 20px; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 40px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { margin-bottom: 30px; color: #333; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; font-weight: 600; color: #555; }
        input, textarea, select { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Segoe UI'; font-size: 14px; }
        input:focus, textarea:focus, select:focus { outline: none; border-color: #667eea; box-shadow: 0 0 5px rgba(102, 126, 234, 0.3); }
        textarea { resize: vertical; min-height: 100px; }
        .btn-group { display: flex; gap: 10px; margin-top: 30px; }
        .btn { padding: 12px 20px; border: none; border-radius: 5px; cursor: pointer; font-weight: bold; text-decoration: none; display: inline-block; }
        .btn-submit { background: #667eea; color: white; }
        .btn-submit:hover { background: #764ba2; }
        .btn-cancel { background: #6c757d; color: white; }
        .btn-cancel:hover { background: #5a6268; }
        .error-message { color: #dc3545; font-size: 12px; margin-top: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 เพิ่มการซ่อมบำรุงใหม่</h1>
        
        @if ($errors->any())
            <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('maintenances.store') }}">
            @csrf

            <div class="form-group">
                <label for="room_id">ห้อง *</label>
                <select id="room_id" name="room_id" required>
                    <option value="">-- เลือกห้อง --</option>
                    @foreach($rooms as $room)
                        <option value="{{ $room->id }}" {{ old('room_id') == $room->id ? 'selected' : '' }}>
                            {{ $room->room_number }}
                        </option>
                    @endforeach
                </select>
                @error('room_id')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="issue_type">ประเภทปัญหา *</label>
                <input type="text" id="issue_type" name="issue_type" value="{{ old('issue_type') }}" required>
                @error('issue_type')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="description">คำอธิบาย</label>
                <textarea id="description" name="description">{{ old('description') }}</textarea>
            </div>

            <div class="form-group">
                <label for="reported_date">วันที่แจ้งซ่อม *</label>
                <input type="date" id="reported_date" name="reported_date" value="{{ old('reported_date') }}" required>
                @error('reported_date')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="completed_date">วันที่เสร็จสิ้น</label>
                <input type="date" id="completed_date" name="completed_date" value="{{ old('completed_date') }}">
            </div>

            <div class="form-group">
                <label for="status">สถานะ *</label>
                <select id="status" name="status" required>
                    <option value="">-- เลือกสถานะ --</option>
                    <option value="pending" {{ old('status') === 'pending' ? 'selected' : '' }}>รอดำเนินการ</option>
                    <option value="in_progress" {{ old('status') === 'in_progress' ? 'selected' : '' }}>กำลังดำเนินการ</option>
                    <option value="completed" {{ old('status') === 'completed' ? 'selected' : '' }}>เสร็จสิ้น</option>
                    <option value="cancelled" {{ old('status') === 'cancelled' ? 'selected' : '' }}>ยกเลิก</option>
                </select>
                @error('status')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="assigned_to">ผู้รับผิดชอบ</label>
                <input type="text" id="assigned_to" name="assigned_to" value="{{ old('assigned_to') }}">
            </div>

            <div class="form-group">
                <label for="cost">ค่าใช้จ่าย</label>
                <input type="number" id="cost" name="cost" value="{{ old('cost') }}" step="0.01">
            </div>

            <div class="form-group">
                <label for="notes">หมายเหตุ</label>
                <textarea id="notes" name="notes">{{ old('notes') }}</textarea>
            </div>

            <div class="btn-group">
                <button type="submit" class="btn btn-submit">💾 บันทึก</button>
                <a href="{{ route('maintenances.index') }}" class="btn btn-cancel">❌ ยกเลิก</a>
            </div>
        </form>
    </div>
</body>
</html>
