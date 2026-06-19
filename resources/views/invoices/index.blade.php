@extends('layouts.app')

@section('title', 'จัดการใบแจ้งหนี้')
@section('page-title', 'จัดการใบแจ้งหนี้')

@push('styles')
    {{-- romar-theme.css include แล้วใน layouts.app --}}
@endpush

@section('content')

    {{-- PAGE HEADER --}}
    <div class="page-header-romar">
        <div class="page-title-wrap">
            <h1><i class="bi bi-receipt"></i> จัดการใบแจ้งหนี้</h1>
            <p>จัดการค่าเช่าห้องและค่าน้ำ/ค่าไฟแยกประเภท</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('invoices.bulk-create') }}" class="btn-outline-romar">
                <i class="bi bi-plus-circle"></i> สร้างหลายใบ
            </a>
            <a href="{{ route('invoices.export') }}" class="btn-outline-romar">
                <i class="bi bi-download"></i> {{ __('ui.export') }}
            </a>
            <a href="{{ route('invoices.create') }}" class="btn-romar">
                <i class="bi bi-plus-lg"></i> เพิ่มใบแจ้งหนี้
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- ✅ TYPE TABS --}}
    <div class="mb-4">
        <ul class="nav nav-tabs" style="border-bottom: 2px solid #e2e8f0;">
            <li class="nav-item">
                <a class="nav-link {{ request('invoice_type', 'all') === 'all' ? 'active' : '' }}"
                    href="{{ route('invoices.index', array_merge(request()->except('invoice_type', 'page'), [])) }}"
                    style="{{ request('invoice_type', 'all') === 'all' ? 'border-bottom: 3px solid var(--romar-primary); color: var(--romar-primary); font-weight:600;' : 'color:#64748b;' }}">
                    <i class="bi bi-receipt me-1"></i> ทั้งหมด
                    <span class="badge bg-secondary ms-1">{{ $stats['total'] ?? 0 }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request('invoice_type') === 'rent' ? 'active' : '' }}"
                    href="{{ route('invoices.index', array_merge(request()->except('invoice_type', 'page'), ['invoice_type' => 'rent'])) }}"
                    style="{{ request('invoice_type') === 'rent' ? 'border-bottom: 3px solid #4f46e5; color: #4f46e5; font-weight:600;' : 'color:#64748b;' }}">
                    <i class="bi bi-house-door me-1"></i> ค่าห้องพัก
                    <span class="badge ms-1" style="background:#4f46e5;">{{ $stats['rent_count'] ?? 0 }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request('invoice_type') === 'utility' ? 'active' : '' }}"
                    href="{{ route('invoices.index', array_merge(request()->except('invoice_type', 'page'), ['invoice_type' => 'utility'])) }}"
                    style="{{ request('invoice_type') === 'utility' ? 'border-bottom: 3px solid #0ea5e9; color: #0ea5e9; font-weight:600;' : 'color:#64748b;' }}">
                    <i class="bi bi-lightning-charge me-1"></i> ค่าน้ำ/ค่าไฟ
                    <span class="badge ms-1" style="background:#0ea5e9;">{{ $stats['utility_count'] ?? 0 }}</span>
                </a>
            </li>
        </ul>
    </div>

    {{-- QUICK STATS --}}
    <div class="stats-grid">
        <div class="stat-card" style="--stat-color: var(--romar-primary); --stat-bg: var(--romar-soft);">
            <div class="stat-icon"><i class="bi bi-receipt-cutoff"></i></div>
            <div class="stat-label">ใบแจ้งหนี้ทั้งหมด</div>
            <div class="stat-value">{{ number_format($stats['total'] ?? 0) }}</div>
            @if (($stats['monthly_diff'] ?? 0) !== 0)
                <div class="stat-meta {{ $stats['monthly_diff'] > 0 ? 'up' : 'down' }}">
                    <i class="bi bi-arrow-{{ $stats['monthly_diff'] > 0 ? 'up' : 'down' }}-short"></i>
                    {{ $stats['monthly_diff'] > 0 ? '+' : '' }}{{ $stats['monthly_diff'] }} จากเดือนที่แล้ว
                </div>
            @endif
        </div>

        <div class="stat-card" style="--stat-color: var(--success); --stat-bg: var(--success-soft);">
            <div class="stat-icon"><i class="bi bi-check-circle"></i></div>
            <div class="stat-label">ชำระแล้ว</div>
            <div class="stat-value">{{ number_format($stats['paid_count'] ?? 0) }}</div>
            <div class="stat-meta">มูลค่ารวม ฿ {{ number_format($stats['paid_amount'] ?? 0) }}</div>
        </div>

        <div class="stat-card" style="--stat-color: var(--warning); --stat-bg: var(--warning-soft);">
            <div class="stat-icon"><i class="bi bi-clock-history"></i></div>
            <div class="stat-label">รอชำระ</div>
            <div class="stat-value">{{ number_format($stats['sent_count'] ?? 0) }}</div>
            <div class="stat-meta">มูลค่ารวม ฿ {{ number_format($stats['sent_amount'] ?? 0) }}</div>
        </div>

        <div class="stat-card" style="--stat-color: var(--danger); --stat-bg: var(--danger-soft);">
            <div class="stat-icon"><i class="bi bi-exclamation-triangle"></i></div>
            <div class="stat-label">เกินกำหนด</div>
            <div class="stat-value">{{ number_format($stats['overdue_count'] ?? 0) }}</div>
            @if (($stats['overdue_count'] ?? 0) > 0)
                <div class="stat-meta down"><i class="bi bi-bell"></i> ต้องติดตามด่วน</div>
            @else
                <div class="stat-meta">ไม่มีรายการเกินกำหนด</div>
            @endif
        </div>
    </div>

    {{-- SEARCH & FILTER --}}
    <form method="GET" action="{{ route('invoices.index') }}" class="filter-bar">
        @if (request('invoice_type'))
            <input type="hidden" name="invoice_type" value="{{ request('invoice_type') }}">
        @endif

        <div class="search-input-romar">
            <i class="bi bi-search"></i>
            <input type="text" name="search" placeholder="ค้นหาเลขที่ใบแจ้งหนี้, ผู้เช่า, ห้อง..."
                value="{{ request('search') }}">
        </div>

        <select name="status" class="filter-select-romar" onchange="this.form.submit()">
            <option value="">ทุกสถานะ</option>
            <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
            <option value="sent" {{ request('status') == 'sent' ? 'selected' : '' }}>รอชำระ</option>
            <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>ชำระแล้ว</option>
            <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>เกินกำหนด</option>
            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>ยกเลิก</option>
        </select>

        <button type="submit" class="btn-romar">
            <i class="bi bi-funnel"></i> กรอง
        </button>

        @if (request()->hasAny(['search', 'status']))
            <a href="{{ route('invoices.index', request('invoice_type') ? ['invoice_type' => request('invoice_type')] : []) }}"
                class="btn-outline-romar">
                <i class="bi bi-x-lg"></i> ล้าง
            </a>
        @endif
    </form>

    {{-- DATA TABLE --}}
    <div class="data-card">
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>เลขที่ใบแจ้งหนี้</th>
                        <th>ประเภท</th>
                        <th>ผู้เช่า / ห้อง</th>
                        <th>ยอดรวม</th>
                        <th>วันออก</th>
                        <th>ครบกำหนด</th>
                        <th>สถานะ</th>
                        <th style="text-align:right;">การกระทำ</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($invoices as $invoice)
                        @php
                            $guest = $invoice->booking?->guest;
                            $room = $invoice->booking?->room;
                            $fullName = $guest ? trim($guest->first_name . ' ' . $guest->last_name) : 'ไม่ระบุ';
                            $initial = $guest ? mb_substr($guest->first_name ?? '?', 0, 2, 'UTF-8') : '?';
                            $variants = ['', 'v1', 'v2', 'v3', 'v4'];
                            $avatarVariant = $variants[$guest ? crc32($guest->id) % 5 : 0];
                            $invoiceType = $invoice->invoice_type ?? 'rent';
                        @endphp
                        <tr>
                            {{-- เลขที่ --}}
                            <td>
                                <div class="doc-num">{{ $invoice->invoice_number }}</div>
                                <div class="doc-num-sub">Booking #{{ $invoice->booking_id }}</div>
                            </td>

                            {{-- ✅ ประเภท --}}
                            <td>
                                @if ($invoiceType === 'utility')
                                    <span class="badge rounded-pill"
                                        style="background:#e0f2fe; color:#0369a1; font-size:0.78rem; padding:5px 10px;">
                                        <i class="bi bi-lightning-charge-fill me-1"></i>ค่าน้ำ/ไฟ
                                    </span>
                                @else
                                    <span class="badge rounded-pill"
                                        style="background:#ede9fe; color:#4f46e5; font-size:0.78rem; padding:5px 10px;">
                                        <i class="bi bi-house-door-fill me-1"></i>ค่าห้อง
                                    </span>
                                @endif
                            </td>

                            {{-- ผู้เช่า / ห้อง --}}
                            <td>
                                <div class="person">
                                    <div class="person-avatar {{ $avatarVariant }}">{{ $initial }}</div>
                                    <div>
                                        <div class="person-name">{{ $fullName }}</div>
                                        @if ($room)
                                            <span class="room-badge">
                                                <i class="bi bi-door-closed"></i> {{ $room->room_number }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            {{-- ยอดรวม --}}
                            <td>
                                <span class="amount">
                                    <span class="currency">฿</span>{{ number_format($invoice->total, 2) }}
                                </span>
                            </td>

                            {{-- วันออก --}}
                            <td>{{ optional($invoice->issue_date)->format('d/m/Y') }}</td>

                            {{-- ครบกำหนด --}}
                            <td>{{ optional($invoice->due_date)->format('d/m/Y') }}</td>

                            {{-- สถานะ --}}
                            <td>
                                @php
                                    $overdueDays = null;
                                    if ($invoice->status === 'overdue' && $invoice->due_date) {
                                        $overdueDays = abs(
                                            now()
                                                ->startOfDay()
                                                ->diffInDays($invoice->due_date->startOfDay(), false),
                                        );
                                    }
                                @endphp
                                <span class="status-pill status-{{ $invoice->status }}">
                                    @if ($invoice->status === 'overdue' && $overdueDays)
                                        เกินกำหนด {{ $overdueDays }} วัน
                                    @else
                                        {{ enum_bi('invoice_status', $invoice->status, ucfirst($invoice->status)) }}
                                    @endif
                                </span>
                            </td>

                            {{-- การกระทำ --}}
                            <td>
                                <div class="action-bar">
                                    <a href="{{ route('invoices.show', $invoice) }}" class="action-btn-romar view"
                                        title="ดูรายละเอียด">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('invoices.edit', $invoice) }}" class="action-btn-romar edit"
                                        title="แก้ไข">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    @if (in_array($invoice->status, ['sent', 'overdue']))
                                        <form action="{{ route('invoices.markAsPaid', $invoice) }}" method="POST"
                                            class="d-inline">
                                            @csrf
                                            <button type="submit" class="action-btn-romar paid"
                                                title="ทำเครื่องหมายชำระแล้ว"
                                                onclick="return confirm('ยืนยันการชำระเงิน?')">
                                                <i class="bi bi-check-lg"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">
                                <div class="empty-state">
                                    <div class="empty-state-icon"><i class="bi bi-inbox"></i></div>
                                    <div class="empty-state-title">ยังไม่มีข้อมูลใบแจ้งหนี้</div>
                                    <div class="empty-state-text">
                                        @if (request()->hasAny(['search', 'status']))
                                            ไม่พบรายการที่ตรงกับเงื่อนไขที่เลือก ลองล้างตัวกรองใหม่
                                        @else
                                            เพิ่มใบแจ้งหนี้ใหม่ได้ทันที
                                        @endif
                                    </div>
                                    @if (!request()->hasAny(['search', 'status']))
                                        <a href="{{ route('invoices.create') }}" class="btn-romar">
                                            <i class="bi bi-plus-lg"></i> เพิ่มใบแจ้งหนี้
                                        </a>
                                    @else
                                        <a href="{{ route('invoices.index') }}" class="btn-outline-romar">
                                            <i class="bi bi-arrow-counterclockwise"></i> ล้างตัวกรอง
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($invoices->hasPages())
            <div class="pagination-wrap">
                <div class="pagination-info">
                    แสดง {{ $invoices->firstItem() }}-{{ $invoices->lastItem() }}
                    จาก {{ number_format($invoices->total()) }} รายการ
                </div>
                <div>
                    {{ $invoices->withQueryString()->links('pagination::bootstrap-5') }}
                </div>
            </div>
        @endif
    </div>

@endsection
