@extends('layouts.app')
 
@section('content')
<style>
    .room-scroll-box {
        max-height: 120px;
        overflow-y: auto;
        background: #fdfdfd;
        border: 1px solid #eee;
        padding: 10px;
        border-radius: 10px;
    }
    .badge-room {
        font-size: 0.75rem;
        padding: 4px 8px;
        border-radius: 5px;
        margin: 1px;
    }
    .stat-card-custom {
        border: none;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        border-radius: 12px;
    }
    .search-card {
        background: #fff;
        border-radius: 12px;
        border: none;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }
    .floor-badge {
        display: inline-block;
        background: #e8f4ff;
        color: #1a5ca8;
        font-size: 0.7rem;
        font-weight: 600;
        padding: 2px 7px;
        border-radius: 99px;
        margin-left: 6px;
    }
    .zone-badge {
        display: inline-block;
        background: #fff0e8;
        color: #c05a00;
        font-size: 0.7rem;
        font-weight: 600;
        padding: 2px 7px;
        border-radius: 99px;
        margin-left: 3px;
    }
</style>
 
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0 text-dark">จัดการห้องพัก</h4>
    <div class="d-flex gap-2">
        <a href="{{ route('rooms.bulk-create') }}" class="btn btn-outline-primary">
            <i class="bi bi-plus-square me-1"></i>เพิ่มหลายห้อง
        </a>
        <a href="{{ route('rooms.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i>เพิ่มห้องใหม่
        </a>
    </div>
</div>
 
{{-- สถิติ --}}
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card stat-card-custom h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="p-3 bg-success bg-opacity-10 text-success rounded-circle me-3">
                        <i class="bi bi-door-open fs-4"></i>
                    </div>
                    <div>
                        <small class="text-muted d-block">ห้องว่าง</small>
                        <h4 class="mb-0 fw-bold text-success">{{ $availableCount }}</h4>
                    </div>
                </div>
                <div class="room-scroll-box">
                    <small class="text-muted d-block mb-1 small">รายชื่อห้องว่าง:</small>
                    <div class="d-flex flex-wrap">
                        @forelse($availableRoomList as $num)
                            <span class="badge bg-success-subtle text-success border border-success-subtle badge-room">{{ $num }}</span>
                        @empty
                            <span class="text-muted small">- ไม่มีห้องว่าง -</span>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
 
    <div class="col-md-4">
        <div class="card stat-card-custom h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="p-3 bg-primary bg-opacity-10 text-primary rounded-circle me-3">
                        <i class="bi bi-door-closed fs-4"></i>
                    </div>
                    <div>
                        <small class="text-muted d-block">ใช้งานอยู่</small>
                        <h4 class="mb-0 fw-bold text-primary">{{ $occupiedCount }}</h4>
                    </div>
                </div>
                <div class="room-scroll-box">
                    <small class="text-muted d-block mb-1 small">รายชื่อห้องไม่ว่าง:</small>
                    <div class="d-flex flex-wrap">
                        @foreach($occupiedRoomList as $num)
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle badge-room">{{ $num }}</span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
 
    <div class="col-md-4">
        <div class="card stat-card-custom h-100 d-flex align-items-center justify-content-center p-4">
            <div class="p-3 bg-warning bg-opacity-10 text-warning rounded-circle me-3">
                <i class="bi bi-tools fs-4"></i>
            </div>
            <div>
                <small class="text-muted d-block">ซ่อมบำรุง</small>
                <h4 class="mb-0 fw-bold text-warning">{{ $maintenanceCount }} รายการ</h4>
            </div>
        </div>
    </div>
</div>
 
{{-- ส่วนการค้นหา --}}
<div class="card search-card mb-4">
    <div class="card-body p-3">
        <form action="{{ route('rooms.index') }}" method="GET" class="row g-2 align-items-end">
 
            {{-- ค้นหาหมายเลขห้อง --}}
            <div class="col-md-3">
                <label class="form-label small text-muted mb-1">หมายเลขห้อง</label>
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-end-0">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" name="search"
                        class="form-control border-start-0"
                        placeholder="เช่น A101, B202..."
                        value="{{ request('search') }}">
                </div>
            </div>
 
            {{-- กรองตามสถานะ --}}
            <div class="col-md-2">
                <label class="form-label small text-muted mb-1">สถานะ</label>
                <select name="status" class="form-select">
                    <option value="">ทุกสถานะ</option>
                    <option value="available"   {{ request('status') == 'available'   ? 'selected' : '' }}>ว่าง</option>
                    <option value="occupied"    {{ request('status') == 'occupied'    ? 'selected' : '' }}>ใช้งานอยู่</option>
                    <option value="maintenance" {{ request('status') == 'maintenance' ? 'selected' : '' }}>ซ่อมบำรุง</option>
                </select>
            </div>
 
            {{-- กรองตามชั้น --}}
            <div class="col-md-2">
                <label class="form-label small text-muted mb-1">ชั้น</label>
                <select name="floor" class="form-select">
                    <option value="">ทุกชั้น</option>
                    @for ($f = 1; $f <= 5; $f++)
                        <option value="{{ $f }}" {{ request('floor') == $f ? 'selected' : '' }}>
                            ชั้น {{ $f }}
                        </option>
                    @endfor
                </select>
            </div>
 
            {{-- กรองตามโซน --}}
            <div class="col-md-2">
                <label class="form-label small text-muted mb-1">โซน</label>
                <select name="zone" class="form-select">
                    <option value="">ทุกโซน</option>
                    <option value="A" {{ request('zone') == 'A' ? 'selected' : '' }}>โซน A</option>
                    <option value="B" {{ request('zone') == 'B' ? 'selected' : '' }}>โซน B</option>
                </select>
            </div>
 
            {{-- ปุ่ม --}}
            <div class="col-md-3">
                <label class="form-label small text-muted mb-1 invisible">ค้นหา</label>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1">
                        <i class="bi bi-search me-1"></i>ค้นหา
                    </button>
                    @if(request('search') || request('status') || request('floor') || request('zone'))
                        <a href="{{ route('rooms.index') }}" class="btn btn-light border" title="ล้างตัวกรอง">
                            <i class="bi bi-x-lg"></i>
                        </a>
                    @endif
                </div>
            </div>
 
        </form>
    </div>
