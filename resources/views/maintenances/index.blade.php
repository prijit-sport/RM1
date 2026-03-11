@extends('layouts.app')

@section('title', 'จัดการการบำรุงรักษา')

@section('page-title', 'จัดการการบำรุงรักษา')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">รายการการบำรุงรักษา</h4>
    <div class="d-flex gap-2">
        <a href="{{ route('maintenances.export') }}" class="btn btn-success">
            <i class="bi bi-download me-1"></i>{{ __("ui.export") }}
        </a>
        <a href="{{ route('maintenances.create') }}" class="btn btn-primary-custom">
            <i class="bi bi-plus-lg me-1"></i>เพิ่มการบำรุงรักษา
        </a>
    </div>
</div>

<!-- Search and Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('maintenances.index') }}" class="row g-3">
            <div class="col-md-3">
                <input type="text" name="search" class="form-control" placeholder="ค้นหาประเภทปัญหา..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">ทุกสถานะ</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>รอดำเนินการ</option>
                    <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>กำลังดำเนินการ</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>เสร็จสิ้น</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>ยกเลิก</option>
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

<!-- Maintenances Table -->
<div class="table-card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>ห้อง</th>
                    <th>ประเภทปัญหา</th>
                    <th>วันที่แจ้ง</th>
                    <th>ผู้รัดชอบผิบ</th>
                    <th>สถานะ</th>
                    <th>ค่าใช้จ่าย</th>
                    <th>การกระทำ</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($maintenances as $maintenance)
                    <tr>
                        <td>{{ optional($maintenance->room)->room_number ?? '-' }}</td>
                        <td>{{ $maintenance->issue_type }}</td>
                        <td>{{ optional($maintenance->reported_date)->format('d/m/Y') }}</td>
                        <td>{{ $maintenance->assigned_to ?? '-' }}</td>
                        <td>
                            @php
                                $statusClasses = [
                                    'pending' => 'bg-warning',
                                    'in_progress' => 'bg-info',
                                    'completed' => 'bg-success',
                                    'cancelled' => 'bg-secondary',
                                ];
                            @endphp
                            <span class="badge {{ $statusClasses[$maintenance->status] ?? '' }}">
                                {{ enum_th('maintenance_status', $maintenance->status) }}
                            </span>
                        </td>
                        <td>{{ $maintenance->cost ? number_format($maintenance->cost, 2) . ' ฿' : '-' }}</td>
                        <td>
                            <div class="d-flex gap-2">
                                <a href="{{ route('maintenances.show', $maintenance) }}" class="btn btn-sm btn-outline-info">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('maintenances.edit', $maintenance) }}" class="btn btn-sm btn-outline-warning">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @if($maintenance->status == 'pending')
                                    <form action="{{ route('maintenances.start', $maintenance) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-primary" onclick="return confirm('เริ่มดำเนินการ?')">
                                            <i class="bi bi-play-lg"></i>
                                        </button>
                                    </form>
                                @endif
                                @if($maintenance->status == 'in_progress')
                                    <form action="{{ route('maintenances.complete', $maintenance) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-success" onclick="return confirm('ดำเนินการเสร็จสิ้น?')">
                                            <i class="bi bi-check-lg"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-4">
                            <i class="bi bi-inbox fs-1 text-muted"></i>
                            <p class="text-muted mt-2">ไม่มีข้อมูลการบำรุงรักษา</p>
                            <a href="{{ route('maintenances.create') }}" class="btn btn-primary-custom mt-2">เพิ่มการบำรุงรักษา</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Pagination -->
@if ($maintenances->hasPages())
    <div class="d-flex justify-content-center mt-4">
        {{ $maintenances->links('pagination::bootstrap-5') }}
    </div>
@endif
@endsection




