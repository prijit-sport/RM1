@extends('layouts.app')

@section('content')
    <div class="container py-4">

        {{-- Header --}}
        <div class="d-flex align-items-center gap-2 mb-4">
            <a href="{{ route('bookings.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-arrow-left"></i>
            </a>
            <h5 class="mb-0"><i class="bi bi-calendar-check text-success me-2"></i>รายละเอียดการเข้าพัก #{{ $booking->id }}
            </h5>
        </div>

        <div class="row g-4">

            {{-- ===== คอลัมน์ซ้าย ===== --}}
            <div class="col-lg-6">

                {{-- ผู้เช่า --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="mb-0 text-primary">
                            <i class="bi bi-person-circle me-2"></i>ผู้เช่า
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-muted">ผู้เช่าคนที่ 1</label>
                            <p class="fs-5 fw-bold mb-0">
                                @if ($booking->guest)
                                    {{ $booking->guest->first_name }} {{ $booking->guest->last_name }}
                                @else
                                    <span class="text-muted fst-italic">ไม่ระบุผู้เช่า</span>
                                @endif
                            </p>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold text-muted">ผู้เช่าคนที่ 2</label>
                            <p class="fs-5 fw-bold mb-0">
                                @if ($booking->guest2)
                                    {{ $booking->guest2->first_name }} {{ $booking->guest2->last_name }}
                                @else
                                    <span class="text-muted fst-italic">ไม่ระบุผู้เช่า</span>
                                @endif
                            </p>
                        </div>

                        <div class="mb-0">
                            <label class="form-label fw-semibold text-muted">ผู้เช่าคนที่ 3</label>
                            <p class="fs-5 fw-bold mb-0">
                                @if ($booking->guest3)
                                    {{ $booking->guest3->first_name }} {{ $booking->guest3->last_name }}
                                @else
                                    <span class="text-muted fst-italic">ไม่ระบุผู้เช่า</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                {{-- ข้อมูลห้องพัก --}}
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="mb-0 text-primary">
                            <i class="bi bi-door-open me-2"></i>ข้อมูลห้องพัก
                        </h6>
                    </div>
                    <div class="card-body">

                        {{-- ห้องหมายเลข --}}
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

                        {{-- ประเภทห้อง --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-muted">ประเภทห้อง</label>
                            <p class="fs-5 fw-bold mb-0">
                                @if ($booking->room)
                                    @php
                                        $type = $booking->room->room_type;
                                        $typeLabel = match ($type) {
                                            'fan' => ['🌀 พัดลม', 'bg-info'],
                                            'air' => ['❄️ แอร์', 'bg-primary'],
                                            'air_conditioning' => ['❄️ แอร์', 'bg-primary'],
                                            default => [$type ?: '-', 'bg-secondary'],
                                        };
                                    @endphp
                                    <span class="badge {{ $typeLabel[1] }} text-white">{{ $typeLabel[0] }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </p>
                        </div>

                        {{-- โซน --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-muted">โซน</label>
                            <p class="fs-5 fw-bold mb-0">
                                @if ($booking->room?->zone)
                                    {{ $booking->room->zone }}
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </p>
                        </div>

                        {{-- วันที่เข้าพัก --}}
                        <div class="mb-0">
                            <label class="form-label fw-semibold text-muted">วันที่เข้าพัก</label>
                            <p class="fs-5 fw-bold mb-0">
                                @if ($booking->check_in_date)
                                    <i class="bi bi-calendar me-1"></i>{{ $booking->check_in_date->format('d/m/Y') }}
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </p>
                        </div>

                    </div>
                </div>

            </div>{{-- end col-left --}}


            {{-- ===== คอลัมน์ขวา ===== --}}
            <div class="col-lg-6">

                {{-- มัดจำและค่าใช้จ่ายแรกเข้า --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="mb-0 text-primary">
                            <i class="bi bi-cash-coin me-2"></i>มัดจำและค่าใช้จ่ายแรกเข้า
                        </h6>
                    </div>
                    <div class="card-body">

                        {{-- ค่าเช่าเดือนแรก --}}
                        <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                            <div>
                                <div class="fw-semibold">ค่าเช่าเดือนแรก</div>
                                <small class="text-muted">ห้องที่เลือก</small>
                            </div>
                            <div class="text-end">
                                <span class="fw-bold fs-5 text-dark">{{ number_format($booking->rent_amount ?? 0) }}</span>
                                <span class="text-muted ms-1">฿</span>
                            </div>
                        </div>

                        {{-- เงินมัดจำ --}}
                        <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                            <div>
                                <div class="fw-semibold">เงินมัดจำ</div>
                                <small class="text-muted">1 เดือน</small>
                            </div>
                            <div class="text-end">
                                <span
                                    class="fw-bold fs-5 text-dark">{{ number_format($booking->deposit_amount ?? 0) }}</span>
                                <span class="text-muted ms-1">฿</span>
                            </div>
                        </div>

                        {{-- ยอดรวมที่ต้องชำระ --}}
                        <div class="d-flex justify-content-between align-items-center pt-3 mt-1">
                            <div class="fw-bold fs-6">ยอดรวมที่ต้องชำระ</div>
                            <div class="text-end">
                                <span class="fw-bold text-primary"
                                    style="font-size:1.5rem">{{ number_format(($booking->rent_amount ?? 0) + ($booking->deposit_amount ?? 0)) }}</span>
                                <span class="text-primary ms-1 fw-semibold">฿</span>
                            </div>
                        </div>

                    </div>
                </div>

                {{-- เลขมิเตอร์เริ่มต้น --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="mb-0 text-primary">
                            <i class="bi bi-speedometer2 me-2"></i>เลขมิเตอร์เริ่มต้น
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-6">
                                <label class="form-label fw-semibold text-muted">
                                    <i class="bi bi-lightning-charge text-warning me-1"></i>มิเตอร์ไฟ
                                </label>
                                <p class="fs-5 fw-bold mb-0">
                                    {{ $booking->electric_meter_start ?? '—' }} หน่วย
                                </p>
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-semibold text-muted">
                                    <i class="bi bi-droplet text-info me-1"></i>มิเตอร์น้ำ
                                </label>
                                <p class="fs-5 fw-bold mb-0">
                                    {{ $booking->water_meter_start ?? '—' }} หน่วย
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- สถานะการจอง --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <label class="form-label fw-semibold text-muted mb-2">สถานะการจอง</label>
                        @php
                            $statusClasses = [
                                'pending' => 'bg-warning',
                                'confirmed' => 'bg-info',
                                'checked_in' => 'bg-success',
                                'checked_out' => 'bg-secondary',
                                'cancelled' => 'bg-danger',
                            ];
                        @endphp
                        <p class="mb-0">
                            <span class="badge {{ $statusClasses[$booking->status] ?? 'bg-light text-dark' }} fs-6">
                                {{ enum_th('booking_status', $booking->status) }}
                            </span>
                        </p>
                    </div>
                </div>

                {{-- หมายเหตุ --}}
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <label class="form-label fw-semibold text-muted mb-2">หมายเหตุ</label>
                        <p class="mb-0">
                            @if ($booking->notes)
                                <span class="text-dark">{{ $booking->notes }}</span>
                            @else
                                <span class="text-muted fst-italic">ไม่มีหมายเหตุ</span>
                            @endif
                        </p>
                    </div>
                </div>

            </div>{{-- end col-right --}}

        </div>{{-- end row --}}

        {{-- ===== Action Buttons ===== --}}
        <div class="d-flex justify-content-between mt-4">
            <a href="{{ route('bookings.index') }}" class="btn btn-outline-secondary px-4">
                <i class="bi bi-arrow-left me-1"></i> กลับ
            </a>

            <div class="d-flex gap-2">
                {{-- Edit Button --}}
                <a href="{{ route('bookings.edit', $booking) }}" class="btn btn-outline-primary px-4">
                    <i class="bi bi-pencil me-1"></i> Edit
                </a>

                {{-- Confirm Button (pending only) --}}
                @if ($booking->status == 'pending')
                    <form action="{{ route('bookings.confirm', $booking) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-success px-4"
                            onclick="return confirm('ยืนยันการเข้าพักนี้?')">
                            <i class="bi bi-check-circle me-1"></i> ยืนยัน
                        </button>
                    </form>
                @endif

                {{-- Cancel Button (not cancelled or checked_out) --}}
                @if ($booking->status != 'cancelled' && $booking->status != 'checked_out')
                    <form action="{{ route('bookings.cancel', $booking) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-danger px-4"
                            onclick="return confirm('ยกเลิกการเข้าพักนี้?')">
                            <i class="bi bi-x-circle me-1"></i> ยกเลิก
                        </button>
                    </form>
                @endif
            </div>
        </div>

    </div>
@endsection
