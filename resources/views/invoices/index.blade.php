@extends('layouts.app')

@section('title', 'จัดการใบแจ้งหนี้')

@section('page-title', 'จัดการใบแจ้งหนี้')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">รายการใบแจ้งหนี้</h4>
    <div class="d-flex gap-2">
        <a href="{{ route('invoices.bulk-create') }}" class="btn btn-outline-primary">
            <i class="bi bi-plus-circle me-1"></i>สร้างหลายใบ
        </a>
        <a href="{{ route('invoices.export') }}" class="btn btn-outline-success">
            <i class="bi bi-download me-1"></i>{{ __("ui.export") }}
        </a>
        <a href="{{ route('invoices.create') }}" class="btn btn-primary-custom">
            <i class="bi bi-plus-lg me-1"></i>เพิ่มใบแจ้งหนี้
        </a>
    </div>
</div>

<!-- Search and Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('invoices.index') }}" class="row g-3">
            <div class="col-md-3">
                <input type="text" name="search" class="form-control" placeholder="ค้นหาเลขที่ใบแจ้งหนี้..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">ทุกสถานะ</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>รอชำระ</option>
                    <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>ชำระแล้ว</option>
                    <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>เกินกำหนด</option>
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

<!-- Invoices Table -->
<div class="table-card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>เลขที่ใบแจ้งหนี้</th>
                    <th>การจอง</th>
                    <th>ยอดรวม</th>
                    <th>วันที่ออก</th>
                    <th>วันครบกำหนด</th>
                    <th>สถานะ</th>
                    <th>การกระทำ</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($invoices as $invoice)
                    <tr>
                        <td><strong>{{ $invoice->invoice_number }}</strong></td>
                        <td>#{{ $invoice->booking_id }}</td>
                        <td>฿{{ number_format($invoice->total, 2) }}</td>
                        <td>{{ optional($invoice->issue_date)->format('d/m/Y') }}</td>
                        <td>{{ optional($invoice->due_date)->format('d/m/Y') }}</td>
                        <td>
                            @php
                                $statusClasses = [
                                    'pending' => 'bg-warning',
                                    'paid' => 'bg-success',
                                    'overdue' => 'bg-danger',
                                    'cancelled' => 'bg-secondary',
                                ];
                            @endphp
                            <span class="badge {{ $statusClasses[$invoice->status] ?? '' }}">
                                {{ enum_bi('invoice_status', $invoice->status, ucfirst($invoice->status)) }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex gap-2">
                                <a href="{{ route('invoices.show', $invoice) }}" class="btn btn-sm btn-outline-info">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('invoices.edit', $invoice) }}" class="btn btn-sm btn-outline-warning">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @if($invoice->status == 'pending')
                                    <form action="{{ route('invoices.markAsPaid', $invoice) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-success" onclick="return confirm('ยืนยันการชำระเงิน?')">
                                            <i class="bi bi-check-lg"></i>
                                        </button>
                                    </form>
                                @endif
                                <a href="{{ route('invoices.pdf', $invoice) }}" class="btn btn-sm btn-outline-danger" target="_blank">
                                    <i class="bi bi-file-pdf"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-4">
                            <i class="bi bi-inbox fs-1 text-muted"></i>
                            <p class="text-muted mt-2">ไม่มีข้อมูลใบแจ้งหนี้</p>
                            <a href="{{ route('invoices.create') }}" class="btn btn-primary-custom mt-2">เพิ่มใบแจ้งหนี้</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Pagination -->
@if ($invoices->hasPages())
    <div class="d-flex justify-content-center mt-4">
        {{ $invoices->links('pagination::bootstrap-5') }}
    </div>
@endif
@endsection






