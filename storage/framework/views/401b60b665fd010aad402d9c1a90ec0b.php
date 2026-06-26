<?php $__env->startSection('title', 'ตั้งค่า'); ?>
<?php $__env->startSection('page-title', 'ตั้งค่า'); ?>

<?php $__env->startSection('content'); ?>
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white">
                <h5 class="mb-0">การตั้งค่าระบบ</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="<?php echo e(route('settings.update')); ?>">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PUT'); ?>

                    <div class="mb-3">
                        <label for="locale" class="form-label">ภาษา</label>
                        <select id="locale" name="locale" class="form-select <?php $__errorArgs = ['locale'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                            <option value="th" <?php if(old('locale', $settings['locale']) === 'th'): echo 'selected'; endif; ?>>ไทย</option>
                            <option value="en" <?php if(old('locale', $settings['locale']) === 'en'): echo 'selected'; endif; ?>>English</option>
                        </select>
                        <?php $__errorArgs = ['locale'];
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
                        <label for="items_per_page" class="form-label">จำนวนรายการต่อหน้า</label>
                        <select id="items_per_page" name="items_per_page" class="form-select <?php $__errorArgs = ['items_per_page'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                            <?php $__currentLoopData = [10, 20, 50, 100]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $size): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($size); ?>" <?php if((int) old('items_per_page', $settings['items_per_page']) === $size): echo 'selected'; endif; ?>><?php echo e($size); ?> รายการ</option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <?php $__errorArgs = ['items_per_page'];
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

                    <div class="form-check form-switch mb-4">
                        <input class="form-check-input" type="checkbox" role="switch" id="compact_mode" name="compact_mode" value="1"
                               <?php if(old('compact_mode', $settings['compact_mode'])): echo 'checked'; endif; ?>>
                        <label class="form-check-label" for="compact_mode">เปิดโหมดหน้าจอกระชับ (Compact)</label>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary-custom">บันทึกการตั้งค่า</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\Rm1\resources\views/profile/settings.blade.php ENDPATH**/ ?>