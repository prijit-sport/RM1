<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เพิ่มใบแจ้งหนี้ใหม่</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI'; background: #f5f5f5; padding: 20px; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 40px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { margin-bottom: 30px; color: #333; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; font-weight: 600; color: #555; }
        input, textarea, select { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Segoe UI'; font-size: 14px; }
        input:focus, textarea:focus, select:focus { outline: none; border-color: #667eea; box-shadow: 0 0 5px rgba(102, 126, 234, 0.3); }
        textarea { resize: vertical; min-height: 100px; }
        .btn-group { display: flex; gap: 10px; margin-top: 30px; }
        .btn { padding: 12px 20px; border: none; border-radius: 5px; cursor: pointer; font-weight: bold; text-decoration: none; display: inline-block; }
        .btn-submit { background: #667eea; color: white; }
        .btn-submit:hover { background: #764ba2; }
        .btn-cancel { background: #6c757d; color: white; }
        .btn-cancel:hover { background: #5a6268; }
        .error-message { color: #dc3545; font-size: 12px; margin-top: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🧾 เพิ่มใบแจ้งหนี้ใหม่</h1>
        
        @if ($errors->any())
            <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('invoices.store') }}">
            @csrf

            <div class="form-group">
                <label for="booking_id">การจอง *</label>
                <select id="booking_id" name="booking_id" required>
                    <option value="">-- เลือกการจอง --</option>
                    @foreach($bookings as $booking)
                        <option value="{{ $booking->id }}" {{ old('booking_id') == $booking->id ? 'selected' : '' }}>
                            การจอง #{{ $booking->id }} - {{ $booking->room->room_number ?? '-' }}
                        </option>
                    @endforeach
                </select>
                @error('booking_id')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="invoice_number">เลขที่ใบแจ้งหนี้ *</label>
                <input type="text" id="invoice_number" name="invoice_number" value="{{ old('invoice_number') }}" required>
                @error('invoice_number')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="amount">จำนวนเงิน *</label>
                <input type="number" id="amount" name="amount" value="{{ old('amount') }}" step="0.01" required>
                @error('amount')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="tax">ภาษี *</label>
                <input type="number" id="tax" name="tax" value="{{ old('tax') }}" step="0.01" required>
                @error('tax')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="total">ยอดรวม *</label>
                <input type="number" id="total" name="total" value="{{ old('total') }}" step="0.01" required>
                @error('total')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="issue_date">วันที่ออกใบแจ้งหนี้ *</label>
                <input type="date" id="issue_date" name="issue_date" value="{{ old('issue_date') }}" required>
                @error('issue_date')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="due_date">วันที่ครบกำหนด *</label>
                <input type="date" id="due_date" name="due_date" value="{{ old('due_date') }}" required>
                @error('due_date')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="status">สถานะ *</label>
                <select id="status" name="status" required>
                    <option value="">-- เลือกสถานะ --</option>
                    <option value="draft" {{ old('status') === 'draft' ? 'selected' : '' }}>ร่าง</option>
                    <option value="sent" {{ old('status') === 'sent' ? 'selected' : '' }}>ส่งแล้ว</option>
                    <option value="paid" {{ old('status') === 'paid' ? 'selected' : '' }}>ชำระแล้ว</option>
                    <option value="overdue" {{ old('status') === 'overdue' ? 'selected' : '' }}>เกินกำหนด</option>
                    <option value="cancelled" {{ old('status') === 'cancelled' ? 'selected' : '' }}>ยกเลิก</option>
                </select>
                @error('status')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="notes">หมายเหตุ</label>
                <textarea id="notes" name="notes">{{ old('notes') }}</textarea>
            </div>

            <div class="btn-group">
                <button type="submit" class="btn btn-submit">💾 บันทึก</button>
                <a href="{{ route('invoices.index') }}" class="btn btn-cancel">❌ ยกเลิก</a>
            </div>
        </form>
    </div>
</body>
</html>
