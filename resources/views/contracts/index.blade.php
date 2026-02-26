@extends('layouts.app')

@section('title', 'จัดการสัญญา')

@section('page-title', 'จัดการสัญญา')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">รายการสัญญา</h4>
    <div class="d-flex gap-2">
        <a href="{{ route('contracts.expiring') }}" class="btn btn-outline-warning">
            <i class="bi bi-exclamation-triangle me-1"></i>สัญญาหมดอายุเร็ว
        </a>
        <a href="{{ route('contracts.export') }}" class="btn btn-outline-success">
            <i class="bi bi-download me-1"></i>Export
        </a>
        <a href="{{ route('contracts.create') }}" class="btn btn-primary-custom">
            <i class="bi bi-plus-lg me-1"></i>เพิ่มสัญญา
        </a>
    </div>
</div>

<!-- Search and Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('contracts.index') }}" class="row g-3">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="ค้นหาเลขที่สัญญา..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">ทุกสถานะ</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>ใช้งาน</option>
                    <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>หมดอายุ</option>
                    <option value="terminated" {{ request('status') == 'terminated' ? 'selected' : '' }}>ยกเลิก</option>
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

<!-- Contracts Table -->
<div class="table-card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>เลขที่สัญญา</th>
                    <th>ผู้รับเหมา</th>
                    <th>วันเริ่มต้น</th>
                    <th>วันสิ้นสุด</th>
                    <th>จำนวนเงิน</th>
                    <th>สถานะ</th>
                    <th>การกระทำ</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($contracts as $contract)
                    <tr>
                        <td><strong>{{ $contract->contract_number }}</strong></td>
                        <td>{{ $contract->contractor_name }}</td>
                        <td>{{ optional($contract->start_date)->format('d/m/Y') }}</td>
                        <td>{{ optional($contract->end_date)->format('d/m/Y') }}</td>
                        <td>฿{{ number_format($contract->amount, 2) }}</td>
                        <td>
                            @php
                                $statusClasses = [
                                    'active' => 'bg-success',
                                    'expired' => 'bg-danger',
                                    'terminated' => 'bg-secondary',
                                ];
                            @endphp
                            <span class="badge {{ $statusClasses[$contract->status] ?? '' }}">
                                {{ ucfirst($contract->status) }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex gap-2">
                                <a href="{{ route('contracts.show', $contract) }}" class="btn btn-sm btn-outline-info">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('contracts.edit', $contract) }}" class="btn btn-sm btn-outline-warning">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <a href="{{ route('contracts.pdf', $contract) }}" class="btn btn-sm btn-outline-danger" target="_blank">
                                    <i class="bi bi-file-pdf"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-4">
                            <i class="bi bi-inbox fs-1 text-muted"></i>
                            <p class="text-muted mt-2">ไม่มีข้อมูลสัญญา</p>
                            <a href="{{ route('contracts.create') }}" class="btn btn-primary-custom mt-2">เพิ่มสัญญา</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Pagination -->
@if ($contracts->hasPages())
    <div class="d-flex justify-content-center mt-4">
        {{ $contracts->links('pagination::bootstrap-5') }}
    </div>
@endif
@endsection
