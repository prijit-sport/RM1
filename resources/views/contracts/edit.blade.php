@extends('layouts.app')

@section('title', 'แก้ไขสัญญาเช่า')

@section('page-title', 'จัดการสัญญาเช่า')

@section('content')
<div class="row">
    <div class="col-12">
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('dashboard') }}"><i class="bi bi-house-door"></i> หน้าหลัก</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('contracts.index') }}">สัญญาเช่า</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">แก้ไขสัญญา</li>
            </ol>
        </nav>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0"><i class="bi bi-pencil-square me-2"></i>แก้ไขสัญญาเช่า</h4>
        </div>

        <div class="card shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>ข้อมูลสัญญาเช่า</h5>
            </div>
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-circle me-2"></i>
                        <strong>พบข้อผิดพลาด:</strong> กรุณาตรวจสอบข้อมูลอีกครั้ง
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <form method="POST" action="{{ route('contracts.update', $contract->id) }}">
                    @csrf
                    @method('PUT')

                    <h6 class="text-muted mb-3 border-bottom pb-2"><i class="bi bi-file-text me-1"></i>ข้อมูลพื้นฐาน</h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="contract_number" class="form-label">เลขที่สัญญา</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-hash"></i></span>
                                <input type="text" id="contract_number" name="contract_number" class="form-control" value="{{ $contract->contract_number }}" readonly>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label for="title" class="form-label">ชื่อสัญญา <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-file-text"></i></span>
                                <input type="text" id="title" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $contract->title) }}" required>
                            </div>
                            @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label for="contract_date" class="form-label">วันที่ทำสัญญา</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-calendar-event"></i></span>
                                <input type="date" id="contract_date" name="contract_date" class="form-control @error('contract_date') is-invalid @enderror" value="{{ old('contract_date', $contract->contract_date?->format('Y-m-d')) }}">
                            </div>
                            @error('contract_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label for="room_id" class="form-label">ห้องพัก <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-door-open"></i></span>
                                <select id="room_id" name="room_id" class="form-select @error('room_id') is-invalid @enderror" required>
                                    <option value="">-- เลือกห้องพัก --</option>
                                    @foreach($rooms as $room)
                                        <option value="{{ $room->id }}" {{ old('room_id', $contract->room_id) == $room->id ? 'selected' : '' }}>{{ $room->room_number }} - {{ $room->room_type }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @error('room_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label for="guest_id" class="form-label">ผู้เช่า <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-person"></i></span>
                                <select id="guest_id" name="guest_id" class="form-select @error('guest_id') is-invalid @enderror" required>
                                    <option value="">-- เลือกผู้เช่า --</option>
                                    @foreach($guests as $guest)
                                        <option value="{{ $guest->id }}" {{ old('guest_id', $contract->guest_id) == $guest->id ? 'selected' : '' }}>{{ $guest->first_name }} {{ $guest->last_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @error('guest_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <h6 class="text-muted mb-3 border-bottom pb-2 mt-4"><i class="bi bi-person-badge me-1"></i>ข้อมูลผู้ให้เช่า</h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="landlord_name" class="form-label">ชื่อผู้ให้เช่า</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-person"></i></span>
                                <input type="text" id="landlord_name" name="landlord_name" class="form-control @error('landlord_name') is-invalid @enderror" value="{{ old('landlord_name', $contract->landlord_name) }}">
                            </div>
                            @error('landlord_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label for="landlord_address" class="form-label">ที่อยู่ผู้ให้เช่า</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-house"></i></span>
                                <input type="text" id="landlord_address" name="landlord_address" class="form-control @error('landlord_address') is-invalid @enderror" value="{{ old('landlord_address', $contract->landlord_address) }}">
                            </div>
                            @error('landlord_address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <h6 class="text-muted mb-3 border-bottom pb-2 mt-4"><i class="bi bi-calendar-range me-1"></i>ระยะเวลาสัญญา</h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="start_date" class="form-label">วันที่เริ่มต้นสัญญา <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-calendar"></i></span>
                                <input type="date" id="start_date" name="start_date" class="form-control @error('start_date') is-invalid @enderror" value="{{ old('start_date', $contract->start_date?->format('Y-m-d')) }}" required>
                            </div>
                            @error('start_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label for="end_date" class="form-label">วันที่สิ้นสุดสัญญา <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-calendar-x"></i></span>
                                <input type="date" id="end_date" name="end_date" class="form-control @error('end_date') is-invalid @enderror" value="{{ old('end_date', $contract->end_date?->format('Y-m-d')) }}" required>
                            </div>
                            @error('end_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <h6 class="text-muted mb-3 border-bottom pb-2 mt-4"><i class="bi bi-cash me-1"></i>รายละเอียดค่าใช้จ่าย</h6>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="monthly_rent" class="form-label">ค่าเช่ารายเดือน (บาท) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-currency-bitcoin"></i></span>
                                <input type="number" id="monthly_rent" name="monthly_rent" class="form-control @error('monthly_rent') is-invalid @enderror" value="{{ old('monthly_rent', $contract->monthly_rent) }}" step="0.01" required>
                            </div>
                            @error('monthly_rent')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-4">
                            <label for="deposit" class="form-label">ค่ามัดจำ (บาท)</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-piggy-bank"></i></span>
                                <input type="number" id="deposit" name="deposit" class="form-control @error('deposit') is-invalid @enderror" value="{{ old('deposit', $contract->deposit) }}" step="0.01">
                            </div>
                            @error('deposit')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-4">
                            <label for="advance_payment" class="form-label">จ่ายล่วงหน้า (บาท)</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-credit-card"></i></span>
                                <input type="number" id="advance_payment" name="advance_payment" class="form-control @error('advance_payment') is-invalid @enderror" value="{{ old('advance_payment', $contract->advance_payment) }}" step="0.01">
                            </div>
                            @error('advance_payment')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-4">
                            <label for="electricity_rate" class="form-label">ค่าไฟฟ้า (บาท/หน่วย)</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-lightning"></i></span>
                                <input type="number" id="electricity_rate" name="electricity_rate" class="form-control @error('electricity_rate') is-invalid @enderror" value="{{ old('electricity_rate', $contract->electricity_rate) }}" step="0.01">
                            </div>
                            @error('electricity_rate')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-4">
                            <label for="water_rate" class="form-label">ค่าน้ำ (บาท/หน่วย)</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-droplet"></i></span>
                                <input type="number" id="water_rate" name="water_rate" class="form-control @error('water_rate') is-invalid @enderror" value="{{ old('water_rate', $contract->water_rate) }}" step="0.01">
                            </div>
                            @error('water_rate')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-4">
                            <label for="late_fee" class="form-label">ค่าปรับชำระล่าช้า (บาท/วัน)</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-exclamation-triangle"></i></span>
                                <input type="number" id="late_fee" name="late_fee" class="form-control @error('late_fee') is-invalid @enderror" value="{{ old('late_fee', $contract->late_fee) }}" step="0.01">
                            </div>
                            @error('late_fee')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-12">
                            <label for="other_fees" class="form-label">ค่าใช้จ่ายอื่นๆ</label>
                            <input type="text" id="other_fees" name="other_fees" class="form-control @error('other_fees') is-invalid @enderror" value="{{ old('other_fees', $contract->other_fees) }}">
                            @error('other_fees')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <h6 class="text-muted mb-3 border-bottom pb-2 mt-4"><i class="bi bi-file-earmark-text me-1"></i>ข้อตกลง</h6>
                    <div class="row g-3">
                        <div class="col-12">
                            <label for="terms" class="form-label">ข้อตกลงและเงื่อนไข</label>
                            <textarea id="terms" name="terms" class="form-control @error('terms') is-invalid @enderror" rows="4">{{ old('terms', $contract->terms) }}</textarea>
                            @error('terms')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <h6 class="text-muted mb-3 border-bottom pb-2 mt-4"><i class="bi bi-toggle-on me-1"></i>สถานะ</h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="status" class="form-label">สถานะ <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-toggle-on"></i></span>
                                <select id="status" name="status" class="form-select @error('status') is-invalid @enderror" required>
                                    <option value="draft" {{ old('status', $contract->status) === 'draft' ? 'selected' : '' }}>ร่างสัญญา</option>
                                    <option value="pending" {{ old('status', $contract->status) === 'pending' ? 'selected' : '' }}>รออนุมัติ</option>
                                    <option value="active" {{ old('status', $contract->status) === 'active' ? 'selected' : '' }}>ใช้งาน</option>
                                    <option value="completed" {{ old('status', $contract->status) === 'completed' ? 'selected' : '' }}>เสร็จสิ้น</option>
                                    <option value="cancelled" {{ old('status', $contract->status) === 'cancelled' ? 'selected' : '' }}>ยกเลิก</option>
                                </select>
                            </div>
                            @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <h6 class="text-muted mb-3 border-bottom pb-2 mt-4"><i class="bi bi-card-text me-1"></i>หมายเหตุ</h6>
                    <div class="row g-3">
                        <div class="col-12">
                            <label for="description" class="form-label">คำอธิบาย/รายละเอียด</label>
                            <textarea id="description" name="description" class="form-control @error('description') is-invalid @enderror" rows="3">{{ old('description', $contract->description) }}</textarea>
                            @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-12">
                            <label for="notes" class="form-label">หมายเหตุ</label>
                            <textarea id="notes" name="notes" class="form-control @error('notes') is-invalid @enderror" rows="2">{{ old('notes', $contract->notes) }}</textarea>
                            @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                        <a href="{{ route('contracts.index') }}" class="btn btn-secondary">
                            <i class="bi bi-x-circle me-1"></i>ยกเลิก
                        </a>
                        <button type="submit" class="btn btn-primary-custom">
                            <i class="bi bi-save me-1"></i>บันทึกข้อมูล
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

