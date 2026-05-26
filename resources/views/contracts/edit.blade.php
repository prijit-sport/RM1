@extends('layouts.app')
 
@php
    /** @var \App\Models\Contract $contract */
    /** @var \Illuminate\Support\Collection<\App\Models\Room> $rooms */
    /** @var \Illuminate\Support\Collection<\App\Models\Guest> $guests */
@endphp
 
@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-warning text-dark py-3">
                    <h5 class="mb-0"><i class="bi bi-pencil-square"></i> แก้ไขสัญญาเช่า</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('contracts.update', $contract->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <!-- ข้อมูลห้องและผู้เช่า -->
                        <h6 class="section-title mt-0"><i class="bi bi-info-circle"></i> ข้อมูลการเช่า</h6>
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label required">ห้องพัก</label>
                                <select name="room_id" class="form-select @error('room_id') is-invalid @enderror" required>
                                    <option value="">-- เลือกห้องพัก --</option>
                                    @foreach($rooms ?? [] as $room)
                                        <option value="{{ $room->id }}" {{ $contract->room_id == $room->id ? 'selected' : '' }}>
                                            {{ $room->room_number }} - {{ $room->room_type }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('room_id')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label required">ผู้เช่า</label>
                                <select name="guest_id" class="form-select @error('guest_id') is-invalid @enderror" required>
                                    <option value="">-- เลือกผู้เช่า --</option>
                                    @foreach($guests ?? [] as $guest)
                                        <option value="{{ $guest->id }}" {{ $contract->guest_id == $guest->id ? 'selected' : '' }}>
                                            {{ $guest->first_name }} {{ $guest->last_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('guest_id')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
 
                        <!-- ข้อมูลสัญญา -->
                        <h6 class="section-title"><i class="bi bi-file-earmark-ruled"></i> ข้อมูลสัญญา</h6>
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <label class="form-label">เลขที่สัญญา</label>
                                <input type="text" class="form-control" value="{{ old('contract_number', $contract->contract_number) }}" readonly>
                                <small class="text-muted">ระบบสร้างอัตโนมัติ</small>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">ชื่อสัญญา</label>
                                <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $contract->title) }}">
                                @error('title')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">วันที่ทำสัญญา</label>
                                <input type="date" name="contract_date" class="form-control @error('contract_date') is-invalid @enderror" value="{{ old('contract_date', $contract->contract_date) }}">
                                @error('contract_date')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
 
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label required">สถานะ</label>
                                <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                    <option value="draft" {{ $contract->status == 'draft' ? 'selected' : '' }}>ร่าง</option>
                                    <option value="pending" {{ $contract->status == 'pending' ? 'selected' : '' }}>รออนุมัติ</option>
                                    <option value="active" {{ $contract->status == 'active' ? 'selected' : '' }}>ใช้งาน</option>
                                    <option value="completed" {{ $contract->status == 'completed' ? 'selected' : '' }}>เสร็จสิ้น</option>
                                    <option value="cancelled" {{ $contract->status == 'cancelled' ? 'selected' : '' }}>ยกเลิก</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
 
                        <!-- ระยะเวลาเช่า -->
                        <h6 class="section-title"><i class="bi bi-calendar-range"></i> ระยะเวลาเช่า</h6>
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <label class="form-label required">วันที่เริ่มเช่า</label>
                                <input type="date" name="start_date" class="form-control @error('start_date') is-invalid @enderror" value="{{ old('start_date', $contract->start_date) }}" required>
                                @error('start_date')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label required">วันที่สิ้นสุด</label>
                                <input type="date" name="end_date" class="form-control @error('end_date') is-invalid @enderror" value="{{ old('end_date', $contract->end_date) }}" required>
                                @error('end_date')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">ระยะเวลา (เดือน)</label>
                                <input type="text" class="form-control" value="{{ old('duration', $contract->duration) }}" readonly>
                                <small class="text-muted">คำนวณอัตโนมัติ</small>
                            </div>
                        </div>
 
                        <!-- ค่าเช่าและค่าธรรมเนียม -->
                        <h6 class="section-title"><i class="bi bi-currency-dollar"></i> ค่าเช่าและค่าธรรมเนียม</h6>
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label required">ค่าเช่าต่อเดือน (บาท)</label>
                                <input type="number" name="monthly_rent" class="form-control @error('monthly_rent') is-invalid @enderror" value="{{ old('monthly_rent', $contract->monthly_rent) }}" required min="0" step="0.01">
                                @error('monthly_rent')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">ค่าเช่าเป็นตัวอักษร</label>
                                <input type="text" name="monthly_rent_text" class="form-control" value="{{ old('monthly_rent_text', $contract->monthly_rent_text) }}">
                            </div>
                        </div>
 
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <label class="form-label">เงินประกัน (บาท)</label>
                                <input type="number" name="deposit" class="form-control @error('deposit') is-invalid @enderror" min="0" step="0.01" value="{{ old('deposit', $contract->deposit ?? 0) }}">
                                <small class="text-muted">หากไม่กรอก = ค่าเช่ารายเดือน</small>
                                @error('deposit')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">จ่ายล่วงหน้า (เดือน)</label>
                                <input type="number" name="advance_payment_months" class="form-control @error('advance_payment_months') is-invalid @enderror" min="1" step="1" value="1" readonly>
                                <small class="text-muted">💡 คิดแค่ 1 เดือนเท่านั้น (ระบบตั้งอัตโนมัติ)</small>
                                @error('advance_payment_months')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">จ่ายล่วงหน้า (บาท)</label>
                                <input type="text" class="form-control" value="฿{{ number_format($contract->advance_payment ?? 0, 2) }}" readonly>
                                <small class="text-muted">อ่านอย่างเดียว</small>
                            </div>
                        </div>
 
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <label class="form-label">ค่าไฟฟ้าต่อหน่วย (บาท)</label>
                                <input type="number" name="electricity_rate" class="form-control @error('electricity_rate') is-invalid @enderror" min="0" step="0.01" value="{{ old('electricity_rate', $contract->electricity_rate ?? 0) }}">
                                @error('electricity_rate')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">ค่าน้ำต่อหน่วย (บาท)</label>
                                <input type="number" name="water_rate" class="form-control @error('water_rate') is-invalid @enderror" min="0" step="0.01" value="{{ old('water_rate', $contract->water_rate ?? 0) }}">
                                @error('water_rate')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">ค่าปรับล่าช้า (%)</label>
                                <input type="number" name="late_fee" class="form-control @error('late_fee') is-invalid @enderror" min="0" step="0.01" value="{{ old('late_fee', $contract->late_fee ?? 0) }}">
                                @error('late_fee')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
 
                        <div class="mb-4">
                            <label class="form-label">ค่าธรรมเนียมอื่น ๆ</label>
                            <input type="text" name="other_fees" class="form-control" value="{{ old('other_fees', $contract->other_fees) }}">
                        </div>
 
                        <!-- ข้อมูลผู้ให้เช่า -->
                        <h6 class="section-title"><i class="bi bi-person-badge"></i> ข้อมูลผู้ให้เช่า</h6>
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <label class="form-label">ชื่อผู้ให้เช่า</label>
                                <input type="text" name="landlord_name" class="form-control" value="{{ old('landlord_name', $contract->landlord_name) }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">เลขบัตรประชาชน</label>
                                <input type="text" name="landlord_id_number" class="form-control" value="{{ old('landlord_id_number', $contract->landlord_id_number) }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">เบอร์โทร</label>
                                <input type="text" name="landlord_phone" class="form-control" value="{{ old('landlord_phone', $contract->landlord_phone) }}">
                            </div>
                        </div>
 
                        <div class="mb-4">
                            <label class="form-label">ที่อยู่ผู้ให้เช่า</label>
                            <textarea name="landlord_address" class="form-control" rows="2">{{ old('landlord_address', $contract->landlord_address) }}</textarea>
                        </div>
 
                        <!-- เงื่อนไขและลายเซ็น -->
                        <h6 class="section-title"><i class="bi bi-pen"></i> เงื่อนไขและลายเซ็น</h6>
                        <div class="mb-4">
                            <label class="form-label">เงื่อนไขสัญญา</label>
                            <textarea name="terms" class="form-control" rows="3">{{ old('terms', $contract->terms) }}</textarea>
                        </div>
 
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <label class="form-label">ลายเซ็นผู้เช่า</label>
                                <input type="text" name="tenant_signature" class="form-control" value="{{ old('tenant_signature', $contract->tenant_signature) }}">
                                <small class="text-muted">วันที่ลงนาม</small>
                                <input type="date" name="tenant_sign_date" class="form-control mt-2" value="{{ old('tenant_sign_date', $contract->tenant_sign_date) }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">ลายเซ็นผู้ให้เช่า</label>
                                <input type="text" name="landlord_signature" class="form-control" value="{{ old('landlord_signature', $contract->landlord_signature) }}">
                                <small class="text-muted">วันที่ลงนาม</small>
                                <input type="date" name="landlord_sign_date" class="form-control mt-2" value="{{ old('landlord_sign_date', $contract->landlord_sign_date) }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">ลายเซ็นพยาน</label>
                                <input type="text" name="witness_signature" class="form-control" value="{{ old('witness_signature', $contract->witness_signature) }}">
                                <small class="text-muted">วันที่ลงนาม</small>
                                <input type="date" name="witness_sign_date" class="form-control mt-2" value="{{ old('witness_sign_date', $contract->witness_sign_date) }}">
                            </div>
                        </div>
 
                        <!-- หมายเหตุ -->
                        <h6 class="section-title"><i class="bi bi-card-text"></i> หมายเหตุเพิ่มเติม</h6>
                        <div class="mb-4">
                            <label class="form-label">คำอธิบาย</label>
                            <textarea name="description" class="form-control" rows="2">{{ old('description', $contract->description) }}</textarea>
                        </div>
 
                        <div class="mb-4">
                            <label class="form-label">หมายเหตุ</label>
                            <textarea name="notes" class="form-control" rows="3">{{ old('notes', $contract->notes) }}</textarea>
                        </div>
 
                        <!-- Buttons -->
                        <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                            <a href="{{ route('contracts.index') }}" class="btn btn-secondary">
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
 
.required::after {
    content: ' *';
    color: #dc3545;
}
 
.invalid-feedback {
    display: none;
    font-size: 0.875rem;
    color: #dc3545;
}
 
.form-control.is-invalid,
.form-select.is-invalid {
    border-color: #dc3545;
}
 
.form-control.is-invalid:focus,
.form-select.is-invalid:focus {
    border-color: #dc3545;
    box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
}
</style>
@endsection
 