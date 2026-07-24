@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-warning text-dark py-3">
                        <h5 class="mb-0"><i class="bi bi-door-open"></i> แก้ไขห้องพัก</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('rooms.update', $room->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label class="form-label required">หมายเลขห้อง</label>
                                    <input type="text" name="room_number"
                                        class="form-control @error('room_number') is-invalid @enderror"
                                        value="{{ old('room_number', $room->room_number) }}" required>
                                    @error('room_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label required">ประเภทห้อง</label>
                                    <select name="room_type" class="form-select @error('room_type') is-invalid @enderror"
                                        required>
                                        <option value="fan" {{ $room->room_type == 'fan' ? 'selected' : '' }}>พัด
                                        </option>
                                        <option value="air" {{ $room->room_type == 'air' ? 'selected' : '' }}>แอร์
                                        </option>
                                    </select>
                                    @error('room_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <label class="form-label required">ค่าเช่าต่อเดือน (บาท)</label>
                                        <input type="number" name="price_per_month"
                                            class="form-control @error('price_per_month') is-invalid @enderror"
                                            value="{{ old('price_per_month', $room->price_per_month) }}" required
                                            min="0">
                                        @error('price_per_month')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label required">ความจุ (คน)</label>
                                        <input type="number" name="capacity"
                                            class="form-control @error('capacity') is-invalid @enderror"
                                            value="{{ old('capacity', $room->capacity) }}" required min="1">
                                        @error('capacity')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="row mb-4">
                                        <div class="col-md-4">
                                            <label class="form-label required">ชั้น</label>
                                            <input type="number" name="floor"
                                                class="form-control @error('floor') is-invalid @enderror"
                                                value="{{ old('floor', $room->floor) }}" required min="1">
                                            @error('floor')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">อาคาร</label>
                                            <input type="text" name="building"
                                                class="form-control @error('building') is-invalid @enderror"
                                                value="{{ old('building', $room->building) }}" placeholder="เช่น A, B">
                                            @error('building')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label required">สถานะ</label>
                                            <select name="status" class="form-select @error('status') is-invalid @enderror"
                                                required>


                                                <option value="available"
                                                    {{ $room->status == 'available' ? 'selected' : '' }}>ว่าง</option>
                                                <option value="occupied"
                                                    {{ $room->status == 'occupied' ? 'selected' : '' }}>มีผู้เช่า</option>
                                                <option value="maintenance"
                                                    {{ $room->status == 'maintenance' ? 'selected' : '' }}>ซ่อมบำรุง
                                                </option>
                                            </select>
                                            @error('status')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-4">
                                            <label class="form-label">รายละเอียด</label>
                                            <textarea name="description" class="form-control" rows="3">{{ old('description', $room->description) }}</textarea>
                                        </div>

                                        <div class="d-flex justify-content-between">
                                            <a href="{{ route('rooms.index') }}" class="btn btn-secondary">
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
