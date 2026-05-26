
 
<?php $__env->startSection('content'); ?>
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white py-3">
                    <h5 class="mb-0"><i class="bi bi-tools"></i> แจ้งซ่อมบำรุงใหม่</h5>
                </div>
                <div class="card-body p-4">
 
                    <?php if($errors->any()): ?>
                        <div class="alert alert-danger">
                            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div><?php echo e($error); ?></div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    <?php endif; ?>
 
                    <form action="<?php echo e(route('maintenances.store')); ?>" method="POST" id="maintenanceForm">
                        <?php echo csrf_field(); ?>
 
                        
                        <div class="mb-4">
                            <label class="form-label fw-bold">ห้องพัก <span class="text-danger">*</span></label>
                            <select name="room_id" id="room_id" class="form-select <?php $__errorArgs = ['room_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                <?php echo e($selectedFacilityId ? 'disabled' : ''); ?> required>
                                <option value="">-- เลือกห้องพัก --</option>
                                <?php $__currentLoopData = $rooms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $room): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($room->id); ?>" 
                                        <?php echo e(old('room_id', $selectedRoomId) == $room->id ? 'selected' : ''); ?>>
                                        ห้อง <?php echo e($room->room_number); ?> (<?php echo e($room->room_type); ?>)
                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            
                            
                            <?php if($selectedFacilityId): ?>
                                <small class="text-info d-block mt-2">
                                    🔒 ล็อค: มาจากการแจ้งซ่อมจาก Facility
                                </small>
                            <?php endif; ?>
                            
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
 
                        
                        <div class="mb-4">
                            <label class="form-label fw-bold">สิ่งอำนวยความสะดวก / อุปกรณ์</label>
                            <select name="facility_id" id="facility_id" class="form-select"
                                <?php echo e($selectedFacilityId ? 'disabled' : ''); ?>>
                                <option value="">-- เลือกอุปกรณ์ (ถ้ามี) --</option>
                                <?php $__currentLoopData = $facilities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $facility): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($facility->id); ?>" 
                                        data-room="<?php echo e($facility->room_id); ?>"
                                        <?php echo e(old('facility_id', $selectedFacilityId) == $facility->id ? 'selected' : ''); ?>>
                                        <?php echo e($facility->name); ?> (<?php echo e($facility->type); ?>)
                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <div class="form-text text-muted">เลือกห้องพักก่อน เพื่อดูรายการอุปกรณ์ในห้องนั้น</div>
                            
                            
                            <?php if($selectedFacilityId): ?>
                                <small class="text-info d-block mt-2">
                                    🔒 ล็อค: ตัวเลือกนี้ถูกตรึงแล้ว
                                </small>
                            <?php endif; ?>
                        </div>
 
                        
                        <?php if($selectedFacilityId): ?>
                            <input type="hidden" name="room_id" value="<?php echo e($selectedRoomId); ?>">
                            <input type="hidden" name="facility_id" value="<?php echo e($selectedFacilityId); ?>">
                        <?php endif; ?>
 
                        
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">ประเภทงานซ่อม <span class="text-danger">*</span></label>
                                <select name="maintenance_type" id="maintenance_type" class="form-select <?php $__errorArgs = ['maintenance_type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                                    <option value="">-- เลือกประเภท --</option>
                                    <option value="ไฟฟ้า" <?php echo e(old('maintenance_type') == 'ไฟฟ้า' ? 'selected' : ''); ?>>💡 ไฟฟ้า</option>
                                    <option value="ประปา/ท่อ" <?php echo e(old('maintenance_type') == 'ประปา/ท่อ' ? 'selected' : ''); ?>>🚰 ประปา/ท่อ</option>
                                    <option value="เครื่องปรับอากาศ" <?php echo e(old('maintenance_type') == 'เครื่องปรับอากาศ' ? 'selected' : ''); ?>>❄️ เครื่องปรับอากาศ</option>
                                    <option value="เฟอร์นิเจอร์" <?php echo e(old('maintenance_type') == 'เฟอร์นิเจอร์' ? 'selected' : ''); ?>>🪑 เฟอร์นิเจอร์</option>
                                    <option value="อื่นๆ" <?php echo e(old('maintenance_type') == 'อื่นๆ' ? 'selected' : ''); ?>>🛠️ อื่นๆ</option>
                                </select>
                                <?php $__errorArgs = ['maintenance_type'];
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
                            <div class="col-md-6">
                                <label class="form-label fw-bold">ความเร่งด่วน</label>
                                <select name="priority" id="priority_select" class="form-select">
                                    <option value="ทั่วไป" <?php echo e(old('priority') == 'ทั่วไป' ? 'selected' : ''); ?>>ทั่วไป</option>
                                    <option value="ด่วน" <?php echo e(old('priority') == 'ด่วน' ? 'selected' : ''); ?>>ด่วน</option>
                                    <option value="ด่วนมาก" <?php echo e(old('priority') == 'ด่วนมาก' ? 'selected' : ''); ?>>ด่วนมาก</option>
                                </select>
                            </div>
                        </div>
 
                        
                        <div class="mb-4">
                            <label class="form-label fw-bold">วันที่แจ้งซ่อม <span class="text-danger">*</span></label>
                            <input type="date" name="request_date" class="form-control <?php $__errorArgs = ['request_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                value="<?php echo e(old('request_date', date('Y-m-d'))); ?>" required>
                            <?php $__errorArgs = ['request_date'];
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
 
                        <div class="mb-4">
                            <label class="form-label fw-bold">รายละเอียดปัญหา <span class="text-danger">*</span></label>
                            <textarea name="description" id="description" class="form-control <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                rows="4" placeholder="กรุณาระบุรายละเอียดปัญหา..." required><?php echo e(old('description')); ?></textarea>
                            <?php $__errorArgs = ['description'];
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
 
                        
                        <div class="mb-4">
                            <label class="form-label fw-bold">สถานะ <span class="text-danger">*</span></label>
                            <select name="status" id="status" class="form-select <?php $__errorArgs = ['status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                                <option value="pending" <?php echo e(old('status', 'pending') == 'pending' ? 'selected' : ''); ?>>⏳ รอดำเนิน</option>
                                <option value="in_progress" <?php echo e(old('status') == 'in_progress' ? 'selected' : ''); ?>>🔄 กำลังดำเนิน</option>
                                <option value="completed" <?php echo e(old('status') == 'completed' ? 'selected' : ''); ?>>✅ สำเร็จ</option>
                                <option value="cancelled" <?php echo e(old('status') == 'cancelled' ? 'selected' : ''); ?>>❌ ยกเลิก</option>
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
 
                        <div class="mb-4">
                            <label class="form-label fw-bold">หมายเหตุ</label>
                            <textarea name="notes" id="notes" class="form-control" rows="3" 
                                placeholder="หมายเหตุเพิ่มเติม"><?php echo e(old('notes')); ?></textarea>
                        </div>
 
                        
                        <div class="d-flex justify-content-between mt-5">
                            <a href="<?php echo e(route('maintenances.index')); ?>" class="btn btn-light px-4">ยกเลิก</a>
                            <button type="submit" class="btn btn-primary px-5">ยืนยันการแจ้งซ่อม</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
 

<script>
document.addEventListener('DOMContentLoaded', function() {
    const roomSelect = document.getElementById('room_id');
    const facilitySelect = document.getElementById('facility_id');
    const allFacilityOptions = Array.from(facilitySelect.options);
 
    function filterFacilities() {
        const selectedRoomId = roomSelect.value;
 
        // ล้างค่าเก่า
        facilitySelect.innerHTML = '<option value="">-- เลือกอุปกรณ์ (ถ้ามี) --</option>';
 
        if (selectedRoomId) {
            // ✅ Filter facilities ตามห้องที่เลือก
            const filteredOptions = allFacilityOptions.filter(opt => {
                const dataRoom = opt.getAttribute('data-room');
                return dataRoom === selectedRoomId;
            });
            
            filteredOptions.forEach(opt => facilitySelect.appendChild(opt.cloneNode(true)));
        }
    }
 
    roomSelect.addEventListener('change', filterFacilities);
 
    // ✅ ทำงานทันทีเมื่อโหลดหน้า
    if (roomSelect.value && !facilitySelect.disabled) {
        filterFacilities();
    }
});
</script>
<?php $__env->stopSection(); ?>
 
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Rm1\resources\views/maintenances/create.blade.php ENDPATH**/ ?>