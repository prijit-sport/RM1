<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เพิ่มมิเตอร์</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f0f2f5; }
 
        .container { max-width: 680px; margin: 40px auto; padding: 20px; }
 
        /* ── Header ── */
        .page-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 24px;
        }
        .back-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 36px; height: 36px;
            border-radius: 8px;
            background: white;
            border: 1px solid #ddd;
            color: #555;
            text-decoration: none;
            font-size: 1.1em;
            box-shadow: 0 1px 3px rgba(0,0,0,0.08);
            transition: background 0.2s;
        }
        .back-btn:hover { background: #f8f9fa; }
        .page-title { font-size: 1.3em; font-weight: 700; color: #222; }
        .page-subtitle { font-size: 0.85em; color: #888; margin-top: 2px; }
 
        /* ── Type Selector Card ── */
        .type-selector-card {
            background: white;
            border-radius: 14px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
            padding: 20px;
            margin-bottom: 20px;
        }
 
        .type-selector-title {
            font-weight: 600;
            margin-bottom: 14px;
            color: #444;
            font-size: 0.95em;
            display: flex;
            align-items: center;
            gap: 6px;
        }
 
        .type-buttons {
            display: flex;
            gap: 12px;
        }
 
        .type-btn {
            flex: 1;
            padding: 16px;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 8px;
            background: white;
        }
 
        .type-btn input[type="radio"] {
            display: none;
        }
 
        .type-btn:hover {
            border-color: #667eea;
            background: #f8faff;
            transform: translateY(-2px);
        }
 
        .type-btn input[type="radio"]:checked + label {
            color: #667eea;
        }
 
        .type-btn input[type="radio"]:checked ~ * {
            color: #667eea;
        }
 
        .type-btn.active {
            border-color: #667eea;
            background: #f0f4ff;
            box-shadow: 0 2px 8px rgba(102,126,234,0.15);
        }
 
        .type-icon {
            font-size: 1.8em;
        }
 
        .type-label {
            font-weight: 600;
            color: #333;
            font-size: 0.95em;
        }
 
        .type-meter {
            font-size: 0.75em;
            color: #999;
            margin-top: 2px;
        }
 
        /* ── Card ── */
        .card {
            background: white;
            border-radius: 14px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
            overflow: hidden;
        }
 
        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px 24px;
            color: white;
        }
 
        .card-header h5 {
            font-size: 1.1em;
            font-weight: 600;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }
 
        /* ── Form ── */
        .card-body {
            padding: 24px;
        }
 
        .form-group { margin-bottom: 18px; }
        .form-label {
            display: block;
            margin-bottom: 7px;
            font-weight: 600;
            font-size: 0.9em;
            color: #444;
        }
        .form-label .required { color: #e53e3e; }
        .form-control {
            width: 100%;
            padding: 11px 14px;
            border: 1.5px solid #e2e8f0;
            border-radius: 8px;
            font-size: 0.95em;
            font-family: inherit;
            color: #333;
            transition: border-color 0.2s, box-shadow 0.2s;
            background: #fafafa;
        }
        .form-control:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102,126,234,0.12);
            background: white;
        }
        textarea.form-control { resize: vertical; }
 
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
 
        /* ── Alert ── */
        .alert {
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 18px;
            font-size: 0.88em;
            display: flex;
            align-items: flex-start;
            gap: 8px;
        }
        .alert-warning { background: #fffbeb; border: 1px solid #fcd34d; color: #92400e; }
        .alert-error   { color: #c53030; font-size: 0.85em; margin-top: 5px; }
 
        /* ── Buttons ── */
        .btn-group { display: flex; gap: 10px; margin-top: 24px; }
        .btn {
            flex: 1;
            padding: 12px 16px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 0.95em;
            font-weight: 600;
            font-family: inherit;
            transition: all 0.2s;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }
        .btn-primary { background: #667eea; color: white; }
        .btn-primary:hover { background: #5a6fd6; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(102,126,234,0.35); }
        .btn-secondary { background: #f1f5f9; color: #555; border: 1px solid #e2e8f0; }
        .btn-secondary:hover { background: #e2e8f0; }
        .btn-sm { flex: 0; padding: 10px 18px; }
    </style>
</head>
<body>
<div class="container">
 
    {{-- Header --}}
    <div class="page-header">
        <a href="{{ route('meters.index') }}" class="back-btn">←</a>
        <div>
            <div class="page-title">เพิ่มมิเตอร์</div>
            <div class="page-subtitle">บันทึกข้อมูลมิเตอร์ใหม่</div>
        </div>
    </div>
 
    {{-- ✅ Type Selector Card --}}
    <div class="type-selector-card">
        <div class="type-selector-title">
            🔀 เลือกประเภทมิเตอร์
        </div>
        <div class="type-buttons">
            <label class="type-btn" id="electric-btn">
                <input type="radio" name="type" value="electric" {{ old('type') == 'electric' ? 'checked' : '' }} onchange="setTypeActive()">
                <div class="type-icon">⚡</div>
                <div class="type-label">ไฟฟ้า</div>
            </label>
 
            <label class="type-btn" id="water-btn">
                <input type="radio" name="type" value="water" {{ old('type') == 'water' ? 'checked' : '' }} onchange="setTypeActive()">
                <div class="type-icon">💧</div>
                <div class="type-label">น้ำประปา</div>
            </label>
        </div>
    </div>
 
    {{-- Validation Errors --}}
    @if($errors->any())
        <div class="alert alert-warning" style="margin-bottom:16px;">
            ⚠️ กรุณาตรวจสอบข้อมูลที่กรอก
        </div>
    @endif
 
    <div class="card">
 
        {{-- Card Header --}}
        <div class="card-header">
            <h5><i class="bi bi-speedometer2"></i>บันทึกข้อมูลมิเตอร์</h5>
        </div>
 
        {{-- Card Body --}}
        <div class="card-body">
            <form action="{{ route('meters.store') }}" method="POST" id="meter-form">
                @csrf
 
                {{-- ห้องพัก --}}
                <div class="form-group">
                    <label class="form-label" for="room_id">
                        ห้องพัก <span class="required">*</span>
                    </label>
                    <select id="room_id" name="room_id" class="form-control @error('room_id') is-invalid @enderror" required>
                        <option value="">-- เลือกห้องพัก --</option>
                        @foreach($rooms ?? [] as $room)
                            @continue(!$room instanceof \App\Models\Room)
                            <option value="{{ $room->id }}" {{ old('room_id') == $room->id ? 'selected' : '' }}>
                                {{ $room->room_number ?? '—' }} • {{ $room->room_type ?? '—' }} ({{ number_format($room->price_per_month ?? 0, 2) }} ฿/เดือน)
                            </option>
                        @endforeach
                    </select>
                    @error('room_id')
                        <div class="alert-error">{{ $message }}</div>
                    @enderror
                </div>
 
                {{-- หมายเลขมิเตอร์ + หน่วยวัด --}}
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="meter_number">
                            หมายเลขมิเตอร์ <span class="required">*</span>
                        </label>
                        <input id="meter_number" type="text" name="meter_number" class="form-control @error('meter_number') is-invalid @enderror" 
                            value="{{ old('meter_number') }}" required>
                        @error('meter_number')
                            <div class="alert-error">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="unit">หน่วยวัด</label>
                        <input id="unit" type="text" name="unit" class="form-control @error('unit') is-invalid @enderror" 
                            value="{{ old('unit') }}" placeholder="เช่น kWh หรือ m³">
                        @error('unit')
                            <div class="alert-error">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
 
                {{-- วันติดตั้ง + อัตรา --}}
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="installed_at">วันที่ติดตั้ง</label>
                        <input id="installed_at" type="date" name="installed_at" class="form-control @error('installed_at') is-invalid @enderror" 
                            value="{{ old('installed_at') }}">
                        @error('installed_at')
                            <div class="alert-error">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="rate_per_unit">
                            อัตรา (บาท/หน่วย) <span class="required">*</span>
                        </label>
                        <input id="rate_per_unit" type="number" step="0.01" name="rate_per_unit" 
                            class="form-control @error('rate_per_unit') is-invalid @enderror" 
                            value="{{ old('rate_per_unit', 0) }}" required>
                        @error('rate_per_unit')
                            <div class="alert-error">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
 
                {{-- ภาษี + สถานะ --}}
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="tax_rate">
                            ภาษี (%) <span class="required">*</span>
                        </label>
                        <input id="tax_rate" type="number" step="0.01" name="tax_rate" 
                            class="form-control @error('tax_rate') is-invalid @enderror" 
                            value="{{ old('tax_rate', 0) }}" required>
                        @error('tax_rate')
                            <div class="alert-error">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="is_active">สถานะ</label>
                        <select id="is_active" name="is_active" class="form-control @error('is_active') is-invalid @enderror">
                            <option value="1" {{ old('is_active', '1') == '1' ? 'selected' : '' }}>✓ ใช้งาน</option>
                            <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>✗ ระงับใช้งาน</option>
                        </select>
                        @error('is_active')
                            <div class="alert-error">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
 
                {{-- หมายเหตุ --}}
                <div class="form-group">
                    <label class="form-label" for="notes">หมายเหตุเพิ่มเติม</label>
                    <textarea id="notes" name="notes" class="form-control @error('notes') is-invalid @enderror" 
                        rows="3" placeholder="รายละเอียดเพิ่มเติม...">{{ old('notes') }}</textarea>
                    @error('notes')
                        <div class="alert-error">{{ $message }}</div>
                    @enderror
                </div>
 
                {{-- Buttons --}}
                <div class="btn-group">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle"></i> บันทึกข้อมูล
                    </button>
                    <a href="{{ route('meters.index') }}" class="btn btn-secondary btn-sm">ยกเลิก</a>
                </div>
            </form>
        </div>
 
    </div>{{-- end card --}}
</div>
 
<script>
    // ── Set active type on load ──
    function setTypeActive() {
        const electricBtn = document.getElementById('electric-btn');
        const waterBtn = document.getElementById('water-btn');
        const typeValue = document.querySelector('input[name="type"]:checked')?.value;
 
        electricBtn.classList.toggle('active', typeValue === 'electric');
        waterBtn.classList.toggle('active', typeValue === 'water');
    }
 
    // ── Initialize on page load ──
    document.addEventListener('DOMContentLoaded', setTypeActive);
</script>
 
</body>
</html>
 