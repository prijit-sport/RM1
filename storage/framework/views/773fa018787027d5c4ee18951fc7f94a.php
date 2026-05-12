<?php $__env->startSection('title', 'จัดการสิ่งอำนวยความสะดวก'); ?>

<?php $__env->startSection('page-title', 'จัดการสิ่งอำนวยความสะดวก'); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">รายการสิ่งอำนวยความสะดวก</h4>
    <div class="d-flex gap-2">
        <a href="<?php echo e(route('facilities.export')); ?>" class="btn btn-success">
            <i class="bi bi-download me-1"></i><?php echo e(__("ui.export")); ?>

        </a>
        <a href="<?php echo e(route('facilities.create')); ?>" class="btn btn-primary-custom">
            <i class="bi bi-plus-lg me-1"></i>เพิ่มสิ่งอำนวยความสะดวก
        </a>
    </div>
</div>

<!-- Search and Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="<?php echo e(route('facilities.index')); ?>" class="row g-3">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="ค้นหาชื่อ..." value="<?php echo e(request('search')); ?>">
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">ทุกสถานะ</option>
                    <option value="active" <?php echo e(request('status') == 'active' ? 'selected' : ''); ?>>ใช้งาน</option>
                    <option value="inactive" <?php echo e(request('status') == 'inactive' ? 'selected' : ''); ?>>ไม่ใช้งาน</option>
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

<!-- Facilities Table -->
<div class="table-card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>ชื่อ</th>
                    <th>ประเภท</th>
                    <th>ที่ตั้ง</th>
                    <th>สถานะ</th>
                    <th>การกระทำ</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $facilities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $facility): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><strong><?php echo e($facility->name); ?></strong></td>
                        <td><?php echo e($facility->type); ?></td>
                        <td><?php echo e($facility->location); ?></td>
                        <td>
                            <?php
                                $statusClasses = [
                                    'active' => 'bg-success',
                                    'inactive' => 'bg-secondary',
                                ];
                            ?>
                            <span class="badge <?php echo e($statusClasses[$facility->status] ?? ''); ?>">
                                <?php echo e($facility->status == 'active' ? 'ใช้งาน' : 'ไม่ใช้งาน'); ?>

                            </span>
                        </td>
                        <td>
                            <div class="d-flex gap-2">
                                <a href="<?php echo e(route('facilities.show', $facility)); ?>" class="btn btn-sm btn-outline-info">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="<?php echo e(route('facilities.edit', $facility)); ?>" class="btn btn-sm btn-outline-warning">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="<?php echo e(route('facilities.destroy', $facility)); ?>" method="POST" class="d-inline">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('แน่ใจหรือไม่ที่จะลบ?')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="5" class="text-center py-4">
                            <i class="bi bi-inbox fs-1 text-muted"></i>
                            <p class="text-muted mt-2">ไม่มีข้อมูลสิ่งอำนวยความสะดวก</p>
                            <a href="<?php echo e(route('facilities.create')); ?>" class="btn btn-primary-custom mt-2">เพิ่มสิ่งอำนวยความสะดวก</a>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Pagination -->
<?php if($facilities->hasPages()): ?>
    <div class="d-flex justify-content-center mt-4">
        <?php echo e($facilities->links('pagination::bootstrap-5')); ?>

    </div>
<?php endif; ?>
<?php $__env->stopSection(); ?>




<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Rm1\resources\views/facilities/index.blade.php ENDPATH**/ ?>