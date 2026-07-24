@extends('layouts.app')
 
@section('title', 'สัญญาใกล้หมดอายุ')
 
@section('content')
<div class="container-fluid py-4">
 
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0 fw-bold">
                <i class="bi bi-calendar-x text-warning me-2"></i>สัญญาใกล้หมดอายุ
            </h4>
            <small class="text-muted">สัญญาที่จะหมดอายุภายใน 30 วันข้างหน้า</small>
        </div>
        <a href="{{ route('contracts.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>กลับไปรายการทั้งหมด
        </a>
    </div>
 
    {{-- Alert summary --}}
    @if($contracts->total() > 0)
        <div class="alert alert-warning border-0 shadow-sm">
            <div class="d-flex align-items-center">
                <i class="bi bi-exclamation-triangle-fill fs-3 me-3"></i>
                <div>
                    <strong>พบสัญญา {{ $contracts->total() }} ฉบับ</strong> ที่จะหมดอายุภายใน 30 วัน
                    <br>
                    <small>กรุณาติดต่อผู้เช่าเพื่อต่อสัญญาหรือดำเนินการตามขั้นตอน</small>
                </div>
            </div>
        </div>
    @endif
 
    {{-- ตาราง --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">เลขที่สัญญา</th>
                            <th>ห้อง</th>
                            <th>ผู้เช่า</th>
                            <th>วันเริ่ม</th>
                            <th>วันสิ้นสุด</th>
                            <th class="text-center">เหลือ</th>
                            <th class="text-end">ค่าเช่า/เดือน</th>
                            <th class="text-center pe-4">การกระทำ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($contracts as $contract)
                            @php
                                $daysLeft = $contract->end_date
                                    ? now()->startOfDay()->diffInDays($contract->end_date, false)
                                    : null;
 
                                $urgency = match(true) {
                                    $daysLeft === null || $daysLeft < 0 => ['bg' => '#fee2e2', 'color' => '#991b1b', 'text' => 'หมดอายุแล้ว'],
                                    $daysLeft <= 7   => ['bg' => '#fee2e2', 'color' => '#991b1b', 'text' => $daysLeft . ' วัน'],
                                    $daysLeft <= 14  => ['bg' => '#fef3c7', 'color' => '#92400e', 'text' => $daysLeft . ' วัน'],
                                    default          => ['bg' => '#dbeafe', 'color' => '#1e40af', 'text' => $daysLeft . ' วัน'],
                                };
                            @endphp
                            <tr>
                                <td class="ps-4 fw-bold">
                                    {{ $contract->contract_number ?? '-' }}
                                </td>
                                <td>
                                    @if($contract->room)
                                        #{{ $contract->room->room_number }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($contract->guest)
                                        {{ $contract->guest->first_name }} {{ $contract->guest->last_name }}
                                    @else
                                        <span class="text-muted fst-italic">ไม่ระบุ</span>
                                    @endif
                                </td>
                                <td class="small text-muted">
                                    {{ optional($contract->start_date)->format('d/m/Y') ?? '-' }}
                                </td>
                                <td class="small fw-semibold">
                                    {{ optional($contract->end_date)->format('d/m/Y') ?? '-' }}
                                </td>
                                <td class="text-center">
                                    <span class="badge rounded-pill px-3 py-2"
                                          style="background:{{ $urgency['bg'] }}; color:{{ $urgency['color'] }}; font-weight:500;">
                                        {{ $urgency['text'] }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    @if($contract->monthly_rent)
                                        <span class="fw-semibold">{{ number_format($contract->monthly_rent, 0) }}</span>
                                        <span class="text-muted small">฿</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-center pe-4">
                                    <div class="d-inline-flex gap-1">
                                        <a href="{{ route('contracts.show', $contract) }}"
                                           class="btn btn-outline-info btn-sm py-0 px-2" title="ดูรายละเอียด">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('contracts.edit', $contract) }}"
                                           class="btn btn-outline-warning btn-sm py-0 px-2" title="แก้ไข/ต่อสัญญา">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <i class="bi bi-check2-circle fs-1 d-block mb-2 opacity-25 text-success"></i>
                                    <p class="mb-0 fw-semibold">ไม่มีสัญญาที่ใกล้หมดอายุ</p>
                                    <small>สัญญาทั้งหมดยังอยู่ในระยะเวลาที่ปลอดภัย</small>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
 
            {{-- Pagination --}}
            @if($contracts->hasPages())
                <div class="d-flex justify-content-center py-3 border-top">
                    {{ $contracts->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>
 
</div>
 
<style>
    .table > :not(caption) > * > * { padding: 0.85rem 0.75rem; }
</style>
@endsection
 