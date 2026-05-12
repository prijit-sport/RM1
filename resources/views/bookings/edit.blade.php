@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-warning text-dark py-3">
                    <h5 class="mb-0"><i class="bi bi-calendar-check"></i> แก้ไขการจอง</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('bookings.update', $booking->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <label class="form-label required">ผู้เช่า</label>
                                <select name="guest_id" class="form-select @error('guest_id') is-invalid @enderror" required>
                                    <option value="">-- เลือกผู้เช่า --</option>
                                    @foreach($guests as $guest)
                                        <option value="{{ $guest->id }}" {{ $booking->guest_id == $guest->id ? 'selected' : '' }}>
                                            {{ $guest->first_name }} {{ $guest->last_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('guest_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                        <div class="row mb-4">
                            <div class="col-md-12">
                                <label class="form-label required">ห้องพัก</label>
                                <select name="room_id" class="form-select @error('room_id') is-invalid @enderror" required>
                                    <option value="">-- เลือกห้องพัก --</option>
                                    @foreach($rooms as $room)
                                        <option value="{{ $room->id }}" {{ $booking->room_id == $room->id ? 'selected' : '' }}>
                                            {{ $room->room_number }} - {{ $room->room_type }} ({{ number_format($room->price_per_month) }} บาท/เดือน)
                                        </option>
                                    @endforeach
                                </select>
                                @error('room_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                        <div class="row mb-4">
                            <div class="col-md-12">
                                <label class="form-label required">สถานะ</label>

                                <select name="status" class="form-select @error('status') is-invalid @enderror" required>

                                    <option value="pending" {{ $booking->status == 'pending' ? 'selected' : '' }}>รอยืนยัน</option>
                                    <option value="confirmed" {{ $booking->status == 'confirmed' ? 'selected' : '' }}>ยืนยันแล้ว</option>
                                    <option value="checked_in" {{ $booking->status == 'checked_in' ? 'selected' : '' }}>เช็คอินแล้ว</option>
                                    <option value="checked_out" {{ $booking->status == 'checked_out' ? 'selected' : '' }}>เช็คเอาท์แล้ว</option>
                                    <option value="cancelled" {{ $booking->status == 'cancelled' ? 'selected' : '' }}>ยกเลิก</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                        <div class="mb-4">
                            <label class="form-label">หมายเหตุ</label>
                            <textarea name="notes" class="form-control" rows="3">{{ old('notes', $booking->notes) }}</textarea>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('bookings.index') }}" class="btn btn-secondary">
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
