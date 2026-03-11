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
                    <form action="{{ route('rooms.store') }}" method="POST">
                        @csrf
                        
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label required">หมายเลขห้อง</label>
                                <input type="text" name="room_number" class="form-control @error('room_number') is-invalid @enderror" required>
                                @error('room_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label required">ประเภทห้อง</label>
                                <select name="room_type" class="form-select @error('room_type') is-invalid @enderror" required>
                                    <option value="">-- เลือกประเภท --</option>
                                    <option value="Standard">Standard</option>
                                    <option value="Superior">Superior</option>
                                    <option value="Deluxe">Deluxe</option>
                                    <option value="Suite">Suite</option>
                                </select>
                                @error('room_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label required">ค่าเช่าต่อเดือน (บาท)</label>
                                <input type="number" name="price_per_month" class="form-control @error('price_per_month') is-invalid @enderror" required min="0">
                                @error('price_per_month')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label required">ความจุ (คน)</label>
                                <input type="number" name="capacity" class="form-control @error('capacity') is-invalid @enderror" required min="1">
                                @error('capacity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label required">ชั้น</label>
                                <input type="number" name="floor" class="form-control @error('floor') is-invalid @enderror" required min="1">
                                @error('floor')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label required">สถานะ</label>
                                <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                    <option value="available">ว่าง</option>
                                    <option value="occupied">มีผู้เช่า</option>
                                    <option value="maintenance">ซ่อมบำรุง</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                        <div class="mb-4">
                            <label class="form-label">รายละเอียด</label>
                            <textarea name="description" class="form-control" rows="3"></textarea>
                        </div>

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
@endsection
