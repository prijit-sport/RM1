
 
<?php $__env->startSection('title', 'รายละเอียดสัญญาเช่า'); ?>
 
<?php $__env->startSection('page-title', 'จัดการสัญญาเช่า'); ?>
 
<?php $__env->startSection('content'); ?>
<div class="row">
    <div class="col-12">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="<?php echo e(route('dashboard')); ?>"><i class="bi bi-house-door"></i> หน้าหลัก</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="<?php echo e(route('contracts.index')); ?>">สัญญาเช่า</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">รายละเอียด</li>
            </ol>
        </nav>
 
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0">
                <i class="bi bi-file-earmark-text me-2"></i>รายละเอียดสัญญาเช่า
            </h4>
            <div class="d-flex gap-2">
                <a href="<?php echo e(route('contracts.edit', $contract)); ?>" class="btn btn-warning">
                    <i class="bi bi-pencil me-1"></i>แก้ไข
                </a>
 
                <form method="POST" action="<?php echo e(route('contracts.destroy', $contract)); ?>" style="display:inline;" onsubmit="return confirm('คุณแน่ใจหรือไม่ที่จะลบสัญญานี้?');">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('DELETE'); ?>
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash me-1"></i>ลบ
                    </button>
                </form>
            </div>
        </div>
 
        <div class="row">
            <!-- Contract Details -->
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0">
                            <i class="bi bi-info-circle me-2"></i>ข้อมูลสัญญา
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label text-muted">เลขที่สัญญา</label>
                                <p class="fs-5 fw-bold"><?php echo e($contract->contract_number); ?></p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted">สถานะ</label>
                                <p>
                                    <?php
                                        $statusClasses = [
                                            'draft' => 'bg-secondary',
                                            'pending' => 'bg-warning text-dark',
                                            'active' => 'bg-success',
                                            'completed' => 'bg-info',
                                            'cancelled' => 'bg-danger',
                                        ];
                                        $statusLabels = [
                                            'draft' => 'ร่างสัญญา',
                                            'pending' => 'รออนุมัติ',
                                            'active' => 'ใช้งาน',
                                            'completed' => 'เสร็จสิ้น',
                                            'cancelled' => 'ยกเลิก',
                                        ];
                                    ?>
                                    <span class="badge <?php echo e($statusClasses[$contract->status] ?? 'bg-secondary'); ?> fs-6">
                                        <?php echo e($statusLabels[$contract->status] ?? $contract->status); ?>

                                    </span>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted">ห้องพัก</label>
                                <p class="fs-5">
                                    <?php if($contract->room): ?>
                                        <span class="badge bg-primary fs-6"><?php echo e($contract->room->room_number); ?></span>
                                        <small class="text-muted"><?php echo e($contract->room->room_type); ?></small>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted">ผู้เช่า</label>
                                <p class="fs-5">
                                    <?php if($contract->guest): ?>
                                        <?php echo e($contract->guest->first_name); ?> <?php echo e($contract->guest->last_name); ?>

                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted">วันที่เริ่มต้น</label>
                                <p class="fs-5"><?php echo e(optional($contract->start_date)->format('d/m/Y')); ?></p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted">วันที่สิ้นสุด</label>
                                <p class="fs-5"><?php echo e(optional($contract->end_date)->format('d/m/Y')); ?></p>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label text-muted">รายละเอียด/คำอธิบาย</label>
                                <p><?php echo e($contract->description ?? '-'); ?></p>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label text-muted">หมายเหตุ</label>
                                <p><?php echo e($contract->notes ?? '-'); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
 
            <!-- Payment Info -->
            <div class="col-lg-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0">
                            <i class="bi bi-credit-card me-2"></i>ข้อมูลการเงิน
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label text-muted">ค่าเช่ารายเดือน</label>
                            <p class="fs-4 fw-bold text-success">฿<?php echo e(number_format($contract->monthly_rent, 2)); ?></p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted">เงินประกัน</label>
                            <p class="fs-5">฿<?php echo e(number_format($contract->deposit ?? 0, 2)); ?></p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted">จ่ายล่วงหน้า</label>
                            <p class="fs-5">฿<?php echo e(number_format($contract->advance_payment ?? 0, 2)); ?></p>
                        </div>
                        <hr>
                        <div class="mb-0">
                            <label class="form-label text-muted">รวมทั้งหมด</label>
                            <p class="fs-4 fw-bold text-primary">
                                ฿<?php echo e(number_format(($contract->monthly_rent ?? 0) + ($contract->deposit ?? 0) + ($contract->advance_payment ?? 0), 2)); ?>

                            </p>
                        </div>
                    </div>
                </div>
 
                <!-- Back Button -->
                <div class="mt-3">
                    <a href="<?php echo e(route('contracts.index')); ?>" class="btn btn-secondary w-100">
                        <i class="bi bi-arrow-left me-1"></i>กลับไปหน้ารายการ
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
 
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Rm1\resources\views/contracts/show.blade.php ENDPATH**/ ?>