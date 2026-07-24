@extends('layouts.app')

@section('title', 'สถานะผู้เช่าและห้องพัก')

@section('content')
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <div>
                <h3 class="fw-bold mb-1">📋 สถานะผู้เช่าและห้องพัก</h3>
                <p class="text-muted mb-0">อัปเดตข้อมูล ณ วันที่ {{ now()->format('d/m/Y H:i') }}</p>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-12 col-md-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <div class="text-muted small">ผู้เช่าทั้งหมด (กำลังพัก)</div>
                        <div class="fs-3 fw-bold">{{ $totalTenants ?? 0 }}</div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <div class="text-muted small">ห้องแอร์</div>
                        <div class="fs-3 fw-bold text-primary">{{ $acRoomsCount ?? 0 }}</div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <div class="text-muted small">ห้องพัดลม</div>
                        <div class="fs-3 fw-bold text-info">{{ $fanRoomsCount ?? 0 }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <form method="GET" action="{{ url()->current() }}">
                    <div class="row g-2 align-items-end">
                        <div class="col-12 col-md-4">
                            <label class="form-label">ค้นหาด้วยชื่อ/เลขห้อง</label>
                            <input type="text" name="search" value="{{ request('search') }}" class="form-control"
                                placeholder="เช่น สมชาย หรือ 101">
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label">โซน</label>
                            <select name="zone" class="form-select">
                                <option value="">-- ทั้งหมด --</option>
                                @foreach ($zones ?? [] as $z)
                                    <option value="{{ $z }}" {{ request('zone') == $z ? 'selected' : '' }}>
                                        {{ $z }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-4 d-grid gap-2 d-md-flex">
                            <button class="btn btn-primary px-4">
                                <i class="bi bi-search me-1"></i>ค้นหา
                            </button>
                            <a href="{{ url()->current() }}" class="btn btn-outline-secondary">
                                ล้างค่า
                            </a>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-12">
                            <small class="text-muted">
                                ระบบนี้แสดงข้อมูลจาก Booking (สถานะ: checked_in) และสรุปบิล (paid/overdue) ตามตัวแปรจาก
                                Controller
                            </small>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0">รายชื่อผู้เช่า</h5>
                    <div class="text-muted small">พบทั้งหมด {{ $tenants?->total() ?? 0 }} รายการ</div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ผู้เช่า</th>
                            <th>เลขห้อง</th>
                            <th>ประเภทห้อง</th>
                            <th>โซน</th>
                            <th>สถานะสัญญา</th>
                            <th>วันหมดสัญญา</th>
                            <th>สถานะการชำระ</th>
                            <th class="text-end">บิล/หมายเหตุ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tenants ?? [] as $b)
                            @php
                                $guest = $b->guest;
                                $room = $b->room;

                                $roomType = $room?->room_type;
                                $roomTypeLabel = match ($roomType) {
                                    'air' => 'แอร์',
                                    'air_conditioning' => 'แอร์',
                                    'fan' => 'พัดลม',
                                    default => $roomType ?? '-',
                                };

                                $zone = $room?->zone ?? '-';

                                // Booking/Contract status
                                // หมายเหตุ: Controller ปัจจุบันยังส่งแค่ tenants แต่มี invoices relation ที่ eager load
                                // ดังนั้นเราจะแสดงสถานะการชำระจาก invoices
                                $invoices = $b->invoices ?? collect();

                                // หาบิลที่ใกล้/ล่าสุด (เพื่อแสดงสถานะ)
                                $latestInvoice = null;
                                if ($invoices instanceof \Illuminate\Support\Collection) {
                                    $latestInvoice = $invoices->sortByDesc('id')->first();
                                }

                                $paymentStatus = '—';
                                $dueText = '—';
                                if ($latestInvoice) {
                                    $paymentStatus = match ($latestInvoice->status) {
                                        'paid' => 'ชำระแล้ว',
                                        'overdue' => 'เกินกำหนด',
                                        'pending' => 'รอชำระ',
                                        default => $latestInvoice->status ?? '—',
                                    };

                                    $dueDate = $latestInvoice->due_date ?? null;
                                    if ($dueDate) {
                                        $dueText =
                                            $dueDate instanceof \Carbon\Carbon ? $dueDate->format('d/m/Y') : $dueDate;
                                    }
                                }
                            @endphp

                            <tr>
                                <td>
                                    @if ($guest)
                                        {{ $guest->first_name ?? '-' }} {{ $guest->last_name ?? '' }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>{{ $room?->room_number ?? '-' }}</td>
                                <td>
                                    <span class="badge bg-primary">{{ $roomTypeLabel }}</span>
                                </td>
                                <td>{{ $zone }}</td>
                                <td>
                                    <span class="badge bg-success">กำลังพัก</span>
                                </td>
                                <td>{{ $b->check_in_date
                                    ? ($b->check_in_date instanceof \Carbon\Carbon
                                        ? $b->check_in_date->format('d/m/Y')
                                        : $b->check_in_date)
                                    : '-' }}
                                </td>
                                <td>
                                    @if ($latestInvoice)
                                        <span
                                            class="badge {{ $latestInvoice->status === 'paid' ? 'bg-success' : ($latestInvoice->status === 'overdue' ? 'bg-danger' : 'bg-warning text-dark') }}">
                                            {{ $paymentStatus }}
                                        </span>
                                    @else
                                        <span class="text-muted">ไม่มีบิล</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    @if ($latestInvoice)
                                        <div class="small text-muted">
                                            กำหนด: {{ $dueText }}
                                        </div>
                                        <div class="fw-bold">฿{{ number_format($latestInvoice->total ?? 0, 2) }}</div>
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">
                                    ไม่พบข้อมูลผู้เช่าตามเงื่อนไข
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-3">
                {{ $tenants?->links() }}
            </div>
        </div>
    </div>
@endsection
