
 
<?php $__env->startSection('title', 'จัดการการจอง'); ?>
 
<?php $__env->startSection('page-title', 'จัดการการจอง'); ?>
 
<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">รายการการจอง</h4>
    <div class="d-flex gap-2">
        <a href="<?php echo e(route('bookings.export')); ?>" class="btn btn-success">
            <i class="bi bi-download me-1"></i><?php echo e(__("ui.export")); ?>

        </a>
        <a href="<?php echo e(route('bookings.create')); ?>" class="btn btn-primary-custom">
            <i class="bi bi-plus-lg me-1"></i>เพิ่มการจองใหม่
        </a>
    </div>
</div>
 
<!-- Search and Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="<?php echo e(route('bookings.index')); ?>" class="row g-3">
            <div class="col-md-3">
                <input type="text" name="search" class="form-control" placeholder="ค้นหาชื่อแขก..." value="<?php echo e(request('search')); ?>">
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">ทุกสถานะ</option>
                    <option value="pending" <?php echo e(request('status') == 'pending' ? 'selected' : ''); ?>>รอการยืนยัน</option>
                    <option value="confirmed" <?php echo e(request('status') == 'confirmed' ? 'selected' : ''); ?>>ยืนยันแล้ว</option>
                    <option value="checked_in" <?php echo e(request('status') == 'checked_in' ? 'selected' : ''); ?>>เช็คอินแล้ว</option>
                    <option value="checked_out" <?php echo e(request('status') == 'checked_out' ? 'selected' : ''); ?>>เช็คเอาท์แล้ว</option>
                    <option value="cancelled" <?php echo e(request('status') == 'cancelled' ? 'selected' : ''); ?>>ยกเลิก</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search me-1"></i><?php echo e(__("ui.search")); ?>

                </button>
            </div>
        </form>
    </div>
</div>
 
<!-- Bookings Table -->
<div class="table-card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>ห้อง</th>
                    <th>แขก</th>
                    <th>ราคา</th>
                    <th>สถานะ</th>
                    <th>การกระทำ</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $bookings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $booking): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td>
                            <strong>
                                <?php if($booking->room): ?>
                                    #<?php echo e($booking->room->room_number); ?>

                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </strong>
                        </td>
                        <td>
                            <?php if($booking->guest): ?>
                                <?php echo e($booking->guest->first_name); ?> <?php echo e($booking->guest->last_name); ?>

                            <?php else: ?>
                                <span class="text-muted fst-italic">ไม่ระบุผู้เช่า</span>
                            <?php endif; ?>
                        </td>
                        <td>฿<?php echo e(number_format($booking->total_price ?? 0, 2)); ?></td>
                        <td>
                            <?php
                                $statusClasses = [
                                    'pending' => 'bg-warning',
                                    'confirmed' => 'bg-info',
                                    'checked_in' => 'bg-success',
                                    'checked_out' => 'bg-secondary',
                                    'cancelled' => 'bg-danger',
                                ];
                            ?>
                            <span class="badge <?php echo e($statusClasses[$booking->status] ?? 'bg-light text-dark'); ?>">
                                <?php echo e(enum_th('booking_status', $booking->status)); ?>

                            </span>
                        </td>
                        <td>
                            <div class="d-flex gap-2">
                                <a href="<?php echo e(route('bookings.show', $booking)); ?>" class="btn btn-sm btn-outline-info">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="<?php echo e(route('bookings.edit', $booking)); ?>" class="btn btn-sm btn-outline-warning">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <?php if($booking->status == 'pending'): ?>
                                    <form action="<?php echo e(route('bookings.confirm', $booking)); ?>" method="POST" class="d-inline">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" class="btn btn-sm btn-outline-success" onclick="return confirm('ยืนยันการจองนี้?')">
                                            <i class="bi bi-check-lg"></i>
                                        </button>
                                    </form>
                                <?php endif; ?>
                                <?php if($booking->status != 'cancelled' && $booking->status != 'checked_out'): ?>
                                    <form action="<?php echo e(route('bookings.cancel', $booking)); ?>" method="POST" class="d-inline">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('ยกเลิกการจองนี้?')">
                                            <i class="bi bi-x-lg"></i>
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="5" class="text-center py-4">
                            <i class="bi bi-inbox fs-1 text-muted"></i>
                            <p class="text-muted mt-2">ไม่มีข้อมูลการจอง</p>
                            <a href="<?php echo e(route('bookings.create')); ?>" class="btn btn-primary-custom mt-2">เพิ่มการจองใหม่</a>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
 
<!-- Pagination -->
<?php if($bookings->hasPages()): ?>
    <div class="d-flex justify-content-center mt-4">
        <?php echo e($bookings->links('pagination::bootstrap-5')); ?>

    </div>
<?php endif; ?>
<?php $__env->stopSection(); ?>
 
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Rm1\resources\views/bookings/index.blade.php ENDPATH**/ ?>