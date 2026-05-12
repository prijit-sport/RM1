@extends('layouts.app')

@php
    /** @var \Illuminate\Support\Collection<\App\Models\Room> $rooms */
@endphp

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white py-3">
                    <h5 class="mb-0"><i class="bi bi-tools"></i> แจ้งซ่อมบำรุงใหม่</h5>
                </div>
                <div class="card-body p-4">

                    {{-- แสดง validation errors --}}
                    @if ($errors->any())
                        <div class="alert alert-danger border-0 shadow-sm">
                            <strong><i class="bi bi-exclamation-triangle me-1"></i> กรุณาตรวจสอบข้อมูล:</strong>
                            <ul class="mb-0 mt-2">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('maintenances.store') }}" method="POST">
                        @csrf

                        {{-- กำหนดสถานะเริ่มต้นเป็น pending --}}
                        <input type="hidden" name="status" value="pending">

                        {{-- ── ห้องพัก ── --}}
                        <div class="mb-4">
                            <label class="form-label fw-bold">ห้องพัก <span class="text-danger">*</span></label>
                            <select name="room_id" class="form-select @error('room_id') is-invalid @enderror" required>
                                <option value="">-- เลือกห้องพัก --</option>
                                @foreach($rooms ?? [] as $room)
                                    <option value="{{ $room->id }}" {{ old('room_id') == $room->id ? 'selected' : '' }}>
                                        ห้อง {{ $room->room_number }} - {{ $room->room_type }}
                                    </option>
                                @endforeach
                            </select>
                            @error('room_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- ── ประเภทงาน + ความเร่งด่วน ── --}}
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">ประเภทงานซ่อม <span class="text-danger">*</span></label>
                                {{-- แก้ไขชื่อเป็น maintenance_type ให้ตรงกับ DB --}}
                                <select name="maintenance_type" class="form-select @error('maintenance_type') is-invalid @enderror" required>
                                    <option value="">-- เลือกประเภท --</option>
                                    <option value="ไฟฟ้า" {{ old('maintenance_type') == 'ไฟฟ้า' ? 'selected' : '' }}>💡 ไฟฟ้า</option>
                                    <option value="ประปา/ท่อ" {{ old('maintenance_type') == 'ประปา/ท่อ' ? 'selected' : '' }}>🚰 ประปา/ท่อ</option>
                                    <option value="เครื่องปรับอากาศ" {{ old('maintenance_type') == 'เครื่องปรับอากาศ' ? 'selected' : '' }}>❄️ เครื่องปรับอากาศ</option>
                                    <option value="เฟอร์นิเจอร์" {{ old('maintenance_type') == 'เฟอร์นิเจอร์' ? 'selected' : '' }}>🪑 เฟอร์นิเจอร์</option>
                                    <option value="อื่นๆ" {{ old('maintenance_type') == 'อื่นๆ' ? 'selected' : '' }}>🛠️ อื่นๆ</option>
                                </select>
                                @error('maintenance_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">ความเร่งด่วน</label>
                                <select id="priority_select" class="form-select">
                                    <option value="ทั่วไป">ทั่วไป</option>
                                    <option value="ด่วน">ด่วน</option>
                                    <option value="ด่วนมาก">ด่วนมาก</option>
                                </select>
                            </div>
                        </div>

                        {{-- ── วันที่แจ้ง ── --}}
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <label class="form-label fw-bold">วันที่แจ้งซ่อม <span class="text-danger">*</span></label>
                                {{-- แก้ไขชื่อเป็น request_date ให้ตรงกับ DB --}}
                                <input type="date" name="request_date"
                                       class="form-control @error('request_date') is-invalid @enderror"
                                       value="{{ old('request_date', date('Y-m-d')) }}" required>
                                @error('request_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- ── รายละเอียดปัญหา ── --}}
                        <div class="mb-4">
                            <label class="form-label fw-bold">รายละเอียดปัญหา <span class="text-danger">*</span></label>
                            <textarea name="description"
                                      class="form-control @error('description') is-invalid @enderror"
                                      rows="4" placeholder="กรุณาระบุรายละเอียดปัญหาที่พบ..." required>{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- ── หมายเหตุ ── --}}
                        <div class="mb-4">
                            <label class="form-label fw-bold">หมายเหตุเพิ่มเติม</label>
                            <textarea name="notes" class="form-control" rows="2" placeholder="ข้อมูลเพิ่มเติมสำหรับช่าง...">{{ old('notes') }}</textarea>
                        </div>

                        {{-- ปุ่มดำเนินการ --}}
                        <div class="d-flex justify-content-between mt-5">
                            <a href="{{ route('maintenances.index') }}" class="btn btn-light px-4">
                                <i class="bi bi-arrow-left"></i> ยกเลิก
                            </a>
                            <button type="submit" class="btn btn-primary px-5">
                                <i class="bi bi-check-circle me-1"></i> ยืนยันการแจ้งซ่อม
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // สคริปต์สำหรับนำความเร่งด่วนไปใส่ในรายละเอียดอัตโนมัติ
    document.querySelector('form').addEventListener('submit', function(e) {
        const priority = document.getElementById('priority_select').value;
        const descBox = document.querySelector('textarea[name="description"]');
        if (priority && !descBox.value.includes('[ความเร่งด่วน:')) {
            descBox.value = `[ความเร่งด่วน: ${priority}] ` + descBox.value;
        }
    });
</script>
@endsection