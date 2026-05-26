<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แก้ไขสิ่งอำนวยความสะดวก</title>
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
        .info-box { background: #e8f4f8; border-left: 4px solid #0284c7; padding: 12px 15px; border-radius: 5px; margin-bottom: 20px; font-size: 0.9em; color: #075985; }
        .two-cols { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
        @media (max-width: 600px) { .two-cols { grid-template-columns: 1fr; } }
        select:disabled { background-color: #f0f0f0; cursor: not-allowed; color: #666; }
    </style>
</head>
<body>
    <div class="container">
        <h1>✏️ แก้ไขสิ่งอำนวยความสะดวก</h1>
        
        @if ($errors->any())
            <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif
 
        <form method="POST" action="{{ route('facilities.update', $facility->id) }}">
            @csrf
            @method('PUT')
 
            <!-- ✅ Lock Dropdown Room -->
            <div class="form-group">
                <label for="room_id">ห้องพัก <span class="required"></span></label>
                <select name="room_id" id="room_id" disabled required>
                    <option value="">-- เลือกห้องพัก --</option>
                    @foreach($rooms as $room)
                        <option value="{{ $room->id }}" 
                            {{ old('room_id', $facility->room_id) == $room->id ? 'selected' : '' }}>
                            ห้อง {{ $room->room_number }} ({{ $room->room_type }})
                        </option>
                    @endforeach
                </select>
                <div class="info-box">🔒 ล็อค: ไม่สามารถเปลี่ยนห้องได้ (ติดต่อแอดมินหากต้องการเปลี่ยน)</div>
                {{-- ✅ Hidden input เพื่อส่งค่า room_id --}}
                <input type="hidden" name="room_id" value="{{ $facility->room_id }}">
                @error('room_id')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>
 
            <div class="form-group">
                <label for="name">ชื่อ <span class="required"></span></label>
                <input type="text" id="name" name="name" value="{{ old('name', $facility->name) }}" required>
                @error('name')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>
 
            <div class="form-group">
                <label for="type">ประเภท <span class="required"></span></label>
                <select name="type" id="type" required>
                    <option value="">-- เลือกประเภท --</option>
                    @php $currentType = old('type', $facility->type); @endphp
                    <option value="bed"            {{ $currentType == 'bed'            ? 'selected' : '' }}>🛏️ เตียง</option>
                    <option value="mattress"       {{ $currentType == 'mattress'       ? 'selected' : '' }}>🛌 ที่นอน</option>
                    <option value="wardrobe"       {{ $currentType == 'wardrobe'       ? 'selected' : '' }}>🚪 ตู้เสื้อผ้า</option>
                    <option value="dressing_table" {{ $currentType == 'dressing_table' ? 'selected' : '' }}>💄 โต๊ะเครื่องแป้ง</option>
                    <option value="tv_stand"       {{ $currentType == 'tv_stand'       ? 'selected' : '' }}>📺 ชั้นวางทีวี</option>
                    <option value="clothes_rack"   {{ $currentType == 'clothes_rack'   ? 'selected' : '' }}>👔 ราวแขวนผ้า</option>
                </select>
                @error('type')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>
 
            <!-- ✅ แปลง location → ชั้นและโซน (auto-display) -->
            <div class="two-cols">
                <div class="form-group">
                    <label for="floor">ชั้น</label>
                    <input type="text" id="floor" name="floor" 
                        value="{{ old('floor', $facility->room?->floor ?? '') }}" 
                        placeholder="ชั้น" readonly style="background-color: #f0f0f0; cursor: not-allowed;">
                </div>
                <div class="form-group">
                    <label for="zone">โซน</label>
                    <input type="text" id="zone" name="zone" 
                        value="{{ old('zone', $facility->room?->zone ?? '') }}" 
                        placeholder="โซน" readonly style="background-color: #f0f0f0; cursor: not-allowed;">
                </div>
            </div>
 
            <div class="form-group">
                <label for="location">หมายเหตุเพิ่มเติม</label>
                <input type="text" id="location" name="location" value="{{ old('location', $facility->location) }}" placeholder="เช่น มุมซ้ายห้องนอน, ใกล้หน้าต่าง">
                @error('location')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>
 
            <div class="form-group">
                <label for="description">คำอธิบาย</label>
                <textarea id="description" name="description">{{ old('description', $facility->description) }}</textarea>
            </div>
 
            <div class="form-group">
                <label for="status">สถานะ <span class="required"></span></label>
                <select name="status" id="status" required>
                    @php $currentStatus = old('status', $facility->status); @endphp
                    <option value="good"         {{ $currentStatus == 'good'         ? 'selected' : '' }}>✅ ใช้งานได้</option>
                    <option value="fair"         {{ $currentStatus == 'fair'         ? 'selected' : '' }}>🟡 สภาพปานกลาง</option>
                    <option value="needs_repair" {{ $currentStatus == 'needs_repair' ? 'selected' : '' }}>⚠️ ต้องซ่อม</option>
                    <option value="maintenance"  {{ $currentStatus == 'maintenance'  ? 'selected' : '' }}>🔧 กำลังซ่อมบำรุง</option>
                    <option value="damaged"      {{ $currentStatus == 'damaged'      ? 'selected' : '' }}>❌ ชำรุด</option>
                    <option value="retired"      {{ $currentStatus == 'retired'      ? 'selected' : '' }}>🗑️ ปลดประจำการ</option>
                </select>
                @error('status')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>
 
            <div class="form-group">
                <label for="maintenance_schedule">ตารางบำรุงรักษา</label>
                <input type="text" id="maintenance_schedule" name="maintenance_schedule" value="{{ old('maintenance_schedule', $facility->maintenance_schedule) }}" placeholder="เช่น ทุก 3 เดือน">
            </div>
 
            <div class="form-group">
                <label for="last_maintenance_date">วันที่บำรุงรักษาล่าสุด</label>
                <input type="date" id="last_maintenance_date" name="last_maintenance_date" 
                    value="{{ old('last_maintenance_date', $facility->last_maintenance_date ? \Carbon\Carbon::parse($facility->last_maintenance_date)->format('Y-m-d') : '') }}">
            </div>
 
            <div class="form-group">
                <label for="next_maintenance_date">วันที่บำรุงรักษาครั้งถัดไป</label>
                <input type="date" id="next_maintenance_date" name="next_maintenance_date" 
                    value="{{ old('next_maintenance_date', $facility->next_maintenance_date ? \Carbon\Carbon::parse($facility->next_maintenance_date)->format('Y-m-d') : '') }}">
            </div>
 
            <div class="btn-group">
                <button type="submit" class="btn btn-submit">💾 บันทึก</button>
                <a href="{{ route('facilities.show', $facility->id) }}" class="btn btn-cancel">❌ ยกเลิก</a>
            </div>
        </form>
    </div>
</body>
</html>
 