@extends('layouts.app')

@section('title', 'สร้างใบแจ้งหนี้หลายใบ')
@section('page-title', 'สร้างใบแจ้งหนี้หลายใบ')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">สร้างใบแจ้งหนี้หลายใบ</h4>
    <a href="{{ route('invoices.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>กลับ
    </a>
</div>

@if ($errors->any())
    <div class="alert alert-danger">
        <strong>กรุณาตรวจสอบข้อมูล:</strong>
        <ul class="mb-0 mt-2">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('invoices.bulk-store') }}" id="bulkInvoiceForm">
            @csrf

            @php
                $oldInvoices = old('invoices', [[
                    'booking_id' => '',
                    'invoice_number' => '',
                    'amount' => '',
                    'tax' => '0',
                    'total' => '',
                    'issue_date' => now()->format('Y-m-d'),
                    'due_date' => now()->addDays(7)->format('Y-m-d'),
                    'status' => 'draft',
                    'notes' => '',
                ]]);
            @endphp

            <div class="table-responsive">
                <table class="table align-middle" id="invoicesTable">
                    <thead>
                        <tr>
                            <th>การจอง</th>
                            <th>เลขใบแจ้งหนี้</th>
                            <th>ยอดก่อนภาษี</th>
                            <th>ภาษี</th>
                            <th>ยอดรวม</th>
                            <th>วันที่ออก</th>
                            <th>วันครบกำหนด</th>
                            <th>สถานะ</th>
                            <th>หมายเหตุ</th>
                            <th style="width: 80px;">ลบ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($oldInvoices as $i => $invoice)
                            <tr>
                                <td>
                                    <select class="form-select" name="invoices[{{ $i }}][booking_id]" required>
                                        <option value="">-- เลือกการจอง --</option>
                                        @foreach ($bookings as $booking)
                                            <option value="{{ $booking->id }}" @selected(($invoice['booking_id'] ?? '') == $booking->id)>
                                                #{{ $booking->id }}{{ $booking->room?->room_number ? ' - ห้อง ' . $booking->room->room_number : '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td><input type="text" class="form-control" name="invoices[{{ $i }}][invoice_number]" value="{{ $invoice['invoice_number'] ?? '' }}" required></td>
                                <td><input type="number" min="0" step="0.01" class="form-control" name="invoices[{{ $i }}][amount]" value="{{ $invoice['amount'] ?? '' }}" required></td>
                                <td><input type="number" min="0" step="0.01" class="form-control" name="invoices[{{ $i }}][tax]" value="{{ $invoice['tax'] ?? '0' }}" required></td>
                                <td><input type="number" min="0" step="0.01" class="form-control" name="invoices[{{ $i }}][total]" value="{{ $invoice['total'] ?? '' }}" required></td>
                                <td><input type="date" class="form-control" name="invoices[{{ $i }}][issue_date]" value="{{ $invoice['issue_date'] ?? '' }}" required></td>
                                <td><input type="date" class="form-control" name="invoices[{{ $i }}][due_date]" value="{{ $invoice['due_date'] ?? '' }}" required></td>
                                <td>
                                    <select class="form-select" name="invoices[{{ $i }}][status]" required>
                                        <option value="draft" @selected(($invoice['status'] ?? 'draft') === 'draft')>Draft (ร่าง)</option>
                                        <option value="sent" @selected(($invoice['status'] ?? '') === 'sent')>Sent (ส่งแล้ว)</option>
                                        <option value="paid" @selected(($invoice['status'] ?? '') === 'paid')>Paid (ชำระแล้ว)</option>
                                        <option value="overdue" @selected(($invoice['status'] ?? '') === 'overdue')>Overdue (เกินกำหนด)</option>
                                        <option value="cancelled" @selected(($invoice['status'] ?? '') === 'cancelled')>Cancelled (ยกเลิก)</option>
                                    </select>
                                </td>
                                <td><input type="text" class="form-control" name="invoices[{{ $i }}][notes]" value="{{ $invoice['notes'] ?? '' }}"></td>
                                <td>
                                    <button type="button" class="btn btn-outline-danger btn-sm remove-row">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="d-flex gap-2 mt-3">
                <button type="button" class="btn btn-outline-primary" id="addRowBtn">
                    <i class="bi bi-plus-circle me-1"></i>เพิ่มแถว
                </button>
                <button type="submit" class="btn btn-primary-custom">
                    <i class="bi bi-save me-1"></i>บันทึกทั้งหมด
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    (function () {
        const bookings = @json($bookings->map(fn($b) => ['id' => $b->id, 'room_number' => $b->room?->room_number])->values());
        const tableBody = document.querySelector('#invoicesTable tbody');
        const addRowBtn = document.getElementById('addRowBtn');

        function nextIndex() {
            return tableBody.querySelectorAll('tr').length;
        }

        function bookingOptions() {
            let html = '<option value="">-- เลือกการจอง --</option>';
            bookings.forEach((booking) => {
                const roomLabel = booking.room_number ? ` - ห้อง ${booking.room_number}` : '';
                html += `<option value="${booking.id}">#${booking.id}${roomLabel}</option>`;
            });
            return html;
        }

        function rowTemplate(i) {
            const today = new Date().toISOString().slice(0, 10);

            return `
                <tr>
                    <td>
                        <select class="form-select" name="invoices[${i}][booking_id]" required>
                            ${bookingOptions()}
                        </select>
                    </td>
                    <td><input type="text" class="form-control" name="invoices[${i}][invoice_number]" required></td>
                    <td><input type="number" min="0" step="0.01" class="form-control" name="invoices[${i}][amount]" required></td>
                    <td><input type="number" min="0" step="0.01" class="form-control" name="invoices[${i}][tax]" value="0" required></td>
                    <td><input type="number" min="0" step="0.01" class="form-control" name="invoices[${i}][total]" required></td>
                    <td><input type="date" class="form-control" name="invoices[${i}][issue_date]" value="${today}" required></td>
                    <td><input type="date" class="form-control" name="invoices[${i}][due_date]" value="${today}" required></td>
                    <td>
                        <select class="form-select" name="invoices[${i}][status]" required>
                            <option value="draft">Draft (ร่าง)</option>
                            <option value="sent">Sent (ส่งแล้ว)</option>
                            <option value="paid">Paid (ชำระแล้ว)</option>
                            <option value="overdue">Overdue (เกินกำหนด)</option>
                            <option value="cancelled">Cancelled (ยกเลิก)</option>
                        </select>
                    </td>
                    <td><input type="text" class="form-control" name="invoices[${i}][notes]"></td>
                    <td>
                        <button type="button" class="btn btn-outline-danger btn-sm remove-row">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
        }

        addRowBtn.addEventListener('click', function () {
            tableBody.insertAdjacentHTML('beforeend', rowTemplate(nextIndex()));
        });

        tableBody.addEventListener('click', function (e) {
            const button = e.target.closest('.remove-row');
            if (!button) return;
            const rows = tableBody.querySelectorAll('tr');
            if (rows.length <= 1) return;
            button.closest('tr').remove();
        });
    })();
</script>
@endsection
