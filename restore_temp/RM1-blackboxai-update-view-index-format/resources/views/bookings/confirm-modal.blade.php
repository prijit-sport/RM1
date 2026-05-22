{{--
    ไฟล์นี้: resources/views/bookings/_confirm-modal.blade.php
    วิธีใช้: @include('bookings._confirm-modal', ['booking' => $booking])
    ต้องวางไว้ในหน้า bookings/show.blade.php
--}}
 
{{-- ปุ่ม Confirm (แทนที่ปุ่มเดิม) --}}
<button type="button"
    class="btn btn-success"
    data-bs-toggle="modal"
    data-bs-target="#confirmBookingModal">
    <i class="bi bi-check-circle me-1"></i> ยืนยันการจอง
</button>
 
{{-- ═══════════════════════════════════════════ --}}
{{--  Modal กรอกอัตรามิเตอร์                    --}}
{{-- ═══════════════════════════════════════════ --}}
<div class="modal fade" id="confirmBookingModal" tabindex="-1"
     aria-labelledby="confirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
 
            {{-- Header --}}
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="confirmModalLabel">
                    <i class="bi bi-check-circle me-2"></i>ยืนยันการจอง
                </h5>
                <button type="button" class="btn-close btn-close-white"
                        data-bs-dismiss="modal"></button>
            </div>
 
            {{-- Body --}}
            <form action="{{ route('bookings.confirm', $booking) }}" method="POST">
                @csrf
                <div class="modal-body p-4">
 
                    {{-- ข้อมูลสรุป --}}
                    <div class="alert alert-info d-flex gap-2 align-items-start mb-4">
                        <i class="bi bi-info-circle-fill mt-1"></i>
                        <div>
                            <strong>ห้อง {{ $booking->room?->room_number }}</strong>
                            | {{ $booking->guest?->first_name }} {{ $booking->guest?->last_name }}<br>
                            <small>เลขมิเตอร์ไฟเริ่มต้น: <strong>{{ $booking->electric_meter_start ?? 0 }}</strong>
                            | เลขมิเตอร์น้ำเริ่มต้น: <strong>{{ $booking->water_meter_start ?? 0 }}</strong></small>
                        </div>
                    </div>
 
                    <p class="text-muted small mb-3">
                        กรอกอัตราค่าน้ำ/ไฟ เพื่อให้ระบบสร้างมิเตอร์และบันทึกเลขเริ่มต้นโดยอัตโนมัติ
                    </p>
 
                    {{-- อัตราค่าไฟ --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            <i class="bi bi-lightning-charge text-warning me-1"></i>
                            อัตราค่าไฟฟ้า (฿/หน่วย) <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <input type="number" name="electric_rate"
                                   class="form-control @error('electric_rate') is-invalid @enderror"
                                   step="0.01" min="0" placeholder="เช่น 7.00"
                                   value="{{ old('electric_rate', 7.00) }}" required>
                            <span class="input-group-text">฿/หน่วย</span>
                        </div>
                        @error('electric_rate')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
 
                    {{-- อัตราค่าน้ำ --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            <i class="bi bi-droplet text-info me-1"></i>
                            อัตราค่าน้ำ (฿/หน่วย) <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <input type="number" name="water_rate"
                                   class="form-control @error('water_rate') is-invalid @enderror"
                                   step="0.01" min="0" placeholder="เช่น 18.00"
                                   value="{{ old('water_rate', 18.00) }}" required>
                            <span class="input-group-text">฿/หน่วย</span>
                        </div>
                        @error('water_rate')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
 
                    {{-- ภาษี/VAT --}}
                    <div class="mb-0">
                        <label class="form-label fw-semibold">
                            <i class="bi bi-percent me-1"></i>
                            ภาษี / VAT (%) <span class="text-muted fw-normal">(ถ้ามี)</span>
                        </label>
                        <div class="input-group">
                            <input type="number" name="tax_rate"
                                   class="form-control"
                                   step="0.01" min="0" max="100" placeholder="เช่น 7"
                                   value="{{ old('tax_rate', 0) }}">
                            <span class="input-group-text">%</span>
                        </div>
                    </div>
 
                </div>
 
                {{-- Footer --}}
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                            data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-success px-4">
                        <i class="bi bi-check-circle me-1"></i> ยืนยันและสร้างมิเตอร์
                    </button>
                </div>
            </form>
 
        </div>
    </div>
</div>
 