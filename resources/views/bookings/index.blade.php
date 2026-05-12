@extends('layouts.app')
 
@section('title', 'จัดการการจอง')
 
@section('page-title', 'จัดการการจอง')
 
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">รายการการจอง</h4>
    <div class="d-flex gap-2">
        <a href="{{ route('bookings.export') }}" class="btn btn-success">
            <i class="bi bi-download me-1"></i>{{ __("ui.export") }}
        </a>
        <a href="{{ route('bookings.create') }}" class="btn btn-primary-custom">
            <i class="bi bi-plus-lg me-1"></i>เพิ่มการจองใหม่
        </a>
    </div>
</div>
 
<!-- Search and Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('bookings.index') }}" class="row g-3">
            <div class="col-md-3">
                <input type="text" name="search" class="form-control" placeholder="ค้นหาชื่อแขก..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">ทุกสถานะ</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>รอการยืนยัน</option>
                    <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>ยืนยันแล้ว</option>
                    <option value="checked_in" {{ request('status') == 'checked_in' ? 'selected' : '' }}>เช็คอินแล้ว</option>
                    <option value="checked_out" {{ request('status') == 'checked_out' ? 'selected' : '' }}>เช็คเอาท์แล้ว</option>
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
 
<!-- Bookings Table -->
<div class="table-card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>ห้อง</th>
                    <th>แขก</th>
                    <th>ราคา</th>
                    <th>สถานะ</th>
                    <th>การกระทำ</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($bookings as $booking)
                    <tr>
                        <td>
                            <strong>
                                @if($booking->room)
                                    #{{ $booking->room->room_number }}
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </strong>
                        </td>
                        <td>
                            @if($booking->guest)
                                {{ $booking->guest->first_name }} {{ $booking->guest->last_name }}
                            @else
                                <span class="text-muted fst-italic">ไม่ระบุผู้เช่า</span>
                            @endif
                        </td>
                        <td>฿{{ number_format($booking->total_price ?? 0, 2) }}</td>
                        <td>
                            @php
                                $statusClasses = [
                                    'pending' => 'bg-warning',
                                    'confirmed' => 'bg-info',
                                    'checked_in' => 'bg-success',
                                    'checked_out' => 'bg-secondary',
                                    'cancelled' => 'bg-danger',
                                ];
                            @endphp
                            <span class="badge {{ $statusClasses[$booking->status] ?? 'bg-light text-dark' }}">
                                {{ enum_th('booking_status', $booking->status) }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex gap-2">
                                <a href="{{ route('bookings.show', $booking) }}" class="btn btn-sm btn-outline-info">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('bookings.edit', $booking) }}" class="btn btn-sm btn-outline-warning">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @if($booking->status == 'pending')
                                    <form action="{{ route('bookings.confirm', $booking) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-success" onclick="return confirm('ยืนยันการจองนี้?')">
                                            <i class="bi bi-check-lg"></i>
                                        </button>
                                    </form>
                                @endif
                                @if($booking->status != 'cancelled' && $booking->status != 'checked_out')
                                    <form action="{{ route('bookings.cancel', $booking) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('ยกเลิกการจองนี้?')">
                                            <i class="bi bi-x-lg"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-4">
                            <i class="bi bi-inbox fs-1 text-muted"></i>
                            <p class="text-muted mt-2">ไม่มีข้อมูลการจอง</p>
                            <a href="{{ route('bookings.create') }}" class="btn btn-primary-custom mt-2">เพิ่มการจองใหม่</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
 
<!-- Pagination -->
@if ($bookings->hasPages())
    <div class="d-flex justify-content-center mt-4">
        {{ $bookings->links('pagination::bootstrap-5') }}
    </div>
@endif
@endsection
 