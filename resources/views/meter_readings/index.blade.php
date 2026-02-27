@extends('layouts.app')

@section('title', 'บันทึกเลขมิเตอร์')

@section('page-title', 'บันทึกเลขมิเตอร์')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">บันทึกเลขมิเตอร์</h4>
        <p class="text-muted mb-0">
            ห้อง <strong>{{ $meter->room->room_number ?? '-' }}</strong>
            @if($meter->type === 'water')
                <span class="badge bg-info">น้ำ</span>
            @else
                <span class="badge bg-warning">ไฟฟ้า</span>
            @endif
            | เลขมิเตอร์: <strong>{{ $meter->meter_number }}</strong>
        </p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('meters.show', $meter) }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>กลับ
        </a>
        <a href="{{ route('meters.readings.export', $meter) . '?' . http_build_query(request()->only(['date', 'search'])) }}" class="btn btn-success">
            <i class="bi bi-download me-1"></i>ส่งออก CSV
        </a>
        <a href="{{ route('meters.readings.create', $meter) }}" class="btn btn-primary-custom">
            <i class="bi bi-plus-lg me-1"></i>เพิ่มเลข
        </a>
    </div>
</div>

<!-- Search and Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('meters.readings.index', $meter) }}" class="row g-3">
            <div class="col-md-6">
                <input type="date" name="date" class="form-control" value="{{ request('date') }}">
            </div>
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="ค้นหาหมายเหตุ..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search me-1"></i>{{ __("ui.search") }}
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Readings Table -->
<div class="table-card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>วันที่</th>
                    <th>เลขมิเตอร์</th>
                    <th>ผู้บันทึก</th>
                    <th>หมายเหตุ</th>
                    <th>การกระทำ</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($readings as $reading)
                    <tr>
                        <td>{{ $reading->reading_date?->format('d/m/Y') ?? '-' }}</td>
                        <td><strong>{{ number_format((float)$reading->reading_value, 2) }}</strong></td>
                        <td>{{ $reading->recordedBy->name ?? '-' }}</td>
                        <td>{{ $reading->notes ?? '-' }}</td>
                        <td>
                            <div class="d-flex gap-2">
                                <a href="{{ route('meters.readings.edit', [$meter, $reading]) }}" class="btn btn-sm btn-outline-warning">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('meters.readings.destroy', [$meter, $reading]) }}" method="POST" class="d-inline">
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
                            <p class="text-muted mt-2">ไม่มีรายการบันทึกเลขมิเตอร์</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Pagination -->
@if ($readings->hasPages())
    <div class="d-flex justify-content-center mt-4">
        {{ $readings->links('pagination::bootstrap-5') }}
    </div>
@endif
@endsection



