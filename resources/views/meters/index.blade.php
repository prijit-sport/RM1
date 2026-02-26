@extends('layouts.app')

@section('title', 'จัดการมิเตอร์')

@section('page-title', 'จัดการมิเตอร์')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">รายการมิเตอร์</h4>
    <div class="d-flex gap-2">
        <a href="{{ route('meters.export') }}" class="btn btn-outline-success">
            <i class="bi bi-download me-1"></i>Export
        </a>
        <a href="{{ route('meters.create') }}" class="btn btn-primary-custom">
            <i class="bi bi-plus-lg me-1"></i>เพิ่มมิเตอร์
        </a>
    </div>
</div>

<!-- Search and Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('meters.index') }}" class="row g-3">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="ค้นหาเลขมิเตอร์..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="type" class="form-select">
                    <option value="">ทุกประเภท</option>
                    <option value="water" {{ request('type') == 'water' ? 'selected' : '' }}>น้ำ</option>
                    <option value="electric" {{ request('type') == 'electric' ? 'selected' : '' }}>ไฟฟ้า</option>
                </select>
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">ทุกสถานะ</option>
                    <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>ใช้งาน</option>
                    <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>ปิดใช้งาน</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search me-1"></i>ค้นหา
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Meters Table -->
<div class="table-card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>ห้อง</th>
                    <th>ประเภท</th>
                    <th>เลขมิเตอร์</th>
                    <th>หน่วย</th>
                    <th>สถานะ</th>
                    <th>การกระทำ</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($meters as $meter)
                    <tr>
                        <td><strong>{{ $meter->room->room_number ?? '-' }}</strong></td>
                        <td>
                            @if($meter->type === 'water')
                                <span class="badge bg-info">น้ำ</span>
                            @else
                                <span class="badge bg-warning">ไฟฟ้า</span>
                            @endif
                        </td>
                        <td>{{ $meter->meter_number }}</td>
                        <td>{{ $meter->unit ?? '-' }}</td>
                        <td>
                            @if($meter->is_active)
                                <span class="badge bg-success">ใช้งาน</span>
                            @else
                                <span class="badge bg-secondary">ปิดใช้งาน</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex gap-2">
                                <a href="{{ route('meters.show', $meter) }}" class="btn btn-sm btn-outline-info">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('meters.edit', $meter) }}" class="btn btn-sm btn-outline-warning">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <a href="{{ route('meters.readings.index', $meter) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-journal-text"></i>
                                </a>
                                <form action="{{ route('meters.destroy', $meter) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('แน่ใจหรือไม่ที่จะลบ?')">
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
                            <p class="text-muted mt-2">ไม่มีข้อมูลมิเตอร์</p>
                            <a href="{{ route('meters.create') }}" class="btn btn-primary-custom mt-2">เพิ่มมิเตอร์</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Pagination -->
@if ($meters->hasPages())
    <div class="d-flex justify-content-center mt-4">
        {{ $meters->links('pagination::bootstrap-5') }}
    </div>
@endif
@endsection
