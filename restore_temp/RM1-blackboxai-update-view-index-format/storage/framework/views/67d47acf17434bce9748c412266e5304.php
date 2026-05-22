

<?php $__env->startSection('page-title', 'จัดการแขก'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0 text-dark">จัดการแขก</h4>
    </div>

    <!-- Main Card -->
    <div class="card border-0 shadow-sm rounded-3">
        <!-- Card Header & Actions -->
        <div class="card-header bg-white border-bottom py-3 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <h5 class="mb-0 fw-bold text-dark d-flex align-items-center">
                <i class="bi bi-people me-2"></i> รายการแขก
            </h5>
            <div class="d-flex flex-wrap gap-2">
                <a href="<?php echo e(route('guests.export')); ?>" class="btn btn-success btn-sm d-flex align-items-center">
                    <i class="bi bi-download me-1"></i> Export
                </a>
                <a href="<?php echo e(route('guests.bulk-create')); ?>" class="btn btn-outline-primary btn-sm d-flex align-items-center">
                    <i class="bi bi-plus-circle me-1"></i> เพิ่มหลายคน
                </a>
                <a href="<?php echo e(route('guests.create')); ?>" class="btn btn-primary btn-sm d-flex align-items-center">
                    <i class="bi bi-plus-lg me-1"></i> เพิ่มแขกใหม่
                </a>
            </div>
        </div>

        <div class="card-body p-4">
            <!-- Search & Filter Form -->
            <form action="<?php echo e(route('guests.index')); ?>" method="GET" class="mb-4">
                <div class="row g-2">
                    <div class="col-md-8 col-lg-8">
                        <input type="text" name="search" class="form-control" placeholder="ค้นหาชื่อ, อีเมล, เบอร์โทร..." value="<?php echo e(request('search')); ?>">
                    </div>
                    <div class="col-md-2 col-lg-2">
                        <a href="<?php echo e(route('guests.index')); ?>" class="btn btn-light border w-100">
                            รีเซ็ต
                        </a>
                    </div>
                    <div class="col-md-2 col-lg-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-search me-1"></i> Search
                        </button>
                    </div>
                </div>
            </form>

            <!-- Data Table -->
            <div class="border rounded-3 overflow-hidden">
                <?php if(isset($guests) && $guests->count() > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light text-muted" style="font-size: 0.9rem;">
                                <tr>
                                    <th scope="col" class="py-3 px-4 fw-medium border-bottom-0">ชื่อ-นามสกุล</th>
                                    <th scope="col" class="py-3 fw-medium border-bottom-0">อีเมล</th>
                                    <th scope="col" class="py-3 fw-medium border-bottom-0">เบอร์โทร</th>
                                    <th scope="col" class="py-3 fw-medium border-bottom-0">เลขบัตรประจำตัว</th>
                                    <th scope="col" class="py-3 fw-medium border-bottom-0">ข้อมูลห้องพัก (โซน/ห้อง)</th>
                                    <th scope="col" class="py-3 px-4 fw-medium text-center border-bottom-0">การกระทำ</th>
                                </tr>
                            </thead>
                            <tbody style="font-size: 0.95rem;">
                                <?php $__currentLoopData = $guests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $guest): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <!-- ใช้ Accessor full_name จาก Model -->
                                    <td class="px-4 py-3 fw-medium text-dark"><?php echo e($guest->full_name); ?></td>
                                    <td class="py-3 text-muted"><?php echo e($guest->email ?? '-'); ?></td>
                                    <td class="py-3"><?php echo e($guest->phone ?? '-'); ?></td>
                                    <!-- ฟิลด์ id_number ให้ตรงกับ Model -->
                                    <td class="py-3 text-muted"><?php echo e($guest->id_number ?? '-'); ?></td>
                                    
                                    <!-- ส่วนแสดงข้อมูลโซนและห้องพัก -->
                                    <td class="py-3">
                                        <?php
                                            // ดึงข้อมูลสัญญาหรือการจองล่าสุดขึ้นมา (ไม่สนว่าสถานะจะเป็นคำว่าอะไร)
                                            $activeRecord = $guest->contracts->sortByDesc('id')->first() ?? $guest->bookings->sortByDesc('id')->first() ?? null;
                                            $room = $activeRecord ? $activeRecord->room : null;
                                            
                                            // สมมติว่ามีฟิลด์ zone ในตาราง rooms 
                                            $zone = $room ? ($room->zone ?? 'ไม่ระบุโซน') : null; 
                                        ?>

                                        <?php if($room): ?>
                                            <div class="d-flex flex-column">
                                                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary mb-1 text-start" style="width: fit-content; font-size: 0.85rem;">
                                                    <i class="bi bi-door-closed me-1"></i> ห้อง <?php echo e($room->room_number); ?>

                                                </span>
                                                <small class="text-muted fw-medium" style="font-size: 0.75rem;">
                                                    <i class="bi bi-geo-alt me-1"></i> <?php echo e($zone); ?>

                                                </small>
                                            </div>
                                        <?php else: ?>
                                            <span class="badge bg-light text-secondary border" style="font-size: 0.85rem;">
                                                <i class="bi bi-dash-circle me-1"></i> ยังไม่มีห้องพัก
                                            </span>
                                        <?php endif; ?>
                                    </td>

                                    <td class="py-3 px-4 text-center">
                                        <div class="btn-group" role="group">
                                            <a href="<?php echo e(route('guests.show', $guest->id)); ?>" class="btn btn-sm btn-outline-info" data-bs-toggle="tooltip" title="ดูรายละเอียด">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="<?php echo e(route('guests.edit', $guest->id)); ?>" class="btn btn-sm btn-outline-warning" data-bs-toggle="tooltip" title="แก้ไข">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                            <form action="<?php echo e(route('guests.destroy', $guest->id)); ?>" method="POST" class="d-inline" onsubmit="return confirm('คุณแน่ใจหรือไม่ที่จะลบข้อมูลแขกท่านนี้?');">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('DELETE'); ?>
                                                <button type="submit" class="btn btn-sm btn-outline-danger" data-bs-toggle="tooltip" title="ลบ">
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
                    
                    <!-- Pagination -->
                    <?php if($guests->hasPages()): ?>
                    <div class="bg-light d-flex flex-column flex-md-row justify-content-between align-items-center py-3 px-4 border-top">
                        <div class="text-muted mb-2 mb-md-0" style="font-size: 0.85rem;">
                            แสดง <?php echo e($guests->firstItem()); ?> ถึง <?php echo e($guests->lastItem()); ?> จากทั้งหมด <?php echo e($guests->total()); ?> รายการ
                        </div>
                        <div>
                            <?php echo e($guests->links('pagination::bootstrap-5')); ?>

                        </div>
                    </div>
                    <?php endif; ?>

                <?php else: ?>
                    <!-- Empty State -->
                    <div class="text-center py-5">
                        <div class="mb-3">
                            <i class="bi bi-people text-muted opacity-50" style="font-size: 4rem;"></i>
                        </div>
                        <h6 class="text-muted mb-3 fw-normal">ไม่มีข้อมูลแขกในระบบ หรือไม่พบผลลัพธ์การค้นหา</h6>
                        <a href="<?php echo e(route('guests.create')); ?>" class="btn btn-primary btn-sm px-4">
                            <i class="bi bi-plus-lg me-1"></i> เพิ่มแขกใหม่
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
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Rm1\resources\views/guests/index.blade.php ENDPATH**/ ?>