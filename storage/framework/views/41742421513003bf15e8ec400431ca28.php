
 
<?php $__env->startSection('content'); ?>
<style>
    .room-scroll-box {
        max-height: 120px;
        overflow-y: auto;
        background: #fdfdfd;
        border: 1px solid #eee;
        padding: 10px;
        border-radius: 10px;
    }
    .badge-room {
        font-size: 0.75rem;
        padding: 4px 8px;
        border-radius: 5px;
        margin: 1px;
    }
    .stat-card-custom {
        border: none;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        border-radius: 12px;
    }
    .search-card {
        background: #fff;
        border-radius: 12px;
        border: none;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }
    .floor-badge {
        display: inline-block;
        background: #e8f4ff;
        color: #1a5ca8;
        font-size: 0.7rem;
        font-weight: 600;
        padding: 2px 7px;
        border-radius: 99px;
        margin-left: 6px;
    }
    .zone-badge {
        display: inline-block;
        background: #fff0e8;
        color: #c05a00;
        font-size: 0.7rem;
        font-weight: 600;
        padding: 2px 7px;
        border-radius: 99px;
        margin-left: 3px;
    }
</style>
 
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0 text-dark">จัดการห้องพัก</h4>
    <div class="d-flex gap-2">
        <a href="<?php echo e(route('rooms.bulk-create')); ?>" class="btn btn-outline-primary">
            <i class="bi bi-plus-square me-1"></i>เพิ่มหลายห้อง
        </a>
        <a href="<?php echo e(route('rooms.create')); ?>" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i>เพิ่มห้องใหม่
        </a>
    </div>
</div>
 

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card stat-card-custom h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="p-3 bg-success bg-opacity-10 text-success rounded-circle me-3">
                        <i class="bi bi-door-open fs-4"></i>
                    </div>
                    <div>
                        <small class="text-muted d-block">ห้องว่าง</small>
                        <h4 class="mb-0 fw-bold text-success"><?php echo e($availableCount); ?></h4>
                    </div>
                </div>
                <div class="room-scroll-box">
                    <small class="text-muted d-block mb-1 small">รายชื่อห้องว่าง:</small>
                    <div class="d-flex flex-wrap">
                        <?php $__empty_1 = true; $__currentLoopData = $availableRoomList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $num): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <span class="badge bg-success-subtle text-success border border-success-subtle badge-room"><?php echo e($num); ?></span>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <span class="text-muted small">- ไม่มีห้องว่าง -</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
 
    <div class="col-md-4">
        <div class="card stat-card-custom h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="p-3 bg-primary bg-opacity-10 text-primary rounded-circle me-3">
                        <i class="bi bi-door-closed fs-4"></i>
                    </div>
                    <div>
                        <small class="text-muted d-block">ใช้งานอยู่</small>
                        <h4 class="mb-0 fw-bold text-primary"><?php echo e($occupiedCount); ?></h4>
                    </div>
                </div>
                <div class="room-scroll-box">
                    <small class="text-muted d-block mb-1 small">รายชื่อห้องไม่ว่าง:</small>
                    <div class="d-flex flex-wrap">
                        <?php $__currentLoopData = $occupiedRoomList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $num): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle badge-room"><?php echo e($num); ?></span>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
 
    <div class="col-md-4">
        <div class="card stat-card-custom h-100 d-flex align-items-center justify-content-center p-4">
            <div class="p-3 bg-warning bg-opacity-10 text-warning rounded-circle me-3">
                <i class="bi bi-tools fs-4"></i>
            </div>
            <div>
                <small class="text-muted d-block">ซ่อมบำรุง</small>
                <h4 class="mb-0 fw-bold text-warning"><?php echo e($maintenanceCount); ?> รายการ</h4>
            </div>
        </div>
    </div>
</div>
 

<div class="card search-card mb-4">
    <div class="card-body p-3">
        <form action="<?php echo e(route('rooms.index')); ?>" method="GET" class="row g-2 align-items-end">
 
            
            <div class="col-md-3">
                <label class="form-label small text-muted mb-1">หมายเลขห้อง</label>
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-end-0">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" name="search"
                        class="form-control border-start-0"
                        placeholder="เช่น A101, B202..."
                        value="<?php echo e(request('search')); ?>">
                </div>
            </div>
 
            
            <div class="col-md-2">
                <label class="form-label small text-muted mb-1">สถานะ</label>
                <select name="status" class="form-select">
                    <option value="">ทุกสถานะ</option>
                    <option value="available"   <?php echo e(request('status') == 'available'   ? 'selected' : ''); ?>>ว่าง</option>
                    <option value="occupied"    <?php echo e(request('status') == 'occupied'    ? 'selected' : ''); ?>>ใช้งานอยู่</option>
                    <option value="maintenance" <?php echo e(request('status') == 'maintenance' ? 'selected' : ''); ?>>ซ่อมบำรุง</option>
                </select>
            </div>
 
            
            <div class="col-md-2">
                <label class="form-label small text-muted mb-1">ชั้น</label>
                <select name="floor" class="form-select">
                    <option value="">ทุกชั้น</option>
                    <?php for($f = 1; $f <= 5; $f++): ?>
                        <option value="<?php echo e($f); ?>" <?php echo e(request('floor') == $f ? 'selected' : ''); ?>>
                            ชั้น <?php echo e($f); ?>

                        </option>
                    <?php endfor; ?>
                </select>
            </div>
 
            
            <div class="col-md-2">
                <label class="form-label small text-muted mb-1">โซน</label>
                <select name="zone" class="form-select">
                    <option value="">ทุกโซน</option>
                    <option value="A" <?php echo e(request('zone') == 'A' ? 'selected' : ''); ?>>โซน A</option>
                    <option value="B" <?php echo e(request('zone') == 'B' ? 'selected' : ''); ?>>โซน B</option>
                </select>
            </div>
 
            
            <div class="col-md-3">
                <label class="form-label small text-muted mb-1 invisible">ค้นหา</label>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1">
                        <i class="bi bi-search me-1"></i>ค้นหา
                    </button>
                    <?php if(request('search') || request('status') || request('floor') || request('zone')): ?>
                        <a href="<?php echo e(route('rooms.index')); ?>" class="btn btn-light border" title="ล้างตัวกรอง">
                            <i class="bi bi-x-lg"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
 
        </form>
    </div>
