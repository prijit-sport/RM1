
 
<?php $__env->startSection('title', 'จัดการแขก'); ?>
 
<?php $__env->startSection('page-title', 'จัดการแขก'); ?>
 
<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">รายการแขก</h4>
    <div class="d-flex gap-2">
        <a href="<?php echo e(route('guests.export')); ?>" class="btn btn-success">
            <i class="bi bi-download me-1"></i><?php echo e(__("ui.export")); ?>

        </a>
        <a href="<?php echo e(route('guests.bulk-create')); ?>" class="btn btn-outline-primary">
            <i class="bi bi-plus-circle me-1"></i>เพิ่มหลายคน
        </a>
        <a href="<?php echo e(route('guests.create')); ?>" class="btn btn-primary-custom">
            <i class="bi bi-plus-lg me-1"></i>เพิ่มแขกใหม่
        </a>
    </div>
</div>
 

<?php if(session('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i><?php echo e(session('success')); ?>

        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>
 
<!-- Search -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="<?php echo e(route('guests.index')); ?>" class="row g-3">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="ค้นหาชื่อ, อีเมล, เบอร์โทร..." value="<?php echo e(request('search')); ?>">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search me-1"></i><?php echo e(__("ui.search")); ?>

                </button>
            </div>
            <div class="col-md-2">
                <a href="<?php echo e(route('guests.index')); ?>" class="btn btn-outline-secondary w-100">
                    รีเซ็ต
                </a>
            </div>
        </form>
    </div>
</div>
 
<!-- Guests Table -->
<div class="table-card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>ชื่อ-นามสกุล</th>
                    <th>อีเมล</th>
                    <th>เบอร์โทร</th>
                    <th>เลขบัตรประจำตัว</th>
                    <th>การกระทำ</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $guests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $guest): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td>
                            <strong><?php echo e($guest->first_name); ?> <?php echo e($guest->last_name); ?></strong>
                        </td>
                        <td><?php echo e($guest->email ?? '-'); ?></td>
                        <td><?php echo e($guest->phone ?? '-'); ?></td>
                        <td><?php echo e($guest->id_number ?? '-'); ?></td>
                        <td>
                            <div class="d-flex gap-2">
                                <a href="<?php echo e(route('guests.show', $guest)); ?>" class="btn btn-sm btn-outline-info" title="ดู">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="<?php echo e(route('guests.edit', $guest)); ?>" class="btn btn-sm btn-outline-warning" title="แก้ไข">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="<?php echo e(route('guests.destroy', $guest)); ?>" method="POST" class="d-inline">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="ลบ"
                                            onclick="return confirm('แน่ใจหรือไม่ที่จะลบแขกนี้?')">
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
                            <p class="text-muted mt-2">ไม่มีข้อมูลแขก</p>
                            <div class="d-flex gap-2 justify-content-center flex-wrap mt-2">
                                <a href="<?php echo e(route('guests.bulk-create')); ?>" class="btn btn-outline-primary">เพิ่มหลายคน</a>
                                <a href="<?php echo e(route('guests.create')); ?>" class="btn btn-primary-custom">เพิ่มแขกใหม่</a>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
 
<!-- Pagination -->
<?php if($guests->hasPages()): ?>
    <div class="d-flex justify-content-center mt-4">
        <?php echo e($guests->links('pagination::bootstrap-5')); ?>

    </div>
<?php endif; ?>
<?php $__env->stopSection(); ?>
 
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Rm1\resources\views/guests/index.blade.php ENDPATH**/ ?>