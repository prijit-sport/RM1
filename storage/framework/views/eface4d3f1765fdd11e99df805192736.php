 
<?php $__env->startSection('page-title', 'จัดการสัญญาเช่า'); ?>
 
<?php $__env->startSection('content'); ?>
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0 text-dark">จัดการสัญญาเช่า</h4>
    </div>
 
    <!-- Main Card -->
    <div class="card border-0 shadow-sm rounded-3">
        <!-- Card Header & Actions -->
        <div class="card-header bg-white border-bottom py-3 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <h5 class="mb-0 fw-bold text-dark d-flex align-items-center">
                <i class="bi bi-file-text me-2"></i> รายการสัญญาเช่า
            </h5>
            <div class="d-flex flex-wrap gap-2">
                <a href="<?php echo e(route('contracts.expiring')); ?>" class="btn btn-outline-warning text-dark btn-sm d-flex align-items-center">
                    <i class="bi bi-exclamation-triangle text-warning me-1"></i> สัญญาหมดอายุเร็ว
                </a>
                <a href="<?php echo e(route('contracts.export')); ?>" class="btn btn-success btn-sm d-flex align-items-center">
                    <i class="bi bi-download me-1"></i> Export
                </a>
                <a href="<?php echo e(route('contracts.create')); ?>" class="btn btn-primary btn-sm d-flex align-items-center">
                    <i class="bi bi-plus-lg me-1"></i> เพิ่มสัญญา
                </a>
            </div>
        </div>
 
        <div class="card-body p-4">
            <!-- Search & Filter Form -->
            <form action="<?php echo e(route('contracts.index')); ?>" method="GET" class="mb-4">
                <div class="row g-2">
                    <div class="col-md-6 col-lg-5">
                        <input type="text" name="search" class="form-control" placeholder="ค้นหาเลขที่สัญญา, ชื่อผู้เช่า, ห้องพัก..." value="<?php echo e(request('search')); ?>">
                    </div>
                    <div class="col-md-4 col-lg-3">
                        <select name="status" class="form-select">
                            <option value="">ทุกสถานะ</option>
                            <option value="active" <?php echo e(request('status') == 'active' ? 'selected' : ''); ?>>ใช้งาน</option>
                            <option value="expiring" <?php echo e(request('status') == 'expiring' ? 'selected' : ''); ?>>ใกล้หมดอายุ</option>
                            <option value="expired" <?php echo e(request('status') == 'expired' ? 'selected' : ''); ?>>สิ้นสุด</option>
                        </select>
                    </div>
                    <div class="col-md-2 col-lg-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-search me-1"></i> Search
                        </button>
                    </div>
                </div>
            </form>
 
            <!-- Data Table or Empty State -->
            <div class="border rounded-3 overflow-hidden">
                <?php if(isset($contracts) && $contracts->count() > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light text-muted" style="font-size: 0.9rem;">
                                <tr>
                                    <th scope="col" class="py-3 px-4 fw-medium border-bottom-0">เลขที่สัญญา</th>
                                    <th scope="col" class="py-3 fw-medium border-bottom-0">ห้องพัก</th>
                                    <th scope="col" class="py-3 fw-medium border-bottom-0">ผู้เช่า</th>
                                    <th scope="col" class="py-3 fw-medium border-bottom-0">วันเริ่มต้น</th>
                                    <th scope="col" class="py-3 fw-medium border-bottom-0">วันสิ้นสุด</th>
                                    <th scope="col" class="py-3 fw-medium border-bottom-0">ค่าเช่า/เดือน</th>
                                    <th scope="col" class="py-3 fw-medium text-center border-bottom-0">สถานะ</th>
                                    <th scope="col" class="py-3 px-4 fw-medium text-center border-bottom-0">การกระทำ</th>
                                </tr>
                            </thead>
                            <tbody style="font-size: 0.95rem;">
                                <?php $__currentLoopData = $contracts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $contract): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td class="px-4 py-3 text-primary fw-semibold"><?php echo e($contract->contract_number); ?></td>
                                    <td class="py-3">
                                        <span class="badge bg-light text-dark border"><i class="bi bi-door-closed me-1"></i> <?php echo e($contract->room->room_number ?? '-'); ?></span>
                                    </td>
                                    <td class="py-3">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-secondary bg-gradient rounded-circle text-white d-flex justify-content-center align-items-center me-2 shadow-sm" style="width: 35px; height: 35px; font-size: 0.9rem;">
                                                <?php echo e(mb_substr($contract->guest->first_name ?? '?', 0, 1)); ?>

                                            </div>
                                            <div>
                                                <div class="fw-medium text-dark"><?php echo e(($contract->guest->first_name ?? '') . ' ' . ($contract->guest->last_name ?? '')); ?></div>
                                                <div class="text-muted" style="font-size: 0.8rem;"><?php echo e($contract->guest->phone ?? '-'); ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-3"><?php echo e(\Carbon\Carbon::parse($contract->start_date)->format('d/m/Y')); ?></td>
                                    <td class="py-3"><?php echo e(\Carbon\Carbon::parse($contract->end_date)->format('d/m/Y')); ?></td>
                                    <td class="py-3">฿<?php echo e(number_format($contract->monthly_rent, 2)); ?></td>
                                    <td class="py-3 text-center">
                                        <?php if($contract->status == 'active'): ?>
                                            <span class="badge bg-success bg-opacity-10 text-success border border-success"><i class="bi bi-check-circle-fill me-1"></i> ใช้งาน</span>
                                        <?php elseif($contract->status == 'expiring'): ?>
                                            <span class="badge bg-warning bg-opacity-10 text-warning border border-warning"><i class="bi bi-exclamation-triangle-fill me-1"></i> ใกล้หมดอายุ</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary"><i class="bi bi-x-circle-fill me-1"></i> สิ้นสุด</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="py-3 px-4 text-center">
                                        <div class="btn-group" role="group">
                                            <a href="<?php echo e(route('contracts.show', $contract->id)); ?>" class="btn btn-sm btn-outline-secondary" data-bs-toggle="tooltip" title="ดูรายละเอียด">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="<?php echo e(route('contracts.edit', $contract->id)); ?>" class="btn btn-sm btn-outline-primary" data-bs-toggle="tooltip" title="แก้ไข">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <?php if($contracts->hasPages()): ?>
                    <div class="bg-light d-flex flex-column flex-md-row justify-content-between align-items-center py-3 px-4 border-top">
                        <div class="text-muted mb-2 mb-md-0" style="font-size: 0.85rem;">
                            แสดง <?php echo e($contracts->firstItem()); ?> ถึง <?php echo e($contracts->lastItem()); ?> จากทั้งหมด <?php echo e($contracts->total()); ?> รายการ
                        </div>
                        <div>
                            <?php echo e($contracts->links('pagination::bootstrap-5')); ?>

                        </div>
                    </div>
                    <?php endif; ?>
 
                <?php else: ?>
                    <!-- Empty State -->
                    <div class="text-center py-5">
                        <div class="mb-3">
                            <i class="bi bi-inbox text-muted opacity-50" style="font-size: 4rem;"></i>
                        </div>
                        <h6 class="text-muted mb-3 fw-normal">ไม่มีข้อมูลสัญญาเช่าในระบบ หรือไม่พบผลลัพธ์การค้นหา</h6>
                        <a href="<?php echo e(route('contracts.create')); ?>" class="btn btn-primary btn-sm px-4">
                            <i class="bi bi-plus-lg me-1"></i> เพิ่มสัญญาเช่า
                        </a>
                    </div>
                <?php endif; ?>
            </div>
            
        </div>
    </div>
</div>
 
<!-- Script สำหรับ Tooltip -->
<?php $__env->startPush('scripts'); ?>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    });
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>
 
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Rm1\resources\views/contracts/index.blade.php ENDPATH**/ ?>