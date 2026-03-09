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
<div class="row g-4 mb-4">
    <!-- KPI Cards -->
    <div class="col-md-6 col-xl-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <p class="text-muted mb-1">ห้องทั้งหมด</p>
                    <h3 class="mb-0">{{ number_format($roomCount ?? 0) }}</h3>
                    <small class="text-success">
                        <i class="bi bi-arrow-up"></i> {{ number_format((($occupiedCount ?? 0) / max($roomCount ?? 1, 1)) * 100, 1) }}% ถูกเช่า
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
                    <h3 class="mb-0">{{ number_format(($roomCount ?? 0) - ($occupiedCount ?? 0)) }}</h3>
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

<!-- Charts Row -->
<div class="row g-4 mb-4">
    <div class="col-lg-8">
        <div class="table-card p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="mb-0">สถิติการเช่าห้อง</h5>
                <select class="form-select form-select-sm w-auto">
                    <option value="7">7 วันที่ผ่านมา</option>
                    <option value="30" selected>30 วันที่ผ่านมา</option>
                    <option value="90">90 วันที่ผ่านมา</option>
                </select>
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
            $occupiedPercent = ($occupiedCount ?? 0) / $totalRooms * 100;
            $availablePercent = (($roomCount ?? 0) - ($occupiedCount ?? 0)) / $totalRooms * 100;
            $maintenancePercent = ($maintenanceCount ?? 0) / $totalRooms * 100;
            $hasRooms = ($roomCount ?? 0) > 0;
            @endphp
            
            @if($hasRooms)
            <div class="mb-4">
                <div class="d-flex justify-content-between mb-2">
                    <span>ห้องที่ถูกเช่า</span>
                    <span class="fw-bold">{{ number_format($occupiedPercent, 1) }}%</span>
                </div>
                <div class="progress" style="height: 10px;">
                    <div class="progress-bar bg-primary" role="progressbar" 
                         style="width: {{ min($occupiedPercent, 95) }}%"></div>
                </div>
            </div>
            
            <div class="mb-4">
                <div class="d-flex justify-content-between mb-2">
                    <span>ห้องว่าง</span>
                    <span class="fw-bold">{{ number_format($availablePercent, 1) }}%</span>
                </div>
                <div class="progress" style="height: 10px;">
                    <div class="progress-bar bg-success" role="progressbar" 
                         style="width: {{ min($availablePercent, 95) }}%"></div>
                </div>
            </div>
            
            <div class="mb-4">
                <div class="d-flex justify-content-between mb-2">
                    <span>ห้องปรับปรุง</span>
                    <span class="fw-bold">{{ number_format($maintenancePercent, 1) }}%</span>
                </div>
                <div class="progress" style="height: 10px;">
                    <div class="progress-bar bg-warning" role="progressbar" 
                         style="width: {{ min($maintenancePercent, 95) }}%"></div>
                </div>
            </div>
            @else
            <div class="text-center text-muted py-4">
                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                ไม่มีข้อมูลห้อง
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Tables Row -->
<div class="row g-4">
    <div class="col-lg-6">
        <div class="table-card">
            <div class="p-4 border-bottom">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">การจองล่าสุด</h5>
                    <a href="{{ route('bookings.index') }}" class="btn btn-sm btn-outline-primary">
                        ดูทั้งหมด <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
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
                        <tr>
                            <td>
                                <a href="{{ route('rooms.show', $booking->room_id) }}" class="text-decoration-none">
                                    {{ $booking->room->room_number ?? '-' }}
                                </a>
                            </td>
                            <td>{{ $booking->guest->full_name ?? '-' }}</td>
                            <td>{{ \Carbon\Carbon::parse($booking->check_in_date)->format('d/m/Y') }}</td>
                            <td>
                                @php
                                $statusClass = match($booking->status) {
                                    'confirmed' => 'success',
                                    'pending' => 'warning',
                                    'cancelled' => 'danger',
                                    default => 'secondary'
                                };
                                @endphp
                                <span class="status-badge bg-{{ $statusClass }} bg-opacity-10 text-{{ $statusClass }}">
                                    {{ enum_bi('booking_status', $booking->status, $booking->status) }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                ไม่มีการจองล่าสุด
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
            <div class="p-4 border-bottom">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">ใบแจ้งหนี้ที่รอชำระ</h5>
                    <a href="{{ route('invoices.index') }}" class="btn btn-sm btn-outline-primary">
                        ดูทั้งหมด <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>Invoice</th>
                            <th>Guest</th>
                            <th>Total</th>
                            <th>Due Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pendingInvoices ?? [] as $invoice)
                        <tr>
                            <td>
                                <a href="{{ route('invoices.show', $invoice->id) }}" class="text-decoration-none">
                                    {{ $invoice->invoice_number }}
                                </a>
                            </td>
                            <td>{{ $invoice->booking->guest->full_name ?? '-' }}</td>
                            <td>{{ number_format($invoice->total, 2) }} &#3647;</td>
                            <td>
                                @php
                                $dueDate = \Carbon\Carbon::parse($invoice->due_date);
                                $isOverdue = $dueDate->isPast();
                                @endphp
                                <span class="{{ $isOverdue ? 'text-danger' : 'text-muted' }}">
                                    {{ $dueDate->format('d/m/Y') }}
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
    // Booking Chart
    const ctx = document.getElementById('bookingChart').getContext('2d');
    const bookingChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($chartLabels ?? ['ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.']) !!},
            datasets: [{
                label: 'จำนวนการจอง',
                data: {!! json_encode($chartData ?? [12, 19, 8, 15, 22, 18]) !!},
                backgroundColor: 'rgba(79, 70, 229, 0.8)',
                borderColor: 'rgba(79, 70, 229, 1)',
                borderWidth: 1,
                borderRadius: 6,
                maxBarThickness: 50
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    }
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






