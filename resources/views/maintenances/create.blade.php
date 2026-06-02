@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-primary text-white py-3">
                        <h5 class="mb-0"><i class="bi bi-tools"></i> แจ้งซ่อมบำรุงใหม่</h5>
                    </div>
                    <div class="card-body p-4">

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                @foreach ($errors->all() as $error)
                                    <div>{{ $error }}</div>
                                @endforeach
                            </div>
                        @endif

                        <form action="{{ route('maintenances.store') }}" method="POST" id="maintenanceForm">
                            @csrf

                            {{-- ── 1. เลือกห้องพัก (Lock ถ้ามาจาก facility) ── --}}
                            <div class="mb-4">
                                <label class="form-label fw-bold">ห้องพัก <span class="text-danger">*</span></label>
                                <select name="room_id" id="room_id"
                                    class="form-select @error('room_id') is-invalid @enderror"
                                    {{ $selectedFacilityId ? 'disabled' : '' }} required>
                                    <option value="">-- เลือกห้องพัก --</option>
                                    @foreach ($rooms as $room)
                                        <option value="{{ $room->id }}"
                                            {{ old('room_id', $selectedRoomId) == $room->id ? 'selected' : '' }}>
                                            ห้อง {{ $room->room_number }} ({{ $room->room_type }})
                                        </option>
                                    @endforeach
                                </select>

                                @if ($selectedFacilityId)
                                    <small class="text-info d-block mt-2">
                                        🔒 ล็อค: มาจากการแจ้งซ่อมจาก Facility
                                    </small>
                                @endif

                                @error('room_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- ── 2. สิ่งอำนวยความสะดวก — 6 ประเภทคงที่ ── --}}
                            <div class="mb-4">
                                <label class="form-label fw-bold">สิ่งอำนวยความสะดวก / อุปกรณ์</label>

                                @if ($selectedFacilityId)
                                    {{-- โหมด Lock: แสดงชื่อ facility ที่มาจากหน้า Facility --}}
                                    <input type="text" class="form-control bg-light"
                                        value="{{ $facilities->firstWhere('id', $selectedFacilityId)?->name ?? 'ไม่ระบุ' }}"
                                        disabled>
                                    <small class="text-info d-block mt-2">🔒 ล็อค: ตัวเลือกนี้ถูกตรึงแล้ว</small>
                                @else
                                    {{-- โหมดปกติ: dropdown 6 ประเภทคงที่ ไม่ซ้ำ ไม่ขึ้นกับ DB --}}
                                    <select name="facility_type" id="facility_type" class="form-select">
                                        <option value="">-- เลือกประเภทอุปกรณ์ (ถ้ามี) --</option>
                                        <option value="bed"
                                            {{ old('facility_type') == 'bed' ? 'selected' : '' }}>🛏️ เตียง
                                        </option>
                                        <option value="mattress"
                                            {{ old('facility_type') == 'mattress' ? 'selected' : '' }}>🛌 ที่นอน
                                        </option>
                                        <option value="wardrobe"
                                            {{ old('facility_type') == 'wardrobe' ? 'selected' : '' }}>🚪 ตู้เสื้อผ้า
                                        </option>
                                        <option value="dressing_table"
                                            {{ old('facility_type') == 'dressing_table' ? 'selected' : '' }}>💄
                                            โต๊ะเครื่องแป้ง</option>
                                        <option value="tv_stand"
                                            {{ old('facility_type') == 'tv_stand' ? 'selected' : '' }}>📺 ชั้นวางทีวี
                                        </option>
                                        <option value="clothes_rack"
                                            {{ old('facility_type') == 'clothes_rack' ? 'selected' : '' }}>👔 ราวแขวนผ้า
                                        </option>
                                    </select>
                                    <div class="form-text text-muted">เลือกประเภทอุปกรณ์ที่ต้องการแจ้งซ่อม (ถ้ามี)</div>
                                @endif
                            </div>

                            {{-- Hidden inputs เมื่อล็อคจาก Facility --}}
                            @if ($selectedFacilityId)
                                <input type="hidden" name="room_id" value="{{ $selectedRoomId }}">
                                <input type="hidden" name="facility_id" value="{{ $selectedFacilityId }}">
                            @endif

                            {{-- ── 3. ประเภทงานซ่อม + ความเร่งด่วน ── --}}
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">ประเภทงานซ่อม <span
                                            class="text-danger">*</span></label>
                                    <select name="maintenance_type" id="maintenance_type"
                                        class="form-select @error('maintenance_type') is-invalid @enderror" required>
                                        <option value="">-- เลือกประเภท --</option>
                                        <option value="ไฟฟ้า"
                                            {{ old('maintenance_type') == 'ไฟฟ้า' ? 'selected' : '' }}>💡 ไฟฟ้า
                                        </option>
                                        <option value="ประปา/ท่อ"
                                            {{ old('maintenance_type') == 'ประปา/ท่อ' ? 'selected' : '' }}>🚰
                                            ประปา/ท่อ</option>
                                        <option value="เครื่องปรับอากาศ"
                                            {{ old('maintenance_type') == 'เครื่องปรับอากาศ' ? 'selected' : '' }}>❄️
                                            เครื่องปรับอากาศ</option>
                                        <option value="เฟอร์นิเจอร์"
                                            {{ old('maintenance_type') == 'เฟอร์นิเจอร์' ? 'selected' : '' }}>🪑
                                            เฟอร์นิเจอร์</option>
                                        <option value="อื่นๆ"
                                            {{ old('maintenance_type') == 'อื่นๆ' ? 'selected' : '' }}>🛠️
                                            อื่นๆ</option>
                                    </select>
                                    @error('maintenance_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">ความเร่งด่วน</label>
                                    <select name="priority" id="priority_select" class="form-select">
                                        <option value="ทั่วไป" {{ old('priority') == 'ทั่วไป' ? 'selected' : '' }}>ทั่วไป
                                        </option>
                                        <option value="ด่วน" {{ old('priority') == 'ด่วน' ? 'selected' : '' }}>ด่วน
                                        </option>
                                        <option value="ด่วนมาก" {{ old('priority') == 'ด่วนมาก' ? 'selected' : '' }}>
                                            ด่วนมาก</option>
                                    </select>
                                </div>
                            </div>

                            {{-- ── 4. วันที่แจ้งซ่อม ── --}}
                            <div class="mb-4">
                                <label class="form-label fw-bold">วันที่แจ้งซ่อม <span class="text-danger">*</span></label>
                                <input type="date" name="request_date"
                                    class="form-control @error('request_date') is-invalid @enderror"
                                    value="{{ old('request_date', date('Y-m-d')) }}" required>
                                @error('request_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- ── 5. รายละเอียดปัญหา ── --}}
                            <div class="mb-4">
                                <label class="form-label fw-bold">รายละเอียดปัญหา <span class="text-danger">*</span></label>
                                <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror"
                                    rows="4" placeholder="กรุณาระบุรายละเอียดปัญหา..." required>{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- ── 6. สถานะ ── --}}
                            <div class="mb-4">
                                <label class="form-label fw-bold">สถานะ <span class="text-danger">*</span></label>
                                <select name="status" id="status"
                                    class="form-select @error('status') is-invalid @enderror" required>
                                    <option value="pending"
                                        {{ old('status', 'pending') == 'pending' ? 'selected' : '' }}>⏳ รอดำเนิน
                                    </option>
                                    <option value="in_progress"
                                        {{ old('status') == 'in_progress' ? 'selected' : '' }}>🔄 กำลังดำเนิน
                                    </option>
                                    <option value="completed"
                                        {{ old('status') == 'completed' ? 'selected' : '' }}>✅ สำเร็จ</option>
                                    <option value="cancelled"
                                        {{ old('status') == 'cancelled' ? 'selected' : '' }}>❌ ยกเลิก</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- ── 7. หมายเหตุ ── --}}
                            <div class="mb-4">
                                <label class="form-label fw-bold">หมายเหตุ</label>
                                <textarea name="notes" id="notes" class="form-control" rows="3" placeholder="หมายเหตุเพิ่มเติม">{{ old('notes') }}</textarea>
                            </div>

                            {{-- ── ปุ่ม ── --}}
                            <div class="d-flex justify-content-between mt-5">
                                <a href="{{ route('maintenances.index') }}" class="btn btn-light px-4">ยกเลิก</a>
                                <button type="submit" class="btn btn-primary px-5">ยืนยันการแจ้งซ่อม</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
