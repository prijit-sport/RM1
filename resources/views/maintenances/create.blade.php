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
                <div class="card-body">
                    <form action="{{ route('maintenances.store') }}" method="POST">
                        @csrf
                        
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <label class="form-label required">ห้องพัก</label>
                                <select name="room_id" class="form-select @error('room_id') is-invalid @enderror" required>
                                    <option value="">-- เลือกห้องพัก --</option>
                                    @foreach($rooms ?? [] as $room)
                                        <option value="{{ $room->id }}">{{ $room->room_number }} - {{ $room->room_type }}</option>
                                    @endforeach
                                </select>
                                @error('room_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label required">ประเภทงานซ่อม</label>
                                <select name="maintenance_type" class="form-select @error('maintenance_type') is-invalid @enderror" required>
                                    <option value="">-- เลือกประเภท --</option>
                                    <option value="electrical">ระบบไฟฟ้า</option>
                                    <option value="plumbing">ระบบประปา</option>
                                    <option value="air_conditioning">ระบบแอร์</option>
                                    <option value="furniture">เฟอร์นิเจอร์</option>
                                    <option value="structural">โครงสร้าง</option>
                                    <option value="other">อื่นๆ</option>
                                </select>
                                @error('maintenance_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label required">ความเร่งด่วน</label>
                                <select name="priority" class="form-select @error('priority') is-invalid @enderror" required>
                                    <option value="low">ทั่วไป</option>
                                    <option value="medium">ด่วน</option>
                                    <option value="high">ด่วนมาก</option>
                                </select>
                                @error('priority')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label required">วันที่แจ้ง</label>
                                <input type="date" name="request_date" class="form-control @error('request_date') is-invalid @enderror" value="{{ date('Y-m-d') }}" required>
                                @error('request_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">วันที่นัดหมาย</label>
                                <input type="date" name="scheduled_date" class="form-control">
                            </div>

                        <div class="mb-4">
                            <label class="form-label required">รายละเอียดปัญหา</label>
                            <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="4" required></textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label">หมายเหตุ</label>
                            <textarea name="notes" class="form-control" rows="2"></textarea>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('maintenances.index') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> ยกเลิก
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> บันทึกการแจ้งซ่อม
                            </button>
                        </div>
                    </form>
                </div>
        </div>
</div>
@endsection