</div>
 

<?php if(request('search') || request('status') || request('floor') || request('zone')): ?>
<div class="mb-3 d-flex align-items-center gap-2 flex-wrap">
    <small class="text-muted">กรองโดย:</small>
    <?php if(request('search')): ?>
        <span class="badge bg-light text-dark border">
            <i class="bi bi-search me-1"></i><?php echo e(request('search')); ?>

        </span>
    <?php endif; ?>
    <?php if(request('status')): ?>
        <span class="badge bg-light text-dark border">
            สถานะ: <?php echo e(['available'=>'ว่าง','occupied'=>'ใช้งานอยู่','maintenance'=>'ซ่อมบำรุง'][request('status')]); ?>

        </span>
    <?php endif; ?>
    <?php if(request('floor')): ?>
        <span class="badge bg-light text-dark border">
            <i class="bi bi-building me-1"></i>ชั้น <?php echo e(request('floor')); ?>

        </span>
    <?php endif; ?>
    <?php if(request('zone')): ?>
        <span class="badge bg-light text-dark border">
            โซน <?php echo e(request('zone')); ?>

        </span>
    <?php endif; ?>
    <small class="text-muted">— พบ <?php echo e($rooms->total()); ?> ห้อง</small>
</div>
<?php endif; ?>
 

<div class="card stat-card-custom overflow-hidden border-0">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light">
                <tr>
                    <th class="ps-4">หมายเลขห้อง</th>
                    <th>โซน / ชั้น</th>
                    <th>ประเภท</th>
                    <th>ราคา/เดือน</th>
                    <th>สถานะ</th>
                    <th class="text-end pe-4">จัดการ</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $rooms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $room): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td class="ps-4 fw-bold text-dark"><?php echo e($room->room_number); ?></td>
                    <td>
                        <?php if($room->zone): ?>
                            <span class="zone-badge">โซน <?php echo e($room->zone); ?></span>
                        <?php endif; ?>
                        <?php if($room->floor): ?>
                            <span class="floor-badge">ชั้น <?php echo e($room->floor); ?></span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if($room->room_type == 'air'): ?>
                            <span class="badge bg-info bg-opacity-10 text-info border border-info-subtle">
                                <i class="bi bi-snow me-1"></i>แอร์
                            </span>
                        <?php else: ?>
                            <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary-subtle">
                                <i class="bi bi-wind me-1"></i>พัดลม
                            </span>
                        <?php endif; ?>
                    </td>
                    <td class="text-success fw-semibold">
                        <?php echo e(number_format($room->price_per_month)); ?> ฿
                    </td>
                    <td>
                        <?php
                            $badgeClass = match($room->status) {
                                'available'   => 'success',
                                'occupied'    => 'primary',
                                'maintenance' => 'warning',
                                default       => 'secondary'
                            };
                            $statusText = match($room->status) {
                                'available'   => 'ว่าง',
                                'occupied'    => 'ใช้งาน',
                                'maintenance' => 'ซ่อมบำรุง',
                                default       => 'ไม่ระบุ'
                            };
                        ?>
                        <span class="badge bg-<?php echo e($badgeClass); ?> bg-opacity-10 text-<?php echo e($badgeClass); ?> border border-<?php echo e($badgeClass); ?>-subtle">
                            <?php echo e($statusText); ?>

                        </span>
                    </td>
                    <td class="text-end pe-4">
                        <a href="<?php echo e(route('rooms.show', $room)); ?>"
                           class="btn btn-sm btn-light text-info" title="ดูรายละเอียด">
                            <i class="bi bi-eye"></i>
                        </a>
                        <a href="<?php echo e(route('rooms.edit', $room)); ?>"
                           class="btn btn-sm btn-light text-warning" title="แก้ไข">
                            <i class="bi bi-pencil"></i>
                        </a>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="6" class="text-center py-5 text-muted">
                        <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                        ไม่พบข้อมูลห้องพักที่ค้นหา
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
 

<div class="mt-4 d-flex justify-content-between align-items-center">
    <small class="text-muted">
        แสดง <?php echo e($rooms->firstItem() ?? 0); ?>–<?php echo e($rooms->lastItem() ?? 0); ?>

        จาก <?php echo e($rooms->total()); ?> ห้อง
    </small>
    <?php echo e($rooms->links('pagination::bootstrap-5')); ?>

</div>
 
<?php $__env->stopSection(); ?>
 
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\Rm1\resources\views/rooms/index.blade.php ENDPATH**/ ?>