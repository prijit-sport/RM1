

<?php $__env->startSection('content'); ?>
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-warning text-dark py-3">
                    <h5 class="mb-0"><i class="bi bi-calendar-check"></i> แก้ไขการจอง</h5>
                </div>
                <div class="card-body">
                    <form action="<?php echo e(route('bookings.update', $booking->id)); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PUT'); ?>
                        
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <label class="form-label required">ผู้เช่า</label>
                                <select name="guest_id" class="form-select <?php $__errorArgs = ['guest_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                                    <option value="">-- เลือกผู้เช่า --</option>
                                    <?php $__currentLoopData = $guests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $guest): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($guest->id); ?>" <?php echo e($booking->guest_id == $guest->id ? 'selected' : ''); ?>>
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

                        <div class="row mb-4">
                            <div class="col-md-12">
                                <label class="form-label required">ห้องพัก</label>
                                <select name="room_id" class="form-select <?php $__errorArgs = ['room_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                                    <option value="">-- เลือกห้องพัก --</option>
                                    <?php $__currentLoopData = $rooms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $room): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($room->id); ?>" <?php echo e($booking->room_id == $room->id ? 'selected' : ''); ?>>
                                            <?php echo e($room->room_number); ?> - <?php echo e($room->room_type); ?> (<?php echo e(number_format($room->price_per_month)); ?> บาท/เดือน)
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
                            <div class="col-md-12">
                                <label class="form-label required">สถานะ</label>

                                <select name="status" class="form-select <?php $__errorArgs = ['status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>

                                    <option value="pending" <?php echo e($booking->status == 'pending' ? 'selected' : ''); ?>>รอยืนยัน</option>
                                    <option value="confirmed" <?php echo e($booking->status == 'confirmed' ? 'selected' : ''); ?>>ยืนยันแล้ว</option>
                                    <option value="checked_in" <?php echo e($booking->status == 'checked_in' ? 'selected' : ''); ?>>เช็คอินแล้ว</option>
                                    <option value="checked_out" <?php echo e($booking->status == 'checked_out' ? 'selected' : ''); ?>>เช็คเอาท์แล้ว</option>
                                    <option value="cancelled" <?php echo e($booking->status == 'cancelled' ? 'selected' : ''); ?>>ยกเลิก</option>
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
                            <label class="form-label">หมายเหตุ</label>
                            <textarea name="notes" class="form-control" rows="3"><?php echo e(old('notes', $booking->notes)); ?></textarea>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="<?php echo e(route('bookings.index')); ?>" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> ยกเลิก
                            </a>
                            <button type="submit" class="btn btn-warning">
                                <i class="bi bi-check-circle"></i> บันทึกการแก้ไข
                            </button>
                        </div>
                    </form>
                </div>
        </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Rm1\resources\views/bookings/edit.blade.php ENDPATH**/ ?>