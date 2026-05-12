@extends('layouts.app')

@section('title', 'รายงาน')

@section('page-title', 'รายงานสถิติและรายได้')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">📊 รายงานสถิติและรายได้</h4>
        <p class="text-muted mb-0">วันที่: {{ now()->format('d/m/Y H:i') }}</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('reports.export') }}" class="btn btn-success">
            <i class="bi bi-download me-1"></i>{{ __("ui.export") }}
        </a>
    </div>
</div>

<!-- Key Statistics -->
<div class="row g-4 mb-4">
    <!-- Rooms Stats -->
    <div class="col-md-6 col-lg-4">
        <div class="card stat-card border-start border-5 border-primary">
            <div class="card-body">
                <div class="text-muted mb-2">🛏️ ห้องทั้งหมด</div>
                <div class="fs-2 fw-bold text-primary">{{ $total_rooms }}</div>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-lg-4">
        <div class="card stat-card border-start border-5 border-success">
            <div class="card-body">
                <div class="text-muted mb-2">✓ ห้องว่าง</div>
                <div class="fs-2 fw-bold text-success">{{ $available_rooms }}</div>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-lg-4">
        <div class="card stat-card border-start border-5 border-warning">
            <div class="card-body">
                <div class="text-muted mb-2">👤 ห้องใช้งาน</div>
                <div class="fs-2 fw-bold text-warning">{{ $occupied_rooms }}</div>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-lg-4">
        <div class="card stat-card border-start border-5 border-danger">
            <div class="card-body">
                <div class="text-muted mb-2">🔧 ระหว่างซ่อม</div>
                <div class="fs-2 fw-bold text-danger">{{ $maintenance_rooms }}</div>
            </div>
        </div>
    </div>

    <!-- Occupancy Rate -->
    <div class="col-md-6 col-lg-4">
        <div class="card stat-card border-start border-5 border-info">
            <div class="card-body">
                <div class="text-muted mb-2">📈 อัตราการเข้าพัก</div>
                <div class="fs-2 fw-bold text-info">{{ $occupancy_rate }}%</div>
            </div>
        </div>
    </div>



    <!-- Revenue -->
    <div class="col-md-6 col-lg-4">
        <div class="card stat-card border-start border-5 border-warning">
            <div class="card-body">
                <div class="text-muted mb-2">💰 รายได้รวม</div>
                <div class="fs-2 fw-bold text-warning">฿{{ number_format($total_revenue, 0) }}</div>
            </div>
        </div>
    </div>

    <!-- Bookings This Month -->
    <div class="col-md-6 col-lg-4">
        <div class="card stat-card border-start border-5 border-info">
            <div class="card-body">
                <div class="text-muted mb-2">📋 การจองเดือนนี้</div>
                <div class="fs-2 fw-bold text-info">{{ $bookings_this_month }}</div>
            </div>
        </div>
    </div>

    <!-- Total Bookings -->
    <div class="col-md-6 col-lg-4">
        <div class="card stat-card border-start border-5 border-primary">
            <div class="card-body">
                <div class="text-muted mb-2">📊 การจองทั้งหมด</div>
                <div class="fs-2 fw-bold text-primary">{{ $total_bookings }}</div>
            </div>
        </div>
    </div>
</div>

<!-- Status Breakdown -->
<div class="row g-4">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0">📝 สถานะการจอง</h5>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    <div class="list-group-item d-flex justify-content-between align-items-center py-3">
                        <span>รอการยืนยัน</span>
                        <span class="badge bg-secondary rounded-pill">{{ $pending_bookings }}</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center py-3">
                        <span>ยืนยันแล้ว</span>
                        <span class="badge bg-primary rounded-pill">{{ $confirmed_bookings }}</span>
                    </div>

                    <div class="list-group-item d-flex justify-content-between align-items-center py-3">
                        <span>ยกเลิก</span>
                        <span class="badge bg-danger rounded-pill">{{ $cancelled }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Popular Rooms -->
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0">🏆 ห้องยอดนิยม</h5>
            </div>
            <div class="card-body p-0">
                @forelse ($popular_rooms as $room)
                    <div class="list-group-item d-flex justify-content-between align-items-center py-3">
                                                <span>
                            <strong>#{{ $room->room_number }}</strong> - {{ enum_bi('room_type', $room->room_type) }}
                        </span>
                        <span class="badge bg-primary rounded-pill">{{ $room->bookings_count }} การจอง</span>
                    </div>
                @empty
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-inbox fs-1"></i>
                        <p class="mt-2">ยังไม่มีข้อมูล</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection






