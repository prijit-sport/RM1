@extends('layouts.app')

@section('title', 'รายงานประสิทธิภาพธุรกิจ')

@section('content')
<style>
    /* ปรับแต่งความสวยงามเพิ่มเติม */
    .report-stat-card {
        border: none;
        border-radius: 15px;
        transition: transform 0.2s;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }
    .report-stat-card:hover {
        transform: translateY(-5px);
    }
    .card-title-icon {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        margin-bottom: 15px;
    }
</style>

<div class="container-fluid py-2">
    {{-- Header Section --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-dark mb-1">📊 รายงานสถิติและรายได้</h3>
            <p class="text-muted mb-0">ข้อมูลสรุป ณ วันที่ {{ now()->format('d/m/Y H:i') }}</p>
        </div>
        <div>
            <a href="{{ route('reports.export') }}" class="btn btn-success px-4 shadow-sm">
                <i class="bi bi-file-earmark-excel me-2"></i>ส่งออกข้อมูล (Excel)
            </a>
        </div>
    </div>

    {{-- Main KPI Section --}}
    <div class="row g-4 mb-5">
        <div class="col-md-6 col-lg-4">
            <div class="card report-stat-card h-100 p-3" style="background: linear-gradient(135deg, #fff 0%, #fff9e6 100%); border-left: 5px solid #ffc107;">
                <div class="card-body">
                    <div class="card-title-icon bg-warning bg-opacity-10 text-warning">
                        <i class="bi bi-currency-dollar fs-4"></i>
                    </div>
                    <h6 class="text-muted fw-bold text-uppercase small">รายได้รวมทั้งหมด</h6>
                    <h2 class="display-6 fw-bold text-dark">฿{{ number_format($total_revenue, 2) }}</h2>
                    <p class="text-success mb-0 small"><i class="bi bi-graph-up me-1"></i> รายได้สะสมจากระบบ</p>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-4">
            <div class="card report-stat-card h-100 p-3" style="background: linear-gradient(135deg, #fff 0%, #eef6ff 100%); border-left: 5px solid #0dcaf0;">
                <div class="card-body">
                    <div class="card-title-icon bg-info bg-opacity-10 text-info">
                        <i class="bi bi-percent fs-4"></i>
                    </div>
                    <h6 class="text-muted fw-bold text-uppercase small">อัตราการเข้าพัก</h6>
                    <h2 class="display-6 fw-bold text-dark">{{ $occupancy_rate }}%</h2>
                    <div class="progress" style="height: 6px;">
                        <div class="progress-bar bg-info" role="progressbar" style="width: {{ $occupancy_rate }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-4">
            <div class="card report-stat-card h-100 p-3" style="background: linear-gradient(135deg, #fff 0%, #f0fff4 100%); border-left: 5px solid #198754;">
                <div class="card-body">
                    <div class="card-title-icon bg-success bg-opacity-10 text-success">
                        <i class="bi bi-calendar-check fs-4"></i>
                    </div>
                    <h6 class="text-muted fw-bold text-uppercase small">การจองในเดือนนี้</h6>
                    <h2 class="display-6 fw-bold text-dark">{{ $bookings_this_month }} <small class="fs-6 text-muted">รายการ</small></h2>
                    <p class="text-muted mb-0 small">จากทั้งหมด {{ $total_bookings }} รายการ</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Secondary Stats & Breakdown --}}
    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3 border-0">
                    <h5 class="fw-bold mb-0"><i class="bi bi-door-open me-2 text-primary"></i>สรุปสถานะห้องพัก</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4">ประเภทสถานะ</th>
                                    <th class="text-center">จำนวนห้อง</th>
                                    <th>สัดส่วน</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="ps-4"><span class="badge bg-primary bg-opacity-10 text-primary p-2 rounded-circle me-2"><i class="bi bi-house"></i></span> ห้องทั้งหมด</td>
                                    <td class="text-center fw-bold">{{ $total_rooms }}</td>
                                    <td style="width: 40%;">
                                        <div class="progress" style="height: 5px;"><div class="progress-bar" style="width: 100%"></div></div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="ps-4"><span class="badge bg-success bg-opacity-10 text-success p-2 rounded-circle me-2"><i class="bi bi-check-lg"></i></span> ห้องว่าง</td>
                                    <td class="text-center fw-bold">{{ $available_rooms }}</td>
                                    <td>
                                        <div class="progress" style="height: 5px;"><div class="progress-bar bg-success" style="width: {{ ($available_rooms/$total_rooms)*100 }}%"></div></div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="ps-4"><span class="badge bg-warning bg-opacity-10 text-warning p-2 rounded-circle me-2"><i class="bi bi-person"></i></span> ห้องใช้งาน</td>
                                    <td class="text-center fw-bold">{{ $occupied_rooms }}</td>
                                    <td>
                                        <div class="progress" style="height: 5px;"><div class="progress-bar bg-warning" style="width: {{ ($occupied_rooms/$total_rooms)*100 }}%"></div></div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="ps-4"><span class="badge bg-danger bg-opacity-10 text-danger p-2 rounded-circle me-2"><i class="bi bi-tools"></i></span> ระหว่างซ่อม</td>
                                    <td class="text-center fw-bold">{{ $maintenance_rooms }}</td>
                                    <td>
                                        <div class="progress" style="height: 5px;"><div class="progress-bar bg-danger" style="width: {{ ($maintenance_rooms/$total_rooms)*100 }}%"></div></div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3 border-0">
                    <h5 class="fw-bold mb-0"><i class="bi bi-clipboard-data me-2 text-primary"></i>สถานะการจอง</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-3 pb-3 border-bottom">
                        <div class="d-flex align-items-center">
                            <div class="bg-secondary bg-opacity-10 p-2 rounded me-3"><i class="bi bi-hourglass-split text-secondary"></i></div>
                            <span>รอการยืนยัน</span>
                        </div>
                        <span class="fw-bold">{{ $pending_bookings }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3 pb-3 border-bottom">
                        <div class="d-flex align-items-center">
                            <div class="bg-primary bg-opacity-10 p-2 rounded me-3"><i class="bi bi-check-all text-primary"></i></div>
                            <span>ยืนยันแล้ว</span>
                        </div>
                        <span class="fw-bold">{{ $confirmed_bookings }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <div class="d-flex align-items-center">
                            <div class="bg-danger bg-opacity-10 p-2 rounded me-3"><i class="bi bi-x-circle text-danger"></i></div>
                            <span>ยกเลิกการจอง</span>
                        </div>
                        <span class="fw-bold">{{ $cancelled }}</span>
                    </div>
                </div>
                <div class="card-footer bg-light border-0 py-3 text-center">
                    <small class="text-muted">รวมรายการจองทั้งหมด {{ $total_bookings }} รายการ</small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection