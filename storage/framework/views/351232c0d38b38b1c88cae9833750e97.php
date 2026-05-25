
 
<?php $__env->startSection('content'); ?>
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white py-3">
                    <h5 class="mb-0"><i class="bi bi-door-open"></i> เพิ่มห้องพักใหม่</h5>
                </div>
                <div class="card-body">
 
                    
                    <?php if($errors->any()): ?>
                        <div class="alert alert-danger">
                            <strong><i class="bi bi-exclamation-triangle me-1"></i>กรุณาตรวจสอบข้อมูล:</strong>
                            <ul class="mb-0 mt-2">
                                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li><?php echo e($error); ?></li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        </div>
                    <?php endif; ?>
 
                    <form action="<?php echo e(route('rooms.store')); ?>" method="POST">
                        <?php echo csrf_field(); ?>
 
                        
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label required">หมายเลขห้อง <span class="text-danger">*</span></label>
                                <input type="text" name="room_number"
                                       class="form-control <?php $__errorArgs = ['room_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                       value="<?php echo e(old('room_number')); ?>"
                                       placeholder="เช่น 101, A-201"
                                       required>
                                <?php $__errorArgs = ['room_number'];
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
                                <label class="form-label required">ประเภทห้อง <span class="text-danger">*</span></label>
                                <select name="room_type" id="room_type_select"
                                        class="form-select <?php $__errorArgs = ['room_type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                                    <option value="">-- เลือกประเภท --</option>
                                    <option value="fan"              data-price="2800" <?php echo e(old('room_type') == 'fan' ? 'selected' : ''); ?>>พัดลม</option>
                                    <option value="air_conditioning" data-price="3500" <?php echo e(old('room_type') == 'air_conditioning' ? 'selected' : ''); ?>>แอร์</option>
                                </select>
                                <?php $__errorArgs = ['room_type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                <small class="text-muted">เปลี่ยนประเภทห้อง ราคาจะอัปเดตอัตโนมัติ</small>
                            </div>
                        </div>
 
                        
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label required">ค่าเช่าต่อเดือน (บาท) <span class="text-danger">*</span></label>
                                <input type="number" name="price_per_month" id="price_input"
                                       class="form-control <?php $__errorArgs = ['price_per_month'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                       value="<?php echo e(old('price_per_month')); ?>"
                                       required min="0" step="100">
                                <?php $__errorArgs = ['price_per_month'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                <small class="text-muted" id="price_hint" style="display:none;">
                                    <i class="bi bi-info-circle text-success"></i> ราคาถูกเติมอัตโนมัติ (แก้ได้ถ้าต้องการ)
                                </small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label required">ความจุ (คน) <span class="text-danger">*</span></label>
                                <input type="number" name="capacity"
                                       class="form-control <?php $__errorArgs = ['capacity'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                       value="<?php echo e(old('capacity', 1)); ?>"
                                       required min="1">
                                <?php $__errorArgs = ['capacity'];
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
 
                        
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label required">ชั้น <span class="text-danger">*</span></label>
                                <input type="number" name="floor"
                                       class="form-control <?php $__errorArgs = ['floor'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                       value="<?php echo e(old('floor')); ?>"
                                       required min="1">
                                <?php $__errorArgs = ['floor'];
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
                                <label class="form-label required">สถานะ <span class="text-danger">*</span></label>
                                <select name="status" class="form-select <?php $__errorArgs = ['status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                                    <option value="available"   <?php echo e(old('status', 'available') == 'available'   ? 'selected' : ''); ?>>ว่าง</option>
                                    <option value="occupied"    <?php echo e(old('status') == 'occupied'    ? 'selected' : ''); ?>>มีผู้เช่า</option>
                                    <option value="maintenance" <?php echo e(old('status') == 'maintenance' ? 'selected' : ''); ?>>ซ่อมบำรุง</option>
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
                        </div>
 
                        
                        <div class="mb-4">
                            <label class="form-label">รายละเอียด</label>
                            <textarea name="description" class="form-control" rows="3"
                                      placeholder="รายละเอียดเพิ่มเติม (ถ้ามี)"><?php echo e(old('description')); ?></textarea>
                        </div>
 
                        
                        <div class="d-flex justify-content-between">
                            <a href="<?php echo e(route('rooms.index')); ?>" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> ยกเลิก
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> บันทึกข้อมูล
                            </button>
                        </div>
                    </form>
 
                </div>
            </div>
        </div>
    </div>
</div>
 
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const roomTypeEl = document.getElementById('room_type_select');
        const priceEl    = document.getElementById('price_input');
        const priceHint  = document.getElementById('price_hint');
 
        if (!roomTypeEl || !priceEl) return;
 
        // ── เมื่อเปลี่ยนประเภทห้อง → เติมราคาอัตโนมัติ ──
        roomTypeEl.addEventListener('change', function () {
            const selectedOption = this.options[this.selectedIndex];
            const price = selectedOption.getAttribute('data-price');
 
            if (price) {
                priceEl.value = price;
 
                // แสดง hint + ไฮไลต์ช่องราคา
                priceHint.style.display = 'block';
                priceEl.style.transition = 'background-color 0.6s';
                priceEl.style.backgroundColor = '#fef3c7';
                setTimeout(() => {
                    priceEl.style.backgroundColor = '';
                }, 1000);
            } else {
                // ถ้าเลือก "-- เลือกประเภท --" ก็เคลียร์
                priceEl.value = '';
                priceHint.style.display = 'none';
            }
        });
 
        // ── ถ้าผู้ใช้แก้ราคาเองหลังจาก auto-fill → ซ่อน hint ──
        priceEl.addEventListener('input', function () {
            priceHint.style.display = 'none';
        });
    });
</script>
<?php $__env->stopSection(); ?>
 
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Rm1\resources\views/rooms/create.blade.php ENDPATH**/ ?>