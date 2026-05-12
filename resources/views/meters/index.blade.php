@extends('layouts.app')
 
@section('content')
<div class="container-fluid py-4">
 
    {{-- ── Header ── --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-0 fw-bold">
                <i class="bi bi-speedometer2 text-primary me-2"></i>จัดการมิเตอร์น้ำ/ไฟ
            </h4>
            <small class="text-muted">แสดงมิเตอร์จัดกลุ่มตามห้องพัก</small>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('meters.export') }}" class="btn btn-outline-success btn-sm">
                <i class="bi bi-download me-1"></i>Export
            </a>
            <a href="{{ route('meters.create') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-circle me-1"></i>เพิ่มมิเตอร์
            </a>
        </div>
    </div>
 
    {{-- Flash --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
 
    {{-- ── Filter ── --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body py-3">
            <form method="GET" action="{{ route('meters.index') }}" class="row g-2 align-items-end">
                <div class="col-md-5">
                    <input type="text" name="search" class="form-control form-control-sm"
                           placeholder="ค้นหาห้อง / หมายเลขมิเตอร์ / ผู้เช่า..."
                           value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <select name="type" class="form-select form-select-sm">
                        <option value="">ประเภทมิเตอร์ทั้งหมด</option>
                        <option value="electric" {{ request('type') === 'electric' ? 'selected' : '' }}>⚡ ไฟฟ้า</option>
                        <option value="water"    {{ request('type') === 'water'    ? 'selected' : '' }}>💧 น้ำประปา</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select form-select-sm">
                        <option value="">สถานะทั้งหมด</option>
                        <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>ใช้งาน</option>
                        <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>ปิดใช้</option>
                    </select>
                </div>
                <div class="col-md-auto">
                    <button type="submit" class="btn btn-primary btn-sm px-4">
                        <i class="bi bi-search me-1"></i>ค้นหา
                    </button>
                    <a href="{{ route('meters.index') }}" class="btn btn-outline-secondary btn-sm ms-1">
                        รีเซ็ต
                    </a>
                </div>
            </form>
        </div>
    </div>
 
    {{-- ── Summary Stats ── --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm text-center py-3">
                <div class="fs-2 fw-bold text-primary">{{ $rooms->total() }}</div>
                <div class="text-muted small">ห้องที่มีมิเตอร์</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm text-center py-3">
                <div class="fs-2 fw-bold text-warning">
                    {{ $rooms->getCollection()->sum(fn($r) => $r->meters->where('type','electric')->count()) }}
                </div>
                <div class="text-muted small">⚡ มิเตอร์ไฟ</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm text-center py-3">
                <div class="fs-2 fw-bold text-info">
                    {{ $rooms->getCollection()->sum(fn($r) => $r->meters->where('type','water')->count()) }}
                </div>
                <div class="text-muted small">💧 มิเตอร์น้ำ</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm text-center py-3">
                <div class="fs-2 fw-bold text-success">
                    {{ $rooms->getCollection()->sum(fn($r) => $r->meters->where('is_active',true)->count()) }}
                </div>
                <div class="text-muted small">ใช้งานอยู่</div>
            </div>
        </div>
    </div>
 
    {{-- ── Room Cards ── --}}
    @forelse($rooms as $room)
        @php
            $electricMeter = $room->meters->where('type', 'electric')->first();
            $waterMeter    = $room->meters->where('type', 'water')->first();
            $tenant        = $room->currentBooking?->guest;
        @endphp
 
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body p-0">
 
                {{-- Room Header --}}
                <div class="d-flex align-items-center justify-content-between px-4 py-3"
                     style="background: linear-gradient(135deg, #f8f9ff 0%, #eef2ff 100%);
                            border-bottom: 1px solid #e8ecf8; border-radius: 12px 12px 0 0;">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-white"
                             style="width:44px; height:44px; background: linear-gradient(135deg,#667eea,#764ba2); font-size:0.9em;">
                            {{ substr($room->room_number, 0, 2) }}
                        </div>
                        <div>
                            <div class="fw-bold text-dark fs-6">ห้อง {{ $room->room_number }}</div>
                            <div class="text-muted small">
                                @if($tenant)
                                    <i class="bi bi-person-fill me-1 text-success"></i>
                                    {{ $tenant->first_name }} {{ $tenant->last_name }}
                                @else
                                    <i class="bi bi-person-dash me-1 text-muted"></i>
                                    ไม่มีผู้เช่า
                                @endif
                            </div>
                        </div>
                    </div>
 
                    {{-- Action buttons --}}
                    <div class="d-flex gap-2">
                        {{-- ปุ่มบันทึกมิเตอร์รายเดือน (ถ้ามีทั้ง 2 มิเตอร์) --}}
                        @if($electricMeter)
                            <a href="{{ route('meters.readings.create', $electricMeter) }}"
                               class="btn btn-sm btn-success">
                                <i class="bi bi-pencil-square me-1"></i>บันทึกมิเตอร์
                            </a>
                        @endif
                        @if($waterMeter && $waterMeter->id !== $electricMeter?->id)
                            <a href="{{ route('meters.readings.create', $waterMeter) }}"
                               class="btn btn-sm btn-outline-info btn-sm">
                                <i class="bi bi-droplet me-1"></i>มิเตอร์น้ำ
                            </a>
                        @endif
                    </div>
                </div>
 
                {{-- Meter Details --}}
                <div class="row g-0">
 
                    {{-- ⚡ ไฟฟ้า --}}
                    <div class="col-md-6 p-4 {{ $waterMeter ? 'border-end' : '' }}">
                        @if($electricMeter)
                            <div class="d-flex align-items-start justify-content-between">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <span class="badge rounded-pill px-3 py-2"
                                          style="background:#fff3cd; color:#856404; font-size:0.82em;">
                                        ⚡ ไฟฟ้า
                                    </span>
                                    @if($electricMeter->is_active)
                                        <span class="badge bg-success-subtle text-success">ใช้งาน</span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary">ปิดใช้</span>
                                    @endif
                                </div>
                                <div class="d-flex gap-1">
                                    {{-- 👁️ ปุ่มดูรายละเอียด (ใหม่) --}}
                                    <a href="{{ route('meters.show', $electricMeter) }}"
                                       class="btn btn-outline-success btn-sm py-0 px-2"
                                       title="ดูรายละเอียด">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('meters.readings.index', $electricMeter) }}"
                                       class="btn btn-xs btn-outline-secondary btn-sm py-0 px-2"
                                       title="ประวัติ">
                                        <i class="bi bi-clock-history"></i>
                                    </a>
                                    <a href="{{ route('meters.edit', $electricMeter) }}"
                                       class="btn btn-xs btn-outline-primary btn-sm py-0 px-2"
                                       title="แก้ไข">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                </div>
                            </div>
 
                            <div class="text-muted small mb-1">
                                <i class="bi bi-upc me-1"></i>{{ $electricMeter->meter_number }}
                            </div>
 
                            <div class="d-flex align-items-end gap-3 mt-2">
                                <div>
                                    <div class="text-muted" style="font-size:0.75em;">การอ่านล่าสุด</div>
                                    <div class="fw-bold fs-5 text-dark">
                                        {{ number_format($electricMeter->latestReading?->reading_value ?? 0, 2) }}
                                        <span class="text-muted fw-normal" style="font-size:0.7em;">kWh</span>
                                    </div>
                                </div>
                                <div>
                                    <div class="text-muted" style="font-size:0.75em;">อัตรา</div>
                                    <div class="fw-semibold text-warning-emphasis">
                                        {{ number_format($electricMeter->rate_per_unit ?? 0, 2) }} ฿/หน่วย
                                    </div>
                                </div>
                                @if($electricMeter->latestReading)
                                    <div>
                                        <div class="text-muted" style="font-size:0.75em;">วันที่อ่าน</div>
                                        <div class="small text-muted">
                                            {{ $electricMeter->latestReading->reading_date?->format('d/m/Y') }}
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="text-center py-3 text-muted">
                                <i class="bi bi-lightning-charge fs-2 d-block mb-2 opacity-25"></i>
                                <small>ยังไม่มีมิเตอร์ไฟ</small><br>
                                <a href="{{ route('meters.create') }}?room_id={{ $room->id }}&type=electric"
                                   class="btn btn-outline-warning btn-sm mt-2">
                                    <i class="bi bi-plus me-1"></i>เพิ่มมิเตอร์ไฟ
                                </a>
                            </div>
                        @endif
                    </div>
 
                    {{-- 💧 น้ำ --}}
                    <div class="col-md-6 p-4">
                        @if($waterMeter)
                            <div class="d-flex align-items-start justify-content-between">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <span class="badge rounded-pill px-3 py-2"
                                          style="background:#cff4fc; color:#055160; font-size:0.82em;">
                                        💧 น้ำประปา
                                    </span>
                                    @if($waterMeter->is_active)
                                        <span class="badge bg-success-subtle text-success">ใช้งาน</span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary">ปิดใช้</span>
                                    @endif
                                </div>
                                <div class="d-flex gap-1">
                                    {{-- 👁️ ปุ่มดูรายละเอียด (ใหม่) --}}
                                    <a href="{{ route('meters.show', $waterMeter) }}"
                                       class="btn btn-outline-success btn-sm py-0 px-2"
                                       title="ดูรายละเอียด">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('meters.readings.index', $waterMeter) }}"
                                       class="btn btn-outline-secondary btn-sm py-0 px-2"
                                       title="ประวัติ">
                                        <i class="bi bi-clock-history"></i>
                                    </a>
                                    <a href="{{ route('meters.edit', $waterMeter) }}"
                                       class="btn btn-outline-primary btn-sm py-0 px-2"
                                       title="แก้ไข">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                </div>
                            </div>
 
                            <div class="text-muted small mb-1">
                                <i class="bi bi-upc me-1"></i>{{ $waterMeter->meter_number }}
                            </div>
 
                            <div class="d-flex align-items-end gap-3 mt-2">
                                <div>
                                    <div class="text-muted" style="font-size:0.75em;">การอ่านล่าสุด</div>
                                    <div class="fw-bold fs-5 text-dark">
                                        {{ number_format($waterMeter->latestReading?->reading_value ?? 0, 2) }}
                                        <span class="text-muted fw-normal" style="font-size:0.7em;">หน่วย</span>
                                    </div>
                                </div>
                                <div>
                                    <div class="text-muted" style="font-size:0.75em;">อัตรา</div>
                                    <div class="fw-semibold text-info-emphasis">
                                        {{ number_format($waterMeter->rate_per_unit ?? 0, 2) }} ฿/หน่วย
                                    </div>
                                </div>
                                @if($waterMeter->latestReading)
                                    <div>
                                        <div class="text-muted" style="font-size:0.75em;">วันที่อ่าน</div>
                                        <div class="small text-muted">
                                            {{ $waterMeter->latestReading->reading_date?->format('d/m/Y') }}
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="text-center py-3 text-muted">
                                <i class="bi bi-droplet fs-2 d-block mb-2 opacity-25"></i>
                                <small>ยังไม่มีมิเตอร์น้ำ</small><br>
                                <a href="{{ route('meters.create') }}?room_id={{ $room->id }}&type=water"
                                   class="btn btn-outline-info btn-sm mt-2">
                                    <i class="bi bi-plus me-1"></i>เพิ่มมิเตอร์น้ำ
                                </a>
                            </div>
                        @endif
                    </div>
 
                </div>{{-- end row --}}
            </div>
        </div>
    @empty
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <i class="bi bi-speedometer2 fs-1 text-muted opacity-25 d-block mb-3"></i>
                <p class="text-muted mb-3">ยังไม่มีข้อมูลมิเตอร์</p>
                <a href="{{ route('meters.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-1"></i>เพิ่มมิเตอร์
                </a>
            </div>
        </div>
    @endforelse
 
    {{-- Pagination --}}
    <div class="d-flex justify-content-center mt-4">
        {{ $rooms->links() }}
    </div>
 
</div>
@endsection