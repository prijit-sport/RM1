@extends('layouts.app')

@section('content')
    <div class="container py-4">
        {{-- Header --}}
        <div class="d-flex align-items-center gap-2 mb-4">
            <a href="{{ route('bookings.show', $booking) }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-arrow-left"></i>
            </a>
            <h5 class="mb-0"><i class="bi bi-pencil-square text-primary me-2"></i>แก้ไขการเข้าพัก #{{ $booking->id }}</h5>
        </div>

        <form action="{{ route('bookings.update', $booking) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row g-4">
                {{-- ===== คอลัมน์ซ้าย ===== --}}
                <div class="col-lg-6">
                    {{-- ผู้เช่า --}}
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="mb-0 text-primary">
                                <i class="bi bi-people-fill me-2"></i>ผู้เช่า (1-3 คน)
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">ผู้เช่าคนที่ 1 <span
                                        class="text-danger">*</span></label>
                                <select name="guest_id" class="form-select @error('guest_id') is-invalid @enderror"
                                    required>
                                    <option value="">-- เลือกผู้เช่า --</option>
                                    @foreach ($guests as $guest)
                                        <option value="{{ $guest->id }}"
                                            {{ old('guest_id', $booking->guest_id) == $guest->id ? 'selected' : '' }}>
                                            {{ $guest->first_name }} {{ $guest->last_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('guest_id')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">ผู้เช่าคนที่ 2</label>
                                <select name="guest_id_2" class="form-select @error('guest_id_2') is-invalid @enderror">
                                    <option value="">-- ไม่ระบุ --</option>
                                    @foreach ($guests as $guest)
                                        <option value="{{ $guest->id }}"
                                            {{ old('guest_id_2', $booking->guest_id_2) == $guest->id ? 'selected' : '' }}>
                                            {{ $guest->first_name }} {{ $guest->last_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('guest_id_2')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-0">
                                <label class="form-label fw-semibold">ผู้เช่าคนที่ 3</label>
                                <select name="guest_id_3" class="form-select @error('guest_id_3') is-invalid @enderror">
                                    <option value="">-- ไม่ระบุ --</option>
                                    @foreach ($guests as $guest)
                                        <option value="{{ $guest->id }}"
                                            {{ old('guest_id_3', $booking->guest_id_3) == $guest->id ? 'selected' : '' }}>
                                            {{ $guest->first_name }} {{ $guest->last_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('guest_id_3')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- ข้อมูลห้อง/วันที่เข้าพัก --}}
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="mb-0 text-primary">
                                <i class="bi bi-door-open me-2"></i>ข้อมูลห้องพัก
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label fw-semibold text-muted">ห้องหมายเลข</label>
                                <p class="fs-5 fw-bold mb-0">
                                    @if ($booking->room)
                                        <span class="badge bg-dark">#{{ $booking->room->room_number }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </p>
                            </div>

                            <div class="mb-0">
                                <label class="form-label fw-semibold">วันที่เข้าพัก <span
                                        class="text-danger">*</span></label>
                                <input type="date" name="check_in_date"
                                    class="form-control @error('check_in_date') is-invalid @enderror"
                                    value="{{ old('check_in_date', optional($booking->check_in_date)->format('Y-m-d')) }}"
                                    required>
                                @error('check_in_date')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ===== คอลัมน์ขวา ===== --}}
                <div class="col-lg-6">
                    {{-- มิเตอร์ --}}
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="mb-0 text-primary">
                                <i class="bi bi-speedometer2 me-2"></i>เลขมิเตอร์เริ่มต้น
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-6">
                                    <label class="form-label fw-semibold">มิเตอร์ไฟ (หน่วย) <span
                                            class="text-danger">*</span></label>
                                    <input type="number" name="electric_meter_start" min="0"
                                        class="form-control @error('electric_meter_start') is-invalid @enderror"
                                        value="{{ old('electric_meter_start', $booking->electric_meter_start) }}" required>
                                    @error('electric_meter_start')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-6">
                                    <label class="form-label fw-semibold">มิเตอร์น้ำ (หน่วย) <span
                                            class="text-danger">*</span></label>
                                    <input type="number" name="water_meter_start" min="0"
                                        class="form-control @error('water_meter_start') is-invalid @enderror"
                                        value="{{ old('water_meter_start', $booking->water_meter_start) }}" required>
                                    @error('water_meter_start')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- หมายเหตุ + ปุ่ม --}}
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">หมายเหตุ</label>
                                <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" rows="4">{{ old('notes', $booking->notes) }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-flex justify-content-between mt-4">
                                <a href="{{ route('bookings.show', $booking) }}" class="btn btn-outline-secondary px-4">
                                    <i class="bi bi-x-circle me-1"></i>ยกเลิก
                                </a>
                                <button type="submit" class="btn btn-primary px-5">
                                    <i class="bi bi-check-circle me-1"></i>บันทึกการเข้าพัก
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
