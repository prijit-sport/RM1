@extends('layouts.app')

@section('title', 'เพิ่มมิเตอร์')

@section('page-title', 'เพิ่มมิเตอร์')

@php
    /** @var \Illuminate\Support\Collection<\App\Models\Room>|\App\Models\Room[] $rooms */
@endphp

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white py-3">
                    <h5 class="mb-0"><i class="bi bi-speedometer2 me-2"></i>บันทึกข้อมูลมิเตอร์</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('meters.store') }}" method="POST">
                        @csrf

                        <div class="row g-3 mb-4">
                            <div class="col-md-12">
                                <label for="room_id" class="form-label required">ห้องพัก</label>
                                <select id="room_id" name="room_id" class="form-select @error('room_id') is-invalid @enderror" required>
                                    <option value="">-- เลือกห้องพัก --</option>
                                    @foreach($rooms ?? [] as $room)
                                        @continue(!$room instanceof \App\Models\Room)
                                        <option value="{{ $room->id }}" {{ old('room_id') == $room->id ? 'selected' : '' }}>
                                            {{ $room->room_number ?? '—' }} • {{ $room->room_type ?? '—' }} ({{ number_format($room->price_per_month ?? 0, 2) }} ฿/เดือน)
                                        </option>
                                    @endforeach
                                </select>
                                @error('room_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="type" class="form-label required">ประเภทมิเตอร์</label>
                                <select id="type" name="type" class="form-select @error('type') is-invalid @enderror" required>
                                    <option value="">-- เลือกประเภท --</option>
                                    <option value="electric" {{ old('type') == 'electric' ? 'selected' : '' }}>ไฟฟ้า</option>
                                    <option value="water" {{ old('type') == 'water' ? 'selected' : '' }}>น้ำ</option>
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="meter_number" class="form-label required">หมายเลขมิเตอร์</label>
                                <input id="meter_number" type="text" name="meter_number" class="form-control @error('meter_number') is-invalid @enderror" value="{{ old('meter_number') }}" required>
                                @error('meter_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="unit" class="form-label">หน่วยวัด</label>
                                <input id="unit" type="text" name="unit" class="form-control @error('unit') is-invalid @enderror" value="{{ old('unit') }}" placeholder="เช่น kWh หรือ m³">
                                @error('unit')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="installed_at" class="form-label">วันที่ติดตั้ง</label>
                                <input id="installed_at" type="date" name="installed_at" class="form-control @error('installed_at') is-invalid @enderror" value="{{ old('installed_at') }}">
                                @error('installed_at')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="rate_per_unit" class="form-label required">อัตรา (บาท/หน่วย)</label>
                                <input id="rate_per_unit" type="number" step="0.01" name="rate_per_unit" class="form-control @error('rate_per_unit') is-invalid @enderror" value="{{ old('rate_per_unit', 0) }}" required>
                                @error('rate_per_unit')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="tax_rate" class="form-label required">ภาษี (%)</label>
                                <input id="tax_rate" type="number" step="0.01" name="tax_rate" class="form-control @error('tax_rate') is-invalid @enderror" value="{{ old('tax_rate', 0) }}" required>
                                @error('tax_rate')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="is_active" class="form-label">สถานะ</label>
                                <select id="is_active" name="is_active" class="form-select @error('is_active') is-invalid @enderror">
                                    <option value="1" {{ old('is_active', '1') == '1' ? 'selected' : '' }}>ใช้งาน</option>
                                    <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>ระงับใช้งาน</option>
                                </select>
                                @error('is_active')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="notes" class="form-label">หมายเหตุเพิ่มเติม</label>
                            <textarea id="notes" name="notes" class="form-control @error('notes') is-invalid @enderror" rows="3">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('meters.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left"></i> ย้อนกลับ
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
@endsection
