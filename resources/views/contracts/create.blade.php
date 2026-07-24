@extends('layouts.app')

@php
    /** @var \Illuminate\Support\Collection<\App\Models\Room> $rooms */
    /** @var \Illuminate\Support\Collection<\App\Models\Guest> $guests */
@endphp

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white py-3">
                    <h5 class="mb-0"><i class="bi bi-file-earmark-text"></i> สร้างสัญญาเช่าใหม่</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('contracts.store') }}" method="POST">
                        @csrf
                        
                        {{-- ข้อมูลห้องและผู้เช่า --}}
                        <h6 class="section-title mt-3"><i class="bi bi-info-circle"></i> ข้อมูลการเช่า</h6>
                        <div class="row mb-4">
                            <div class="col-md-6">
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
                            <div class="col-md-6">
                                <label class="form-label required">ผู้เช่า</label>
                                <select name="guest_id" class="form-select @error('guest_id') is-invalid @enderror" required>
                                    <option value="">-- เลือกผู้เช่า --</option>
                                    @foreach($guests ?? [] as $guest)
                                        <option value="{{ $guest->id }}">{{ $guest->first_name }} {{ $guest->last_name }}</option>
                                    @endforeach
                                </select>
                                @error('guest_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                        {{-- ข้อมูลสัญญา --}}
                        <h6 class="section-title mt-3"><i class="bi bi-file-earmark-ruled"></i> ข้อมูลสัญญา</h6>
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label">เลขที่สัญญา</label>
                                <input type="text" name="contract_number" value="{{ old('contract_number', $contractNumber ?? '') }}" class="form-control" placeholder="ระบบจะสร้างอัตโนมัติ">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">ชื่อสัญญา</label>
                                <input type="text" name="title" class="form-control" placeholder="เช่น สัญญาเช่าหอพัก">
                            </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label required">วันที่ทำสัญญา</label>
                                <input type="date" name="contract_date" class="form-control @error('contract_date') is-invalid @enderror" required>
                                @error('contract_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label required">สถานะ</label>
                                <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                    <option value="draft">ร่าง</option>
                                    <option value="pending">รออนุมัติ</option>
                                    <option value="active">ใช้งาน</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                        {{-- ระยะเวลาเช่า --}}
                        <h6 class="section-title mt-3"><i class="bi bi-calendar-range"></i> ระยะเวลาเช่า</h6>
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <label class="form-label required">วันที่เริ่มเช่า</label>
                                <input type="date" name="start_date" class="form-control @error('start_date') is-invalid @enderror" required>
                                @error('start_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label required">วันที่สิ้นสุด</label>
                                <input type="date" name="end_date" class="form-control @error('end_date') is-invalid @enderror" required>
                                @error('end_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">ระยะเวลา</label>
                                <input type="text" name="duration" class="form-control" placeholder="เช่น 12 เดือน">
                            </div>

                        {{-- ค่าเช่าและค่าธรรมเนียม --}}
                        <h6 class="section-title mt-3"><i class="bi bi-currency-dollar"></i> ค่าเช่าและค่าธรรมเนียม</h6>
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <label class="form-label required">ค่าเช่าต่อเดือน (บาท)</label>
                                <input type="number" name="monthly_rent" class="form-control @error('monthly_rent') is-invalid @enderror" required min="0">
                                @error('monthly_rent')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">ค่าเช่าเป็นตัวอักษร</label>
                                <input type="text" name="monthly_rent_text" class="form-control" placeholder="เช่น ห้าพันบาทถ้วน">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">เงินประกัน (บาท)</label>
                                <input type="number" name="deposit" class="form-control" min="0" value="0">
                            </div>

                        <div class="row mb-4">
                            <div class="col-md-4">
                                <label class="form-label">จ่ายล่วงหน้า (เดือน)</label>
<input type="number" name="advance_payment_months" class="form-control" min="0" value="1" step="1" data-force-advance-1="1">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">ค่าไฟฟ้าต่อหน่วย</label>
                                <input type="number" name="electricity_rate" class="form-control" min="0" step="0.01" value="0">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">ค่าน้ำต่อหน่วย</label>
                                <input type="number" name="water_rate" class="form-control" min="0" step="0.01" value="0">
                            </div>

                        {{-- ข้อมูลผู้ให้เช่า --}}
                        <h6 class="section-title mt-3"><i class="bi bi-person-badge"></i> ข้อมูลผู้ให้เช่า</h6>
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label">ชื่อผู้ให้เช่า</label>
                                <input type="text" name="landlord_name" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">เลขบัตรประชาชน</label>
                                <input type="text" name="landlord_id_number" class="form-control">
                            </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label">ที่อยู่ผู้ให้เช่า</label>
                                <textarea name="landlord_address" class="form-control" rows="2"></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">เบอร์โทรผู้ให้เช่า</label>
                                <input type="text" name="landlord_phone" class="form-control">
                            </div>

                        {{-- หมายเหตุ --}}
                        <div class="mb-4">
                            <label class="form-label">หมายเหตุ</label>
                            <textarea name="notes" class="form-control" rows="3"></textarea>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('contracts.index') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> ยกเลิก
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> บันทึกสัญญา
                            </button>
                        </div>
                    </form>
                </div>
        </div>
</div>

<style>
.section-title {
    color: #1e3a5f;
    font-weight: 600;
    border-bottom: 2px solid #e9ecef;
    padding-bottom: 8px;
    margin-bottom: 16px;
}
</style>
@endsection
