@extends('layouts.app')

@section('title', 'สร้างใบแจ้งหนี้หลายใบ')
@section('page-title', 'สร้างใบแจ้งหนี้หลายใบ')

@section('content')
    <style>
        .bulk-header {
            border-radius: 16px;
            padding: 24px 28px;
            color: white;
            margin-bottom: 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 16px;
        }

        .bulk-header.rent {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
        }

        .bulk-header.utility {
            background: linear-gradient(135deg, #0369a1 0%, #0ea5e9 100%);
        }

        .bulk-header h4 {
            margin: 0;
            font-weight: 700;
            font-size: 1.3rem;
        }

        .bulk-header p {
            margin: 4px 0 0;
            opacity: 0.85;
            font-size: 0.9rem;
        }

        .type-tabs {
            display: flex;
            gap: 0;
            background: white;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            overflow: hidden;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
        }

        .type-tab {
            flex: 1;
            padding: 14px 20px;
            text-align: center;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.95rem;
            color: #64748b;
            border-right: 1px solid #e2e8f0;
            transition: all 0.2s;
        }

        .type-tab:last-child {
            border-right: none;
        }

        .type-tab:hover {
            background: #f8faff;
            color: #4f46e5;
        }

        .type-tab.active-rent {
            background: #ede9fe;
            color: #4f46e5;
        }

        .type-tab.active-utility {
            background: #e0f2fe;
            color: #0369a1;
        }

        .period-card {
            background: white;
            border-radius: 14px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
            padding: 20px 24px;
            margin-bottom: 20px;
        }

        .period-card h6 {
            font-weight: 700;
            color: #4f46e5;
            margin-bottom: 16px;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .summary-bar {
            background: #f8faff;
            border: 1px solid #e0e7ff;
            border-radius: 12px;
            padding: 16px 20px;
            margin-bottom: 20px;
            display: flex;
            gap: 32px;
            flex-wrap: wrap;
            align-items: center;
        }

        .s-item {
            text-align: center;
        }

        .s-label {
            font-size: 0.75rem;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .s-value {
            font-size: 1.4rem;
            font-weight: 700;
            color: #4f46e5;
        }

        .rooms-table-card {
            background: white;
            border-radius: 14px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
            overflow: hidden;
            margin-bottom: 20px;
        }

        .rooms-table-card table {
            width: 100%;
            border-collapse: collapse;
        }

        .rooms-table-card thead th {
            background: #f8faff;
            padding: 12px 16px;
            font-size: 0.78rem;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #e2e8f0;
        }

        .rooms-table-card tbody td {
            padding: 10px 16px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 0.88rem;
            vertical-align: middle;
        }

        .rooms-table-card tbody tr:last-child td {
            border-bottom: none;
        }

        .rooms-table-card tbody tr:hover {
            background: #fafbff;
        }

        .rooms-table-card tbody tr.excluded {
            opacity: 0.4;
            background: #fef2f2;
        }

        .rooms-table-card tbody tr.no-reading {
            background: #fffbeb;
        }

        .room-chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 10px;
            background: #ede9fe;
            color: #4f46e5;
            border-radius: 6px;
            font-weight: 600;
            font-size: 0.85rem;
        }

        .utility-chip {
            background: #e0f2fe;
            color: #0369a1;
        }

        .tenant-name {
            font-weight: 500;
            color: #1e293b;
        }

        .tenant-sub {
            font-size: 0.78rem;
            color: #94a3b8;
        }

        .amount-cell {
            font-weight: 700;
            color: #059669;
            font-family: monospace;
        }

        .include-toggle {
            width: 18px;
            height: 18px;
            cursor: pointer;
            accent-color: #4f46e5;
        }

        .no-reading-badge {
            display: inline-block;
            padding: 2px 8px;
            background: #fef3c7;
            color: #92400e;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .action-footer {
            background: white;
            border-radius: 14px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
            padding: 20px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
        }

        .btn-generate {
            color: white;
            border: none;
            padding: 12px 28px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s;
        }

        .btn-generate.rent {
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
        }

        .btn-generate.utility {
            background: linear-gradient(135deg, #0369a1, #0ea5e9);
        }

        .btn-generate:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 15px rgba(79, 70, 229, 0.4);
        }

        .btn-generate:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }
    </style>

    {{-- Header --}}
    <div class="bulk-header {{ $type }}">
        <div>
            @if ($type === 'utility')
                <h4>⚡ สร้างใบแจ้งหนี้ค่าน้ำ/ค่าไฟหลายใบ</h4>
                <p>ดึงข้อมูลมิเตอร์อัตโนมัติ คำนวณยอดและสร้างพร้อมกันทุกห้อง</p>
            @else
                <h4>🏠 สร้างใบแจ้งหนี้ค่าเช่าหลายใบ</h4>
                <p>สร้างใบแจ้งหนี้ค่าเช่าพร้อมกันทุกห้องในคลิกเดียว</p>
            @endif
        </div>
        <a href="{{ route('invoices.index', ['invoice_type' => $type]) }}" class="btn btn-light btn-sm">
            <i class="bi bi-arrow-left me-1"></i> กลับ
        </a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger rounded-3 mb-4">
            <strong>กรุณาตรวจสอบข้อมูล:</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- ✅ Type tabs --}}
    <div class="type-tabs">
        <a href="{{ route('invoices.bulk-create', ['type' => 'rent', 'month' => $month, 'year' => $year]) }}"
            class="type-tab {{ $type === 'rent' ? 'active-rent' : '' }}">
            🏠 ใบแจ้งหนี้ค่าเช่า
        </a>
        <a href="{{ route('invoices.bulk-create', ['type' => 'utility', 'month' => $month, 'year' => $year]) }}"
            class="type-tab {{ $type === 'utility' ? 'active-utility' : '' }}">
            ⚡ ใบแจ้งค่าน้ำ/ค่าไฟ
        </a>
    </div>

    {{-- ✅ เลือกเดือนและตั้งค่า --}}
    <div class="period-card">
        <h6><i class="bi bi-calendar3 me-2"></i>เลือกช่วงเวลา</h6>
        <div class="row g-3">
            <div class="col-md-2">
                <label class="form-label fw-semibold">เดือน</label>
                <select id="periodMonth" class="form-select" onchange="changePeriod()">
                    @foreach (range(1, 12) as $m)
                        <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($m)->locale('th')->monthName }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-semibold">ปี (ค.ศ.)</label>
                <select id="periodYear" class="form-select" onchange="changePeriod()">
                    @for ($y = now()->year; $y >= now()->year - 2; $y--)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>
                            {{ $y + 543 }} ({{ $y }})
                        </option>
                    @endfor
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">วันที่ออกเอกสาร</label>
                <input type="date" id="globalIssueDate" class="form-control"
                    value="{{ \Carbon\Carbon::create($year, $month, 1)->format('Y-m-d') }}" onchange="syncAllDates()">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">วันครบกำหนดชำระ</label>
                <input type="date" id="globalDueDate" class="form-control"
                    value="{{ \Carbon\Carbon::create($year, $month, $type === 'utility' ? 21 : 7)->format('Y-m-d') }}"
                    onchange="syncAllDates()">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <div class="text-muted small">
                    @if ($type === 'utility')
                        <i class="bi bi-info-circle"></i> ออกวันที่ 21
                    @else
                        <i class="bi bi-info-circle"></i> ออกวันที่ 1
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ✅ Summary --}}
    <div class="summary-bar">
        <div class="s-item">
            <div class="s-label">ห้องทั้งหมด</div>
            <div class="s-value" id="totalRooms">{{ $bookings->count() }}</div>
        </div>
        <div class="s-item">
            <div class="s-label">เลือกแล้ว</div>
            <div class="s-value" id="selectedRooms">0</div>
        </div>
        <div class="s-item">
            <div class="s-label">ยอดรวมทั้งสิ้น</div>
            <div class="s-value" id="totalAmount">฿0.00</div>
        </div>
        @if ($type === 'utility')
            <div class="s-item">
                <div class="s-label">ไม่มีข้อมูลมิเตอร์</div>
                <div class="s-value text-warning" id="noReadingCount">
                    {{ $utilityData->where('has_reading', false)->count() }}
                </div>
            </div>
        @endif
        <div class="ms-auto d-flex gap-2">
            <button type="button" class="btn btn-sm btn-outline-primary" onclick="selectAll(true)">
                <i class="bi bi-check-all me-1"></i>เลือกทั้งหมด
            </button>
            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="selectAll(false)">
                <i class="bi bi-x me-1"></i>ยกเลิกทั้งหมด
            </button>
        </div>
    </div>

    {{-- ✅ Form --}}
    <form method="POST" action="{{ route('invoices.bulk-store') }}" id="bulkForm">
        @csrf
        <input type="hidden" name="invoice_type" value="{{ $type }}">

        {{-- ลดจำนวน input ที่ต้อง submit เพื่อลดปัญหา max_input_vars>1000 --}}
        <input type="hidden" name="issue_date"
            value="{{ \Carbon\Carbon::create($year, $month, $type === 'utility' ? 21 : 1)->format('Y-m-d') }}">
        <input type="hidden" name="due_date"
            value="{{ \Carbon\Carbon::create($year, $month, $type === 'utility' ? 21 : 7)->format('Y-m-d') }}">
        <input type="hidden" name="status" value="sent">


        <div class="rooms-table-card">
            <table>
                <thead>
                    <tr>
                        <th><input type="checkbox" id="checkAll" class="include-toggle" onchange="toggleAll(this)"></th>
                        <th>ห้อง</th>
                        <th>ผู้เช่า</th>
                        @if ($type === 'utility')
                            <th>⚡ ค่าไฟ</th>
                            <th>💧 ค่าน้ำ</th>
                        @endif
                        <th>เลขที่ใบแจ้งหนี้</th>
                        <th>ยอดก่อนภาษี</th>
                        <th>ภาษี 7%</th>
                        <th>ยอดรวม</th>
                        <th>วันออก</th>
                        <th>วันครบกำหนด</th>
                        <th>สถานะ</th>
                    </tr>
                </thead>
                <tbody id="roomsTableBody">
                    @php $i = 0; @endphp
                    @foreach ($bookings as $booking)
                        @php
                            $room = $booking->room;
                            $guest = $booking->guest;
                            $guestName = $guest
                                ? trim(($guest->first_name ?? '') . ' ' . ($guest->last_name ?? ''))
                                : 'ไม่ระบุ';

                            if ($type === 'utility') {
                                $ud = $utilityData->get($booking->id, []);
                                $hasRead = $ud['has_reading'] ?? false;
                                $elec = $ud['electric'] ?? [];
                                $water = $ud['water'] ?? [];
                                $amount = (float) ($ud['base_cost'] ?? 0);
                                $tax = (float) ($ud['tax'] ?? 0);
                                $total = (float) ($ud['total'] ?? 0);
                            } else {
                                $hasRead = true;
                                $amount = (float) ($booking->rent_amount ?? ($room?->price_per_month ?? 0));
                                $tax = round($amount * 0.07, 2);
                                $total = round($amount + $tax, 2);
                            }

                            $thaiMonths = [
                                '',
                                'มกราคม',
                                'กุมภาพันธ์',
                                'มีนาคม',
                                'เมษายน',
                                'พฤษภาคม',
                                'มิถุนายน',
                                'กรกฎาคม',
                                'สิงหาคม',
                                'กันยายน',
                                'ตุลาคม',
                                'พฤศจิกายน',
                                'ธันวาคม',
                            ];
                            $monthName = $thaiMonths[$month] ?? $month;
                            $buddhistYear = $year + 543;

                            $prefix = $type === 'utility' ? 'UTL' : 'INV';
                            $invNum =
                                $prefix .
                                '-' .
                                $year .
                                str_pad($month, 2, '0', STR_PAD_LEFT) .
                                '-' .
                                str_pad($i + 1, 5, '0', STR_PAD_LEFT);
                            $issueDate = \Carbon\Carbon::create($year, $month, $type === 'utility' ? 21 : 1)->format(
                                'Y-m-d',
                            );
                            $dueDate = \Carbon\Carbon::create($year, $month, $type === 'utility' ? 21 : 7)->format(
                                'Y-m-d',
                            );
                            $notes =
                                $type === 'utility'
                                    ? "ค่าน้ำ/ไฟ ห้อง {$room?->room_number} ประจำเดือน {$monthName} {$buddhistYear}"
                                    : "ค่าเช่าห้อง {$room?->room_number} ประจำเดือน {$monthName} {$buddhistYear}";
                        @endphp
                        <tr data-index="{{ $i }}" data-amount="{{ $total }}"
                            class="{{ !$hasRead && $type === 'utility' ? 'no-reading' : '' }}">
                            <td>
                                <input type="checkbox" class="include-toggle room-check" value="{{ $booking->id }}"
                                    {{ $hasRead || $type === 'rent' ? 'checked' : '' }}
                                    onchange="toggleRow(this, {{ $i }})">
                            </td>

                            <td>
                                <span class="room-chip {{ $type === 'utility' ? 'utility-chip' : '' }}">
                                    <i class="bi bi-door-closed"></i>
                                    {{ $room?->room_number ?? '-' }}
                                </span>
                            </td>
                            <td>
                                <div class="tenant-name">{{ $guestName }}</div>
                                <div class="tenant-sub">Booking #{{ $booking->id }}</div>
                            </td>

                            @if ($type === 'utility')
                                <td>
                                    @if (!empty($elec['has_reading']))
                                        <div style="font-size:0.78rem;">
                                            <span class="text-muted">{{ $elec['previous_value'] ?? 0 }}</span>
                                            → <strong>{{ $elec['current_value'] ?? 0 }}</strong><br>
                                            <span class="text-success">{{ $elec['usage'] ?? 0 }} หน่วย ×
                                                ฿{{ $elec['rate'] ?? 0 }}</span><br>
                                            <strong
                                                class="amount-cell">฿{{ number_format($elec['cost'] ?? 0, 2) }}</strong>
                                        </div>
                                    @else
                                        <span class="no-reading-badge">ไม่มีข้อมูล</span>
                                    @endif
                                </td>
                                <td>
                                    @if (!empty($water['has_reading']))
                                        <div style="font-size:0.78rem;">
                                            <span class="text-muted">{{ $water['previous_value'] ?? 0 }}</span>
                                            → <strong>{{ $water['current_value'] ?? 0 }}</strong><br>
                                            <span class="text-primary">{{ $water['usage'] ?? 0 }} หน่วย ×
                                                ฿{{ $water['rate'] ?? 0 }}</span><br>
                                            <strong
                                                class="amount-cell">฿{{ number_format($water['cost'] ?? 0, 2) }}</strong>
                                        </div>
                                    @else
                                        <span class="no-reading-badge">ไม่มีข้อมูล</span>
                                    @endif
                                </td>
                            @endif

                            <td>
                                {{-- NOTE: ลด input ที่ถูก submit เพื่อไม่ให้เกิน max_input_vars
                                 UI เดิมยังแสดงยอด แต่ค่าที่จำเป็นจะถูกคำนวณ/กำหนดฝั่ง server --}}
                            <td class="d-none"></td>
                            <td class="d-none"></td>
                            <td class="d-none"></td>
                            <td class="d-none"></td>
                            <td class="d-none"></td>
                            <td class="d-none"></td>
                            <td class="d-none"></td>

                            {{-- submit เฉพาะ booking_id ผ่าน checkbox --}}
                        </tr>

                        @php $i++; @endphp
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="action-footer">
            <div class="text-muted small">
                <i class="bi bi-info-circle me-1"></i>
                @if ($type === 'utility')
                    ห้องที่ไม่มีข้อมูลมิเตอร์จะถูกข้ามโดยอัตโนมัติ
                @else
                    ระบบจะสร้างใบแจ้งหนี้เฉพาะห้องที่ติ๊กเลือกเท่านั้น
                @endif
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('invoices.index', ['invoice_type' => $type]) }}"
                    class="btn btn-outline-secondary px-4">ยกเลิก</a>
                <button type="submit" class="btn-generate {{ $type }}" id="submitBtn">
                    <i class="bi bi-save"></i>
                    <span id="submitText">บันทึกใบแจ้งหนี้ทั้งหมด</span>
                </button>
            </div>
        </div>

    </form>

    <script>
        const TYPE = '{{ $type }}';

        function changePeriod() {
            const month = document.getElementById('periodMonth').value;
            const year = document.getElementById('periodYear').value;
            window.location.href = `{{ route('invoices.bulk-create') }}?type=${TYPE}&month=${month}&year=${year}`;
        }

        function recalcRow(el, idx) {
            const row = document.querySelector(`tr[data-index="${idx}"]`);
            const amount = parseFloat(row.querySelector('.inv-amount').value) || 0;
            const tax = parseFloat(row.querySelector('.inv-tax').value) || 0;
            const total = (amount + tax).toFixed(2);
            row.querySelector('.inv-total').value = total;
            row.dataset.amount = total;
            updateSummary();
        }

        function syncAllDates() {
            const issue = document.getElementById('globalIssueDate').value;
            const due = document.getElementById('globalDueDate').value;
            document.querySelectorAll('.room-check:checked').forEach(cb => {
                const row = cb.closest('tr');
                const issueInput = row.querySelector('.inv-issue');
                const dueInput = row.querySelector('.inv-due');
                if (issueInput && !issueInput.disabled) issueInput.value = issue;
                if (dueInput && !dueInput.disabled) dueInput.value = due;
            });
        }

        function toggleRow(checkbox, idx) {
            const row = document.querySelector(`tr[data-index="${idx}"]`);
            // เมื่อเราไม่ submit input รายแถวแล้ว การ disable input จึงไม่จำเป็น
            if (checkbox.checked) {
                row.classList.remove('excluded');
            } else {
                row.classList.add('excluded');
            }
            updateSummary();
            updateCheckAll();
        }


        function toggleAll(masterCheckbox) {
            const isChecked = masterCheckbox.checked;
            document.querySelectorAll('.room-check').forEach(cb => {
                // ข้ามห้องที่ไม่มี reading (utility)
                const row = cb.closest('tr');
                if (TYPE === 'utility' && row.classList.contains('no-reading')) return;
                cb.checked = isChecked;
                const idx = parseInt(row.dataset.index);
                const inputs = row.querySelectorAll('input:not(.include-toggle), select');
                if (isChecked) {
                    row.classList.remove('excluded');
                    inputs.forEach(el => {
                        el.disabled = false;
                    });
                } else {
                    row.classList.add('excluded');
                    inputs.forEach(el => {
                        el.disabled = true;
                    });
                }
            });
            updateSummary();
        }

        function selectAll(checked) {
            const master = document.getElementById('checkAll');
            master.checked = checked;
            master.indeterminate = false;
            toggleAll(master);
        }

        function updateCheckAll() {
            const all = document.querySelectorAll('.room-check');
            const checked = document.querySelectorAll('.room-check:checked');
            document.getElementById('checkAll').checked = all.length === checked.length;
            document.getElementById('checkAll').indeterminate = checked.length > 0 && checked.length < all.length;
        }

        function updateSummary() {
            const checkedRows = document.querySelectorAll('.room-check:checked');
            let total = 0;
            checkedRows.forEach(cb => {
                total += parseFloat(cb.closest('tr').dataset.amount) || 0;
            });
            document.getElementById('selectedRooms').textContent = checkedRows.length;
            document.getElementById('totalAmount').textContent = '฿' + total.toLocaleString('th-TH', {
                minimumFractionDigits: 2
            });
        }

        // submit
        document.getElementById('bulkForm').addEventListener('submit', function() {
            const btn = document.getElementById('submitBtn');
            btn.disabled = true;
            document.getElementById('submitText').textContent = 'กำลังบันทึก...';

            // รวบรวม booking_id จาก checkbox ที่ติ๊กไว้
            const selected = Array.from(document.querySelectorAll('.room-check:checked'))
                .map(cb => cb.value)
                .filter(Boolean);

            // clear hidden เดิม (ถ้ามี)
            document.querySelectorAll('input[name="selected_bookings[]"]').forEach(el => el.remove());

            selected.forEach(id => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'selected_bookings[]';
                input.value = id;
                document.getElementById('bulkForm').appendChild(input);
            });
        });


        // init
        updateSummary();
    </script>

@endsection
