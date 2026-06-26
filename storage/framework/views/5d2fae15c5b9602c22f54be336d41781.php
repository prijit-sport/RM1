<?php $__env->startSection('title', 'จัดการบทบาท'); ?>

<?php $__env->startSection('page-title', 'จัดการบทบาท'); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">รายการบทบาท</h4>
    <div class="d-flex gap-2">
        <a href="<?php echo e(route('roles.export')); ?>" class="btn btn-success">
            <i class="bi bi-download me-1"></i><?php echo e(__("ui.export")); ?>

        </a>
        <a href="<?php echo e(route('roles.create')); ?>" class="btn btn-primary-custom">
            <i class="bi bi-plus-lg me-1"></i>เพิ่มบทบาท
        </a>
    </div>
</div>

<!-- Roles Table -->
<div class="table-card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>ชื่อบทบาท</th>
                    <th>รายละเอียด</th>
                    <th>การกระทำ</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><strong><?php echo e($role->name); ?></strong></td>
                        <td><?php echo e($role->description ?? '-'); ?></td>
                        <td>
                            <div class="d-flex gap-2">
                                <a href="<?php echo e(route('roles.show', $role)); ?>" class="btn btn-sm btn-outline-info">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="<?php echo e(route('roles.edit', $role)); ?>" class="btn btn-sm btn-outline-warning">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <?php if($role->name !== 'Admin'): ?>
                                <form action="<?php echo e(route('roles.destroy', $role)); ?>" method="POST" class="d-inline">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('แน่ใจหรือไม่ที่จะลบ?')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="3" class="text-center py-4">
                            <i class="bi bi-inbox fs-1 text-muted"></i>
                            <p class="text-muted mt-2">ไม่มีข้อมูลบทบาท</p>
                            <a href="<?php echo e(route('roles.create')); ?>" class="btn btn-primary-custom mt-2">เพิ่มบทบาท</a>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Pagination -->
<?php if($roles->hasPages()): ?>
    <div class="d-flex justify-content-center mt-4">
        <?php echo e($roles->links('pagination::bootstrap-5')); ?>

    </div>
<?php endif; ?>
<?php $__env->stopSection(); ?>




<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\Rm1\resources\views/roles/index.blade.php ENDPATH**/ ?>