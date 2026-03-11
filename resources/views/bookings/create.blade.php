@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white py-3">
                    <h5 class="mb-0"><i class="bi bi-calendar-plus"></i> สร้างการจองใหม่</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('bookings.store') }}" method="POST">
                        @csrf

                        <div class="row mb-4">
                            <div class="col-md-12">
                                <label for="guest_id" class="form-label required">ผู้เช่า</label>
                                <select id="guest_id" name="guest_id" class="form-select @error('guest_id') is-invalid @enderror" required>
                                    <option value="">-- เลือกผู้เช่า --</option>
                                    @foreach($guests as $guest)
                                        <option value="{{ $guest->id }}">{{ $guest->first_name }} {{ $guest->last_name }}</option>
                                    @endforeach
                                </select>
                                @error('guest_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-12">
                                <label for="room_id" class="form-label required">ห้องพัก</label>
                                <select id="room_id" name="room_id" class="form-select @error('room_id') is-invalid @enderror" required>
                                    <option value="">-- เลือกห้องพัก --</option>
                                    @foreach($rooms as $room)
                                        <option value="{{ $room->id }}">{{ $room->room_number }} - {{ $room->room_type }} ({{ number_format($room->price_per_month) }} บาท/เดือน)</option>
                                    @endforeach
                                </select>
                                @error('room_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="check_in_date" class="form-label required">วันที่เช็คอิน</label>
                                <input id="check_in_date" type="date" name="check_in_date" class="form-control @error('check_in_date') is-invalid @enderror" required>
                                @error('check_in_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="check_out_date" class="form-label required">วันที่เช็คเอาท์</label>
                                <input id="check_out_date" type="date" name="check_out_date" class="form-control @error('check_out_date') is-invalid @enderror" required>
                                @error('check_out_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-12">
                                <label for="status" class="form-label required">สถานะ</label>
                                <select id="status" name="status" class="form-select @error('status') is-invalid @enderror" required>
                                    <option value="pending">รอยืนยัน</option>
                                    <option value="confirmed">ยืนยันแล้ว</option>
                                    <option value="cancelled">ยกเลิก</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="notes" class="form-label">หมายเหตุ</label>
                            <textarea id="notes" name="notes" class="form-control" rows="3"></textarea>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('bookings.index') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> ยกเลิก
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> บันทึกการจอง
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
