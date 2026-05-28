@extends('layouts.app')

@section('title', 'รายงานประสิทธิภาพธุรกิจ')

@section('content')
    <style>
        .report-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
            transition: transform 0.2s;
        }

        .report-card:hover {
            transform: translateY(-3px);
        }

        .kpi-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
        }

        .report-tabs .nav-link {
            color: #6c757d;
            font-weight: 500;
            border: none;
            padding: 12px 18px;
            border-radius: 10px 10px 0 0;
        }

        .report-tabs .nav-link.active {
            color: #4f46e5;
            background: white;
            border-bottom: 3px solid #4f46e5;
        }

        .report-tabs .nav-link:hover:not(.active) {
            color: #4f46e5;
            background: #f8f9fa;
        }

        .chart-container {
            position: relative;
            height: 320px;
        }

        .chart-container-sm {
            position: relative;
            height: 240px;
        }
    </style>

    <div class="container-fluid py-2">
        {{-- ════════ Header ════════ --}}
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <div>
                <h3 class="fw-bold text-dark mb-1">📊 รายงานสถิติและรายได้</h3>
                <p class="text-muted mb-0">ข้อมูลสรุป ณ วันที่ {{ now()->format('d/m/Y H:i') }}</p>
            </div>
            <a href="{{ route('reports.export') }}" class="btn btn-success px-4 shadow-sm">
                <i class="bi bi-file-earmark-excel me-2"></i>Export
            </a>
        </div>

        {{-- ════════ Tabs ════════ --}}
        <ul class="nav nav-tabs report-tabs mb-4 flex-nowrap" style="overflow-x: auto;" id="reportTabs" role="tablist">
            <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-overview"
                    type="button">📈 ภาพรวม</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-financial"
                    type="button">💰 การเงิน</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-rooms" type="button">🏠
                    ห้องพัก</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-guests"
                    type="button">👥 ผู้เช่า</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-contracts"
                    type="button">📝 สัญญา</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-meters" type="button">⚡
                    มิเตอร์</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-maintenance"
                    type="button">🔧 ซ่อมบำรุง</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-facilities"
                    type="button">📦 เฟอร์นิเจอร์</button></li>
        </ul>

        <div class="tab-content">

            {{-- ════════════════════════════════════════════════════════ --}}
            {{--  📈 Tab 1: ภาพรวม (Overview)                            --}}
            {{-- ════════════════════════════════════════════════════════ --}}
            <div class="tab-pane fade show active" id="tab-overview" role="tabpanel">
                {{-- KPI Cards --}}
                <div class="row g-3 mb-4">
                    <div class="col-6 col-md-4 col-xl-2">
                        <div class="card report-card h-100">
                            <div class="card-body">
                                <div class="kpi-icon" style="background:#fff9e6; color:#f59e0b;"><i
                                        class="bi bi-currency-dollar"></i></div>
                                <div class="text-muted small mt-2">รายได้รวม</div>
                                <h5 class="mb-0 fw-bold">฿{{ number_format($total_revenue, 0) }}</h5>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-4 col-xl-2">
                        <div class="card report-card h-100">
                            <div class="card-body">
                                <div class="kpi-icon" style="background:#dbeafe; color:#3b82f6;"><i
                                        class="bi bi-percent"></i></div>
                                <div class="text-muted small mt-2">อัตราเข้าพัก</div>
                                <h5 class="mb-0 fw-bold">{{ $occupancy_rate }}%</h5>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-4 col-xl-2">
                        <div class="card report-card h-100">
                            <div class="card-body">
                                <div class="kpi-icon" style="background:#d1fae5; color:#10b981;"><i
                                        class="bi bi-calendar-check"></i></div>
                                <div class="text-muted small mt-2">จองเดือนนี้</div>
                                <h5 class="mb-0 fw-bold">{{ $bookings_this_month }}</h5>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-4 col-xl-2">
                        <div class="card report-card h-100">
                            <div class="card-body">
                                <div class="kpi-icon" style="background:#e0e7ff; color:#4f46e5;"><i
                                        class="bi bi-file-earmark-text"></i></div>
                                <div class="text-muted small mt-2">สัญญา Active</div>
                                <h5 class="mb-0 fw-bold">{{ $contracts_active }}</h5>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-4 col-xl-2">
                        <div class="card report-card h-100">
                            <div class="card-body">
                                <div class="kpi-icon" style="background:#fee2e2; color:#ef4444;"><i
                                        class="bi bi-exclamation-octagon"></i></div>
                                <div class="text-muted small mt-2">ค้างชำระ</div>
                                <h5 class="mb-0 fw-bold">฿{{ number_format($invoices_overdue_amount, 0) }}</h5>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-4 col-xl-2">
                        <div class="card report-card h-100">
                            <div class="card-body">
                                <div class="kpi-icon" style="background:#fef3c7; color:#d97706;"><i
                                        class="bi bi-tools"></i></div>
                                <div class="text-muted small mt-2">รอซ่อมบำรุง</div>
                                <h5 class="mb-0 fw-bold">{{ $maint_pending }}</h5>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Chart รายได้ 12 เดือน --}}
                <div class="card report-card">
                    <div class="card-body">
                        <h5 class="fw-bold mb-3"><i class="bi bi-graph-up me-2 text-primary"></i>รายได้ 12 เดือนย้อนหลัง
                        </h5>
                        <div class="chart-container">
                            <canvas id="chartOverviewRevenue"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ════════════════════════════════════════════════════════ --}}
            {{--  💰 Tab 2: การเงิน (Financial)                          --}}
            {{-- ════════════════════════════════════════════════════════ --}}
            <div class="tab-pane fade" id="tab-financial" role="tabpanel">
                {{-- Cards สรุปสถานะใบแจ้งหนี้ --}}
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <div class="card report-card h-100" style="background: linear-gradient(135deg,#d1fae5,#a7f3d0);">
                            <div class="card-body">
                                <small class="text-success fw-semibold">ชำระแล้ว</small>
                                <h3 class="fw-bold text-success mt-2">฿{{ number_format($invoices_paid_amount, 0) }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card report-card h-100" style="background: linear-gradient(135deg,#fef3c7,#fde68a);">
                            <div class="card-body">
                                <small class="text-warning fw-semibold">รอชำระ</small>
                                <h3 class="fw-bold text-warning mt-2">฿{{ number_format($invoices_pending_amount, 0) }}
                                </h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card report-card h-100" style="background: linear-gradient(135deg,#fee2e2,#fecaca);">
                            <div class="card-body">
                                <small class="text-danger fw-semibold">เกินกำหนด</small>
                                <h3 class="fw-bold text-danger mt-2">฿{{ number_format($invoices_overdue_amount, 0) }}
                                </h3>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-lg-8">
                        <div class="card report-card h-100">
                            <div class="card-body">
                                <h5 class="fw-bold mb-3"><i
                                        class="bi bi-bar-chart-line me-2 text-primary"></i>รายได้รายเดือน</h5>
                                <div class="chart-container">
                                    <canvas id="chartMonthlyRevenue"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="card report-card h-100">
                            <div class="card-body">
                                <h5 class="fw-bold mb-3"><i
                                        class="bi bi-pie-chart me-2 text-primary"></i>รายได้แยกประเภทห้อง</h5>
                                <div class="chart-container">
                                    <canvas id="chartRevenueByType"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Top 10 ผู้เช่าค้างชำระ --}}
                <div class="card report-card">
                    <div class="card-header bg-white">
                        <h5 class="fw-bold mb-0"><i class="bi bi-exclamation-circle me-2 text-danger"></i>Top 10
                            ใบแจ้งหนี้ค้างชำระ</h5>
                    </div>
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-3">#</th>
                                    <th>ผู้เช่า</th>
                                    <th>เลขใบแจ้งหนี้</th>
                                    <th>กำหนดชำระ</th>
                                    <th class="text-end pe-3">ยอด</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($top_overdue_guests as $i => $inv)
                                    <tr>
                                        <td class="ps-3">{{ $i + 1 }}</td>
                                        <td>
                                            @if ($inv->guest)
                                                {{ $inv->guest->first_name }} {{ $inv->guest->last_name }}
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td><code>{{ $inv->invoice_number ?? '#' . $inv->id }}</code></td>
                                        <td><small
                                                class="text-danger">{{ optional($inv->due_date)->format('d/m/Y') ?? '-' }}</small>
                                        </td>
                                        <td class="text-end pe-3 fw-bold text-danger">
                                            ฿{{ number_format($inv->total ?? 0, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-3 text-muted">ไม่มีรายการค้างชำระ ✨</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- ════════════════════════════════════════════════════════ --}}
            {{--  🏠 Tab 3: ห้องพัก (Rooms)                              --}}
            {{-- ════════════════════════════════════════════════════════ --}}
            <div class="tab-pane fade" id="tab-rooms" role="tabpanel">
                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <div class="card report-card text-center">
                            <div class="card-body">
                                <div class="text-muted small">ห้องทั้งหมด</div>
                                <h3 class="fw-bold mt-2">{{ $total_rooms }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card report-card text-center">
                            <div class="card-body">
                                <div class="text-success small">ห้องว่าง</div>
                                <h3 class="fw-bold text-success mt-2">{{ $available_rooms }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card report-card text-center">
                            <div class="card-body">
                                <div class="text-warning small">ห้องมีผู้พัก</div>
                                <h3 class="fw-bold text-warning mt-2">{{ $occupied_rooms }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card report-card text-center">
                            <div class="card-body">
                                <div class="text-danger small">ระหว่างซ่อม</div>
                                <h3 class="fw-bold text-danger mt-2">{{ $maintenance_rooms }}</h3>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-lg-6">
                        <div class="card report-card h-100">
                            <div class="card-body">
                                <h5 class="fw-bold mb-3"><i class="bi bi-pie-chart me-2 text-primary"></i>ห้องตามประเภท
                                </h5>
                                <div class="chart-container-sm"><canvas id="chartRoomsByType"></canvas></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="card report-card h-100">
                            <div class="card-body">
                                <h5 class="fw-bold mb-3"><i class="bi bi-bar-chart me-2 text-primary"></i>ห้องตามโซน</h5>
                                <div class="chart-container-sm"><canvas id="chartRoomsByZone"></canvas></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card report-card">
                    <div class="card-header bg-white">
                        <h5 class="fw-bold mb-0"><i class="bi bi-trophy me-2 text-warning"></i>Top 5 ห้องทำรายได้สูงสุด
                        </h5>
                    </div>
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-3">อันดับ</th>
                                    <th>ห้อง</th>
                                    <th>ประเภท</th>
                                    <th>โซน</th>
                                    <th class="text-end pe-3">รายได้</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($top_revenue_rooms as $i => $room)
                                    <tr>
                                        <td class="ps-3 fw-bold">#{{ $i + 1 }}</td>
                                        <td><strong>#{{ $room->room_number }}</strong></td>
                                        <td>
                                            @if ($room->room_type === 'fan')
                                                <span class="badge bg-info">🌀 พัดลม</span>
                                            @elseif($room->room_type === 'air_conditioning')
                                                <span class="badge bg-primary">❄️ แอร์</span>
                                            @else<span class="badge bg-secondary">{{ $room->room_type }}</span>
                                            @endif
                                        </td>
                                        <td>{{ $room->zone ?? '-' }}</td>
                                        <td class="text-end pe-3 fw-bold text-success">
                                            ฿{{ number_format($room->revenue ?? 0, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-3 text-muted">ไม่มีข้อมูล</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- ════════════════════════════════════════════════════════ --}}
            {{--  👥 Tab 4: ผู้เช่า (Guests)                              --}}
            {{-- ════════════════════════════════════════════════════════ --}}
            <div class="tab-pane fade" id="tab-guests" role="tabpanel">
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <div class="card report-card h-100">
                            <div class="card-body d-flex align-items-center gap-3">
                                <div class="kpi-icon" style="background:#e0e7ff; color:#4f46e5;"><i
                                        class="bi bi-people-fill"></i></div>
                                <div>
                                    <div class="text-muted small">ผู้เช่าทั้งหมด</div>
                                    <h3 class="mb-0 fw-bold">{{ $total_guests }} <small
                                            class="fs-6 text-muted">คน</small></h3>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card report-card h-100">
                            <div class="card-body d-flex align-items-center gap-3">
                                <div class="kpi-icon" style="background:#d1fae5; color:#10b981;"><i
                                        class="bi bi-person-plus-fill"></i></div>
                                <div>
                                    <div class="text-muted small">ผู้เช่าใหม่ในเดือนนี้</div>
                                    <h3 class="mb-0 fw-bold text-success">+{{ $new_guests_this_month }} <small
                                            class="fs-6 text-muted">คน</small></h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-lg-7">
                        <div class="card report-card h-100">
                            <div class="card-body">
                                <h5 class="fw-bold mb-3"><i class="bi bi-graph-up-arrow me-2 text-primary"></i>ผู้เช่าใหม่
                                    6 เดือนย้อนหลัง</h5>
                                <div class="chart-container"><canvas id="chartGuestsTrend"></canvas></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-5">
                        <div class="card report-card h-100">
                            <div class="card-header bg-white">
                                <h5 class="fw-bold mb-0"><i class="bi bi-award me-2 text-warning"></i>Top 10 ลูกค้าประจำ
                                </h5>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-sm align-middle mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="ps-3">ชื่อ</th>
                                            <th class="text-end pe-3">จอง</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($top_loyal_guests as $g)
                                            <tr>
                                                <td class="ps-3">{{ $g->first_name }} {{ $g->last_name }}</td>
                                                <td class="text-end pe-3"><span
                                                        class="badge bg-primary">{{ $g->bookings_count }}</span></td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="2" class="text-center py-3 text-muted">ไม่มีข้อมูล</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ════════════════════════════════════════════════════════ --}}
            {{--  📝 Tab 5: สัญญา (Contracts)                            --}}
            {{-- ════════════════════════════════════════════════════════ --}}
            <div class="tab-pane fade" id="tab-contracts" role="tabpanel">
                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <div class="card report-card text-center">
                            <div class="card-body">
                                <div class="text-success small">Active</div>
                                <h3 class="fw-bold text-success mt-2">{{ $contracts_active }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card report-card text-center">
                            <div class="card-body">
                                <div class="text-warning small">ใกล้หมด 30 วัน</div>
                                <h3 class="fw-bold text-warning mt-2">
                                    {{ is_countable($contracts_expiring_30) ? count($contracts_expiring_30) : 0 }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card report-card text-center">
                            <div class="card-body">
                                <div class="text-info small">ใกล้หมด 60 วัน</div>
                                <h3 class="fw-bold text-info mt-2">{{ $contracts_expiring_60 }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card report-card text-center">
                            <div class="card-body">
                                <div class="text-secondary small">หมดอายุแล้ว</div>
                                <h3 class="fw-bold text-secondary mt-2">{{ $contracts_expired }}</h3>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card report-card">
                    <div class="card-header bg-white">
                        <h5 class="fw-bold mb-0"><i
                                class="bi bi-clock-history me-2 text-warning"></i>สัญญาใกล้หมดอายุภายใน 30 วัน</h5>
                    </div>
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-3">เลขสัญญา</th>
                                    <th>ผู้เช่า</th>
                                    <th>ห้อง</th>
                                    <th>วันสิ้นสุด</th>
                                    <th class="text-end pe-3">เหลือ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($contracts_expiring_30 as $c)
                                    @php
                                        $daysLeft = $c->end_date
                                            ? \Carbon\Carbon::parse($c->end_date)->diffInDays(now(), false) * -1
                                            : 0;
                                    @endphp
                                    <tr>
                                        <td class="ps-3"><code>{{ $c->contract_number ?? '#' . $c->id }}</code></td>
                                        <td>{{ $c->guest->first_name ?? '-' }} {{ $c->guest->last_name ?? '' }}</td>
                                        <td>#{{ $c->room->room_number ?? '-' }}</td>
                                        <td>{{ optional($c->end_date)->format('d/m/Y') }}</td>
                                        <td class="text-end pe-3">
                                            <span
                                                class="badge {{ $daysLeft <= 7 ? 'bg-danger' : 'bg-warning text-dark' }}">{{ $daysLeft }}
                                                วัน</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-3 text-muted">ไม่มีสัญญาใกล้หมด ✨</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- ════════════════════════════════════════════════════════ --}}
            {{--  ⚡ Tab 6: มิเตอร์ (Meters)                              --}}
            {{-- ════════════════════════════════════════════════════════ --}}
            <div class="tab-pane fade" id="tab-meters" role="tabpanel">
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <div class="card report-card h-100" style="background: linear-gradient(135deg,#fef3c7,#fde68a);">
                            <div class="card-body d-flex align-items-center gap-3">
                                <div class="kpi-icon" style="background:rgba(255,255,255,0.6); color:#d97706;"><i
                                        class="bi bi-lightning-charge-fill"></i></div>
                                <div>
                                    <div class="text-muted small">ค่าไฟรวมเดือนนี้</div>
                                    <h3 class="mb-0 fw-bold">{{ number_format($current_month_electric) }} <small
                                            class="fs-6">หน่วย</small></h3>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card report-card h-100" style="background: linear-gradient(135deg,#dbeafe,#bfdbfe);">
                            <div class="card-body d-flex align-items-center gap-3">
                                <div class="kpi-icon" style="background:rgba(255,255,255,0.6); color:#2563eb;"><i
                                        class="bi bi-droplet-fill"></i></div>
                                <div>
                                    <div class="text-muted small">ค่าน้ำรวมเดือนนี้</div>
                                    <h3 class="mb-0 fw-bold">{{ number_format($current_month_water) }} <small
                                            class="fs-6">หน่วย</small></h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card report-card">
                    <div class="card-header bg-white">
                        <h5 class="fw-bold mb-0"><i class="bi bi-bar-chart-line me-2 text-warning"></i>Top 5
                            ห้องใช้ไฟมากที่สุดเดือนนี้</h5>
                    </div>
                    <div class="card-body">
                        @if ($top_electric_rooms->isEmpty())
                            <div class="text-center py-4 text-muted">
                                <i class="bi bi-info-circle fs-2"></i>
                                <div class="mt-2">ยังไม่มีข้อมูลมิเตอร์ในเดือนนี้</div>
                                <small>(ต้องมีตาราง meter_readings + field: electric_usage, water_usage, reading_date,
                                    room_id)</small>
                            </div>
                        @else
                            <table class="table align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th>อันดับ</th>
                                        <th>ห้อง</th>
                                        <th class="text-end">หน่วยที่ใช้</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($top_electric_rooms as $i => $room)
                                        <tr>
                                            <td><strong>#{{ $i + 1 }}</strong></td>
                                            <td>#{{ $room->room_number }}</td>
                                            <td class="text-end fw-bold text-warning">{{ number_format($room->total) }}
                                                หน่วย</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>
                </div>
            </div>

            {{-- ════════════════════════════════════════════════════════ --}}
            {{--  🔧 Tab 7: ซ่อมบำรุง (Maintenance)                      --}}
            {{-- ════════════════════════════════════════════════════════ --}}
            <div class="tab-pane fade" id="tab-maintenance" role="tabpanel">
                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <div class="card report-card text-center">
                            <div class="card-body">
                                <div class="text-warning small">รอดำเนินการ</div>
                                <h3 class="fw-bold text-warning mt-2">{{ $maint_pending }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card report-card text-center">
                            <div class="card-body">
                                <div class="text-info small">กำลังดำเนินการ</div>
                                <h3 class="fw-bold text-info mt-2">{{ $maint_in_progress }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card report-card text-center">
                            <div class="card-body">
                                <div class="text-success small">เสร็จแล้ว</div>
                                <h3 class="fw-bold text-success mt-2">{{ $maint_completed }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card report-card text-center">
                            <div class="card-body">
                                <div class="text-secondary small">ยกเลิก</div>
                                <h3 class="fw-bold text-secondary mt-2">{{ $maint_cancelled }}</h3>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-lg-6">
                        <div class="card report-card h-100">
                            <div class="card-body">
                                <h5 class="fw-bold mb-3"><i
                                        class="bi bi-pie-chart me-2 text-primary"></i>ประเภทการซ่อมยอดนิยม</h5>
                                <div class="chart-container"><canvas id="chartMaintTypes"></canvas></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="card report-card h-100" style="background: linear-gradient(135deg,#fef2f2,#fee2e2);">
                            <div
                                class="card-body d-flex flex-column justify-content-center align-items-center text-center">
                                <i class="bi bi-cash-coin text-danger" style="font-size: 3rem;"></i>
                                <h6 class="text-muted mt-3">ค่าใช้จ่ายซ่อมบำรุงเดือนนี้</h6>
                                <h2 class="fw-bold text-danger">฿{{ number_format($maint_cost_this_month, 2) }}</h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ════════════════════════════════════════════════════════ --}}
            {{--  📦 Tab 8: เฟอร์นิเจอร์ (Facilities)                    --}}
            {{-- ════════════════════════════════════════════════════════ --}}
            <div class="tab-pane fade" id="tab-facilities" role="tabpanel">
                <div class="row g-3 mb-4">
                    <div class="col-lg-5">
                        <div class="card report-card h-100">
                            <div class="card-body">
                                <h5 class="fw-bold mb-3"><i
                                        class="bi bi-pie-chart-fill me-2 text-primary"></i>เฟอร์นิเจอร์ตามสถานะ (รวม
                                    {{ $fac_total }} ชิ้น)</h5>
                                <div class="chart-container"><canvas id="chartFacilityStatus"></canvas></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-7">
                        <div class="card report-card h-100">
                            <div class="card-header bg-white">
                                <h5 class="fw-bold mb-0"><i
                                        class="bi bi-calendar-event me-2 text-warning"></i>เฟอร์นิเจอร์ที่ใกล้ครบกำหนดซ่อม
                                    (30 วัน)</h5>
                            </div>
                            <div class="table-responsive">
                                <table class="table align-middle mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="ps-3">ชื่อ</th>
                                            <th>ที่ตั้ง</th>
                                            <th class="text-end pe-3">วันที่ซ่อม</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($fac_upcoming_maint as $f)
                                            <tr>
                                                <td class="ps-3">{{ $f->name }}</td>
                                                <td>{{ $f->location }}</td>
                                                <td class="text-end pe-3"><small
                                                        class="text-warning fw-bold">{{ \Carbon\Carbon::parse($f->next_maintenance_date)->format('d/m/Y') }}</small>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="text-center py-3 text-muted">ไม่มีรายการ ✨</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>{{-- /.tab-content --}}
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
        <script>
            // ───── Data จาก Controller ─────
            const monthlyRevenue = @json($monthly_revenue);
            const revenueByType = @json($revenue_by_room_type);
            const roomsByType = @json($rooms_by_type);
            const roomsByZone = @json($rooms_by_zone);
            const guestsPerMonth = @json($guests_per_month);
            const maintTypes = @json($maint_types);
            const facStatus = @json($fac_status);

            // ───── Mapping ไทย ─────
            const roomTypeLabels = {
                fan: '🌀 พัดลม',
                air_conditioning: '❄️ แอร์'
            };
            const statusLabels = {
                good: '✅ ใช้งานได้',
                fair: '🟡 สภาพปานกลาง',
                needs_repair: '⚠️ ต้องซ่อม',
                maintenance: '🔧 กำลังซ่อมบำรุง',
                damaged: '❌ ชำรุด',
                retired: '🗑️ ปลดประจำการ'
            };

            const palette = ['#4f46e5', '#10b981', '#f59e0b', '#ef4444', '#3b82f6', '#8b5cf6', '#ec4899', '#14b8a6', '#6b7280'];

            // ───── Chart 1: รายได้ 12 เดือน (Overview) ─────
            new Chart(document.getElementById('chartOverviewRevenue'), {
                type: 'line',
                data: {
                    labels: monthlyRevenue.map(r => r.label),
                    datasets: [{
                        label: 'รายได้ (฿)',
                        data: monthlyRevenue.map(r => r.value),
                        fill: true,
                        backgroundColor: 'rgba(79,70,229,0.15)',
                        borderColor: '#4f46e5',
                        borderWidth: 2,
                        tension: 0.3,
                        pointRadius: 4,
                        pointBackgroundColor: '#4f46e5',
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });

            // ───── Chart 2: รายได้รายเดือน (Financial) ─────
            new Chart(document.getElementById('chartMonthlyRevenue'), {
                type: 'bar',
                data: {
                    labels: monthlyRevenue.map(r => r.label),
                    datasets: [{
                        label: 'รายได้ (฿)',
                        data: monthlyRevenue.map(r => r.value),
                        backgroundColor: '#4f46e5',
                        borderRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });

            // ───── Chart 3: รายได้แยกประเภท (Financial) ─────
            new Chart(document.getElementById('chartRevenueByType'), {
                type: 'doughnut',
                data: {
                    labels: Object.keys(revenueByType).map(k => roomTypeLabels[k] || k),
                    datasets: [{
                        data: Object.values(revenueByType),
                        backgroundColor: ['#3b82f6', '#8b5cf6', '#10b981', '#f59e0b']
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });

            // ───── Chart 4: ห้องตามประเภท (Rooms) ─────
            new Chart(document.getElementById('chartRoomsByType'), {
                type: 'pie',
                data: {
                    labels: Object.keys(roomsByType).map(k => roomTypeLabels[k] || k),
                    datasets: [{
                        data: Object.values(roomsByType),
                        backgroundColor: ['#3b82f6', '#8b5cf6', '#10b981']
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });

            // ───── Chart 5: ห้องตามโซน (Rooms) ─────
            new Chart(document.getElementById('chartRoomsByZone'), {
                type: 'bar',
                data: {
                    labels: Object.keys(roomsByZone),
                    datasets: [{
                        label: 'จำนวนห้อง',
                        data: Object.values(roomsByZone),
                        backgroundColor: '#10b981',
                        borderRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });

            // ───── Chart 6: ผู้เช่า 6 เดือน (Guests) ─────
            new Chart(document.getElementById('chartGuestsTrend'), {
                type: 'line',
                data: {
                    labels: guestsPerMonth.map(r => r.label),
                    datasets: [{
                        label: 'ผู้เช่าใหม่',
                        data: guestsPerMonth.map(r => r.value),
                        fill: true,
                        backgroundColor: 'rgba(16,185,129,0.15)',
                        borderColor: '#10b981',
                        borderWidth: 2,
                        tension: 0.3,
                        pointRadius: 4,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });

            // ───── Chart 7: ประเภทการซ่อม (Maintenance) ─────
            new Chart(document.getElementById('chartMaintTypes'), {
                type: 'doughnut',
                data: {
                    labels: Object.keys(maintTypes),
                    datasets: [{
                        data: Object.values(maintTypes),
                        backgroundColor: palette
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });

            // ───── Chart 8: สถานะเฟอร์นิเจอร์ (Facilities) ─────
            new Chart(document.getElementById('chartFacilityStatus'), {
                type: 'doughnut',
                data: {
                    labels: Object.keys(facStatus).map(k => statusLabels[k] || k),
                    datasets: [{
                        data: Object.values(facStatus),
                        backgroundColor: ['#10b981', '#3b82f6', '#f59e0b', '#4f46e5', '#ef4444', '#6b7280']
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });
        </script>
    @endpush
@endsection
