
 
<?php $__env->startSection('content'); ?>
<div class="container-fluid py-4">
 
    
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-0 fw-bold">
                <i class="bi bi-speedometer2 text-primary me-2"></i>จัดการมิเตอร์น้ำ/ไฟ
            </h4>
            <small class="text-muted">แสดงมิเตอร์จัดกลุ่มตามห้องพัก</small>
        </div>
        <div class="d-flex gap-2">
            <a href="<?php echo e(route('meters.export')); ?>" class="btn btn-outline-success btn-sm">
                <i class="bi bi-download me-1"></i>Export
            </a>
            <a href="<?php echo e(route('meters.create')); ?>" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-circle me-1"></i>เพิ่มมิเตอร์
            </a>
        </div>
    </div>
 
    
    <?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i><?php echo e(session('success')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
 
    
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body py-3">
            <form method="GET" action="<?php echo e(route('meters.index')); ?>" class="row g-2 align-items-end">
                <div class="col-md-5">
                    <input type="text" name="search" class="form-control form-control-sm"
                           placeholder="ค้นหาห้อง / หมายเลขมิเตอร์ / ผู้เช่า..."
                           value="<?php echo e(request('search')); ?>">
                </div>
                <div class="col-md-2">
                    <select name="type" class="form-select form-select-sm">
                        <option value="">ประเภทมิเตอร์ทั้งหมด</option>
                        <option value="electric" <?php echo e(request('type') === 'electric' ? 'selected' : ''); ?>>⚡ ไฟฟ้า</option>
                        <option value="water"    <?php echo e(request('type') === 'water'    ? 'selected' : ''); ?>>💧 น้ำประปา</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select form-select-sm">
                        <option value="">สถานะทั้งหมด</option>
                        <option value="1" <?php echo e(request('status') === '1' ? 'selected' : ''); ?>>ใช้งาน</option>
                        <option value="0" <?php echo e(request('status') === '0' ? 'selected' : ''); ?>>ปิดใช้</option>
                    </select>
                </div>
                <div class="col-md-auto">
                    <button type="submit" class="btn btn-primary btn-sm px-4">
                        <i class="bi bi-search me-1"></i>ค้นหา
                    </button>
                    <a href="<?php echo e(route('meters.index')); ?>" class="btn btn-outline-secondary btn-sm ms-1">
                        รีเซ็ต
                    </a>
                </div>
            </form>
        </div>
    </div>
 
    
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm text-center py-3">
                <div class="fs-2 fw-bold text-primary"><?php echo e($rooms->total()); ?></div>
                <div class="text-muted small">ห้องที่มีมิเตอร์</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm text-center py-3">
                <div class="fs-2 fw-bold text-warning">
                    <?php echo e($rooms->getCollection()->sum(fn($r) => $r->meters->where('type','electric')->count())); ?>

                </div>
                <div class="text-muted small">⚡ มิเตอร์ไฟ</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm text-center py-3">
                <div class="fs-2 fw-bold text-info">
                    <?php echo e($rooms->getCollection()->sum(fn($r) => $r->meters->where('type','water')->count())); ?>

                </div>
                <div class="text-muted small">💧 มิเตอร์น้ำ</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm text-center py-3">
                <div class="fs-2 fw-bold text-success">
                    <?php echo e($rooms->getCollection()->sum(fn($r) => $r->meters->where('is_active',true)->count())); ?>

                </div>
                <div class="text-muted small">ใช้งานอยู่</div>
            </div>
        </div>
    </div>
 
    
    <?php $__empty_1 = true; $__currentLoopData = $rooms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $room): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <?php
            $electricMeter = $room->meters->where('type', 'electric')->first();
            $waterMeter    = $room->meters->where('type', 'water')->first();
            $tenant        = $room->currentBooking?->guest;
        ?>
 
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body p-0">
 
                
                <div class="d-flex align-items-center justify-content-between px-4 py-3"
                     style="background: linear-gradient(135deg, #f8f9ff 0%, #eef2ff 100%);
                            border-bottom: 1px solid #e8ecf8; border-radius: 12px 12px 0 0;">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-white"
                             style="width:44px; height:44px; background: linear-gradient(135deg,#667eea,#764ba2); font-size:0.9em;">
                            <?php echo e(substr($room->room_number, 0, 2)); ?>

                        </div>
                        <div>
                            <div class="fw-bold text-dark fs-6">ห้อง <?php echo e($room->room_number); ?></div>
                            <div class="text-muted small">
                                <?php if($tenant): ?>
                                    <i class="bi bi-person-fill me-1 text-success"></i>
                                    <?php echo e($tenant->first_name); ?> <?php echo e($tenant->last_name); ?>

                                <?php else: ?>
                                    <i class="bi bi-person-dash me-1 text-muted"></i>
                                    ไม่มีผู้เช่า
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
 
                    
                    <div class="d-flex gap-2">
                        
                        <?php if($electricMeter): ?>
                            <a href="<?php echo e(route('meters.readings.create', $electricMeter)); ?>"
                               class="btn btn-sm btn-success">
                                <i class="bi bi-pencil-square me-1"></i>บันทึกมิเตอร์
                            </a>
                        <?php endif; ?>
                        <?php if($waterMeter && $waterMeter->id !== $electricMeter?->id): ?>
                            <a href="<?php echo e(route('meters.readings.create', $waterMeter)); ?>"
                               class="btn btn-sm btn-outline-info btn-sm">
                                <i class="bi bi-droplet me-1"></i>มิเตอร์น้ำ
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
 
                
                <div class="row g-0">
 
                    
                    <div class="col-md-6 p-4 <?php echo e($waterMeter ? 'border-end' : ''); ?>">
                        <?php if($electricMeter): ?>
                            <div class="d-flex align-items-start justify-content-between">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <span class="badge rounded-pill px-3 py-2"
                                          style="background:#fff3cd; color:#856404; font-size:0.82em;">
                                        ⚡ ไฟฟ้า
                                    </span>
                                    <?php if($electricMeter->is_active): ?>
                                        <span class="badge bg-success-subtle text-success">ใช้งาน</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary-subtle text-secondary">ปิดใช้</span>
                                    <?php endif; ?>
                                </div>
                                <div class="d-flex gap-1">
                                    
                                    <a href="<?php echo e(route('meters.show', $electricMeter)); ?>"
                                       class="btn btn-outline-success btn-sm py-0 px-2"
                                       title="ดูรายละเอียด">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="<?php echo e(route('meters.readings.index', $electricMeter)); ?>"
                                       class="btn btn-xs btn-outline-secondary btn-sm py-0 px-2"
                                       title="ประวัติ">
                                        <i class="bi bi-clock-history"></i>
                                    </a>
                                    <a href="<?php echo e(route('meters.edit', $electricMeter)); ?>"
                                       class="btn btn-xs btn-outline-primary btn-sm py-0 px-2"
                                       title="แก้ไข">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                </div>
                            </div>
 
                            <div class="text-muted small mb-1">
                                <i class="bi bi-upc me-1"></i><?php echo e($electricMeter->meter_number); ?>

                            </div>
 
                            <div class="d-flex align-items-end gap-3 mt-2">
                                <div>
                                    <div class="text-muted" style="font-size:0.75em;">การอ่านล่าสุด</div>
                                    <div class="fw-bold fs-5 text-dark">
                                        <?php echo e(number_format($electricMeter->latestReading?->reading_value ?? 0, 2)); ?>

                                        <span class="text-muted fw-normal" style="font-size:0.7em;">kWh</span>
                                    </div>
                                </div>
                                <div>
                                    <div class="text-muted" style="font-size:0.75em;">อัตรา</div>
                                    <div class="fw-semibold text-warning-emphasis">
                                        <?php echo e(number_format($electricMeter->rate_per_unit ?? 0, 2)); ?> ฿/หน่วย
                                    </div>
                                </div>
                                <?php if($electricMeter->latestReading): ?>
                                    <div>
                                        <div class="text-muted" style="font-size:0.75em;">วันที่อ่าน</div>
                                        <div class="small text-muted">
                                            <?php echo e($electricMeter->latestReading->reading_date?->format('d/m/Y')); ?>

                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-3 text-muted">
                                <i class="bi bi-lightning-charge fs-2 d-block mb-2 opacity-25"></i>
                                <small>ยังไม่มีมิเตอร์ไฟ</small><br>
                                <a href="<?php echo e(route('meters.create')); ?>?room_id=<?php echo e($room->id); ?>&type=electric"
                                   class="btn btn-outline-warning btn-sm mt-2">
                                    <i class="bi bi-plus me-1"></i>เพิ่มมิเตอร์ไฟ
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
 
                    
                    <div class="col-md-6 p-4">
                        <?php if($waterMeter): ?>
                            <div class="d-flex align-items-start justify-content-between">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <span class="badge rounded-pill px-3 py-2"
                                          style="background:#cff4fc; color:#055160; font-size:0.82em;">
                                        💧 น้ำประปา
                                    </span>
                                    <?php if($waterMeter->is_active): ?>
                                        <span class="badge bg-success-subtle text-success">ใช้งาน</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary-subtle text-secondary">ปิดใช้</span>
                                    <?php endif; ?>
                                </div>
                                <div class="d-flex gap-1">
                                    
                                    <a href="<?php echo e(route('meters.show', $waterMeter)); ?>"
                                       class="btn btn-outline-success btn-sm py-0 px-2"
                                       title="ดูรายละเอียด">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="<?php echo e(route('meters.readings.index', $waterMeter)); ?>"
                                       class="btn btn-outline-secondary btn-sm py-0 px-2"
                                       title="ประวัติ">
                                        <i class="bi bi-clock-history"></i>
                                    </a>
                                    <a href="<?php echo e(route('meters.edit', $waterMeter)); ?>"
                                       class="btn btn-outline-primary btn-sm py-0 px-2"
                                       title="แก้ไข">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                </div>
                            </div>
 
                            <div class="text-muted small mb-1">
                                <i class="bi bi-upc me-1"></i><?php echo e($waterMeter->meter_number); ?>

                            </div>
 
                            <div class="d-flex align-items-end gap-3 mt-2">
                                <div>
                                    <div class="text-muted" style="font-size:0.75em;">การอ่านล่าสุด</div>
                                    <div class="fw-bold fs-5 text-dark">
                                        <?php echo e(number_format($waterMeter->latestReading?->reading_value ?? 0, 2)); ?>

                                        <span class="text-muted fw-normal" style="font-size:0.7em;">หน่วย</span>
                                    </div>
                                </div>
                                <div>
                                    <div class="text-muted" style="font-size:0.75em;">อัตรา</div>
                                    <div class="fw-semibold text-info-emphasis">
                                        <?php echo e(number_format($waterMeter->rate_per_unit ?? 0, 2)); ?> ฿/หน่วย
                                    </div>
                                </div>
                                <?php if($waterMeter->latestReading): ?>
                                    <div>
                                        <div class="text-muted" style="font-size:0.75em;">วันที่อ่าน</div>
                                        <div class="small text-muted">
                                            <?php echo e($waterMeter->latestReading->reading_date?->format('d/m/Y')); ?>

                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-3 text-muted">
                                <i class="bi bi-droplet fs-2 d-block mb-2 opacity-25"></i>
                                <small>ยังไม่มีมิเตอร์น้ำ</small><br>
                                <a href="<?php echo e(route('meters.create')); ?>?room_id=<?php echo e($room->id); ?>&type=water"
                                   class="btn btn-outline-info btn-sm mt-2">
                                    <i class="bi bi-plus me-1"></i>เพิ่มมิเตอร์น้ำ
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
 
                </div>
            </div>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <i class="bi bi-speedometer2 fs-1 text-muted opacity-25 d-block mb-3"></i>
                <p class="text-muted mb-3">ยังไม่มีข้อมูลมิเตอร์</p>
                <a href="<?php echo e(route('meters.create')); ?>" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-1"></i>เพิ่มมิเตอร์
                </a>
            </div>
        </div>
    <?php endif; ?>
 
    
    <div class="d-flex justify-content-center mt-4">
        <?php echo e($rooms->links()); ?>

    </div>
 
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Rm1\resources\views/meters/index.blade.php ENDPATH**/ ?>