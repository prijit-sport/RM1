@extends('layouts.app')

@section('title', 'จัดการสิ่งอำนวยความสะดวก')

@section('page-title', 'จัดการสิ่งอำนวยความสะดวก')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">รายการสิ่งอำนวยความสะดวก</h4>
    <div class="d-flex gap-2">
        <a href="{{ route('facilities.export') }}" class="btn btn-success">
            <i class="bi bi-download me-1"></i>{{ __("ui.export") }}
        </a>
        <a href="{{ route('facilities.create') }}" class="btn btn-primary-custom">
            <i class="bi bi-plus-lg me-1"></i>เพิ่มสิ่งอำนวยความสะดวก
        </a>
    </div>
</div>

<!-- Search and Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('facilities.index') }}" class="row g-3">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="ค้นหาชื่อ..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">ทุกสถานะ</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>ใช้งาน</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>ไม่ใช้งาน</option>
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

<!-- Facilities Table -->
<div class="table-card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>ชื่อ</th>
                    <th>ประเภท</th>
                    <th>ที่ตั้ง</th>
                    <th>สถานะ</th>
                    <th>การกระทำ</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($facilities as $facility)
                    <tr>
                        <td><strong>{{ $facility->name }}</strong></td>
                        <td>{{ $facility->type }}</td>
                        <td>{{ $facility->location }}</td>
                        <td>
                            @php
                                $statusClasses = [
                                    'active' => 'bg-success',
                                    'inactive' => 'bg-secondary',
                                ];
                            @endphp
                            <span class="badge {{ $statusClasses[$facility->status] ?? '' }}">
                                {{ $facility->status == 'active' ? 'ใช้งาน' : 'ไม่ใช้งาน' }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex gap-2">
                                <a href="{{ route('facilities.show', $facility) }}" class="btn btn-sm btn-outline-info">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('facilities.edit', $facility) }}" class="btn btn-sm btn-outline-warning">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('facilities.destroy', $facility) }}" method="POST" class="d-inline">
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
                        <td colspan="5" class="text-center py-4">
                            <i class="bi bi-inbox fs-1 text-muted"></i>
                            <p class="text-muted mt-2">ไม่มีข้อมูลสิ่งอำนวยความสะดวก</p>
                            <a href="{{ route('facilities.create') }}" class="btn btn-primary-custom mt-2">เพิ่มสิ่งอำนวยความสะดวก</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Pagination -->
@if ($facilities->hasPages())
    <div class="d-flex justify-content-center mt-4">
        {{ $facilities->links('pagination::bootstrap-5') }}
    </div>
@endif
@endsection



