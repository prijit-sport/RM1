@extends('layouts.app')

@section('page-title', 'เพิ่มใบแจ้งหนี้ใหม่')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-receipt me-2"></i> เพิ่มใบแจ้งหนี้</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('invoices.store') }}" method="POST">
                        @csrf

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="booking_id" class="form-label required">การจอง</label>
                                <select id="booking_id" name="booking_id" class="form-select @error('booking_id') is-invalid @enderror" required>
                                    <option value="">-- เลือกการจอง --</option>
                                    @foreach($bookings as $booking)
                                    <option value="{{ $booking->id }}" data-amount="{{ number_format($booking->total_price ?? 0, 2, '.', '') }}" {{ old('booking_id') == $booking->id ? 'selected' : '' }}>
                                            {{ $booking->room->room_number ?? '-' }} · {{ trim(($booking->guest->first_name ?? '') . ' ' . ($booking->guest->last_name ?? '')) ?: 'ผู้เช่าไม่ระบุ' }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('booking_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="invoice_number" class="form-label required">เลขที่ใบแจ้งหนี้</label>
                                <input id="invoice_number" name="invoice_number" class="form-control @error('invoice_number') is-invalid @enderror" value="{{ old('invoice_number', $invoiceNumber ?? '') }}" required>
                                @error('invoice_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="amount" class="form-label required">จำนวนเงิน (บาท)</label>
                                <input id="amount" name="amount" type="number" min="0" step="0.01" class="form-control @error('amount') is-invalid @enderror" value="{{ old('amount') }}" required>
                                @error('amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="tax" class="form-label required">ภาษี (บาท)</label>
                                <input id="tax" name="tax" type="number" min="0" step="0.01" class="form-control @error('tax') is-invalid @enderror" value="{{ old('tax') }}" required>
                                @error('tax')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="total" class="form-label required">ยอดรวม (บาท)</label>
                                <input id="total" name="total" type="number" min="0" step="0.01" class="form-control @error('total') is-invalid @enderror" value="{{ old('total') }}" required readonly>
                                @error('total')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="issue_date" class="form-label required">วันที่ออกใบแจ้งหนี้</label>
                                <input id="issue_date" name="issue_date" type="date" class="form-control @error('issue_date') is-invalid @enderror" value="{{ old('issue_date') }}" required>
                                @error('issue_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="due_date" class="form-label required">วันที่ครบกำหนด</label>
                                <input id="due_date" name="due_date" type="date" class="form-control @error('due_date') is-invalid @enderror" value="{{ old('due_date') }}" required>
                                @error('due_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="status" class="form-label required">สถานะ</label>
                                <select id="status" name="status" class="form-select @error('status') is-invalid @enderror" required>
                                    <option value="draft" {{ old('status') === 'draft' ? 'selected' : '' }}>ร่าง</option>
                                    <option value="sent" {{ old('status') === 'sent' ? 'selected' : '' }}>ส่งแล้ว</option>
                                    <option value="paid" {{ old('status') === 'paid' ? 'selected' : '' }}>ชำระแล้ว</option>
                                    <option value="overdue" {{ old('status') === 'overdue' ? 'selected' : '' }}>เกินกำหนด</option>
                                    <option value="cancelled" {{ old('status') === 'cancelled' ? 'selected' : '' }}>ยกเลิก</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="notes" class="form-label">หมายเหตุ</label>
                                <textarea id="notes" name="notes" class="form-control" rows="3">{{ old('notes') }}</textarea>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('invoices.index') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> กลับ
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> บันทึกใบแจ้งหนี้
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@include('invoices._total-calculator')
@endsection
