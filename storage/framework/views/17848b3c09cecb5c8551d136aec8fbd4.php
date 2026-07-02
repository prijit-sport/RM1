

<?php $__env->startSection('title', 'มิเตอร์อ่านค่า'); ?>

<?php $__env->startSection('page-title', 'มิเตอร์อ่านค่า'); ?>

<?php $__env->startSection('content'); ?>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">มิเตอร์อ่านค่า</h4>
            <p class="text-muted mb-0">
                ห้องพัก <strong><?php echo e($meter->room->room_number ?? '-'); ?></strong>
                <?php if($meter->type === 'water'): ?>
                    <span class="badge bg-info">น้ำ</span>
                <?php else: ?>
                    <span class="badge bg-warning">ไฟ</span>
                <?php endif; ?>
                | เลขมิเตอร์: <strong><?php echo e($meter->meter_number); ?></strong>
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="<?php echo e(route('meters.index')); ?>" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>ย้อนกลับ
            </a>
            <a href="<?php echo e(route('meters.readings.export', $meter) . '?' . http_build_query(request()->only(['date', 'search']))); ?>"
                class="btn btn-success">
                <i class="bi bi-download me-1"></i><?php echo e(__('ui.export')); ?>

            </a>
            <a href="<?php echo e(route('meters.readings.create', $meter)); ?>" class="btn btn-primary-custom">
                <i class="bi bi-plus-lg me-1"></i>เพิ่มการอ่านค่า
            </a>
        </div>
    </div>

    <?php if(isset($billing)): ?>
        <div class="card mb-4">
            <div class="card-body">
                <div class="row gy-2">
                    <?php if($billing['has_reading']): ?>
                        <div class="col-md-3">
                            <div class="text-muted small">ค่าน้ำ/ไฟก่อนหน้า</div>
                            <div><?php echo e(number_format($billing['previous'], 2)); ?> (<?php echo e($billing['previous_date'] ?? '-'); ?>)</div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-muted small">ค่ายอดปัจจุบัน</div>
                            <div><?php echo e(number_format($billing['current'], 2)); ?> (<?php echo e($billing['current_date'] ?? '-'); ?>)</div>
                        </div>
                        <div class="col-md-2">
                            <div class="text-muted small">การใช้งาน</div>
                            <div><?php echo e(number_format($billing['usage'], 2)); ?> <?php echo e($meter->unit ?? ''); ?></div>
                        </div>
                        <div class="col-md-2">
                            <div class="text-muted small">อัตรา</div>
                            <div><?php echo e(number_format($billing['rate'], 2)); ?> บาท/หน่วย</div>
                        </div>
                        <div class="col-md-2">
                            <div class="text-muted small">ยอดรวม</div>
                            <div class="fw-bold"><?php echo e(number_format($billing['total'], 2)); ?> บาท</div>
                        </div>
                    <?php else: ?>
                        <div class="col-12 text-muted">ไม่พบข้อมูลการคำนวณค่าน้ำ/ไฟ</div>
                    <?php endif; ?>
                </div>
                <div class="text-muted small mt-2">
                    สูตรคำนวณ: <?php echo e($billing['formula'] ?? 'Usage × Rate + Tax'); ?>

                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Search and Filter -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="<?php echo e(route('meters.readings.index', $meter)); ?>" class="row g-3">
                <div class="col-md-6">
                    <input type="date" name="date" class="form-control" value="<?php echo e(request('date')); ?>">
                </div>
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="ค้นหา..."
                        value="<?php echo e(request('search')); ?>">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search me-1"></i><?php echo e(__('ui.search')); ?>

                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Readings Table -->
    <div class="table-card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>วันที่อ่านค่า</th>
                        <th>ค่าน้ำ/ไฟ</th>
                        <th>ผู้บันทึก</th>
                        <th>หมายเหตุ</th>
                        <th>การทำงาน</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $readings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $reading): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><?php echo e($reading->reading_date?->format('d/m/Y') ?? '-'); ?></td>
                            <td><strong><?php echo e(number_format((float) $reading->reading_value, 2)); ?></strong></td>
                            <td><?php echo e($reading->recordedBy->name ?? '-'); ?></td>
                            <td><?php echo e($reading->notes ?? '-'); ?></td>
                            <td>
                                <div class="d-flex gap-2">
                                    <a href="<?php echo e(route('meters.readings.edit', [$meter, $reading])); ?>"
                                        class="btn btn-sm btn-outline-warning">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="<?php echo e(route('meters.readings.destroy', [$meter, $reading])); ?>"
                                        method="POST" class="d-inline">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="btn btn-sm btn-outline-danger"
                                            onclick="return confirm('ยืนยันการลบรายการ?')">
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
                                <p class="text-muted mt-2">ไม่พบข้อมูลการอ่านค่า</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <?php if($readings->hasPages()): ?>
        <div class="d-flex justify-content-center mt-4">
            <?php echo e($readings->links('pagination::bootstrap-5')); ?>

        </div>
    <?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\Rm1\resources\views/meter_readings/index.blade.php ENDPATH**/ ?>