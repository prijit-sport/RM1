@extends('layouts.app')

@section('title', 'จัดการสินค้า')

@section('page-title', 'จัดการสินค้า')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">รายการสินค้า</h4>
    <div class="d-flex gap-2">
        <a href="{{ route('items.export') }}" class="btn btn-outline-success">
            <i class="bi bi-download me-1"></i>Export
        </a>
        <a href="{{ route('items.create') }}" class="btn btn-primary-custom">
            <i class="bi bi-plus-lg me-1"></i>เพิ่มสินค้า
        </a>
    </div>
</div>

<!-- Search and Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('items.index') }}" class="row g-3">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="ค้นหาชื่อสินค้า..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="category" class="form-select">
                    <option value="">ทุกหมวดหมู่</option>
                    <option value="อุปกรณ์สำนักงาน" {{ request('category') == 'อุปกรณ์สำนักงาน' ? 'selected' : '' }}>อุปกรณ์สำนักงาน</option>
                    <option value="เฟอร์นิเจอร์" {{ request('category') == 'เฟอร์นิเจอร์' ? 'selected' : '' }}>เฟอร์นิเจอร์</option>
                    <option value="อิเล็กทรอนิกส์" {{ request('category') == 'อิเล็กทรอนิกส์' ? 'selected' : '' }}>อิเล็กทรอนิกส์</option>
                    <option value="อื่นๆ" {{ request('category') == 'อื่นๆ' ? 'selected' : '' }}>อื่นๆ</option>
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

<!-- Items Table -->
<div class="table-card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>ชื่อสินค้า</th>
                    <th>หมวด</th>
                    <th>ราคา</th>
                    <th>จำนวน</th>
                    <th>สถานะ</th>
                    <th>การกระทำ</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($items as $item)
                    <tr>
                        <td><strong>{{ $item->name }}</strong></td>
                        <td>{{ $item->category }}</td>
                        <td>฿{{ number_format($item->price, 2) }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>
                            @php
                                $statusClasses = [
                                    'active' => 'bg-success',
                                    'inactive' => 'bg-secondary',
                                    'out_of_stock' => 'bg-danger',
                                ];
                                $statusLabels = [
                                    'active' => 'ใช้งาน',
                                    'inactive' => 'ไม่ใช้งาน',
                                    'out_of_stock' => 'สินค้าหมด',
                                ];
                            @endphp
                            <span class="badge {{ $statusClasses[$item->status] ?? '' }}">
                                {{ $statusLabels[$item->status] ?? $item->status }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex gap-2">
                                <a href="{{ route('items.show', $item) }}" class="btn btn-sm btn-outline-info">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('items.edit', $item) }}" class="btn btn-sm btn-outline-warning">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('items.destroy', $item) }}" method="POST" class="d-inline">
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
                            <p class="text-muted mt-2">ไม่มีข้อมูลสินค้า</p>
                            <a href="{{ route('items.create') }}" class="btn btn-primary-custom mt-2">เพิ่มสินค้า</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Pagination -->
@if ($items->hasPages())
    <div class="d-flex justify-content-center mt-4">
        {{ $items->links('pagination::bootstrap-5') }}
    </div>
@endif
@endsection
