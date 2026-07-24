<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เพิ่มสิ่งอำนวยความสะดวกใหม่</title>
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
        .required::after { content: ' *'; color: #dc3545; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🏢 เพิ่มสิ่งอำนวยความสะดวกใหม่</h1>
        
        @if ($errors->any())
            <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif
 
        <form method="POST" action="{{ route('facilities.store') }}">
            @csrf
 
            <!-- ห้องพัก (auto-select ถ้ามี room_id จาก query parameter) -->
            <div class="form-group">
                <label for="room_id">ห้องพัก <span class="required"></span></label>
                <select name="room_id" id="room_id" required>
                    <option value="">-- เลือกห้องพัก --</option>
                    @foreach($rooms as $room)
                        {{-- ✅ Auto-select ถ้า selectedRoomId ตรงกับ room->id --}}
                        <option value="{{ $room->id }}" 
                            {{ (old('room_id') ?? $selectedRoomId) == $room->id ? 'selected' : '' }}>
                            ห้อง {{ $room->room_number }} ({{ $room->room_type }})
                        </option>
                    @endforeach
                </select>
                @error('room_id')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>
 
            <div class="form-group">
                <label for="name">ชื่อ <span class="required"></span></label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required>
                @error('name')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>
 
            <div class="form-group">
                <label for="type">ประเภท <span class="required"></span></label>
                <select name="type" id="type" required>
                    <option value="">-- เลือกประเภท --</option>
                    <option value="bed"            {{ old('type', '') == 'bed'            ? 'selected' : '' }}>🛏️ เตียง</option>
                    <option value="mattress"       {{ old('type', '') == 'mattress'       ? 'selected' : '' }}>🛌 ที่นอน</option>
                    <option value="wardrobe"       {{ old('type', '') == 'wardrobe'       ? 'selected' : '' }}>🚪 ตู้เสื้อผ้า</option>
                    <option value="dressing_table" {{ old('type', '') == 'dressing_table' ? 'selected' : '' }}>💄 โต๊ะเครื่องแป้ง</option>
                    <option value="tv_stand"       {{ old('type', '') == 'tv_stand'       ? 'selected' : '' }}>📺 ชั้นวางทีวี</option>
                    <option value="clothes_rack"   {{ old('type', '') == 'clothes_rack'   ? 'selected' : '' }}>👔 ราวแขวนผ้า</option>
                </select>
                @error('type')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>
 
            <div class="form-group">
                <label for="location">ที่ตั้ง/หมายเหตุเพิ่มเติม <span class="required"></span></label>
                <input type="text" id="location" name="location" value="{{ old('location') }}" placeholder="เช่น มุมซ้ายห้องนอน, ใกล้หน้าต่าง" required>
                @error('location')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>
 
            <div class="form-group">
                <label for="description">คำอธิบาย</label>
                <textarea id="description" name="description">{{ old('description') }}</textarea>
            </div>
 
            <div class="form-group">
                <label for="status">สถานะ <span class="required"></span></label>
                <select name="status" id="status" required>
                    <option value="good"         {{ old('status', 'good') == 'good'         ? 'selected' : '' }}>✅ ใช้งานได้</option>
                    <option value="fair"         {{ old('status', '')     == 'fair'         ? 'selected' : '' }}>🟡 สภาพปานกลาง</option>
                    <option value="needs_repair" {{ old('status', '')     == 'needs_repair' ? 'selected' : '' }}>⚠️ ต้องซ่อม</option>
                    <option value="maintenance"  {{ old('status', '')     == 'maintenance'  ? 'selected' : '' }}>🔧 กำลังซ่อมบำรุง</option>
                    <option value="damaged"      {{ old('status', '')     == 'damaged'      ? 'selected' : '' }}>❌ ชำรุด</option>
                    <option value="retired"      {{ old('status', '')     == 'retired'      ? 'selected' : '' }}>🗑️ ปลดประจำการ</option>
                </select>
                @error('status')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>
 
            <div class="form-group">
                <label for="maintenance_schedule">ตารางบำรุงรักษา</label>
                <input type="text" id="maintenance_schedule" name="maintenance_schedule" value="{{ old('maintenance_schedule') }}" placeholder="เช่น ทุก 3 เดือน">
            </div>
 
            <div class="form-group">
                <label for="last_maintenance_date">วันที่บำรุงรักษาล่าสุด</label>
                <input type="date" id="last_maintenance_date" name="last_maintenance_date" value="{{ old('last_maintenance_date') }}">
            </div>
 
            <div class="form-group">
                <label for="next_maintenance_date">วันที่บำรุงรักษาครั้งถัดไป</label>
                <input type="date" id="next_maintenance_date" name="next_maintenance_date" value="{{ old('next_maintenance_date') }}">
            </div>
 
            <div class="btn-group">
                <button type="submit" class="btn btn-submit">💾 บันทึก</button>
                <a href="{{ route('facilities.index') }}" class="btn btn-cancel">❌ ยกเลิก</a>
            </div>
        </form>
    </div>
</body>
</html>
