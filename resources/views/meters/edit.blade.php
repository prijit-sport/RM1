<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แก้ไขมิเตอร์</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f5f5f5; }
        .container { max-width: 700px; margin: 50px auto; padding: 20px; }
        .card { background: white; border-radius: 10px; padding: 30px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #333; margin-bottom: 20px; }
        .form-group { margin-bottom: 16px; }
        label { display:block; margin-bottom: 6px; font-weight: 700; color: #333; }
        input, select, textarea { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 1em; font-family: inherit; }
        textarea { resize: vertical; }
        .row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
        @media (max-width: 640px) { .row { grid-template-columns: 1fr; } }
        .btn-group { display:flex; gap: 10px; margin-top: 20px; }
        .btn { flex: 1; padding: 12px; border: none; border-radius: 5px; cursor: pointer; font-size: 1em; font-weight: 700; transition: background 0.2s; text-decoration:none; display:flex; align-items:center; justify-content:center; }
        .btn-primary { background: #667eea; color: white; }
        .btn-primary:hover { background: #764ba2; }
        .btn-secondary { background: #6c757d; color: white; }
        .btn-secondary:hover { background: #5a6268; }
        .error { color: #dc3545; font-size: 0.9em; margin-top: 6px; }
        .checkbox { display:flex; gap: 8px; align-items:center; }
        .checkbox input { width: auto; }
        .help { color: #666; font-size: 0.9em; margin-top: 6px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <h1>⚡ แก้ไขมิเตอร์น้ำ/ไฟ</h1>

            <form action="{{ route('meters.update', $meter) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="room_id">ห้อง *</label>
                    <select id="room_id" name="room_id" required>
                        <option value="">-- เลือกห้อง --</option>
                        @foreach($rooms as $room)
                            <option value="{{ $room->id }}" {{ old('room_id', $meter->room_id) == $room->id ? 'selected' : '' }}>
                                ห้อง {{ $room->room_number }} ({{ $room->room_type }})
                            </option>
                        @endforeach
                    </select>
                    @error('room_id') <div class="error">{{ $message }}</div> @enderror
                </div>

                <div class="row">
                    <div class="form-group">
                        <label for="type">ประเภท *</label>
                        <select id="type" name="type" required>
                            <option value="water" {{ old('type', $meter->type) == 'water' ? 'selected' : '' }}>น้ำ</option>
                            <option value="electric" {{ old('type', $meter->type) == 'electric' ? 'selected' : '' }}>ไฟ</option>
                        </select>
                        @error('type') <div class="error">{{ $message }}</div> @enderror
                        <div class="help">หมายเหตุ: 1 ห้อง จะมีมิเตอร์น้ำ/ไฟได้อย่างละ 1 ตัว</div>
                    </div>

                    <div class="form-group">
                        <label for="meter_number">เลขมิเตอร์ *</label>
                        <input id="meter_number" name="meter_number" type="text" value="{{ old('meter_number', $meter->meter_number) }}" required>
                        @error('meter_number') <div class="error">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="form-group">
                        <label for="unit">หน่วย</label>
                        <input id="unit" name="unit" type="text" value="{{ old('unit', $meter->unit) }}" placeholder="เช่น m3 / kWh">
                        @error('unit') <div class="error">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group">
                        <label for="installed_at">วันที่ติดตั้ง</label>
                        <input id="installed_at" name="installed_at" type="date" value="{{ old('installed_at', optional($meter->installed_at)->format('Y-m-d')) }}">
                        @error('installed_at') <div class="error">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="form-group">
                    <div class="checkbox">
                        <input id="is_active" name="is_active" type="checkbox" value="1" {{ old('is_active', $meter->is_active) ? 'checked' : '' }}>
                        <label for="is_active" style="margin:0;">เปิดใช้งาน</label>
                    </div>
                    @error('is_active') <div class="error">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label for="notes">หมายเหตุ</label>
                    <textarea id="notes" name="notes" rows="4" placeholder="รายละเอียดเพิ่มเติม...">{{ old('notes', $meter->notes) }}</textarea>
                    @error('notes') <div class="error">{{ $message }}</div> @enderror
                </div>

                <div class="btn-group">
                    <button type="submit" class="btn btn-primary">✓ บันทึก</button>
                    <a href="{{ route('meters.show', $meter) }}" class="btn btn-secondary">← ยกเลิก</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>

