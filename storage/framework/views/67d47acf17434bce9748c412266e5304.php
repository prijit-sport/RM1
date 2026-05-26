
 
<?php $__env->startSection('page-title', 'จัดการผู้เช่า'); ?>
 
<?php $__env->startSection('content'); ?>
<div class="container-fluid py-4">
 
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0 text-dark">จัดการผู้เช่า</h4>
        <div class="d-flex flex-wrap gap-2">
            <a href="<?php echo e(route('guests.export')); ?>" class="btn btn-success btn-sm">
                <i class="bi bi-download me-1"></i>Export
            </a>
            <a href="<?php echo e(route('guests.bulk-create')); ?>" class="btn btn-outline-primary btn-sm">
                <i class="bi bi-plus-circle me-1"></i>เพิ่มหลายคน
            </a>
            <a href="<?php echo e(route('guests.create')); ?>" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-lg me-1"></i>เพิ่มผู้เช่าใหม่
            </a>
        </div>
    </div>
 
    <div class="card border-0 shadow-sm rounded-3">
 
        
        <div class="card-header bg-white border-bottom py-3">
            <form action="<?php echo e(route('guests.index')); ?>" method="GET">
                <div class="row g-2">
                    <div class="col-md-9">
                        <div class="input-group">
                            <span class="input-group-text bg-transparent border-end-0">
                                <i class="bi bi-search"></i>
                            </span>
                            <input type="text" name="search"
                                class="form-control border-start-0"
                                placeholder="ค้นหาชื่อ, อีเมล, เบอร์โทร, เลขบัตร..."
                                value="<?php echo e(request('search')); ?>">
                        </div>
                    </div>
                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-grow-1">ค้นหา</button>
                        <?php if(request('search')): ?>
                            <a href="<?php echo e(route('guests.index')); ?>" class="btn btn-light border">ล้าง</a>
                        <?php endif; ?>
                    </div>
                </div>
            </form>
        </div>
 
        <div class="card-body p-0">
            <?php if($guests->count() > 0): ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light" style="font-size:0.875rem;">
                        <tr>
                            <th class="ps-4 py-3">ชื่อ-นามสกุล</th>
                            <th class="py-3">เบอร์โทร</th>
                            <th class="py-3">เลขบัตร</th>
                            <th class="py-3">ห้องพัก</th>
                            <th class="py-3">สถานะ</th>
                            <th class="py-3 text-center pe-4">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody style="font-size:0.925rem;">
                        <?php $__currentLoopData = $guests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $guest): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $contract = $guest->contracts->where('status','active')->first();
                            $room     = $contract?->room;
                        ?>
                        <tr>
                            <td class="ps-4 py-3 fw-semibold text-dark">
                                <?php echo e($guest->full_name); ?>

                            </td>
                            <td class="py-3 text-muted"><?php echo e($guest->phone ?? '-'); ?></td>
                            <td class="py-3 text-muted" style="font-size:0.85rem;">
                                <?php echo e($guest->id_number ?? '-'); ?>

                            </td>
                            <td class="py-3">
                                <?php if($room): ?>
                                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary-subtle">
                                        <i class="bi bi-door-closed me-1"></i><?php echo e($room->room_number); ?>

                                    </span>
                                    <?php if($room->zone): ?>
                                        <small class="text-muted ms-1">โซน <?php echo e($room->zone); ?></small>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="text-muted small">— ไม่มีห้อง</span>
                                <?php endif; ?>
                            </td>
                            <td class="py-3">
                                <?php if($contract): ?>
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success-subtle">
                                        <i class="bi bi-check-circle me-1"></i>ยืนยันแล้ว
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary-subtle">
                                        <i class="bi bi-dash-circle me-1"></i>ว่าง
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="py-3 text-center pe-4">
                                <div class="btn-group btn-group-sm">
                                    <a href="<?php echo e(route('guests.show', $guest->id)); ?>"
                                       class="btn btn-outline-info" title="ดูรายละเอียด">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="<?php echo e(route('guests.edit', $guest->id)); ?>"
                                       class="btn btn-outline-warning" title="แก้ไข">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="<?php echo e(route('guests.destroy', $guest->id)); ?>"
                                          method="POST" class="d-inline"
                                          onsubmit="return confirm('ยืนยันการลบผู้เช่าท่านนี้?')">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit"
                                                class="btn btn-outline-danger" title="ลบ">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
 
            
            <?php if($guests->hasPages()): ?>
            <div class="d-flex justify-content-between align-items-center px-4 py-3 border-top bg-light">
                <small class="text-muted">
                    แสดง <?php echo e($guests->firstItem()); ?>–<?php echo e($guests->lastItem()); ?>

                    จาก <?php echo e($guests->total()); ?> คน
                </small>
                <?php echo e($guests->links('pagination::bootstrap-5')); ?>

            </div>
            <?php endif; ?>
 
            <?php else: ?>
            <div class="text-center py-5 text-muted">
                <i class="bi bi-people fs-1 d-block mb-3 opacity-25"></i>
                <p>ไม่พบข้อมูลผู้เช่า</p>
                <a href="<?php echo e(route('guests.create')); ?>" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-lg me-1"></i>เพิ่มผู้เช่าใหม่
                </a>
            </div>
            <?php endif; ?>
        </div>
 
    </div>
</div>
<?php $__env->stopSection(); ?>
 
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Rm1\resources\views/guests/index.blade.php ENDPATH**/ ?>