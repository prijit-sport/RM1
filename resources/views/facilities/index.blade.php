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

@php
    $typeMap = [
        'bed'            => ['🛏️', 'เตียง',          'bg-primary'],
        'mattress'       => ['🛌', 'ที่นอน',         'bg-info text-white'],
        'wardrobe'       => ['🚪', 'ตู้เสื้อผ้า',      'bg-success'],
        'dressing_table' => ['💄', 'โต๊ะเครื่องแป้ง',  'bg-warning text-dark'],
        'tv_stand'       => ['📺', 'ชั้นวางทีวี',     'bg-secondary'],
        'clothes_rack'   => ['👔', 'ราวแขวนผ้า',    'bg-dark'],
    ];

    $statusMap = [
        'good'         => ['✅', 'ใช้งานได้',       'bg-success'],
        'fair'         => ['🟡', 'สภาพปานกลาง',    'bg-info text-white'],
        'needs_repair' => ['⚠️', 'ต้องซ่อม',        'bg-warning text-dark'],
        'maintenance'  => ['🔧', 'กำลังซ่อมบำรุง',   'bg-primary'],
        'damaged'      => ['❌', 'ชำรุด',          'bg-danger'],
        'retired'      => ['🗑️', 'ปลดประจำการ',    'bg-secondary'],
    ];

    // ───── การ์ดสถิติ: icon, สี, label, ค่า, link ─────
    $statCards = [
        ['key' => 'total',        'icon' => 'bi-box-seam',            'bg' => '#e0e7ff', 'color' => '#4f46e5', 'label' => 'ทั้งหมด',          'value' => $stats['total'],        'href' => route('facilities.index')],
        ['key' => 'good',         'icon' => 'bi-check-circle-fill',   'bg' => '#d1fae5', 'color' => '#10b981', 'label' => 'ใช้งานได้',        'value' => $stats['good'],         'href' => route('facilities.index', ['status' => 'good'])],
        ['key' => 'fair',         'icon' => 'bi-emoji-neutral-fill',  'bg' => '#dbeafe', 'color' => '#3b82f6', 'label' => 'สภาพปานกลาง',    'value' => $stats['fair'],         'href' => route('facilities.index', ['status' => 'fair'])],
        ['key' => 'needs_repair', 'icon' => 'bi-tools',               'bg' => '#fef3c7', 'color' => '#f59e0b', 'label' => 'ต้องซ่อม',          'value' => $stats['needs_repair'], 'href' => route('facilities.index', ['status' => 'needs_repair'])],
        ['key' => 'maintenance',  'icon' => 'bi-wrench-adjustable',   'bg' => '#fef9c3', 'color' => '#ca8a04', 'label' => 'กำลังซ่อมบำรุง',   'value' => $stats['maintenance'],  'href' => route('facilities.index', ['status' => 'maintenance'])],
        ['key' => 'damaged',      'icon' => 'bi-x-octagon-fill',      'bg' => '#fee2e2', 'color' => '#ef4444', 'label' => 'ชำรุด',            'value' => $stats['damaged'],      'href' => route('facilities.index', ['status' => 'damaged'])],
        ['key' => 'retired',      'icon' => 'bi-trash3-fill',         'bg' => '#f3f4f6', 'color' => '#6b7280', 'label' => 'ปลดประจำการ',    'value' => $stats['retired'],      'href' => route('facilities.index', ['status' => 'retired'])],
    ];
@endphp

