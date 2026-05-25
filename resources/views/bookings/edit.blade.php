@extends('layouts.app')
 
@section('content')
<div class="container py-4">
 
    {{-- Header --}}
    <div class="d-flex align-items-center gap-2 mb-4">
        <a href="{{ route('bookings.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left"></i>
        </a>
        <h5 class="mb-0"><i class="bi bi-calendar-check text-warning me-2"></i>แก้ไขการจอง #{{ $booking->id }}</h5>
    </div>
 
    <form action="{{ route('bookings.update', $booking->id) }}" method="POST">
        @csrf
        @method('PUT')
 
        <div class="row g-4">
 
            {{-- ===== คอลัมน์ซ้าย ===== --}}
            <div class="col-lg-6">
 
                {{-- ผู้เช่า --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="mb-0 text-primary">
                            <i class="bi bi-person-circle me-2"></i>ผู้เช่า
                        </h6>
                    </div>
                    <div class="card-body">
                        <label class="form-label fw-semibold">เลือกผู้เช่า <span class="text-danger">*</span></label>
                        <select name="guest_id" id="guest_id"
                            class="form-select @error('guest_id') is-invalid @enderror" required>
                            <option value="">-- เลือกผู้เช่า --</option>
                            @foreach($guests as $guest)
                                <option value="{{ $guest->id }}" {{ $booking->guest_id == $guest->id ? 'selected' : '' }}>
                                    {{ $guest->first_name }} {{ $guest->last_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('guest_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
 
                {{-- ข้อมูลห้องพัก --}}
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="mb-0 text-primary">
                            <i class="bi bi-door-open me-2"></i>ข้อมูลห้องพัก
                        </h6>
                    </div>
                    <div class="card-body">
 
                        {{-- ห้องหมายเลข --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">ห้องหมายเลข <span class="text-danger">*</span></label>
                            <select name="room_id" id="room_select"
                                class="form-select @error('room_id') is-invalid @enderror" required>
                                <option value="">-- เลือกห้องพัก --</option>
                                @foreach($rooms as $room)
                                    <option value="{{ $room->id }}" 
                                        data-price="{{ $room->price_per_month }}"
                                        data-type="{{ $room->room_type }}"
                                        {{ $booking->room_id == $room->id ? 'selected' : '' }}>
                                        {{ $room->room_number }} — {{ $room->room_type }} ({{ number_format($room->price_per_month) }} ฿/เดือน)
                                    </option>
                                @endforeach
                            </select>
                            @error('room_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
 
                        {{-- ประเภทห้อง (auto-fill) --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">ประเภทห้อง</label>
                            <input type="text" id="room_type_display" class="form-control bg-light"
                                placeholder="—" readonly>
                        </div>
 
                        {{-- วันที่เข้าพัก --}}
                        <div class="mb-0">
                            <label class="form-label fw-semibold">วันที่เข้าพัก <span class="text-danger">*</span></label>
                            <input type="date" name="check_in_date" id="check_in_date"
                                class="form-control @error('check_in_date') is-invalid @enderror"
                                value="{{ $booking->check_in_date ? $booking->check_in_date->format('Y-m-d') : '' }}" required>
                            @error('check_in_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
 
                    </div>
                </div>
 
            </div>{{-- end col-left --}}
 
 
            {{-- ===== คอลัมน์ขวา ===== --}}
            <div class="col-lg-6">
 
                {{-- มัดจำและค่าใช้จ่ายแรกเข้า --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="mb-0 text-primary">
                            <i class="bi bi-cash-coin me-2"></i>มัดจำและค่าใช้จ่ายแรกเข้า
                        </h6>
                    </div>
                    <div class="card-body">
 
                        <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                            <div>
                                <div class="fw-semibold">ค่าเช่าเดือนแรก</div>
                                <small class="text-muted">ห้องที่เลือก</small>
                            </div>
                            <div class="text-end">
                                <span id="display_rent" class="fw-bold fs-5 text-dark">{{ number_format($booking->rent_amount ?? 0) }}</span>
                                <span class="text-muted ms-1">฿</span>
                            </div>
                        </div>
 
                        <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                            <div>
                                <div class="fw-semibold">เงินมัดจำ</div>
                                <small class="text-muted">1 เดือน</small>
                            </div>
                            <div class="text-end">
                                <span id="display_deposit" class="fw-bold fs-5 text-dark">{{ number_format($booking->deposit_amount ?? 0) }}</span>
                                <span class="text-muted ms-1">฿</span>
                            </div>
                        </div>
 
                        <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                            <div>
                                <div class="fw-semibold">ค่าน้ำ (มิเตอร์เริ่มต้น)</div>
                                <small class="text-muted">บันทึกเลขมิเตอร์</small>
                            </div>
                            <div class="text-end">
                                <span class="fw-bold fs-5 text-muted">—</span>
                                <span class="text-muted ms-1">฿</span>
                            </div>
                        </div>
 
                        <div class="d-flex justify-content-between align-items-center pt-3 mt-1">
                            <div class="fw-bold fs-6">ยอดรวมที่ต้องชำระ</div>
                            <div class="text-end">
                                <span id="display_total" class="fw-bold text-primary" style="font-size:1.5rem">{{ number_format(($booking->rent_amount ?? 0) + ($booking->deposit_amount ?? 0)) }}</span>
                                <span class="text-primary ms-1 fw-semibold">฿</span>
                            </div>
                        </div>
 
                        <input type="hidden" name="rent_amount" id="rent_amount" value="{{ $booking->rent_amount }}">
                        <input type="hidden" name="deposit_amount" id="deposit_amount" value="{{ $booking->deposit_amount }}">
 
                    </div>
                </div>
 
                {{-- เลขมิเตอร์เริ่มต้น --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="mb-0 text-primary">
                            <i class="bi bi-speedometer2 me-2"></i>เลขมิเตอร์เริ่มต้น
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-6">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-lightning-charge text-warning me-1"></i>มิเตอร์ไฟ (หน่วย)
                                </label>
                                <input type="number" name="electric_meter_start" id="electric_meter_start"
                                    class="form-control @error('electric_meter_start') is-invalid @enderror"
                                    placeholder="0000" min="0" value="{{ $booking->electric_meter_start ?? 0 }}">
                                @error('electric_meter_start')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-droplet text-info me-1"></i>มิเตอร์น้ำ (หน่วย)
                                </label>
                                <input type="number" name="water_meter_start" id="water_meter_start"
                                    class="form-control @error('water_meter_start') is-invalid @enderror"
                                    placeholder="0000" min="0" value="{{ $booking->water_meter_start ?? 0 }}">
                                @error('water_meter_start')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
 
                {{-- สถานะ + หมายเหตุ --}}
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
 
                        <div class="mb-3">
                            <label class="form-label fw-semibold">สถานะการจอง</label>
                            <select name="status" class="form-select @error('status') is-invalid @enderror">
                                <option value="pending"   {{ $booking->status == 'pending'   ? 'selected' : '' }}>รอยืนยัน</option>
                                <option value="confirmed" {{ $booking->status == 'confirmed' ? 'selected' : '' }}>ยืนยันแล้ว</option>
                                <option value="checked_in" {{ $booking->status == 'checked_in' ? 'selected' : '' }}>เช็คอินแล้ว</option>
                                <option value="checked_out" {{ $booking->status == 'checked_out' ? 'selected' : '' }}>เช็คเอาท์แล้ว</option>
                                <option value="cancelled" {{ $booking->status == 'cancelled' ? 'selected' : '' }}>ยกเลิก</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
 
                        <div class="mb-0">
                            <label class="form-label fw-semibold">หมายเหตุ</label>
                            <textarea name="notes" class="form-control" rows="3"
                                placeholder="บันทึกเพิ่มเติม...">{{ $booking->notes }}</textarea>
                        </div>
 
                    </div>
                </div>
 
            </div>{{-- end col-right --}}
 
        </div>{{-- end row --}}
 
        {{-- Action Buttons --}}
        <div class="d-flex justify-content-between mt-4">
            <a href="{{ route('bookings.show', $booking) }}" class="btn btn-outline-secondary px-4">
                <i class="bi bi-x-circle me-1"></i> ยกเลิก
            </a>
            <button type="submit" class="btn btn-warning px-5">
                <i class="bi bi-check-circle me-1"></i> บันทึกการแก้ไข
            </button>
        </div>
 
    </form>
</div>
@endsection
 
 
@push('scripts')
<script>
    // ข้อมูลห้องทั้งหมดจาก Controller (JSON)
    const allRooms = @json($rooms);
 
    const roomSelect     = document.getElementById('room_select');
    const roomTypeInput  = document.getElementById('room_type_display');
    const displayRent    = document.getElementById('display_rent');
    const displayDeposit = document.getElementById('display_deposit');
    const displayTotal   = document.getElementById('display_total');
    const rentInput      = document.getElementById('rent_amount');
    const depositInput   = document.getElementById('deposit_amount');
 
    // ─── เมื่อเลือกห้อง → คำนวณค่าใช้จ่าย ───
    roomSelect.addEventListener('change', function () {
        const selected = this.options[this.selectedIndex];
        if (!this.value) { resetPrices(); return; }
 
const price   = parseFloat(selected.dataset.price) || 0;
        const deposit = price * 1;
        const total   = price + deposit;
 
        roomTypeInput.value        = selected.dataset.type || '';
        displayRent.textContent    = price.toLocaleString();
        displayDeposit.textContent = deposit.toLocaleString();
        displayTotal.textContent   = total.toLocaleString();
        rentInput.value            = price;
        depositInput.value         = deposit;
    });
 
    function resetPrices() {
        displayRent.textContent    = '—';
        displayDeposit.textContent = '—';
        displayTotal.textContent   = '—';
        rentInput.value            = '';
        depositInput.value         = '';
    }
 
    // ─── Auto-trigger room selection on page load ───
    if (roomSelect.value) {
        roomSelect.dispatchEvent(new Event('change'));
    }
</script>
@endpush
 