</div>
 
{{-- แสดงผลการกรอง --}}
@if(request('search') || request('status') || request('floor') || request('zone'))
<div class="mb-3 d-flex align-items-center gap-2 flex-wrap">
    <small class="text-muted">กรองโดย:</small>
    @if(request('search'))
        <span class="badge bg-light text-dark border">
            <i class="bi bi-search me-1"></i>{{ request('search') }}
        </span>
    @endif
    @if(request('status'))
        <span class="badge bg-light text-dark border">
            สถานะ: {{ ['available'=>'ว่าง','occupied'=>'ใช้งานอยู่','maintenance'=>'ซ่อมบำรุง'][request('status')] }}
        </span>
    @endif
    @if(request('floor'))
        <span class="badge bg-light text-dark border">
            <i class="bi bi-building me-1"></i>ชั้น {{ request('floor') }}
        </span>
    @endif
    @if(request('zone'))
        <span class="badge bg-light text-dark border">
            โซน {{ request('zone') }}
        </span>
    @endif
    <small class="text-muted">— พบ {{ $rooms->total() }} ห้อง</small>
</div>
@endif
 
{{-- ตารางแสดงผล --}}
<div class="card stat-card-custom overflow-hidden border-0">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light">
                <tr>
                    <th class="ps-4">หมายเลขห้อง</th>
                    <th>โซน / ชั้น</th>
                    <th>ประเภท</th>
                    <th>ราคา/เดือน</th>
                    <th>สถานะ</th>
                    <th class="text-end pe-4">จัดการ</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($rooms as $room)
                <tr>
                    <td class="ps-4 fw-bold text-dark">{{ $room->room_number }}</td>
                    <td>
                        @if($room->zone)
                            <span class="zone-badge">โซน {{ $room->zone }}</span>
                        @endif
                        @if($room->floor)
                            <span class="floor-badge">ชั้น {{ $room->floor }}</span>
                        @endif
                    </td>
                    <td>
                        @if($room->room_type == 'air')
                            <span class="badge bg-info bg-opacity-10 text-info border border-info-subtle">
                                <i class="bi bi-snow me-1"></i>แอร์
                            </span>
                        @else
                            <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary-subtle">
                                <i class="bi bi-wind me-1"></i>พัดลม
                            </span>
                        @endif
                    </td>
                    <td class="text-success fw-semibold">
                        {{ number_format($room->price_per_month) }} ฿
                    </td>
                    <td>
                        @php
                            $badgeClass = match($room->status) {
                                'available'   => 'success',
                                'occupied'    => 'primary',
                                'maintenance' => 'warning',
                                default       => 'secondary'
                            };
                            $statusText = match($room->status) {
                                'available'   => 'ว่าง',
                                'occupied'    => 'ใช้งาน',
                                'maintenance' => 'ซ่อมบำรุง',
                                default       => 'ไม่ระบุ'
                            };
                        @endphp
                        <span class="badge bg-{{ $badgeClass }} bg-opacity-10 text-{{ $badgeClass }} border border-{{ $badgeClass }}-subtle">
                            {{ $statusText }}
                        </span>
                    </td>
                    <td class="text-end pe-4">
                        <a href="{{ route('rooms.show', $room) }}"
                           class="btn btn-sm btn-light text-info" title="ดูรายละเอียด">
                            <i class="bi bi-eye"></i>
                        </a>
                        <a href="{{ route('rooms.edit', $room) }}"
                           class="btn btn-sm btn-light text-warning" title="แก้ไข">
                            <i class="bi bi-pencil"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-5 text-muted">
                        <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                        ไม่พบข้อมูลห้องพักที่ค้นหา
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
 
{{-- Pagination --}}
<div class="mt-4 d-flex justify-content-between align-items-center">
    <small class="text-muted">
        แสดง {{ $rooms->firstItem() ?? 0 }}–{{ $rooms->lastItem() ?? 0 }}
        จาก {{ $rooms->total() }} ห้อง
    </small>
    {{ $rooms->links('pagination::bootstrap-5') }}
</div>
 
@endsection
 