<style>
    .stat-summary-card {
        cursor: pointer;
        transition: all 0.2s ease;
        border: none;
    }
    .stat-summary-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 16px rgba(0,0,0,0.12);
    }
    .stat-icon-wrap-sm {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        flex-shrink: 0;
    }
    .stat-value {
        font-size: 1.3rem;
        font-weight: 700;
        line-height: 1.1;
        color: #1f2937;
    }
    .stat-label {
        font-size: 0.75rem;
        color: #6b7280;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* ─── 7 cards ใน 1 row ที่ desktop ─── */
    @media (min-width: 1200px) {
        .row-cols-xl-7 > * {
            flex: 0 0 auto;
            width: 14.2857%;
        }
    }

    .table-card {
        background: white;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        overflow: hidden;
    }
    .pagination { margin-bottom: 0; gap: 2px; }
    .page-link { border-radius: 6px !important; border: none; color: #4f46e5; padding: 0.5rem 0.85rem; }
    .page-item.active .page-link { background-color: #4f46e5; color: white; }
</style>

{{-- ════════ การ์ดสรุปสถานะ (7 ใบ คลิกเพื่อ filter) ════════ --}}
<div class="row row-cols-2 row-cols-md-4 row-cols-xl-7 g-2 mb-4">
    @foreach($statCards as $card)
        <div class="col">
            <a href="{{ $card['href'] }}" class="text-decoration-none text-dark">
                <div class="card stat-summary-card shadow-sm h-100">
                    <div class="card-body d-flex align-items-center gap-2 p-3">
                        <div class="stat-icon-wrap-sm"
                             style="background: {{ $card['bg'] }}; color: {{ $card['color'] }};">
                            <i class="bi {{ $card['icon'] }}"></i>
                        </div>
                        <div class="overflow-hidden flex-grow-1">
                            <div class="stat-label">{{ $card['label'] }}</div>
                            <div class="stat-value">
                                {{ number_format($card['value']) }}
                                <small class="text-muted fw-normal" style="font-size: 0.7rem;">ชิ้น</small>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    @endforeach
</div>

{{-- ════════ Filter ════════ --}}
<div class="card mb-4 border-0 shadow-sm">
    <div class="card-body">
        <form method="GET" action="{{ route('facilities.index') }}" class="row g-3">
            <div class="col-md-3">
                <input type="text" name="search" class="form-control" placeholder="ค้นหาชื่อ/คำอธิบาย..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <select name="type" class="form-select">
                    <option value="">ทุกประเภท</option>
                    @foreach($typeMap as $value => $info)
                        <option value="{{ $value }}" {{ request('type') == $value ? 'selected' : '' }}>
                            {{ $info[0] }} {{ $info[1] }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select">
                    <option value="">ทุกสถานะ</option>
                    @foreach($statusMap as $value => $info)
                        <option value="{{ $value }}" {{ request('status') == $value ? 'selected' : '' }}>
                            {{ $info[0] }} {{ $info[1] }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="location" class="form-select">
                    <option value="">ทุกที่ตั้ง</option>
                    @foreach($locations as $loc)
                        <option value="{{ $loc }}" {{ request('location') == $loc ? 'selected' : '' }}>{{ $loc }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-grow-1">
                    <i class="bi bi-search me-1"></i>ค้นหา
                </button>
                <a href="{{ route('facilities.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-clockwise"></i>
                </a>
            </div>
        </form>
    </div>
</div>

{{-- ════════ Facilities Table ════════ --}}
<div class="table-card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead class="bg-light">
                <tr>
                    <th class="ps-3">ประเภท</th>
                    <th>ชื่อ</th>
                    <th>ที่ตั้ง</th>
                    <th>สถานะ</th>
                    <th>ซ่อมล่าสุด</th>
                    <th>ซ่อมครั้งต่อไป</th>
                    <th class="text-end pe-3">การกระทำ</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($facilities as $facility)
                    @php
                        $type = $typeMap[$facility->type] ?? ['📦', $facility->type, 'bg-secondary'];
                        $status = $statusMap[$facility->status] ?? ['❓', $facility->status, 'bg-secondary'];
                        $nextDate = $facility->next_maintenance_date;
                        $isOverdue = $nextDate && \Carbon\Carbon::parse($nextDate)->isPast();
                        $isUpcoming = $nextDate && !$isOverdue && \Carbon\Carbon::parse($nextDate)->diffInDays(now()) <= 30;
                    @endphp
                    <tr>
                        <td class="ps-3">
                            <span class="badge {{ $type[2] }} px-2 py-1">
                                {{ $type[0] }} {{ $type[1] }}
                            </span>
                        </td>
                        <td>
                            <div class="fw-bold">{{ $facility->name }}</div>
                        </td>
                        <td>{{ $facility->location }}</td>
                        <td>
                            <span class="badge {{ $status[2] }} rounded-pill">
                                {{ $status[0] }} {{ $status[1] }}
                            </span>
                        </td>
                        <td><small>{{ $facility->last_maintenance_date ? \Carbon\Carbon::parse($facility->last_maintenance_date)->format('d/m/Y') : '-' }}</small></td>
                        <td>
                            @if($nextDate)
                                <span class="{{ $isOverdue ? 'text-danger fw-bold' : ($isUpcoming ? 'text-warning' : '') }}">
                                    {{ \Carbon\Carbon::parse($nextDate)->format('d/m/Y') }}
                                </span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="text-end pe-3">
                            <div class="btn-group">
                                <a href="{{ route('facilities.edit', $facility->id) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                                <form action="{{ route('facilities.destroy', $facility->id) }}" method="POST" class="d-inline" onsubmit="return confirm('ยืนยันการลบ?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center py-4 text-muted">ไม่พบข้อมูล</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="card-footer bg-white border-0 py-3">
        <div class="d-flex justify-content-between align-items-center">
            <div class="text-muted small">
                @if($facilities->total() > 0)
                    แสดง {{ $facilities->firstItem() }} ถึง {{ $facilities->lastItem() }} จาก {{ $facilities->total() }} รายการ
                @endif
            </div>
            <div>
                {{ $facilities->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>
@endsection