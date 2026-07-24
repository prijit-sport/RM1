@extends('layouts.app')

@section('content')
    <div class="container py-4">

        {{-- Header --}}
        <div class="d-flex align-items-center gap-2 mb-4">
            <a href="{{ route('bookings.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-arrow-left"></i>
            </a>
            <h5 class="mb-0"><i class="bi bi-calendar-plus text-primary me-2"></i>สร้างการจองใหม่</h5>
        </div>

        @if (request('room_id') || request('room_type'))
            <div class="alert alert-info border-0 d-flex align-items-center mb-4" role="alert">
                <i class="bi bi-info-circle-fill me-2 fs-5"></i>
                <div>
                    @if (request('room_id'))
                        @php $preRoom = $rooms->firstWhere('id', request('room_id')); @endphp
                        @if ($preRoom)
                            ระบบได้เลือกห้อง <strong>#{{ $preRoom->room_number }}</strong>
                            @if ($preRoom->zone)
                                (โซน {{ $preRoom->zone }})
                            @endif
                            ให้อัตโนมัติ — กรุณาเลือกผู้เช่าและกรอกข้อมูลเพิ่มเติม
                        @else
                            ไม่พบห้องที่เลือก — กรุณาเลือกใหม่
                        @endif
                    @elseif(request('room_type'))
                        กำลังจอง: <strong>{{ request('room_type') === 'fan' ? '🌀 ห้องพัดลม' : '❄️ ห้องแอร์' }}</strong>
                        — กรุณาเลือกโซนและห้องที่ต้องการ
                    @endif
                </div>
            </div>
        @endif

        <form action="{{ route('bookings.store') }}" method="POST">
            @csrf

            <div class="row g-4">

                {{-- ===== คอลัมน์ซ้าย ===== --}}
                <div class="col-lg-6">

                    {{-- ผู้เช่า --}}
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="mb-0 text-primary">
                                <i class="bi bi-person-circle me-2"></i>ผู้เช่า
                            </h6>
                        </div>
                        <div class="card-body">
                            <label class="form-label fw-semibold">เลือกผู้เช่า <span class="text-danger">*</span></label>
                            <select name="guest_id" id="guest_id"
                                class="form-select @error('guest_id') is-invalid @enderror" required>
                                <option value="">-- เลือกผู้เช่า --</option>
                                @foreach ($guests as $guest)
                                    <option value="{{ $guest->id }}"
                                        {{ old('guest_id') == $guest->id ? 'selected' : '' }}>
                                        {{ $guest->first_name }} {{ $guest->last_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('guest_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- ข้อมูลห้องพัก --}}
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="mb-0 text-primary">
                                <i class="bi bi-door-open me-2"></i>ข้อมูลห้องพัก
                            </h6>
                        </div>
                        <div class="card-body">

                            {{-- โซน --}}
                            <div class="mb-3">
                                <label class="form-label fw-semibold">โซน <span class="text-danger">*</span></label>
                                <select id="zone_select" class="form-select" required>
                                    <option value="">-- เลือกโซน --</option>
                                    {{-- ✅ แก้: pluck zone จาก rooms ที่มี zone --}}
                                    @foreach ($rooms->pluck('zone')->filter()->unique()->sort() as $zone)
                                        <option value="{{ $zone }}">โซน {{ $zone }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- ห้องหมายเลข --}}
                            <div class="mb-3">
                                <label class="form-label fw-semibold">ห้องหมายเลข <span class="text-danger">*</span></label>
                                <select name="room_id" id="room_select"
                                    class="form-select @error('room_id') is-invalid @enderror" required disabled>
                                    <option value="">-- เลือกโซนก่อน --</option>
                                </select>
                                @error('room_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- ประเภทห้อง --}}
                            <div class="mb-3">
                                <label class="form-label fw-semibold">ประเภทห้อง</label>
                                <input type="text" id="room_type_display" class="form-control bg-light" placeholder="—"
                                    readonly>
                            </div>

                            {{-- วันที่เข้าพัก --}}
                            <div class="mb-0">
                                <label class="form-label fw-semibold">วันที่เข้าพัก <span
                                        class="text-danger">*</span></label>
                                <input type="date" name="check_in_date" id="check_in_date"
                                    class="form-control @error('check_in_date') is-invalid @enderror"
                                    value="{{ old('check_in_date') }}" required>
                                @error('check_in_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                        </div>
                    </div>

                </div>{{-- end col-left --}}

                {{-- ===== คอลัมน์ขวา ===== --}}
                <div class="col-lg-6">

                    {{-- มัดจำและค่าใช้จ่าย --}}
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="mb-0 text-primary">
                                <i class="bi bi-cash-coin me-2"></i>มัดจำและค่าใช้จ่ายแรกเข้า
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                                <div>
                                    <div class="fw-semibold">ค่าเช่าเดือนแรก</div>
                                    <small class="text-muted">ห้องที่เลือก</small>
                                </div>
                                <div class="text-end">
                                    <span id="display_rent" class="fw-bold fs-5">—</span>
                                    <span class="text-muted ms-1">฿</span>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                                <div>
                                    <div class="fw-semibold">เงินมัดจำ</div>
                                    <small class="text-muted">1 เดือน</small>
                                </div>
                                <div class="text-end">
                                    <span id="display_deposit" class="fw-bold fs-5">—</span>
                                    <span class="text-muted ms-1">฿</span>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center pt-3 mt-1">
                                <div class="fw-bold fs-6">ยอดรวมที่ต้องชำระ</div>
                                <div class="text-end">
                                    <span id="display_total" class="fw-bold text-primary" style="font-size:1.5rem">—</span>
                                    <span class="text-primary ms-1 fw-semibold">฿</span>
                                </div>
                            </div>
                            <input type="hidden" name="rent_amount" id="rent_amount">
                            <input type="hidden" name="deposit_amount" id="deposit_amount">
                        </div>
                    </div>

                    {{-- เลขมิเตอร์ --}}
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="mb-0 text-primary">
                                <i class="bi bi-speedometer2 me-2"></i>เลขมิเตอร์เริ่มต้น
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-6">
                                    <label class="form-label fw-semibold">
                                        <i class="bi bi-lightning-charge text-warning me-1"></i>มิเตอร์ไฟ (หน่วย)
                                    </label>
                                    <input type="number" name="electric_meter_start"
                                        class="form-control @error('electric_meter_start') is-invalid @enderror"
                                        placeholder="0" min="0" value="{{ old('electric_meter_start', 0) }}">
                                    @error('electric_meter_start')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-6">
                                    <label class="form-label fw-semibold">
                                        <i class="bi bi-droplet text-info me-1"></i>มิเตอร์น้ำ (หน่วย)
                                    </label>
                                    <input type="number" name="water_meter_start"
                                        class="form-control @error('water_meter_start') is-invalid @enderror"
                                        placeholder="0" min="0" value="{{ old('water_meter_start', 0) }}">
                                    @error('water_meter_start')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- สถานะ + หมายเหตุ --}}
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">สถานะการจอง</label>
                                <select name="status" class="form-select @error('status') is-invalid @enderror">
                                    <option value="confirmed"
                                        {{ old('status', 'confirmed') == 'confirmed' ? 'selected' : '' }}>
                                        ยืนยันแล้ว
                                    </option>
                                    <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>
                                        ยกเลิก
                                    </option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-0">
                                <label class="form-label fw-semibold">หมายเหตุ</label>
                                <textarea name="notes" class="form-control" rows="3" placeholder="บันทึกเพิ่มเติม...">{{ old('notes') }}</textarea>
                            </div>
                        </div>
                    </div>

                </div>{{-- end col-right --}}

            </div>{{-- end row --}}

            <div class="d-flex justify-content-between mt-4">
                <a href="{{ route('bookings.index') }}" class="btn btn-outline-secondary px-4">
                    <i class="bi bi-x-circle me-1"></i> ยกเลิก
                </a>
                <button type="submit" class="btn btn-primary px-5">
                    <i class="bi bi-check-circle me-1"></i> บันทึกการจอง
                </button>
            </div>

        </form>
    </div>
@endsection

@push('scripts')
    <script>
        const allRooms = @json($rooms);
        const zoneSelect = document.getElementById('zone_select');
        const roomSelect = document.getElementById('room_select');
        const roomTypeInput = document.getElementById('room_type_display');
        const displayRent = document.getElementById('display_rent');
        const displayDeposit = document.getElementById('display_deposit');
        const displayTotal = document.getElementById('display_total');
        const rentInput = document.getElementById('rent_amount');
        const depositInput = document.getElementById('deposit_amount');

        const typeLabel = {
            'fan': '🌀 พัดลม',
            'air': '❄️ แอร์'
        };

        // ─── เลือกโซน → โหลดห้องในโซนนั้น ───
        zoneSelect.addEventListener('change', function() {
            const zone = this.value;
            roomSelect.innerHTML = '<option value="">-- เลือกห้อง --</option>';
            roomTypeInput.value = '';
            resetPrices();

            if (!zone) {
                roomSelect.disabled = true;
                return;
            }

            // ✅ แก้: กรอง status === 'available' เฉพาะห้องที่ว่าง
            const filtered = allRooms.filter(r => r.zone === zone && r.status === 'available');

            filtered.forEach(room => {
                const opt = document.createElement('option');
                opt.value = room.id;
                opt.dataset.price = room.price_per_month;
                opt.dataset.type = room.room_type;
                opt.textContent =
                    `${room.room_number} — ${typeLabel[room.room_type] ?? room.room_type} (${Number(room.price_per_month).toLocaleString()} ฿/เดือน)`;
                roomSelect.appendChild(opt);
            });

            roomSelect.disabled = filtered.length === 0;
            if (filtered.length === 0) {
                roomSelect.innerHTML = '<option value="">ไม่มีห้องว่างในโซนนี้</option>';
            }
        });

        // ─── เลือกห้อง → คำนวณค่าใช้จ่าย ───
        roomSelect.addEventListener('change', function() {
            if (!this.value) {
                resetPrices();
                return;
            }

            const selected = this.options[this.selectedIndex];
            const price = parseFloat(selected.dataset.price) || 0;
            const deposit = price * 1;
            const total = price + deposit;

            roomTypeInput.value = typeLabel[selected.dataset.type] ?? selected.dataset.type;
            displayRent.textContent = price.toLocaleString();
            displayDeposit.textContent = deposit.toLocaleString();
            displayTotal.textContent = total.toLocaleString();
            rentInput.value = price;
            depositInput.value = deposit;
        });

        function resetPrices() {
            displayRent.textContent = '—';
            displayDeposit.textContent = '—';
            displayTotal.textContent = '—';
            rentInput.value = '';
            depositInput.value = '';
        }

        // ─── Auto-select จาก URL ?room_id=... ───
        (function autoSelectFromUrl() {
            const params = new URLSearchParams(window.location.search);
            const preselectedRoomId = params.get('room_id');
            if (!preselectedRoomId) return;

            const room = allRooms.find(r => String(r.id) === String(preselectedRoomId));
            if (!room) return;

            zoneSelect.value = room.zone;
            zoneSelect.dispatchEvent(new Event('change'));

            setTimeout(() => {
                roomSelect.value = String(preselectedRoomId);
                roomSelect.dispatchEvent(new Event('change'));
                document.getElementById('check_in_date')?.focus();
            }, 100);
        })();
    </script>
@endpush
