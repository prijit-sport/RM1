@extends('layouts.app')

@section('page-title', 'เพิ่มใบแจ้งหนี้ใหม่')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Header Section -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="fw-bold text-dark mb-0">สร้างใบแจ้งหนี้ (Create Invoice)</h4>
                <a href="{{ route('invoices.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left"></i> กลับสู่หน้ารายการ
                </a>
            </div>

            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-4 p-md-5">
                    <form action="{{ route('invoices.store') }}" method="POST" id="invoiceForm">
                        @csrf

                        <!-- ส่วนที่ 1: ข้อมูลอ้างอิง -->
                        <h6 class="text-uppercase text-muted fw-bold mb-3 border-bottom pb-2">
                            <i class="bi bi-info-circle me-1"></i> ข้อมูลอ้างอิง (Reference Information)
                        </h6>
                        <div class="row g-4 mb-4">
                            <div class="col-md-6">
                                <label for="booking_id" class="form-label fw-semibold required">รายการจองอ้างอิง</label>
                                <select id="booking_id" name="booking_id" class="form-select @error('booking_id') is-invalid @enderror" required>
                                    <option value="">-- ระบุรายการจอง --</option>
                                    @foreach($bookings as $booking)
                                    <option value="{{ $booking->id }}" data-amount="{{ number_format($booking->total_price ?? 0, 2, '.', '') }}" {{ old('booking_id') == $booking->id ? 'selected' : '' }}>
                                        ห้อง {{ $booking->room->room_number ?? '-' }} · ผู้เช่า: {{ trim(($booking->guest->first_name ?? '') . ' ' . ($booking->guest->last_name ?? '')) ?: 'ไม่ระบุชื่อ' }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('booking_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="invoice_number" class="form-label fw-semibold required">เลขที่ใบแจ้งหนี้</label>
                                <input type="text" id="invoice_number" name="invoice_number" class="form-control @error('invoice_number') is-invalid @enderror" value="{{ old('invoice_number', $invoiceNumber ?? '') }}" placeholder="INV-YYYYMM-XXXX" required>
                                @error('invoice_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- ส่วนที่ 2: รายละเอียดจำนวนเงิน -->
                        <h6 class="text-uppercase text-muted fw-bold mb-3 border-bottom pb-2">
                            <i class="bi bi-currency-dollar me-1"></i> รายละเอียดทางการเงิน (Financial Details)
                        </h6>
                        <div class="row g-4 mb-4 bg-light p-3 rounded border border-light-subtle mx-0">
                            <div class="col-md-4">
                                <label for="amount" class="form-label fw-semibold required">จำนวนเงินก่อนภาษี (THB)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white">฿</span>
                                    <input id="amount" name="amount" type="number" min="0" step="0.01" class="form-control text-end @error('amount') is-invalid @enderror" value="{{ old('amount') }}" placeholder="0.00" required>
                                    @error('amount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="tax" class="form-label fw-semibold required">ภาษีมูลค่าเพิ่ม (THB)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white">฿</span>
                                    <input id="tax" name="tax" type="number" min="0" step="0.01" class="form-control text-end @error('tax') is-invalid @enderror" value="{{ old('tax') }}" placeholder="0.00" required>
                                    @error('tax')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="total" class="form-label fw-semibold required text-primary">ยอดชำระสุทธิ (THB)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-primary text-white border-primary">฿</span>
                                    <input id="total" name="total" type="number" min="0" step="0.01" class="form-control text-end fw-bold bg-white @error('total') is-invalid @enderror" value="{{ old('total') }}" placeholder="0.00" required readonly>
                                    @error('total')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- ส่วนที่ 3: กำหนดเวลาและสถานะ -->
                        <h6 class="text-uppercase text-muted fw-bold mb-3 border-bottom pb-2">
                            <i class="bi bi-calendar-check me-1"></i> กำหนดเวลาและเงื่อนไข (Schedule & Terms)
                        </h6>
                        <div class="row g-4 mb-4">
                            <div class="col-md-4">
                                <label for="issue_date" class="form-label fw-semibold required">วันที่ออกเอกสาร</label>
                                <input id="issue_date" name="issue_date" type="date" class="form-control @error('issue_date') is-invalid @enderror" value="{{ old('issue_date') }}" required>
                                @error('issue_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="due_date" class="form-label fw-semibold required">วันครบกำหนดชำระ</label>
                                <input id="due_date" name="due_date" type="date" class="form-control @error('due_date') is-invalid @enderror" value="{{ old('due_date') }}" required>
                                @error('due_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="status" class="form-label fw-semibold required">สถานะเอกสาร</label>
                                <select id="status" name="status" class="form-select @error('status') is-invalid @enderror" required>
                                    <option value="draft" {{ old('status') === 'draft' ? 'selected' : '' }}>ร่าง (Draft)</option>
                                    <option value="sent" {{ old('status') === 'sent' ? 'selected' : '' }}>ส่งมอบแล้ว (Sent)</option>
                                    <option value="paid" {{ old('status') === 'paid' ? 'selected' : '' }}>ชำระเงินเรียบร้อย (Paid)</option>
                                    <option value="overdue" {{ old('status') === 'overdue' ? 'selected' : '' }}>เกินกำหนด (Overdue)</option>
                                    <option value="cancelled" {{ old('status') === 'cancelled' ? 'selected' : '' }}>ยกเลิก (Cancelled)</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- ส่วนที่ 4: หมายเหตุเพิ่มเติม -->
                        <div class="row mb-5">
                            <div class="col-12">
                                <label for="notes" class="form-label fw-semibold">หมายเหตุเพิ่มเติม (Remarks)</label>
                                <textarea id="notes" name="notes" class="form-control bg-light" rows="3" placeholder="ระบุเงื่อนไขการชำระเงิน หรือรายละเอียดเพิ่มเติมสำหรับเอกสารฉบับนี้...">{{ old('notes') }}</textarea>
                            </div>
                        </div>

                        <!-- Button Actions -->
                        <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                            <button type="button" class="btn btn-light border px-4" onclick="window.history.back();">
                                ยกเลิก
                            </button>
                            <button type="submit" class="btn btn-primary px-5">
                                <i class="bi bi-save me-1"></i> บันทึกข้อมูล
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