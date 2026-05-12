<?php $__env->startSection('title', 'จัดการใบแจ้งหนี้'); ?>

<?php $__env->startSection('page-title', 'จัดการใบแจ้งหนี้'); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">รายการใบแจ้งหนี้</h4>
    <div class="d-flex gap-2">
        <a href="<?php echo e(route('invoices.bulk-create')); ?>" class="btn btn-outline-primary">
            <i class="bi bi-plus-circle me-1"></i>สร้างหลายใบ
        </a>
        <a href="<?php echo e(route('invoices.export')); ?>" class="btn btn-success">
            <i class="bi bi-download me-1"></i><?php echo e(__("ui.export")); ?>

        </a>
        <a href="<?php echo e(route('invoices.create')); ?>" class="btn btn-primary-custom">
            <i class="bi bi-plus-lg me-1"></i>เพิ่มใบแจ้งหนี้
        </a>
    </div>
</div>

<!-- Search and Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="<?php echo e(route('invoices.index')); ?>" class="row g-3">
            <div class="col-md-3">
                <input type="text" name="search" class="form-control" placeholder="ค้นหาเลขที่ใบแจ้งหนี้..." value="<?php echo e(request('search')); ?>">
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">ทุกสถานะ</option>
                    <option value="draft" <?php echo e(request('status') == 'draft' ? 'selected' : ''); ?>>Draft</option>
                    <option value="sent" <?php echo e(request('status') == 'sent' ? 'selected' : ''); ?>>Sent</option>
                    <option value="paid" <?php echo e(request('status') == 'paid' ? 'selected' : ''); ?>>ชำระแล้ว</option>
                    <option value="overdue" <?php echo e(request('status') == 'overdue' ? 'selected' : ''); ?>>เกินกำหนด</option>
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

<!-- Invoices Table -->
<div class="table-card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>เลขที่ใบแจ้งหนี้</th>
                    <th>การจอง</th>
                    <th>ยอดรวม</th>
                    <th>วันที่ออก</th>
                    <th>วันครบกำหนด</th>
                    <th>สถานะ</th>
                    <th>การกระทำ</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $invoices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $invoice): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><strong><?php echo e($invoice->invoice_number); ?></strong></td>
                        <td>#<?php echo e($invoice->booking_id); ?></td>
                        <td>฿<?php echo e(number_format($invoice->total, 2)); ?></td>
                        <td><?php echo e(optional($invoice->issue_date)->format('d/m/Y')); ?></td>
                        <td><?php echo e(optional($invoice->due_date)->format('d/m/Y')); ?></td>
                        <td>
                            <?php
                                $statusClasses = [
                                    'draft' => 'bg-secondary',
                                    'sent' => 'bg-warning',
                                    'paid' => 'bg-success',
                                    'overdue' => 'bg-danger',
                                    'cancelled' => 'bg-secondary',
                                ];
                            ?>
                            <span class="badge <?php echo e($statusClasses[$invoice->status] ?? ''); ?>">
                                <?php echo e(enum_bi('invoice_status', $invoice->status, ucfirst($invoice->status))); ?>

                            </span>
                        </td>
                        <td>
                            <div class="d-flex gap-2">
                                <a href="<?php echo e(route('invoices.show', $invoice)); ?>" class="btn btn-sm btn-outline-info">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="<?php echo e(route('invoices.edit', $invoice)); ?>" class="btn btn-sm btn-outline-warning">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <?php if(in_array($invoice->status, ['sent', 'overdue'])): ?>
                                    <form action="<?php echo e(route('invoices.markAsPaid', $invoice)); ?>" method="POST" class="d-inline">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" class="btn btn-sm btn-outline-success" onclick="return confirm('ยืนยันการชำระเงิน?')">
                                            <i class="bi bi-check-lg"></i>
                                        </button>
                                    </form>
                                <?php endif; ?>
                                <a href="<?php echo e(route('invoices.pdf', $invoice)); ?>" class="btn btn-sm btn-outline-danger" target="_blank">
                                    <i class="bi bi-file-pdf"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="7" class="text-center py-4">
                            <i class="bi bi-inbox fs-1 text-muted"></i>
                            <p class="text-muted mt-2">ไม่มีข้อมูลใบแจ้งหนี้</p>
                            <a href="<?php echo e(route('invoices.create')); ?>" class="btn btn-primary-custom mt-2">เพิ่มใบแจ้งหนี้</a>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Pagination -->
<?php if($invoices->hasPages()): ?>
    <div class="d-flex justify-content-center mt-4">
        <?php echo e($invoices->links('pagination::bootstrap-5')); ?>

    </div>
<?php endif; ?>
<?php $__env->stopSection(); ?>






<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Rm1\resources\views/invoices/index.blade.php ENDPATH**/ ?>