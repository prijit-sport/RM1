@extends('layouts.app')

@section('content')
<style>
    .room-scroll-box { max-height: 120px; overflow-y: auto; background: #fdfdfd; border: 1px solid #eee; padding: 10px; border-radius: 10px; }
    .badge-room { font-size: 0.75rem; padding: 4px 8px; border-radius: 5px; margin: 1px; }
    .stat-card-custom { border: none; box-shadow: 0 4px 12px rgba(0,0,0,0.05); border-radius: 12px; }
    .search-card { background: #fff; border-radius: 12px; border: none; box-shadow: 0 2px 8px rgba(0,0,0,0.05); }
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0 text-dark">จัดการห้องพัก</h4>
    <div class="d-flex gap-2">
        <a href="{{ route('rooms.bulk-create') }}" class="btn btn-outline-primary">เพิ่มหลายห้อง</a>
        <a href="{{ route('rooms.create') }}" class="btn btn-primary">เพิ่มห้องใหม่</a>
    </div>
</div>

{{-- ส่วนแสดงสถิติ (คงเดิม) --}}
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card stat-card-custom h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="p-3 bg-success bg-opacity-10 text-success rounded-circle me-3"><i class="bi bi-door-open fs-4"></i></div>
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
                    <div class="p-3 bg-primary bg-opacity-10 text-primary rounded-circle me-3"><i class="bi bi-door-closed fs-4"></i></div>
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
            <div class="p-3 bg-warning bg-opacity-10 text-warning rounded-circle me-3"><i class="bi bi-tools fs-4"></i></div>
            <div>
                <small class="text-muted d-block">ซ่อมบำรุง</small>
                <h4 class="mb-0 fw-bold text-warning">{{ $maintenanceCount }} รายการ</h4>
            </div>
        </div>
    </div>
</div>

{{-- --- ส่วนการค้นหา (เพิ่มใหม่) --- --}}
<div class="card search-card mb-4">
    <div class="card-body p-3">
        <form action="{{ route('rooms.index') }}" method="GET" class="row g-2">
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-end-0"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control border-start-0" placeholder="พิมพ์หมายเลขห้องเพื่อค้นหา..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-4">
                <select name="status" class="form-select">
                    <option value="">ทุกสถานะ</option>
                    <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>ว่าง</option>
                    <option value="occupied" {{ request('status') == 'occupied' ? 'selected' : '' }}>ใช้งานอยู่</option>
                    <option value="maintenance" {{ request('status') == 'maintenance' ? 'selected' : '' }}>ซ่อมบำรุง</option>
                </select>
            </div>
            <div class="col-md-3">
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1">ค้นหา</button>
                    @if(request()->has('search') || request()->has('status'))
                        <a href="{{ route('rooms.index') }}" class="btn btn-light border">ล้าง</a>
                    @endif
                </div>
            </div>
        </form>
    </div>
</div>

{{-- ตารางแสดงผล --}}
<div class="card stat-card-custom overflow-hidden border-0">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light">
                <tr>
                    <th class="ps-4">หมายเลขห้อง</th>
                    <th>ประเภท</th>
                    <th>สถานะ</th>
                    <th class="text-end pe-4">จัดการ</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($rooms as $room)
                <tr>
                    <td class="ps-4 fw-bold text-dark">{{ $room->room_number }}</td>
                    <td>{{ $room->room_type }}</td>
                    <td>
                        @php
                            $badgeClass = match($room->status) {
                                'available' => 'success',
                                'occupied' => 'primary',
                                'maintenance' => 'warning',
                                default => 'secondary'
                            };
                            $statusText = match($room->status) {
                                'available' => 'ว่าง',
                                'occupied' => 'ใช้งาน',
                                'maintenance' => 'ซ่อมบำรุง',
                                default => 'ไม่ระบุ'
                            };
                        @endphp
                        <span class="badge bg-{{ $badgeClass }} bg-opacity-10 text-{{ $badgeClass }}">
                            {{ $statusText }}
                        </span>
                    </td>
                    <td class="text-end pe-4">
                        <a href="{{ route('rooms.show', $room) }}" class="btn btn-sm btn-light text-info"><i class="bi bi-eye"></i></a>
                        <a href="{{ route('rooms.edit', $room) }}" class="btn btn-sm btn-light text-warning"><i class="bi bi-pencil"></i></a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center py-5 text-muted">
                        <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                        ไม่พบข้อมูลห้องพักที่ค้นหา
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4 d-flex justify-content-center">
    {{ $rooms->links('pagination::bootstrap-5') }}
</div>
@endsection