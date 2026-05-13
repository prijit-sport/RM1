

<?php $__env->startSection('content'); ?>
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white py-3">
                    <h5 class="mb-0"><i class="bi bi-tools"></i> แจ้งซ่อมบำรุงใหม่</h5>
                </div>
                <div class="card-body p-4">

                    <form action="<?php echo e(route('maintenances.store')); ?>" method="POST" id="maintenanceForm">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="status" value="pending">

                        
                        <div class="mb-4">
                            <label class="form-label fw-bold">ห้องพัก <span class="text-danger">*</span></label>
                            <select name="room_id" id="room_id" class="form-select <?php $__errorArgs = ['room_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                                <option value="">-- เลือกห้องพัก --</option>
                                <?php $__currentLoopData = $rooms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $room): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($room->id); ?>" data-room-number="<?php echo e($room->room_number); ?>" <?php echo e((old('room_id') == $room->id || (isset($facility) && $facility->location == $room->room_number)) ? 'selected' : ''); ?>>
                                        ห้อง <?php echo e($room->room_number); ?> - <?php echo e($room->room_type); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>

                        
                        <div class="mb-4">
                            <label class="form-label fw-bold">สิ่งอำนวยความสะดวก / อุปกรณ์</label>
                            <select name="facility_id" id="facility_id" class="form-select">
                                <option value="">-- เลือกอุปกรณ์ (ถ้ามี) --</option>
                                <?php $__currentLoopData = $facilities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($f->id); ?>" data-room="<?php echo e($f->location); ?>" <?php echo e((old('facility_id') == $f->id || (isset($facility) && $facility->id == $f->id)) ? 'selected' : ''); ?>>
                                        <?php echo e($f->name); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <div class="form-text text-muted">เลือกห้องพักก่อน เพื่อดูรายการอุปกรณ์ในห้องนั้น</div>
                        </div>

                        
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">ประเภทงานซ่อม <span class="text-danger">*</span></label>
                                <select name="maintenance_type" id="maintenance_type" class="form-select" required>
                                    <option value="">-- เลือกประเภท --</option>
                                    <option value="ไฟฟ้า" <?php echo e(old('maintenance_type') == 'ไฟฟ้า' ? 'selected' : ''); ?>>💡 ไฟฟ้า</option>
                                    <option value="ประปา/ท่อ" <?php echo e(old('maintenance_type') == 'ประปา/ท่อ' ? 'selected' : ''); ?>>🚰 ประปา/ท่อ</option>
                                    <option value="เครื่องปรับอากาศ" <?php echo e(old('maintenance_type') == 'เครื่องปรับอากาศ' ? 'selected' : ''); ?>>❄️ เครื่องปรับอากาศ</option>
                                    <option value="เฟอร์นิเจอร์" <?php echo e((old('maintenance_type') == 'เฟอร์นิเจอร์' || isset($facility)) ? 'selected' : ''); ?>>🪑 เฟอร์นิเจอร์</option>
                                    <option value="อื่นๆ" <?php echo e(old('maintenance_type') == 'อื่นๆ' ? 'selected' : ''); ?>>🛠️ อื่นๆ</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">ความเร่งด่วน</label>
                                <select id="priority_select" class="form-select">
                                    <option value="ทั่วไป">ทั่วไป</option>
                                    <option value="ด่วน">ด่วน</option>
                                    <option value="ด่วนมาก">ด่วนมาก</option>
                                </select>
                            </div>
                        </div>

                        
                        <div class="mb-4">
                            <label class="form-label fw-bold">วันที่แจ้งซ่อม <span class="text-danger">*</span></label>
                            <input type="date" name="request_date" class="form-control" value="<?php echo e(old('request_date', date('Y-m-d'))); ?>" required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">รายละเอียดปัญหา <span class="text-danger">*</span></label>
                            <textarea name="description" id="description" class="form-control" rows="4" placeholder="กรุณาระบุรายละเอียดปัญหา..." required><?php echo e(old('description')); ?></textarea>
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
        const selectedOption = roomSelect.options[roomSelect.selectedIndex];
        const roomNumber = selectedOption ? selectedOption.getAttribute('data-room-number') : '';

        // ล้างค่าเก่า
        facilitySelect.innerHTML = '<option value="">-- เลือกอุปกรณ์ (ถ้ามี) --</option>';

        if (roomNumber) {
            const filteredOptions = allFacilityOptions.filter(opt => opt.getAttribute('data-room') === roomNumber);
            filteredOptions.forEach(opt => facilitySelect.appendChild(opt.cloneNode(true)));
        }
    }

    roomSelect.addEventListener('change', filterFacilities);

    // ทำงานทันทีเมื่อโหลดหน้า (กรณีมีค่า old หรือส่งมาจากหน้า Facility)
    if (roomSelect.value) {
        const currentFacilityId = "<?php echo e(old('facility_id', $facility->id ?? '')); ?>";
        filterFacilities();
        facilitySelect.value = currentFacilityId;
    }
});
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Rm1\resources\views/maintenances/create.blade.php ENDPATH**/ ?>