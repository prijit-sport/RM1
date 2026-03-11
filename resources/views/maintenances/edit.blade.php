@extends('layouts.app')

@php
    /** @var \App\Models\Maintenance $maintenance */
    /** @var \Illuminate\Support\Collection<\App\Models\Room> $rooms */
@endphp

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-warning text-dark py-3">
                    <h5 class="mb-0"><i class="bi bi-wrench-adjustable"></i> แก้ไขการแจ้งซ่อมบำรุง</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('maintenances.update', $maintenance->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <label class="form-label required">ห้องพัก</label>
                                <select name="room_id" class="form-select @error('room_id') is-invalid @enderror" required>
                                    <option value="">-- เลือกห้องพัก --</option>
                                    @foreach($rooms ?? [] as $room)
                                        <option value="{{ $room->id }}" {{ $maintenance->room_id == $room->id ? 'selected' : '' }}>
                                            {{ $room->room_number }} - {{ $room->room_type }}
                                        </option>
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
                                    <option value="electrical" {{ $maintenance->maintenance_type == 'electrical' ? 'selected' : '' }}>ระบบไฟฟ้า</option>
                                    <option value="plumbing" {{ $maintenance->maintenance_type == 'plumbing' ? 'selected' : '' }}>ระบบประปา</option>
                                    <option value="air_conditioning" {{ $maintenance->maintenance_type == 'air_conditioning' ? 'selected' : '' }}>ระบบแอร์</option>
                                    <option value="furniture" {{ $maintenance->maintenance_type == 'furniture' ? 'selected' : '' }}>เฟอร์นิเจอร์</option>
                                    <option value="structural" {{ $maintenance->maintenance_type == 'structural' ? 'selected' : '' }}>โครงสร้าง</option>
                                    <option value="other" {{ $maintenance->maintenance_type == 'other' ? 'selected' : '' }}>อื่นๆ</option>
                                </select>
                                @error('maintenance_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label required">ความเร่งด่วน</label>
                                <select name="priority" class="form-select @error('priority') is-invalid @enderror" required>
                                    <option value="low" {{ $maintenance->priority == 'low' ? 'selected' : '' }}>ทั่วไป</option>
                                    <option value="medium" {{ $maintenance->priority == 'medium' ? 'selected' : '' }}>ด่วน</option>
                                    <option value="high" {{ $maintenance->priority == 'high' ? 'selected' : '' }}>ด่วนมาก</option>
                                </select>
                                @error('priority')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label required">วันที่แจ้ง</label>
                                <input type="date" name="request_date" class="form-control @error('request_date') is-invalid @enderror" value="{{ old('request_date', $maintenance->request_date) }}" required>
                                @error('request_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">วันที่นัดหมาย</label>
                                <input type="date" name="scheduled_date" class="form-control" value="{{ old('scheduled_date', $maintenance->scheduled_date) }}">
                            </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label">วันที่เริ่มซ่อม</label>
                                <input type="date" name="start_date" class="form-control" value="{{ old('start_date', $maintenance->start_date) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">วันที่ซ่อมเสร็จ</label>
                                <input type="date" name="completion_date" class="form-control" value="{{ old('completion_date', $maintenance->completion_date) }}">
                            </div>

                        <div class="row mb-4">
                            <div class="col-md-12">
                                <label class="form-label required">สถานะ</label>
                                <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                    <option value="pending" {{ $maintenance->status == 'pending' ? 'selected' : '' }}>รอดำเนินการ</option>
                                    <option value="in_progress" {{ $maintenance->status == 'in_progress' ? 'selected' : '' }}>กำลังดำเนินการ</option>
                                    <option value="completed" {{ $maintenance->status == 'completed' ? 'selected' : '' }}>เสร็จสิ้น</option>
                                    <option value="cancelled" {{ $maintenance->status == 'cancelled' ? 'selected' : '' }}>ยกเลิก</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                        <div class="mb-4">
                            <label class="form-label required">รายละเอียดปัญหา</label>
                            <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="4" required>{{ old('description', $maintenance->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label">หมายเหตุ</label>
                            <textarea name="notes" class="form-control" rows="2">{{ old('notes', $maintenance->notes) }}</textarea>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('maintenances.index') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> ยกเลิก
                            </a>
                            <button type="submit" class="btn btn-warning">
                                <i class="bi bi-check-circle"></i> บันทึกการแก้ไข
                            </button>
                        </div>
                    </form>
                </div>
        </div>
</div>
@endsection
