

<?php $__env->startSection('content'); ?>
<style>
    .meter-card { border-radius: 16px; transition: transform 0.2s; border: 1px solid #f0f0f0 !important; }
    .meter-card:hover { transform: translateY(-3px); }
    .room-badge { width: 45px; height: 45px; background: #4e73df; color: white; display: flex; align-items: center; justify-content: center; border-radius: 12px; font-weight: bold; box-shadow: 0 4px 10px rgba(78, 115, 223, 0.2); }
    .meter-box { background: #f8f9fc; border-radius: 12px; padding: 15px; border: 1px solid #edf0f5; }
    .reading-value { font-family: 'Monaco', 'Consolas', monospace; font-size: 1.4rem; color: #2e59d9; letter-spacing: -0.5px; }
    .action-btn { width: 32px; height: 32px; padding: 0; display: inline-flex; align-items: center; justify-content: center; border-radius: 8px; transition: 0.2s; }
</style>

<div class="container-fluid py-4">
    
    
    <div class="row align-items-center mb-4">
        <div class="col-md-6">
            <h4 class="fw-bold text-dark mb-1">
                <i class="bi bi-speedometer2 text-primary me-2"></i>ระบบมิเตอร์ น้ำ-ไฟ
            </h4>
            <p class="text-muted small mb-0">ตรวจสอบและบันทึกค่ามิเตอร์รายเดือนแยกตามห้องพัก</p>
        </div>
        <div class="col-md-6 text-md-end mt-3 mt-md-0">
            <a href="<?php echo e(route('meters.export')); ?>" class="btn btn-outline-success border-2 fw-bold px-3">
                <i class="bi bi-file-earmark-excel me-1"></i> Export
            </a>
            <a href="<?php echo e(route('meters.create')); ?>" class="btn btn-primary shadow-sm fw-bold px-3 ms-2">
                <i class="bi bi-plus-lg me-1"></i> เพิ่มมิเตอร์
            </a>
        </div>
    </div>

    
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
        <div class="card-body p-3">
            <form method="GET" action="<?php echo e(route('meters.index')); ?>" class="row g-2">
                <div class="col-md-5">
                    <div class="input-group input-group-merge">
                        <span class="input-group-text bg-light border-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control bg-light border-0" placeholder="ระบุเลขห้อง หรือชื่อผู้เช่า..." value="<?php echo e(request('search')); ?>">
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="type" class="form-select bg-light border-0">
                        <option value="">ทุกประเภทมิเตอร์</option>
                        <option value="electric" <?php echo e(request('type') == 'electric' ? 'selected' : ''); ?>>⚡ มิเตอร์ไฟฟ้า</option>
                        <option value="water" <?php echo e(request('type') == 'water' ? 'selected' : ''); ?>>💧 มิเตอร์น้ำ</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-dark w-100 fw-bold">กรองข้อมูล</button>
                </div>
                <div class="col-md-2">
                    <a href="<?php echo e(route('meters.index')); ?>" class="btn btn-light w-100">ล้างค่า</a>
                </div>
            </form>
        </div>
    </div>

    
    <div class="row g-3 mb-4">
        <?php
            $stats = [
                ['title' => 'ห้องทั้งหมด', 'val' => $rooms->total(), 'icon' => 'bi-building', 'color' => 'primary'],
                ['title' => 'ไฟฟ้า', 'val' => $rooms->getCollection()->sum(fn($r) => $r->meters->where('type','electric')->count()), 'icon' => 'bi-lightning-charge', 'color' => 'warning'],
                ['title' => 'น้ำประปา', 'val' => $rooms->getCollection()->sum(fn($r) => $r->meters->where('type','water')->count()), 'icon' => 'bi-droplet', 'color' => 'info'],
                ['title' => 'สถานะปกติ', 'val' => $rooms->getCollection()->sum(fn($r) => $r->meters->where('is_active',true)->count()), 'icon' => 'bi-check-circle', 'color' => 'success']
            ];
        ?>
        <?php $__currentLoopData = $stats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm p-3 h-100" style="border-radius: 12px;">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 bg-<?php echo e($s['color']); ?> bg-opacity-10 p-3 rounded-3 text-<?php echo e($s['color']); ?>">
                        <i class="bi <?php echo e($s['icon']); ?> fs-4"></i>
                    </div>
                    <div class="ms-3">
                        <h6 class="text-muted mb-0 small"><?php echo e($s['title']); ?></h6>
                        <span class="fs-4 fw-bold text-dark"><?php echo e(number_format($s['val'])); ?></span>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    
    <div class="row">
        <?php $__empty_1 = true; $__currentLoopData = $rooms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $room): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <?php
                $electric = $room->meters->where('type', 'electric')->first();
                $water = $room->meters->where('type', 'water')->first();
                $tenant = $room->currentBooking?->guest;
            ?>
            <div class="col-12 mb-4">
                <div class="card meter-card border-0 shadow-sm overflow-hidden">
                    <div class="card-header bg-white border-0 py-3 px-4">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <div class="room-badge me-3"><?php echo e($room->room_number); ?></div>
                                <div>
                                    <h5 class="mb-0 fw-bold">ห้อง <?php echo e($room->room_number); ?></h5>
                                    <small class="text-<?php echo e($tenant ? 'success' : 'muted'); ?> fw-semibold">
                                        <?php if($tenant): ?>
                                            <i class="bi bi-person-check-fill me-1"></i> <?php echo e($tenant->first_name); ?> <?php echo e($tenant->last_name); ?>

                                        <?php else: ?>
                                            <i class="bi bi-dash-circle me-1"></i> ว่าง (ไม่มีผู้เช่า)
                                        <?php endif; ?>
                                    </small>
                                </div>
                            </div>
                            <div class="btn-group">
                                <?php if($electric): ?>
                                <a href="<?php echo e(route('meters.readings.create', $electric)); ?>" class="btn btn-success btn-sm px-3 rounded-pill fw-bold shadow-sm">
                                    <i class="bi bi-pencil-square me-1"></i> จดมิเตอร์
                                </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-body px-4 pb-4 pt-0">
                        <div class="row g-3">
                            
                            <div class="col-md-6">
                                <div class="meter-box">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <span class="badge bg-warning text-dark px-2 py-1"><i class="bi bi-lightning-fill me-1"></i> ไฟฟ้า</span>
                                        <div class="d-flex gap-1">
                                            <a href="<?php echo e(route('meters.show', $electric)); ?>" class="action-btn btn btn-outline-dark border-0 btn-sm"><i class="bi bi-eye"></i></a>
                                            <a href="<?php echo e(route('meters.readings.index', $electric)); ?>" class="action-btn btn btn-outline-dark border-0 btn-sm"><i class="bi bi-clock-history"></i></a>
                                            <a href="<?php echo e(route('meters.edit', $electric)); ?>" class="action-btn btn btn-outline-dark border-0 btn-sm"><i class="bi bi-gear"></i></a>
                                        </div>
                                    </div>
                                    <?php if($electric): ?>
                                        <div class="row align-items-end">
                                            <div class="col-7">
                                                <div class="text-muted small">ตัวเลขปัจจุบัน</div>
                                                <div class="reading-value"><?php echo e(number_format($electric->latestReading?->reading_value ?? 0, 2)); ?> <span class="fs-6 text-muted">kWh</span></div>
                                            </div>
                                            <div class="col-5 text-end">
                                                <div class="text-muted small">เรทหน่วยละ</div>
                                                <div class="fw-bold text-dark"><?php echo e(number_format($electric->rate_per_unit, 2)); ?> ฿</div>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <div class="text-center py-2"><a href="<?php echo e(route('meters.create', ['room_id' => $room->id, 'type' => 'electric'])); ?>" class="text-decoration-none small text-warning">+ ติดตั้งมิเตอร์ไฟ</a></div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            
                            <div class="col-md-6">
                                <div class="meter-box" style="background: #f0f7ff;">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <span class="badge bg-info text-white px-2 py-1"><i class="bi bi-droplet-fill me-1"></i> น้ำประปา</span>
                                        <div class="d-flex gap-1">
                                            <a href="<?php echo e(route('meters.show', $water)); ?>" class="action-btn btn btn-outline-info border-0 btn-sm text-info"><i class="bi bi-eye"></i></a>
                                            <a href="<?php echo e(route('meters.readings.index', $water)); ?>" class="action-btn btn btn-outline-info border-0 btn-sm text-info"><i class="bi bi-clock-history"></i></a>
                                            <a href="<?php echo e(route('meters.edit', $water)); ?>" class="action-btn btn btn-outline-info border-0 btn-sm text-info"><i class="bi bi-gear"></i></a>
                                        </div>
                                    </div>
                                    <?php if($water): ?>
                                        <div class="row align-items-end">
                                            <div class="col-7">
                                                <div class="text-muted small">ตัวเลขปัจจุบัน</div>
                                                <div class="reading-value text-info"><?php echo e(number_format($water->latestReading?->reading_value ?? 0, 2)); ?> <span class="fs-6 text-muted">Unit</span></div>
                                            </div>
                                            <div class="col-5 text-end">
                                                <div class="text-muted small">เรทหน่วยละ</div>
                                                <div class="fw-bold text-dark"><?php echo e(number_format($water->rate_per_unit, 2)); ?> ฿</div>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <div class="text-center py-2"><a href="<?php echo e(route('meters.create', ['room_id' => $room->id, 'type' => 'water'])); ?>" class="text-decoration-none small text-info">+ ติดตั้งมิเตอร์น้ำ</a></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="col-12 text-center py-5">
                <img src="https://cdn-icons-png.flaticon.com/512/7486/7486744.png" width="80" class="opacity-25 mb-3">
                <p class="text-muted">ไม่พบข้อมูลมิเตอร์ที่ค้นหา</p>
            </div>
        <?php endif; ?>
    </div>

    
    <div class="d-flex justify-content-center mt-2">
        <?php echo e($rooms->links('pagination::bootstrap-5')); ?>

    </div>

</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Rm1\resources\views/meters/index.blade.php ENDPATH**/ ?>