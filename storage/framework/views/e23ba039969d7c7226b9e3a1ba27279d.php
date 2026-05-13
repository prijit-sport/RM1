<?php $__env->startSection('content'); ?>
<div class="container py-4">

    
    <div class="d-flex align-items-center gap-2 mb-4">
        <a href="<?php echo e(route('bookings.index')); ?>" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left"></i>
        </a>
        <h5 class="mb-0"><i class="bi bi-calendar-plus text-primary me-2"></i>สร้างการจองใหม่</h5>
    </div>

    
    <?php if(request('room_id') || request('room_type')): ?>
        <div class="alert alert-info border-0 d-flex align-items-center" role="alert">
            <i class="bi bi-info-circle-fill me-2 fs-5"></i>
            <div>
                <?php if(request('room_id')): ?>
                    <?php
                        $preRoom = $rooms->firstWhere('id', request('room_id'));
                    ?>
                    <?php if($preRoom): ?>
                        ระบบได้เลือกห้อง <strong>#<?php echo e($preRoom->room_number); ?></strong>
                        <?php if($preRoom->zone): ?> (โซน <?php echo e($preRoom->zone); ?>) <?php endif; ?>
                        ให้คุณอัตโนมัติ — กรุณาเลือกผู้เช่าและกรอกข้อมูลเพิ่มเติม
                    <?php else: ?>
                        ไม่พบห้องที่เลือก (อาจถูกจองไปแล้ว) — กรุณาเลือกใหม่
                    <?php endif; ?>
                <?php elseif(request('room_type')): ?>
                    <?php
                        $typeText = request('room_type') === 'fan' ? '🌀 ห้องพัดลม' : '❄️ ห้องแอร์';
                    ?>
                    กำลังจอง: <strong><?php echo e($typeText); ?></strong> — กรุณาเลือกโซนและห้องที่ต้องการ
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>

    <form action="<?php echo e(route('bookings.store')); ?>" method="POST">
        <?php echo csrf_field(); ?>

        <div class="row g-4">

            
            <div class="col-lg-6">

                
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="mb-0 text-primary">
                            <i class="bi bi-person-circle me-2"></i>ผู้เช่า
                        </h6>
                    </div>
                    <div class="card-body">
                        <label class="form-label fw-semibold">เลือกผู้เช่า <span class="text-danger">*</span></label>
                        <select name="guest_id" id="guest_id"
                            class="form-select <?php $__errorArgs = ['guest_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                            <option value="">-- เลือกผู้เช่า --</option>
                            <?php $__currentLoopData = $guests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $guest): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($guest->id); ?>" <?php echo e(old('guest_id') == $guest->id ? 'selected' : ''); ?>>
                                    <?php echo e($guest->first_name); ?> <?php echo e($guest->last_name); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <?php $__errorArgs = ['guest_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="invalid-feedback"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                </div>

                
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="mb-0 text-primary">
                            <i class="bi bi-door-open me-2"></i>ข้อมูลห้องพัก
                        </h6>
                    </div>
                    <div class="card-body">

                        
                        <div class="mb-3">
                            <label class="form-label fw-semibold">โซน <span class="text-danger">*</span></label>
                            <select id="zone_select" class="form-select" required>
                                <option value="">-- เลือกโซน --</option>
                                <?php $__currentLoopData = $rooms->pluck('zone')->unique()->sort(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $zone): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($zone); ?>"><?php echo e($zone); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>

                        
                        <div class="mb-3">
                            <label class="form-label fw-semibold">ห้องหมายเลข <span class="text-danger">*</span></label>
                            <select name="room_id" id="room_select"
                                class="form-select <?php $__errorArgs = ['room_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required disabled>
                                <option value="">-- เลือกโซนก่อน --</option>
                            </select>
                            <?php $__errorArgs = ['room_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        
                        <div class="mb-3">
                            <label class="form-label fw-semibold">ประเภทห้อง</label>
                            <input type="text" id="room_type_display" class="form-control bg-light"
                                placeholder="—" readonly>
                        </div>

                        
                        <div class="mb-0">
                            <label class="form-label fw-semibold">วันที่เข้าพัก <span class="text-danger">*</span></label>
                            <input type="date" name="check_in_date" id="check_in_date"
                                class="form-control <?php $__errorArgs = ['check_in_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                value="<?php echo e(old('check_in_date')); ?>" required>
                            <?php $__errorArgs = ['check_in_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                    </div>
                </div>

            </div>


            
            <div class="col-lg-6">

                
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
                                <span id="display_rent" class="fw-bold fs-5 text-dark">—</span>
                                <span class="text-muted ms-1">฿</span>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                            <div>
                                <div class="fw-semibold">เงินมัดจำ</div>
                                <small class="text-muted">2 เดือน</small>
                            </div>
                            <div class="text-end">
                                <span id="display_deposit" class="fw-bold fs-5 text-dark">—</span>
                                <span class="text-muted ms-1">฿</span>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                            <div>
                                <div class="fw-semibold">ค่าน้ำ (มิเตอร์เริ่มต้น)</div>
                                <small class="text-muted">บันทึกเลขมิเตอร์</small>
                            </div>
                            <div class="text-end">
                                <span class="fw-bold fs-5 text-muted">—</span>
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
                                <input type="number" name="electric_meter_start" id="electric_meter_start"
                                    class="form-control <?php $__errorArgs = ['electric_meter_start'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    placeholder="0000" min="0" value="<?php echo e(old('electric_meter_start', 0)); ?>">
                                <?php $__errorArgs = ['electric_meter_start'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-droplet text-info me-1"></i>มิเตอร์น้ำ (หน่วย)
                                </label>
                                <input type="number" name="water_meter_start" id="water_meter_start"
                                    class="form-control <?php $__errorArgs = ['water_meter_start'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    placeholder="0000" min="0" value="<?php echo e(old('water_meter_start', 0)); ?>">
                                <?php $__errorArgs = ['water_meter_start'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>
                    </div>
                </div>

                
                <div class="card border-0 shadow-sm">
                    <div class="card-body">

                        <div class="mb-3">
                            <label class="form-label fw-semibold">สถานะการจอง</label>
                            <select name="status" class="form-select <?php $__errorArgs = ['status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                <option value="pending"   <?php echo e(old('status','pending') == 'pending'   ? 'selected' : ''); ?>>รอยืนยัน</option>
                                <option value="confirmed" <?php echo e(old('status') == 'confirmed' ? 'selected' : ''); ?>>ยืนยันแล้ว</option>
                                <option value="cancelled" <?php echo e(old('status') == 'cancelled' ? 'selected' : ''); ?>>ยกเลิก</option>
                            </select>
                            <?php $__errorArgs = ['status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div class="mb-0">
                            <label class="form-label fw-semibold">หมายเหตุ</label>
                            <textarea name="notes" class="form-control" rows="3"
                                placeholder="บันทึกเพิ่มเติม..."><?php echo e(old('notes')); ?></textarea>
                        </div>

                    </div>
                </div>

            </div>

        </div>

        
        <div class="d-flex justify-content-between mt-4">
            <a href="<?php echo e(route('bookings.index')); ?>" class="btn btn-outline-secondary px-4">
                <i class="bi bi-x-circle me-1"></i> ยกเลิก
            </a>
            <button type="submit" class="btn btn-primary px-5">
                <i class="bi bi-check-circle me-1"></i> บันทึกการจอง
            </button>
        </div>

    </form>
</div>
<?php $__env->stopSection(); ?>


<?php $__env->startPush('scripts'); ?>
<script>
    // ข้อมูลห้องทั้งหมดจาก Controller (JSON)
    const allRooms = <?php echo json_encode($rooms, 15, 512) ?>;

    const zoneSelect     = document.getElementById('zone_select');
    const roomSelect     = document.getElementById('room_select');
    const roomTypeInput  = document.getElementById('room_type_display');
    const displayRent    = document.getElementById('display_rent');
    const displayDeposit = document.getElementById('display_deposit');
    const displayTotal   = document.getElementById('display_total');
    const rentInput      = document.getElementById('rent_amount');
    const depositInput   = document.getElementById('deposit_amount');

    // ─── เมื่อเลือกโซน → โหลดห้องในโซนนั้น ───
    zoneSelect.addEventListener('change', function () {
        const zone = this.value;
        roomSelect.innerHTML = '<option value="">-- เลือกห้อง --</option>';
        roomTypeInput.value = '';
        resetPrices();

        if (!zone) {
            roomSelect.disabled = true;
            return;
        }

        const filtered = allRooms.filter(r => r.zone === zone && r.status === 'available');
        filtered.forEach(room => {
            const opt = document.createElement('option');
            opt.value         = room.id;
            opt.dataset.price = room.price_per_month;
            opt.dataset.type  = room.room_type;
            opt.textContent   = `${room.room_number} — ${room.room_type} (${Number(room.price_per_month).toLocaleString()} ฿/เดือน)`;
            roomSelect.appendChild(opt);
        });

        roomSelect.disabled = filtered.length === 0;
        if (filtered.length === 0) {
            roomSelect.innerHTML = '<option value="">ไม่มีห้องว่างในโซนนี้</option>';
        }
    });

    // ─── เมื่อเลือกห้อง → คำนวณค่าใช้จ่าย ───
    roomSelect.addEventListener('change', function () {
        const selected = this.options[this.selectedIndex];
        if (!this.value) { resetPrices(); return; }

        const price   = parseFloat(selected.dataset.price) || 0;
        const deposit = price * 2;
        const total   = price + deposit;

        roomTypeInput.value        = selected.dataset.type || '';
        displayRent.textContent    = price.toLocaleString();
        displayDeposit.textContent = deposit.toLocaleString();
        displayTotal.textContent   = total.toLocaleString();
        rentInput.value            = price;
        depositInput.value         = deposit;
    });

    function resetPrices() {
        displayRent.textContent    = '—';
        displayDeposit.textContent = '—';
        displayTotal.textContent   = '—';
        rentInput.value            = '';
        depositInput.value         = '';
    }

    // ─── 🆕 Auto-select ห้องจาก URL query string ?room_id=... ───
    // ใช้เมื่อผู้ใช้คลิก chip ห้องจากหน้า bookings.index
    (function autoSelectFromUrl() {
        const params = new URLSearchParams(window.location.search);
        const preselectedRoomId = params.get('room_id');

        if (!preselectedRoomId) return;

        // หาห้องที่ถูก preselect จาก allRooms
        const room = allRooms.find(r => String(r.id) === String(preselectedRoomId));
        if (!room) {
            console.warn('Room not found or not available:', preselectedRoomId);
            return;
        }

        // 1. เลือกโซน → trigger change event เพื่อให้ระบบ populate dropdown ห้อง
        zoneSelect.value = room.zone;
        zoneSelect.dispatchEvent(new Event('change'));

        // 2. เลือกห้องที่ต้องการ → trigger change event เพื่อคำนวณราคา
        roomSelect.value = String(preselectedRoomId);
        roomSelect.dispatchEvent(new Event('change'));

        // 3. Highlight ฟิลด์ "วันที่เข้าพัก" เพื่อให้ผู้ใช้กรอกต่อ
        setTimeout(() => {
            const checkInField = document.getElementById('check_in_date');
            if (checkInField) checkInField.focus();
        }, 300);
    })();
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Rm1\resources\views/bookings/create.blade.php ENDPATH**/ ?>