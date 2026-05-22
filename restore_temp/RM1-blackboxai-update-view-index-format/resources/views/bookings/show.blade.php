<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายละเอียดการจอง</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f5f5f5; }
 
        .container { max-width: 860px; margin: 40px auto; padding: 20px; }
 
        .card {
            background: white;
            border-radius: 12px;
            padding: 32px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
 
        .card-title {
            font-size: 1.3em;
            font-weight: 700;
            color: #333;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
 
        /* ── Info Grid ── */
        .info-group {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 14px;
            margin-bottom: 14px;
        }
        .info-item {
            padding: 14px 16px;
            background: #f8f9fa;
            border-radius: 8px;
            border-left: 3px solid #dee2e6;
        }
        .info-item.highlight { border-left-color: #667eea; }
        .info-label { color: #888; font-size: 0.82em; margin-bottom: 4px; }
        .info-value { color: #222; font-size: 1.05em; font-weight: 600; }
 
        /* ── Status Badge ── */
        .status {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.88em;
            font-weight: 600;
            display: inline-block;
        }
        .status.pending   { background: #fff3cd; color: #856404; }
        .status.confirmed { background: #d1ecf1; color: #0c5460; }
        .status.checked_in  { background: #d4edda; color: #155724; }
        .status.checked_out { background: #e2e3e5; color: #383d41; }
        .status.cancelled   { background: #f8d7da; color: #721c24; }
 
        /* ── Notes ── */
        .notes {
            padding: 16px;
            background: #f9f9f9;
            border-radius: 8px;
            line-height: 1.6;
            margin-top: 14px;
            font-size: 0.95em;
            color: #555;
        }
 
        /* ── Buttons ── */
        .btn-group { display: flex; gap: 10px; margin-top: 24px; flex-wrap: wrap; }
        .btn {
            padding: 11px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 0.95em;
            font-weight: 600;
            font-family: inherit;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .btn-primary  { background: #667eea; color: white; }
        .btn-primary:hover  { background: #5a6fd6; }
        .btn-success  { background: #28a745; color: white; }
        .btn-success:hover  { background: #218838; transform: translateY(-1px); }
        .btn-warning  { background: #fd7e14; color: white; }
        .btn-warning:hover  { background: #e8700f; }
        .btn-secondary { background: #6c757d; color: white; }
        .btn-secondary:hover { background: #5a6268; }
        .btn-danger   { background: #dc3545; color: white; }
        .btn-danger:hover   { background: #c82333; }
 
        /* ── Alert ── */
        .alert {
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 0.9em;
        }
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-danger  { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
 
        /* ══════════════════════════════════════
           MODAL
        ══════════════════════════════════════ */
        .modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }
        .modal-overlay.show { display: flex; }
 
        .modal-box {
            background: white;
            border-radius: 14px;
            width: 100%;
            max-width: 480px;
            margin: 20px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.3);
            animation: slideUp 0.25s ease;
        }
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .modal-header {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            padding: 18px 24px;
            border-radius: 14px 14px 0 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .modal-header h5 { font-size: 1.05em; font-weight: 700; }
        .modal-close {
            background: none;
            border: none;
            color: white;
            font-size: 1.4em;
            cursor: pointer;
            opacity: 0.8;
            line-height: 1;
        }
        .modal-close:hover { opacity: 1; }
 
        .modal-body { padding: 24px; }
        .modal-footer {
            padding: 16px 24px;
            border-top: 1px solid #f0f0f0;
            display: flex;
            gap: 10px;
            justify-content: flex-end;
        }
 
        /* ── Form inside modal ── */
        .form-group { margin-bottom: 16px; }
        .form-label {
            display: block;
            font-weight: 600;
            font-size: 0.9em;
            color: #444;
            margin-bottom: 7px;
        }
        .form-label span { color: #e53e3e; }
        .input-group { display: flex; }
        .input-group input {
            flex: 1;
            padding: 10px 14px;
            border: 1.5px solid #e2e8f0;
            border-radius: 8px 0 0 8px;
            font-size: 0.95em;
            font-family: inherit;
            transition: border-color 0.2s;
            background: #fafafa;
        }
        .input-group input:focus {
            outline: none;
            border-color: #667eea;
            background: white;
        }
        .input-group-text {
            padding: 10px 14px;
            background: #f0f0f0;
            border: 1.5px solid #e2e8f0;
            border-left: none;
            border-radius: 0 8px 8px 0;
            font-size: 0.88em;
            color: #666;
            white-space: nowrap;
        }
 
        .meter-info-box {
            background: #e8f4fd;
            border: 1px solid #b8daff;
            border-radius: 8px;
            padding: 12px 16px;
            margin-bottom: 18px;
            font-size: 0.88em;
            color: #004085;
        }
        .meter-info-box strong { display: block; margin-bottom: 4px; }
 
        @media (max-width: 600px) {
            .info-group { grid-template-columns: 1fr; }
            .btn-group { flex-direction: column; }
        }
    </style>
</head>
<body>
<div class="container">
 
    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="alert alert-success">✅ {{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">❌ {{ session('error') }}</div>
    @endif
 
    {{-- ── Card หลัก ── --}}
    <div class="card">
        <div class="card-title">📅 รายละเอียดการจอง #{{ $booking->id }}</div>
 
        {{-- ข้อมูลหลัก --}}
        <div class="info-group">
            <div class="info-item highlight">
                <div class="info-label">ห้องพัก</div>
                <div class="info-value">
                    #{{ $booking->room->room_number ?? '-' }}
                    ({{ $booking->room->room_type ?? '-' }})
                </div>
            </div>
 
            <div class="info-item highlight">
                <div class="info-label">ผู้เช่า</div>
                <div class="info-value">
                    {{ $booking->guest->first_name ?? '-' }} {{ $booking->guest->last_name ?? '' }}
                </div>
            </div>
 
            <div class="info-item">
                <div class="info-label">วันที่เข้าพัก</div>
                <div class="info-value">
                    {{ $booking->check_in_date ? \Carbon\Carbon::parse($booking->check_in_date)->format('d/m/Y') : '-' }}
                </div>
            </div>
 
            <div class="info-item">
                <div class="info-label">สถานะ</div>
                <span class="status {{ $booking->status }}">
                    {{ enum_th('booking_status', $booking->status) }}
                </span>
            </div>
 
            <div class="info-item">
                <div class="info-label">ค่าเช่า/เดือน</div>
                <div class="info-value">฿{{ number_format($booking->rent_amount ?? 0, 2) }}</div>
            </div>
 
            <div class="info-item">
                <div class="info-label">เงินมัดจำ</div>
                <div class="info-value">฿{{ number_format($booking->deposit_amount ?? 0, 2) }}</div>
            </div>
 
            <div class="info-item">
                <div class="info-label">⚡ มิเตอร์ไฟเริ่มต้น</div>
                <div class="info-value">{{ $booking->electric_meter_start ?? 0 }} หน่วย</div>
            </div>
 
            <div class="info-item">
                <div class="info-label">💧 มิเตอร์น้ำเริ่มต้น</div>
                <div class="info-value">{{ $booking->water_meter_start ?? 0 }} หน่วย</div>
            </div>
 
            <div class="info-item">
                <div class="info-label">สร้างเมื่อ</div>
                <div class="info-value">{{ $booking->created_at->format('d/m/Y H:i') }}</div>
            </div>
        </div>
 
        @if($booking->notes)
            <div class="notes">
                <strong>หมายเหตุ:</strong><br>
                {!! nl2br(e($booking->notes)) !!}
            </div>
        @endif
 
        {{-- ── Action Buttons ── --}}
        <div class="btn-group">
            {{-- Confirm (เฉพาะ pending) --}}
            @if($booking->status === 'pending')
                <button type="button" class="btn btn-success"
                        onclick="document.getElementById('confirmModal').classList.add('show')">
                    ✅ ยืนยันการจอง
                </button>
            @endif
 
            {{-- Cancel (เฉพาะ pending/confirmed) --}}
            @if(in_array($booking->status, ['pending', 'confirmed']))
                <form action="{{ route('bookings.cancel', $booking) }}" method="POST"
                      onsubmit="return confirm('ยืนยันการยกเลิกการจองนี้?')">
                    @csrf
                    <button type="submit" class="btn btn-warning">❌ ยกเลิกการจอง</button>
                </form>
            @endif
 
            <a href="{{ route('bookings.edit', $booking) }}" class="btn btn-primary">✏️ แก้ไข</a>
            <a href="{{ route('bookings.index') }}" class="btn btn-secondary">← กลับ</a>
 
            <form action="{{ route('bookings.destroy', $booking) }}" method="POST"
                  onsubmit="return confirm('ลบการจองนี้? ไม่สามารถกู้คืนได้')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">🗑️ ลบ</button>
            </form>
        </div>
    </div>
 
</div>{{-- end container --}}
 
 
{{-- ══════════════════════════════════════════════
     MODAL: ยืนยันการจอง + กรอกอัตรามิเตอร์
══════════════════════════════════════════════ --}}
@if($booking->status === 'pending')
<div class="modal-overlay" id="confirmModal"
     onclick="if(event.target===this) this.classList.remove('show')">
    <div class="modal-box">
 
        <div class="modal-header">
            <h5>✅ ยืนยันการจอง</h5>
            <button class="modal-close"
                    onclick="document.getElementById('confirmModal').classList.remove('show')">✕</button>
        </div>
 
        <form action="{{ route('bookings.confirm', $booking) }}" method="POST">
            @csrf
            <div class="modal-body">
 
                {{-- ข้อมูลสรุป --}}
                <div class="meter-info-box">
                    <strong>ห้อง {{ $booking->room?->room_number }}
                        | {{ $booking->guest?->first_name }} {{ $booking->guest?->last_name }}</strong>
                    มิเตอร์ไฟเริ่มต้น: <strong>{{ $booking->electric_meter_start ?? 0 }}</strong> หน่วย
                    &nbsp;|&nbsp;
                    มิเตอร์น้ำเริ่มต้น: <strong>{{ $booking->water_meter_start ?? 0 }}</strong> หน่วย
                </div>
 
                <p style="color:#666; font-size:0.88em; margin-bottom:18px;">
                    กรอกอัตราค่าน้ำ/ไฟ เพื่อให้ระบบสร้างมิเตอร์และบันทึกเลขเริ่มต้นโดยอัตโนมัติ
                </p>
 
                {{-- ค่าไฟ --}}
                <div class="form-group">
                    <label class="form-label">
                        ⚡ อัตราค่าไฟฟ้า <span>*</span>
                    </label>
                    <div class="input-group">
                        <input type="number" name="electric_rate"
                               step="0.01" min="0" placeholder="7.00"
                               value="{{ old('electric_rate', 7.00) }}" required>
                        <span class="input-group-text">฿ / หน่วย</span>
                    </div>
                </div>
 
                {{-- ค่าน้ำ --}}
                <div class="form-group">
                    <label class="form-label">
                        💧 อัตราค่าน้ำ <span>*</span>
                    </label>
                    <div class="input-group">
                        <input type="number" name="water_rate"
                               step="0.01" min="0" placeholder="18.00"
                               value="{{ old('water_rate', 18.00) }}" required>
                        <span class="input-group-text">฿ / หน่วย</span>
                    </div>
                </div>
 
                {{-- VAT --}}
                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label">
                        % ภาษี / VAT
                        <span style="color:#999; font-weight:400;">(ถ้ามี)</span>
                    </label>
                    <div class="input-group">
                        <input type="number" name="tax_rate"
                               step="0.01" min="0" max="100" placeholder="0"
                               value="{{ old('tax_rate', 0) }}">
                        <span class="input-group-text">%</span>
                    </div>
                </div>
 
            </div>
 
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary"
                        onclick="document.getElementById('confirmModal').classList.remove('show')">
                    ยกเลิก
                </button>
                <button type="submit" class="btn btn-success">
                    ✅ ยืนยันและสร้างมิเตอร์
                </button>
            </div>
        </form>
 
    </div>
</div>
@endif
 
</body>
</html>
 