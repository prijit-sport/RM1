@extends('layouts.app')
 
@section('title', 'รายชื่อผู้เช่า')
 
@section('content')
<div class="container-fluid py-4">
 
    {{-- ── Header ── --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-0 fw-bold">
                <i class="bi bi-people-fill text-primary me-2"></i>รายชื่อผู้เช่า
            </h4>
            <small class="text-muted">ภาพรวมผู้เช่าและสถานะการชำระเงิน</small>
        </div>
        <div class="d-flex gap-2 align-items-center">
            {{-- Filter โซน --}}
            <form method="GET" action="{{ route('tenants-status.index') }}" class="d-flex gap-2 align-items-center">
                @if(request('search'))
                    <input type="hidden" name="search" value="{{ request('search') }}">
                @endif
                <select name="zone" class="form-select form-select-sm" onchange="this.form.submit()" style="min-width:130px;">
                    <option value="">ทุกโซน</option>
                    @foreach($zones as $z)
                        <option value="{{ $z }}" {{ request('zone') == $z ? 'selected' : '' }}>
                            โซน {{ $z }}
                        </option>
                    @endforeach
                </select>
            </form>
            <a href="{{ route('guests.create') }}" class="btn btn-primary">
                <i class="bi bi-person-plus me-1"></i>เพิ่มผู้เช่า
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
 
    {{-- ── Summary Stats (5 cards) ── --}}
    <div class="row g-3 mb-4">
        {{-- ผู้เช่าทั้งหมด --}}
        <div class="col-md-6 col-lg">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center py-4">
                    <div class="fs-1 fw-bold text-dark lh-1">{{ $totalTenants }}</div>
                    <div class="text-muted small mt-2">ผู้เช่าทั้งหมด</div>
                </div>
            </div>
        </div>
        {{-- ห้องแอร์ --}}
        <div class="col-md-6 col-lg">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center py-4">
                    <div class="fs-1 fw-bold lh-1" style="color:#2563eb;">{{ $acRoomsCount }}</div>
                    <div class="text-muted small mt-2">ห้องแอร์</div>
                </div>
            </div>
        </div>
        {{-- ห้องพัดลม --}}
        <div class="col-md-6 col-lg">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center py-4">
                    <div class="fs-1 fw-bold lh-1" style="color:#0891b2;">{{ $fanRoomsCount }}</div>
                    <div class="text-muted small mt-2">ห้องพัดลม</div>
                </div>
            </div>
        </div>
        {{-- ชำระแล้วเดือนนี้ --}}
        <div class="col-md-6 col-lg">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center py-4">
                    <div class="fs-1 fw-bold lh-1" style="color:#059669;">{{ $paidThisMonthCount }}</div>
                    <div class="text-muted small mt-2">ชำระแล้วเดือนนี้</div>
                </div>
            </div>
        </div>
        {{-- ค้างชำระ --}}
        <div class="col-md-6 col-lg">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center py-4">
                    <div class="fs-1 fw-bold lh-1" style="color:#dc2626;">{{ $overdueCount }}</div>
                    <div class="text-muted small mt-2">ค้างชำระ</div>
                </div>
            </div>
        </div>
    </div>
 
    {{-- ── Search ── --}}
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body py-3">
            <form method="GET" action="{{ route('tenants-status.index') }}" class="row g-2 align-items-end">
                @if(request('zone'))
                    <input type="hidden" name="zone" value="{{ request('zone') }}">
                @endif
                <div class="col-md-6">
                    <input type="text" name="search" class="form-control form-control-sm"
                           placeholder="ค้นหาชื่อ-นามสกุล / หมายเลขห้อง..."
                           value="{{ request('search') }}">
                </div>
                <div class="col-md-auto">
                    <button type="submit" class="btn btn-primary btn-sm px-4">
                        <i class="bi bi-search me-1"></i>ค้นหา
                    </button>
                    <a href="{{ route('tenants-status.index') }}" class="btn btn-outline-secondary btn-sm ms-1">
                        รีเซ็ต
                    </a>
                </div>
            </form>
        </div>
    </div>
 
    {{-- ── ตารางรายชื่อ ── --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">ห้อง</th>
                            <th>โซน</th>
                            <th>ชื่อ-นามสกุล</th>
                            <th>ประเภท</th>
                            <th class="text-end">ราคา/เดือน</th>
                            <th>เข้าพักวันที่</th>
                            <th class="text-center">สถานะบิล</th>
                            <th class="text-center pe-4">การกระทำ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tenants as $t)
                            @php
                                // ── ประเภทห้อง (แอร์/พัดลม) ── 🔧 [ปรับตรงนี้ถ้า column ชื่ออื่น]
                                $roomType = $t->room?->room_type ?? '';
                                $isAircon = str_contains($roomType, 'แอร์') || str_contains(strtolower($roomType), 'air');
 
                                // ── ราคาห้อง ── 🔧 [ปรับตรงนี้ถ้า column ชื่ออื่น เช่น monthly_rent]
                                $price = $t->room?->price ?? $t->room?->monthly_rent ?? $t->total_price ?? 0;
 
                                // ── สถานะบิลล่าสุด ──
                                $latestInvoice = $t->invoices?->sortByDesc('created_at')->first();
                                $billStatus    = $latestInvoice?->status ?? null;
                                $isOverdue     = $latestInvoice && $billStatus !== 'paid'
                                              && $latestInvoice->due_date
                                              && \Carbon\Carbon::parse($latestInvoice->due_date)->lt($today);
 
                                $billBadge = match(true) {
                                    $billStatus === 'paid' => ['bg' => '#d1fae5', 'color' => '#065f46', 'text' => 'ชำระแล้ว'],
                                    $isOverdue             => ['bg' => '#fee2e2', 'color' => '#991b1b', 'text' => 'ค้างชำระ'],
                                    $billStatus === 'pending' => ['bg' => '#fef3c7', 'color' => '#92400e', 'text' => 'รอชำระ'],
                                    default                => ['bg' => '#f3f4f6', 'color' => '#4b5563', 'text' => 'ไม่มีบิล'],
                                };
                            @endphp
                            <tr>
                                {{-- ห้อง --}}
                                <td class="ps-4 fw-bold">
                                    {{ $t->room?->room_number ?? '-' }}
                                </td>
                                {{-- โซน --}}
                                <td>{{ $t->room?->zone ?? '-' }}</td>
                                {{-- ชื่อ-นามสกุล --}}
                                <td>
                                    @if($t->guest)
                                        {{ $t->guest->first_name }} {{ $t->guest->last_name }}
                                    @else
                                        <span class="text-muted fst-italic">ไม่ระบุ</span>
                                    @endif
                                </td>
                                {{-- ประเภท --}}
                                <td>
                                    @if($isAircon)
                                        <span class="badge rounded-pill px-3 py-2"
                                              style="background:#dbeafe; color:#1e40af; font-weight:500;">
                                            <i class="bi bi-snow me-1"></i>แอร์
                                        </span>
                                    @elseif($roomType)
                                        <span class="badge rounded-pill px-3 py-2"
                                              style="background:#cffafe; color:#0e7490; font-weight:500;">
                                            <i class="bi bi-wind me-1"></i>พัดลม
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                {{-- ราคา --}}
                                <td class="text-end">
                                    @if($price > 0)
                                        <span class="fw-semibold">{{ number_format($price, 0) }}</span>
                                        <span class="text-muted small">฿</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                {{-- เข้าพักวันที่ --}}
                                <td class="small text-muted">
                                    {{ optional($t->check_in_date)->translatedFormat('j M y') ?? '-' }}
                                </td>
                                {{-- สถานะบิล --}}
                                <td class="text-center">
                                    <span class="badge rounded-pill px-3 py-2"
                                          style="background:{{ $billBadge['bg'] }}; color:{{ $billBadge['color'] }}; font-weight:500;">
                                        {{ $billBadge['text'] }}
                                    </span>
                                </td>
                                {{-- การกระทำ --}}
                                <td class="text-center pe-4">
                                    @if($t->guest)
                                        <a href="{{ route('guests.show', $t->guest) }}"
                                           class="btn btn-outline-primary btn-sm px-3" title="ดูข้อมูล">
                                            <i class="bi bi-eye me-1"></i>ดูข้อมูล
                                        </a>
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <i class="bi bi-people fs-1 d-block mb-2 opacity-25"></i>
                                    <p class="mb-0">ไม่พบรายชื่อผู้เช่า</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
 
            {{-- Pagination --}}
            @if($tenants->hasPages())
                <div class="d-flex justify-content-center py-3 border-top">
                    {{ $tenants->links() }}
                </div>
            @endif
        </div>
    </div>
 
</div>
 
<style>
    .table > :not(caption) > * > * { padding: 0.85rem 0.75rem; }
    .badge.rounded-pill { font-size: 0.8em; }
</style>
@endsection
 