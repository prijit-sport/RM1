

<?php
    /** @var \Illuminate\Support\Collection<\App\Models\Room> $rooms */
?>

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
                        <div class="alert alert-danger border-0 shadow-sm">
                            <strong><i class="bi bi-exclamation-triangle me-1"></i> กรุณาตรวจสอบข้อมูล:</strong>
                            <ul class="mb-0 mt-2">
                                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li><?php echo e($error); ?></li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form action="<?php echo e(route('maintenances.store')); ?>" method="POST">
                        <?php echo csrf_field(); ?>

                        
                        <input type="hidden" name="status" value="pending">

                        
                        <div class="mb-4">
                            <label class="form-label fw-bold">ห้องพัก <span class="text-danger">*</span></label>
                            <select name="room_id" class="form-select <?php $__errorArgs = ['room_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                                <option value="">-- เลือกห้องพัก --</option>
                                <?php $__currentLoopData = $rooms ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $room): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($room->id); ?>" <?php echo e(old('room_id') == $room->id ? 'selected' : ''); ?>>
                                        ห้อง <?php echo e($room->room_number); ?> - <?php echo e($room->room_type); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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

                        
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">ประเภทงานซ่อม <span class="text-danger">*</span></label>
                                
                                <select name="maintenance_type" class="form-select <?php $__errorArgs = ['maintenance_type'];
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
                                <select id="priority_select" class="form-select">
                                    <option value="ทั่วไป">ทั่วไป</option>
                                    <option value="ด่วน">ด่วน</option>
                                    <option value="ด่วนมาก">ด่วนมาก</option>
                                </select>
                            </div>
                        </div>

                        
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <label class="form-label fw-bold">วันที่แจ้งซ่อม <span class="text-danger">*</span></label>
                                
                                <input type="date" name="request_date"
                                       class="form-control <?php $__errorArgs = ['request_date'];
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
                        </div>

                        
                        <div class="mb-4">
                            <label class="form-label fw-bold">รายละเอียดปัญหา <span class="text-danger">*</span></label>
                            <textarea name="description"
                                      class="form-control <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                      rows="4" placeholder="กรุณาระบุรายละเอียดปัญหาที่พบ..." required><?php echo e(old('description')); ?></textarea>
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
                            <label class="form-label fw-bold">หมายเหตุเพิ่มเติม</label>
                            <textarea name="notes" class="form-control" rows="2" placeholder="ข้อมูลเพิ่มเติมสำหรับช่าง..."><?php echo e(old('notes')); ?></textarea>
                        </div>

                        
                        <div class="d-flex justify-content-between mt-5">
                            <a href="<?php echo e(route('maintenances.index')); ?>" class="btn btn-light px-4">
                                <i class="bi bi-arrow-left"></i> ยกเลิก
                            </a>
                            <button type="submit" class="btn btn-primary px-5">
                                <i class="bi bi-check-circle me-1"></i> ยืนยันการแจ้งซ่อม
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // สคริปต์สำหรับนำความเร่งด่วนไปใส่ในรายละเอียดอัตโนมัติ
    document.querySelector('form').addEventListener('submit', function(e) {
        const priority = document.getElementById('priority_select').value;
        const descBox = document.querySelector('textarea[name="description"]');
        if (priority && !descBox.value.includes('[ความเร่งด่วน:')) {
            descBox.value = `[ความเร่งด่วน: ${priority}] ` + descBox.value;
        }
    });
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Rm1\resources\views/maintenances/create.blade.php ENDPATH**/ ?>