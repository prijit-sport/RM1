@extends('layouts.app')

@section('title', 'จัดการห้องพัก')

@section('page-title', 'จัดการห้องพัก')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">รายการห้องพัก</h4>
    <div class="d-flex gap-2">
        <a href="{{ route('rooms.bulk-create') }}" class="btn btn-outline-primary">
            <i class="bi bi-plus-circle me-1"></i>เพิ่มหลายห้อง
        </a>
        <a href="{{ route('rooms.export') }}" class="btn btn-success">
            <i class="bi bi-download me-1"></i>{{ __("ui.export") }}
        </a>
        <a href="{{ route('rooms.create') }}" class="btn btn-primary-custom">
            <i class="bi bi-plus-lg me-1"></i>เพิ่มห้องใหม่
        </a>
    </div>
</div>

<!-- Search and Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('rooms.index') }}" class="row g-3">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="ค้นหาหมายเลขห้อง..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">ทุกสถานะ</option>
                    <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>ว่าง</option>
                    <option value="occupied" {{ request('status') == 'occupied' ? 'selected' : '' }}>ใช้งาน</option>
                    <option value="maintenance" {{ request('status') == 'maintenance' ? 'selected' : '' }}>ซ่อมบำรุง</option>
                </select>
            </div>
            <div class="col-md-3">
                <select name="room_type" class="form-select">
                    <option value="">ทุกประเภท</option>
                    <option value="Standard" {{ request('room_type') == 'Standard' ? 'selected' : '' }}>{{ enum_bi('room_type', 'Standard') }}</option>
                    <option value="Deluxe" {{ request('room_type') == 'Deluxe' ? 'selected' : '' }}>{{ enum_bi('room_type', 'Deluxe') }}</option>
                    <option value="Suite" {{ request('room_type') == 'Suite' ? 'selected' : '' }}>{{ enum_bi('room_type', 'Suite') }}</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search me-1"></i>{{ __("ui.search") }}
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Room Stats -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="stat-card">
            <div class="d-flex align-items-center">
                <div class="stat-icon bg-success-subtle text-success">
                    <i class="bi bi-door-open"></i>
                </div>
                <div class="ms-3">
                    <p class="text-muted mb-0">ห้องว่าง</p>
                    <h4 class="mb-0">{{ $rooms->where('status', 'available')->count() }}</h4>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <div class="d-flex align-items-center">
                <div class="stat-icon bg-primary-subtle text-primary">
                    <i class="bi bi-door-closed"></i>
                </div>
                <div class="ms-3">
                    <p class="text-muted mb-0">ห้องใช้งาน</p>
                    <h4 class="mb-0">{{ $rooms->where('status', 'occupied')->count() }}</h4>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <div class="d-flex align-items-center">
                <div class="stat-icon bg-warning-subtle text-warning">
                    <i class="bi bi-tools"></i>
                </div>
                <div class="ms-3">
                    <p class="text-muted mb-0">ซ่อมบำรุง</p>
                    <h4 class="mb-0">{{ $rooms->where('status', 'maintenance')->count() }}</h4>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Rooms Table -->
<div class="table-card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>หมายเลขห้อง</th>
                    <th>ประเภทห้อง</th>
                    <th>ราคา/เดือน</th>
                    <th>ความจุ</th>
                    <th>สถานะ</th>
                    <th>การกระทำ</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($rooms as $room)
                    <tr>
                        <td><strong>{{ $room->room_number }}</strong></td>
                                                <td>{{ enum_bi('room_type', $room->room_type) }}</td>
                        <td>฿{{ number_format($room->price_per_month, 2) }}</td>
                        <td>{{ $room->capacity }} คน</td>
                        <td>
                            @php
                                $statusClasses = [
                                    'available' => 'status-available',
                                    'occupied' => 'status-occupied',
                                    'maintenance' => 'status-maintenance',
                                ];
                            @endphp
                            <span class="status-badge {{ $statusClasses[$room->status] ?? '' }}">
                                {{ enum_th('room_status', $room->status) }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex gap-2">
                                <a href="{{ route('rooms.show', $room) }}" class="btn btn-sm btn-outline-info">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('rooms.edit', $room) }}" class="btn btn-sm btn-outline-warning">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('rooms.destroy', $room) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('แน่ใจหรือไม่ที่จะลบห้องนี้?')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-4">
                            <i class="bi bi-inbox fs-1 text-muted"></i>
                            <p class="text-muted mt-2">ไม่มีข้อมูลห้องพัก</p>
                            <a href="{{ route('rooms.create') }}" class="btn btn-primary-custom mt-2">เพิ่มห้องใหม่</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Pagination -->
@if ($rooms->hasPages())
    <div class="d-flex justify-content-center mt-4">
        {{ $rooms->links('pagination::bootstrap-5') }}
    </div>
@endif
@endsection






