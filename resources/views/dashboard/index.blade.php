@extends('layouts.app')

@section('title', 'แดชบอร์ด')
@section('page-title', 'แดชบอร์ด')

@push('styles')
    <style>
        .stat-card {
            border: none;
            overflow: hidden;
        }

        .stat-card .stat-icon {
            width: 60px;
            height: 60px;
        }

        .chart-container {
            position: relative;
            height: 300px;
        }

        .progress-card {
            transition: all 0.3s ease;
        }

        .progress-card:hover {
            transform: translateY(-3px);
        }
    </style>
@endpush

@section('content')

    {{-- ═══ KPI Cards ═══ --}}
    <div class="row g-4 mb-4">
        <div class="col-md-6 col-xl-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted mb-1">ห้องทั้งหมด</p>
                        <h3 class="mb-0">{{ number_format($roomCount ?? 0) }}</h3>
                        <small class="text-success">
                            <i class="bi bi-arrow-up"></i>
                            {{ number_format((($occupiedCount ?? 0) / max($roomCount ?? 1, 1)) * 100, 1) }}% ถูกเช่า
                        </small>
                    </div>
                    <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                        <i class="bi bi-door-open"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted mb-1">ห้องว่าง</p>
                        <h3 class="mb-0">{{ number_format($availableCount ?? 0) }}</h3>
                        <small class="text-info">
                            <i class="bi bi-info-circle"></i> พร้อมให้เช่า
                        </small>
                    </div>
                    <div class="stat-icon bg-success bg-opacity-10 text-success">
                        <i class="bi bi-check-circle"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted mb-1">ผู้เช่า</p>
                        <h3 class="mb-0">{{ number_format($guestCount ?? 0) }}</h3>
                        <small class="text-warning">
                            <i class="bi bi-people"></i> ลูกค้าทั้งหมด
                        </small>
                    </div>
                    <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                        <i class="bi bi-people-fill"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted mb-1">การจอง</p>
                        <h3 class="mb-0">{{ number_format($bookingCount ?? 0) }}</h3>
                        <small class="text-danger">
                            <i class="bi bi-clock"></i> รอตรวจสอบ
                        </small>
                    </div>
                    <div class="stat-icon bg-danger bg-opacity-10 text-danger">
                        <i class="bi bi-calendar-check"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══ Room Status by Type ═══ --}}
    <div class="row g-4 mb-4">
        @php
            $typeConfig = [
                'fan' => ['label' => 'Fan (พัดลม)', 'icon' => 'bi-wind', 'color' => 'primary'],
                'air' => ['label' => 'Air (แอร์)', 'icon' => 'bi-snow', 'color' => 'info'],
            ];
            $statuses = [
                'available' => ['label' => 'ว่าง', 'class' => 'success'],
                'occupied' => ['label' => 'ไม่ว่าง', 'class' => 'primary'],
                'maintenance' => ['label' => 'ซ่อม', 'class' => 'warning'],
            ];
        @endphp

        @foreach ($typeConfig as $typeKey => $typeInfo)
            @php
                $stats = $roomTypeStats[$typeKey] ?? ['available' => 0, 'occupied' => 0, 'maintenance' => 0];
                $total = ($stats['available'] ?? 0) + ($stats['occupied'] ?? 0) + ($stats['maintenance'] ?? 0);
            @endphp
            <div class="col-lg-6">
                <div class="table-card p-4 h-100">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">
                            <i class="bi {{ $typeInfo['icon'] }} text-{{ $typeInfo['color'] }} me-2"></i>
                            สรุปโซนสถานะ: {{ $typeInfo['label'] }}
                        </h5>
                        <span class="text-muted">ทั้งหมด {{ number_format($total) }} ห้อง</span>
                    </div>

                    @foreach ($statuses as $statusKey => $meta)
                        @php
                            $count = (int) ($stats[$statusKey] ?? 0);
                            $pct = $total > 0 ? ($count / $total) * 100 : 0;
                        @endphp
                        <div class="d-flex justify-content-between mb-2">
                            <span>{{ $meta['label'] }}</span>
                            <span class="fw-bold">
                                {{ number_format($count) }} ห้อง ({{ number_format($pct, 1) }}%)
                            </span>
                        </div>
                        <div class="progress" style="height: 10px; margin-bottom: 14px;">
                            <div class="progress-bar bg-{{ $meta['class'] }}" role="progressbar"
                                style="width: {{ min($pct, 100) }}%"></div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>

    {{-- ═══ Charts Row ═══ --}}
    <div class="row g-4 mb-4">
        <div class="col-lg-8">
            <div class="table-card p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="mb-0">สถิติการเช่าห้อง</h5>
                </div>
                <div class="chart-container">
                    <canvas id="bookingChart"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="table-card p-4">
                <h5 class="mb-4">สถานะห้อง</h5>
                @php
                    $totalRooms = max($roomCount ?? 1, 1);
                    $occupiedPercent = (($occupiedCount ?? 0) / $totalRooms) * 100;
                    $availablePercent = (($availableCount ?? 0) / $totalRooms) * 100;
                    $maintenancePct = (($maintenanceCount ?? 0) / $totalRooms) * 100;
                @endphp

                @if (($roomCount ?? 0) > 0)
                    <div class="mb-4">
                        <div class="d-flex justify-content-between mb-2">
                            <span>ห้องที่ถูกเช่า</span>
                            <span class="fw-bold">{{ number_format($occupiedPercent, 1) }}%</span>
                        </div>
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar bg-primary" style="width: {{ min($occupiedPercent, 100) }}%"></div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="d-flex justify-content-between mb-2">
                            <span>ห้องว่าง</span>
                            <span class="fw-bold">{{ number_format($availablePercent, 1) }}%</span>
                        </div>
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar bg-success" style="width: {{ min($availablePercent, 100) }}%"></div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="d-flex justify-content-between mb-2">
                            <span>ห้องปรับปรุง</span>
                            <span class="fw-bold">{{ number_format($maintenancePct, 1) }}%</span>
                        </div>
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar bg-warning" style="width: {{ min($maintenancePct, 100) }}%"></div>
                        </div>
                    </div>
                @else
                    <div class="text-center text-muted py-4">
                        <i class="bi bi-inbox fs-1 d-block mb-2"></i>ไม่มีข้อมูลห้อง
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ═══ Tables Row ═══ --}}
    <div class="row g-4">
        <div class="col-lg-6">
            <div class="table-card">
                <div class="p-4 border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">การจองล่าสุด</h5>
                    <a href="{{ route('bookings.index') }}" class="btn btn-sm btn-outline-primary">
                        ดูทั้งหมด <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>ห้อง</th>
                                <th>ผู้เช่า</th>
                                <th>วันที่</th>
                                <th>สถานะ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentBookings ?? [] as $booking)
                                @php
                                    $bRoom = $booking['room_id'] ?? null;
                                    $bGuest = $booking['guest_name'] ?? '-';
                                    $bDateStr = $booking['check_in_date'] ?? '-';
                                    $bStatus = (string) ($booking['status'] ?? '');
                                    if ($bStatus === 'confirmed') {
                                        $sc = 'success';
                                        $sl = 'ยืนยันแล้ว';
                                    } elseif ($bStatus === 'cancelled') {
                                        $sc = 'danger';
                                        $sl = 'ยกเลิก';
                                    } else {
                                        $sc = 'secondary';
                                        $sl = $bStatus !== '' ? $bStatus : '-';
                                    }
                                @endphp
                                <tr>
                                    <td>
                                        @if ($bRoom !== null)
                                            <a href="{{ route('rooms.show', $bRoom) }}" class="text-decoration-none">
                                                {{ $booking['room_number'] ?? '-' }}
                                            </a>
                                        @else
                                            {{ $booking['room_number'] ?? '-' }}
                                        @endif
                                    </td>
                                    <td>{{ $bGuest }}</td>
                                    <td>{{ $bDateStr }}</td>
                                    <td>
                                        <span class="badge bg-{{ $sc }} bg-opacity-10 text-{{ $sc }}">
                                            {{ $sl }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">
                                        <i class="bi bi-inbox fs-1 d-block mb-2"></i>ไม่มีการจองล่าสุด
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="table-card">
                <div class="p-4 border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">ใบแจ้งหนี้ที่รอชำระ</h5>
                    <a href="{{ route('invoices.index') }}" class="btn btn-sm btn-outline-primary">
                        ดูทั้งหมด <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>Invoice</th>
                                <th>ผู้เช่า</th>
                                <th>ยอด</th>
                                <th>ครบกำหนด</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pendingInvoices ?? [] as $invoice)
                                @php
                                    $invId = $invoice['id'] ?? null;
                                    $invNumber = $invoice['invoice_number'] ?? '-';
                                    $invTotal = $invoice['total'] ?? 0;
                                    $invGuest = $invoice['guest_name'] ?? '-';
                                    $invDueStr = $invoice['due_date'] ?? '-';
                                    $invOverdue = (bool) ($invoice['is_overdue'] ?? false);
                                @endphp
                                <tr>
                                    <td>
                                        @if ($invId)
                                            <a href="{{ route('invoices.show', $invId) }}" class="text-decoration-none">
                                                {{ $invNumber }}
                                            </a>
                                        @else
                                            {{ $invNumber }}
                                        @endif
                                    </td>
                                    <td>{{ $invGuest }}</td>
                                    <td>{{ number_format($invTotal, 2) }} ฿</td>
                                    <td>
                                        <span class="{{ $invOverdue ? 'text-danger fw-bold' : 'text-muted' }}">
                                            {{ $invDueStr }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">
                                        <i class="bi bi-check-circle fs-1 d-block mb-2 text-success"></i>
                                        ไม่มีบิลที่รอชำระ
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const monthlyData = @json($monthlyBookings ?? []);
        const labels = monthlyData.map(d => d.label);
        const counts = monthlyData.map(d => d.count);

        const ctx = document.getElementById('bookingChart').getContext('2d');

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'จำนวนการจอง',
                    data: counts,
                    backgroundColor: 'rgba(79, 70, 229, 0.8)',
                    borderColor: 'rgba(79, 70, 229, 1)',
                    borderWidth: 1,
                    borderRadius: 6,
                    maxBarThickness: 50,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: ctx => ' ' + ctx.parsed.y + ' การจอง'
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1,
                            callback: val => Number.isInteger(val) ? val : null,
                        },
                        grid: {
                            color: 'rgba(0,0,0,0.05)'
                        },
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    </script>
@endpush
