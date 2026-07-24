@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-primary text-white py-3">
                        <h5 class="mb-0"><i class="bi bi-door-open"></i> เพิ่มห้องพักใหม่</h5>
                    </div>
                    <div class="card-body">

                        {{-- แสดง validation errors --}}
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <strong><i class="bi bi-exclamation-triangle me-1"></i>กรุณาตรวจสอบข้อมูล:</strong>
                                <ul class="mb-0 mt-2">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('rooms.store') }}" method="POST">
                            @csrf

                            {{-- ── หมายเลขห้อง + ประเภทห้อง ── --}}
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label class="form-label required">หมายเลขห้อง <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="room_number"
                                        class="form-control @error('room_number') is-invalid @enderror"
                                        value="{{ old('room_number') }}" placeholder="เช่น 101, A-201" required>
                                    @error('room_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label required">ประเภทห้อง <span class="text-danger">*</span></label>
                                    <select name="room_type" id="room_type_select"
                                        class="form-select @error('room_type') is-invalid @enderror" required>
                                        <option value="">-- เลือกประเภท --</option>
                                        <option value="fan" data-price="2800"
                                            {{ old('room_type') == 'fan' ? 'selected' : '' }}>พัดลม</option>
                                        <option value="air" data-price="3500"
                                            {{ old('room_type') == 'air' ? 'selected' : '' }}>แอร์</option>
                                    </select>
                                    @error('room_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">เปลี่ยนประเภทห้อง ราคาจะอัปเดตอัตโนมัติ</small>
                                </div>
                            </div>

                            {{-- ── ค่าเช่า + ความจุ ── --}}
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label class="form-label required">ค่าเช่าต่อเดือน (บาท) <span
                                            class="text-danger">*</span></label>
                                    <input type="number" name="price_per_month" id="price_input"
                                        class="form-control @error('price_per_month') is-invalid @enderror"
                                        value="{{ old('price_per_month') }}" required min="0" step="100">
                                    @error('price_per_month')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted" id="price_hint" style="display:none;">
                                        <i class="bi bi-info-circle text-success"></i> ราคาถูกเติมอัตโนมัติ
                                        (แก้ได้ถ้าต้องการ)
                                    </small>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label required">ความจุ (คน) <span
                                            class="text-danger">*</span></label>
                                    <input type="number" name="capacity"
                                        class="form-control @error('capacity') is-invalid @enderror"
                                        value="{{ old('capacity', 1) }}" required min="1">
                                    @error('capacity')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- ── ชั้น + อาคาร + สถานะ ── --}}
                            <div class="row mb-4">
                                <div class="col-md-4">
                                    <label class="form-label required">ชั้น <span class="text-danger">*</span></label>
                                    <input type="number" name="floor"
                                        class="form-control @error('floor') is-invalid @enderror"
                                        value="{{ old('floor') }}" required min="1">
                                    @error('floor')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">อาคาร</label>
                                    <input type="text" name="building"
                                        class="form-control @error('building') is-invalid @enderror"
                                        value="{{ old('building') }}" placeholder="เช่น A, B">
                                    @error('building')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label required">สถานะ <span class="text-danger">*</span></label>
                                    <select name="status" class="form-select @error('status') is-invalid @enderror"
                                        required>

                                        <option value="available"
                                            {{ old('status', 'available') == 'available' ? 'selected' : '' }}>ว่าง
                                        </option>
                                        <option value="occupied" {{ old('status') == 'occupied' ? 'selected' : '' }}>
                                            มีผู้เช่า</option>
                                        <option value="maintenance" {{ old('status') == 'maintenance' ? 'selected' : '' }}>
                                            ซ่อมบำรุง</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- ── รายละเอียด ── --}}
                            <div class="mb-4">
                                <label class="form-label">รายละเอียด</label>
                                <textarea name="description" class="form-control" rows="3" placeholder="รายละเอียดเพิ่มเติม (ถ้ามี)">{{ old('description') }}</textarea>
                            </div>

                            {{-- ── ปุ่ม ── --}}
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('rooms.index') }}" class="btn btn-secondary">
                                    <i class="bi bi-arrow-left"></i> ยกเลิก
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-circle"></i> บันทึกข้อมูล
                                </button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const roomTypeEl = document.getElementById('room_type_select');
            const priceEl = document.getElementById('price_input');
            const priceHint = document.getElementById('price_hint');

            if (!roomTypeEl || !priceEl) return;

            // ── เมื่อเปลี่ยนประเภทห้อง → เติมราคาอัตโนมัติ ──
            roomTypeEl.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const price = selectedOption.getAttribute('data-price');

                if (price) {
                    priceEl.value = price;

                    // แสดง hint + ไฮไลต์ช่องราคา
                    priceHint.style.display = 'block';
                    priceEl.style.transition = 'background-color 0.6s';
                    priceEl.style.backgroundColor = '#fef3c7';
                    setTimeout(() => {
                        priceEl.style.backgroundColor = '';
                    }, 1000);
                } else {
                    // ถ้าเลือก "-- เลือกประเภท --" ก็เคลียร์
                    priceEl.value = '';
                    priceHint.style.display = 'none';
                }
            });

            // ── ถ้าผู้ใช้แก้ราคาเองหลังจาก auto-fill → ซ่อน hint ──
            priceEl.addEventListener('input', function() {
                priceHint.style.display = 'none';
            });
        });
    </script>
@endsection
