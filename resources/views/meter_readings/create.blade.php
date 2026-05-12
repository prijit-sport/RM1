<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>บันทึกเลขมิเตอร์</title>
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
 
        /* ── Card ── */
        .card {
            background: white;
            border-radius: 14px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
            overflow: hidden;
        }
 
        /* ── Meter Info Banner ── */
        .meter-banner {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 18px 24px;
            display: flex;
            gap: 24px;
            flex-wrap: wrap;
        }
        .meter-info-item { color: white; }
        .meter-info-item .label { font-size: 0.75em; opacity: 0.8; margin-bottom: 2px; }
        .meter-info-item .value { font-size: 1em; font-weight: 700; }
        .badge-type {
            display: inline-block;
            padding: 2px 10px;
            border-radius: 20px;
            font-size: 0.8em;
            font-weight: 600;
        }
        .badge-electric { background: rgba(255,200,0,0.3); color: #fff5b0; }
        .badge-water    { background: rgba(100,200,255,0.3); color: #c0eeff; }
 
        /* ── Tabs ── */
        .tabs {
            display: flex;
            border-bottom: 2px solid #f0f0f0;
            background: #fafafa;
        }
        .tab-btn {
            flex: 1;
            padding: 14px 16px;
            border: none;
            background: none;
            cursor: pointer;
            font-size: 0.9em;
            font-weight: 600;
            color: #999;
            border-bottom: 3px solid transparent;
            margin-bottom: -2px;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }
        .tab-btn:hover { color: #667eea; background: #f5f5ff; }
        .tab-btn.active { color: #667eea; border-bottom-color: #667eea; background: white; }
 
        /* ── Tab Content ── */
        .tab-content { display: none; padding: 24px; }
        .tab-content.active { display: block; }
 
        /* ── Form ── */
        .form-group { margin-bottom: 18px; }
        .form-label {
            display: block;
            margin-bottom: 7px;
            font-weight: 600;
            font-size: 0.9em;
            color: #444;
        }
        .form-label span { color: #e53e3e; }
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
 
        /* ── Invoice Preview ── */
        .invoice-preview {
            background: #f8f9ff;
            border: 1.5px dashed #c3caf5;
            border-radius: 10px;
            padding: 16px 18px;
            margin-bottom: 18px;
        }
        .invoice-preview-title {
            font-size: 0.8em;
            font-weight: 700;
            color: #667eea;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .invoice-preview-row {
            display: flex;
            justify-content: space-between;
            font-size: 0.88em;
            color: #666;
            padding: 3px 0;
        }
        .invoice-preview-row.total {
            border-top: 1px solid #dde0f5;
            margin-top: 8px;
            padding-top: 8px;
            font-weight: 700;
            font-size: 0.95em;
            color: #333;
        }
 
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
        .alert-info    { background: #eff6ff; border: 1px solid #93c5fd; color: #1e40af; }
        .alert-error   { color: #c53030; font-size: 0.85em; margin-top: 5px; }
 
        /* ── Buttons ── */
        .btn-group { display: flex; gap: 10px; margin-top: 6px; }
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
        .btn-success { background: #38a169; color: white; }
        .btn-success:hover { background: #2f855a; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(56,161,105,0.35); }
        .btn-secondary { background: #f1f5f9; color: #555; border: 1px solid #e2e8f0; }
        .btn-secondary:hover { background: #e2e8f0; }
        .btn-sm { flex: 0; padding: 10px 18px; }
    </style>
</head>
<body>
<div class="container">
 
    {{-- Header --}}
    <div class="page-header">
        <a href="{{ route('meters.readings.index', $meter) }}" class="back-btn">←</a>
        <div>
            <div class="page-title">บันทึกเลขมิเตอร์</div>
            <div class="page-subtitle">ห้อง {{ $meter->room->room_number ?? '-' }} · มิเตอร์ {{ $meter->meter_number }}</div>
        </div>
    </div>
 
    {{-- Validation Errors (global) --}}
    @if($errors->any())
        <div class="alert alert-warning" style="margin-bottom:16px;">
            ⚠️ กรุณาตรวจสอบข้อมูลที่กรอก
        </div>
    @endif
 
    <div class="card">
 
        {{-- Meter Banner --}}
        <div class="meter-banner">
            <div class="meter-info-item">
                <div class="label">ห้องพัก</div>
                <div class="value">{{ $meter->room->room_number ?? '-' }}</div>
            </div>
            <div class="meter-info-item">
                <div class="label">หมายเลขมิเตอร์</div>
                <div class="value">{{ $meter->meter_number }}</div>
            </div>
            <div class="meter-info-item">
                <div class="label">ประเภท</div>
                <div class="value">
                    <span class="badge-type {{ $meter->type === 'electric' ? 'badge-electric' : 'badge-water' }}">
                        {{ $meter->type === 'electric' ? '⚡ ไฟฟ้า' : '💧 น้ำประปา' }}
                    </span>
                </div>
            </div>
            <div class="meter-info-item">
                <div class="label">อัตราค่าบริการ</div>
                <div class="value">{{ number_format($meter->rate_per_unit ?? 0, 2) }} ฿/หน่วย</div>
            </div>
        </div>
 
        {{-- Tabs --}}
        <div class="tabs">
            <button class="tab-btn active" onclick="switchTab('general', this)">
                📝 บันทึกทั่วไป
            </button>
            <button class="tab-btn" onclick="switchTab('monthly', this)">
                📋 รายเดือน + ใบแจ้งหนี้
            </button>
        </div>
 
        {{-- ════ TAB 1: บันทึกทั่วไป ════ --}}
        <div id="tab-general" class="tab-content active">
 
            <div class="alert alert-info">
                ℹ️ บันทึกเลขมิเตอร์ทั่วไป — ไม่สร้างใบแจ้งหนี้อัตโนมัติ
            </div>
 
            <form action="{{ route('meters.readings.store', $meter) }}" method="POST">
                @csrf
 
                <div class="form-group">
                    <label class="form-label" for="reading_date">วันที่ <span>*</span></label>
                    <input class="form-control" id="reading_date" name="reading_date"
                        type="date" value="{{ old('reading_date') }}" required>
                    @error('reading_date')
                        <div class="alert-error">{{ $message }}</div>
                    @enderror
                </div>
 
                <div class="form-group">
                    <label class="form-label" for="reading_value">เลขมิเตอร์ <span>*</span></label>
                    <input class="form-control" id="reading_value" name="reading_value"
                        type="number" step="0.01" min="0"
                        value="{{ old('reading_value') }}"
                        placeholder="เช่น 1234.00" required>
                    @error('reading_value')
                        <div class="alert-error">{{ $message }}</div>
                    @enderror
                </div>
 
                <div class="form-group">
                    <label class="form-label" for="notes">หมายเหตุ</label>
                    <textarea class="form-control" id="notes" name="notes"
                        rows="3" placeholder="รายละเอียดเพิ่มเติม...">{{ old('notes') }}</textarea>
                    @error('notes')
                        <div class="alert-error">{{ $message }}</div>
                    @enderror
                </div>
 
                <div class="btn-group">
                    <button type="submit" class="btn btn-primary">✓ บันทึก</button>
                    <a href="{{ route('meters.readings.index', $meter) }}" class="btn btn-secondary btn-sm">ยกเลิก</a>
                </div>
            </form>
        </div>
 
        {{-- ════ TAB 2: บันทึกรายเดือน + Invoice ════ --}}
        <div id="tab-monthly" class="tab-content">
 
            <div class="alert alert-warning">
                💡 บันทึกเลขมิเตอร์ประจำเดือน และ<strong>สร้างใบแจ้งหนี้ค่าน้ำ/ไฟอัตโนมัติ</strong>
            </div>
 
            <form action="{{ route('meters.readings.monthly', $meter) }}" method="POST"
                  id="monthly-form">
                @csrf
 
                {{-- ช่วงเดือน --}}
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="period_month">เดือน <span>*</span></label>
                        <select class="form-control" id="period_month" name="period_month"
                                onchange="updatePreview()" required>
                            @foreach(range(1,12) as $m)
                                <option value="{{ $m }}"
                                    {{ (old('period_month', now()->month) == $m) ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::create()->month($m)->locale('th')->monthName }}
                                </option>
                            @endforeach
                        </select>
                        @error('period_month')
                            <div class="alert-error">{{ $message }}</div>
                        @enderror
                    </div>
 
                    <div class="form-group">
                        <label class="form-label" for="period_year">ปี (พ.ศ.) <span>*</span></label>
                        <select class="form-control" id="period_year" name="period_year"
                                onchange="updatePreview()" required>
                            @for($y = now()->year; $y >= now()->year - 3; $y--)
                                <option value="{{ $y }}"
                                    {{ (old('period_year', now()->year) == $y) ? 'selected' : '' }}>
                                    {{ $y + 543 }} ({{ $y }})
                                </option>
                            @endfor
                        </select>
                        @error('period_year')
                            <div class="alert-error">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
 
                {{-- เลขมิเตอร์ --}}
                <div class="form-group">
                    <label class="form-label" for="m_reading_value">เลขมิเตอร์ที่อ่านได้ <span>*</span></label>
                    <input class="form-control" id="m_reading_value" name="reading_value"
                        type="number" step="0.01" min="0"
                        value="{{ old('reading_value') }}"
                        placeholder="เช่น 1234.00"
                        oninput="updatePreview()" required>
                    @error('reading_value')
                        <div class="alert-error">{{ $message }}</div>
                    @enderror
                </div>
 
                {{-- Invoice Preview --}}
                <div class="invoice-preview" id="invoice-preview" style="display:none;">
                    <div class="invoice-preview-title">📄 ตัวอย่างใบแจ้งหนี้</div>
                    <div class="invoice-preview-row">
                        <span>เลขมิเตอร์ก่อนหน้า</span>
                        <span id="prev-val">—</span>
                    </div>
                    <div class="invoice-preview-row">
                        <span>เลขมิเตอร์ปัจจุบัน</span>
                        <span id="curr-val">—</span>
                    </div>
                    <div class="invoice-preview-row">
                        <span>จำนวนหน่วยที่ใช้</span>
                        <span id="usage-val">—</span>
                    </div>
                    <div class="invoice-preview-row">
                        <span>อัตรา</span>
                        <span>{{ number_format($meter->rate_per_unit ?? 0, 2) }} ฿/หน่วย</span>
                    </div>
                    <div class="invoice-preview-row total">
                        <span>ยอดประมาณ (มิเตอร์นี้)</span>
                        <span id="total-val">—</span>
                    </div>
                </div>
 
                {{-- หมายเหตุ --}}
                <div class="form-group">
                    <label class="form-label" for="m_notes">หมายเหตุ</label>
                    <textarea class="form-control" id="m_notes" name="notes"
                        rows="3" placeholder="หมายเหตุเพิ่มเติม...">{{ old('notes') }}</textarea>
                    @error('notes')
                        <div class="alert-error">{{ $message }}</div>
                    @enderror
                </div>
 
                <div class="btn-group">
                    <button type="submit" class="btn btn-success">
                        📋 บันทึก + สร้างใบแจ้งหนี้
                    </button>
                    <a href="{{ route('meters.readings.index', $meter) }}" class="btn btn-secondary btn-sm">ยกเลิก</a>
                </div>
 
            </form>
        </div>
 
    </div>{{-- end card --}}
</div>
 
<script>
    // ── ข้อมูลจาก Controller ──
    const rate        = {{ (float) ($meter->rate_per_unit ?? 0) }};
    const taxRate     = {{ (float) ($meter->tax_rate ?? 0) }};
    const latestReading = {{ $meter->latestReading?->reading_value ?? 'null' }};
 
    // ── Switch Tab ──
    function switchTab(name, btn) {
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
        btn.classList.add('active');
        document.getElementById('tab-' + name).classList.add('active');
    }
 
    // ── Invoice Preview ──
    function updatePreview() {
        const currVal = parseFloat(document.getElementById('m_reading_value').value);
        if (isNaN(currVal)) {
            document.getElementById('invoice-preview').style.display = 'none';
            return;
        }
 
        const prevVal  = latestReading !== null ? parseFloat(latestReading) : 0;
        const usage    = Math.max(0, currVal - prevVal);
        const base     = usage * rate;
        const tax      = base * (taxRate / 100);
        const total    = base + tax;
 
        document.getElementById('prev-val').textContent   = prevVal.toLocaleString('th-TH', {minimumFractionDigits: 2}) + ' หน่วย';
        document.getElementById('curr-val').textContent   = currVal.toLocaleString('th-TH', {minimumFractionDigits: 2}) + ' หน่วย';
        document.getElementById('usage-val').textContent  = usage.toLocaleString('th-TH', {minimumFractionDigits: 2}) + ' หน่วย';
        document.getElementById('total-val').textContent  = total.toLocaleString('th-TH', {minimumFractionDigits: 2}) + ' ฿';
 
        document.getElementById('invoice-preview').style.display = 'block';
    }
 
    // ── เปิด tab monthly ถ้า validation error จาก monthly form ──
    @if(old('period_month') || old('period_year'))
        document.addEventListener('DOMContentLoaded', () => {
            switchTab('monthly', document.querySelectorAll('.tab-btn')[1]);
        });
    @endif
</script>
 
</body>
</html>
 