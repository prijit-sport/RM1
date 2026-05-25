 
<?php $__env->startSection('title', 'จัดการสิ่งอำนวยความสะดวก'); ?>
<?php $__env->startSection('page-title', 'จัดการสิ่งอำนวยความสะดวก'); ?>
 
<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">รายการสิ่งอำนวยความสะดวก</h4>
    <div class="d-flex gap-2">
        <a href="<?php echo e(route('facilities.export')); ?>" class="btn btn-success">
            <i class="bi bi-download me-1"></i><?php echo e(__("ui.export")); ?>

        </a>
        <a href="<?php echo e(route('facilities.create')); ?>" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i>เพิ่มสิ่งอำนวยความสะดวก
        </a>
    </div>
</div>
 
<?php
    // ✅ แก้ icon ให้ใช้ Bootstrap Icons แทน emoji
    $typeMap = [
        'bed'            => ['bi-moon-stars-fill',         'เตียง',           'bg-primary'],
        'mattress'       => ['bi-layers-fill',              'ที่นอน',          'bg-info'],
        'wardrobe'       => ['bi-archive-fill',             'ตู้เสื้อผ้า',     'bg-success'],
        'dressing_table' => ['bi-brush-fill',               'โต๊ะเครื่องแป้ง', 'bg-warning text-dark'],
        'tv_stand'       => ['bi-tv-fill',                  'ชั้นวางทีวี',    'bg-secondary'],
        'clothes_rack'   => ['bi-handbag-fill',             'ราวแขวนผ้า',     'bg-dark'],
    ];
 
    // ✅ เพิ่ม 'active' เข้า statusMap ด้วย
    $statusMap = [
        'active'       => ['bi-check-circle-fill',          'ใช้งานได้',        'bg-success'],
        'good'         => ['bi-check-circle-fill',          'ใช้งานได้',        'bg-success'],
        'fair'         => ['bi-dash-circle-fill',           'สภาพปานกลาง',     'bg-info text-white'],
        'needs_repair' => ['bi-exclamation-triangle-fill',  'ต้องซ่อม',         'bg-warning text-dark'],
        'maintenance'  => ['bi-wrench-fill',                'กำลังซ่อมบำรุง',  'bg-primary'],
        'damaged'      => ['bi-x-circle-fill',              'ชำรุด',            'bg-danger'],
        'retired'      => ['bi-trash-fill',                 'ปลดประจำการ',     'bg-secondary'],
    ];
 
    $statCards = [
        ['key' => 'total',        'icon' => 'bi-box-seam',            'bg' => '#e0e7ff', 'color' => '#4f46e5', 'label' => 'ทั้งหมด',         'value' => $stats['total'],        'href' => route('facilities.index')],
        ['key' => 'good',         'icon' => 'bi-check-circle-fill',   'bg' => '#d1fae5', 'color' => '#10b981', 'label' => 'ใช้งานได้',       'value' => $stats['good'],         'href' => route('facilities.index', ['status' => 'good'])],
        ['key' => 'fair',         'icon' => 'bi-dash-circle-fill',    'bg' => '#dbeafe', 'color' => '#3b82f6', 'label' => 'สภาพปานกลาง',   'value' => $stats['fair'],         'href' => route('facilities.index', ['status' => 'fair'])],
        ['key' => 'needs_repair', 'icon' => 'bi-tools',               'bg' => '#fef3c7', 'color' => '#f59e0b', 'label' => 'ต้องซ่อม',         'value' => $stats['needs_repair'], 'href' => route('facilities.index', ['status' => 'needs_repair'])],
        ['key' => 'maintenance',  'icon' => 'bi-wrench-adjustable',   'bg' => '#fef9c3', 'color' => '#ca8a04', 'label' => 'กำลังซ่อมบำรุง',  'value' => $stats['maintenance'],  'href' => route('facilities.index', ['status' => 'maintenance'])],
        ['key' => 'damaged',      'icon' => 'bi-x-octagon-fill',      'bg' => '#fee2e2', 'color' => '#ef4444', 'label' => 'ชำรุด',           'value' => $stats['damaged'],      'href' => route('facilities.index', ['status' => 'damaged'])],
        ['key' => 'retired',      'icon' => 'bi-trash3-fill',         'bg' => '#f3f4f6', 'color' => '#6b7280', 'label' => 'ปลดประจำการ',   'value' => $stats['retired'],      'href' => route('facilities.index', ['status' => 'retired'])],
    ];
?>
 
<style>
    .stat-summary-card { cursor: pointer; transition: all 0.2s ease; border: none; }
    .stat-summary-card:hover { transform: translateY(-3px); box-shadow: 0 6px 16px rgba(0,0,0,0.12); }
    .stat-icon-wrap-sm { width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; flex-shrink: 0; }
    .stat-value { font-size: 1.3rem; font-weight: 700; line-height: 1.1; color: #1f2937; }
    .stat-label { font-size: 0.75rem; color: #6b7280; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    @media (min-width: 1200px) { .row-cols-xl-7 > * { flex: 0 0 auto; width: 14.2857%; } }
    .table-card { background: white; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); overflow: hidden; }
    .pagination { margin-bottom: 0; gap: 2px; }
    .page-link { border-radius: 6px !important; border: none; color: #4f46e5; padding: 0.5rem 0.85rem; }
    .page-item.active .page-link { background-color: #4f46e5; color: white; }
</style>
 

<div class="row row-cols-2 row-cols-md-4 row-cols-xl-7 g-2 mb-4">
    <?php $__currentLoopData = $statCards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $card): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="col">
        <a href="<?php echo e($card['href']); ?>" class="text-decoration-none text-dark">
            <div class="card stat-summary-card shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-2 p-3">
                    <div class="stat-icon-wrap-sm"
                         style="background: <?php echo e($card['bg']); ?>; color: <?php echo e($card['color']); ?>;">
                        <i class="bi <?php echo e($card['icon']); ?>"></i>
                    </div>
                    <div class="overflow-hidden flex-grow-1">
                        <div class="stat-label"><?php echo e($card['label']); ?></div>
                        <div class="stat-value">
                            <?php echo e(number_format($card['value'])); ?>

                            <small class="text-muted fw-normal" style="font-size:0.7rem;">ชิ้น</small>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
 

<div class="card mb-4 border-0 shadow-sm">
    <div class="card-body">
        <form method="GET" action="<?php echo e(route('facilities.index')); ?>" class="row g-3">
            <div class="col-md-3">
                <input type="text" name="search" class="form-control"
                    placeholder="ค้นหาชื่อ/คำอธิบาย..."
                    value="<?php echo e(request('search')); ?>">
            </div>
 
            
            <div class="col-md-2">
                <select name="type" class="form-select">
                    <option value="">ทุกประเภท</option>
                    <?php $__currentLoopData = $typeMap; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $info): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($value); ?>" <?php echo e(request('type') == $value ? 'selected' : ''); ?>>
                            <?php echo e($info[1]); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
 
            
            <div class="col-md-2">
                <select name="status" class="form-select">
                    <option value="">ทุกสถานะ</option>
                    <?php $__currentLoopData = $statusMap; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $info): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if($value !== 'active'): ?> 
                            <option value="<?php echo e($value); ?>" <?php echo e(request('status') == $value ? 'selected' : ''); ?>>
                                <?php echo e($info[1]); ?>

                            </option>
                        <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
 
            <div class="col-md-2">
                <select name="location" class="form-select">
                    <option value="">ทุกที่ตั้ง</option>
                    <?php $__currentLoopData = $locations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $loc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($loc); ?>" <?php echo e(request('location') == $loc ? 'selected' : ''); ?>>
                            <?php echo e($loc); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
 
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-grow-1">
                    <i class="bi bi-search me-1"></i>ค้นหา
                </button>
                <a href="<?php echo e(route('facilities.index')); ?>" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-clockwise"></i>
                </a>
            </div>
        </form>
    </div>
</div>
 

<div class="table-card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead class="bg-light">
                <tr>
                    <th class="ps-3">ประเภท</th>
                    <th>ชื่อ</th>
                    <th>ที่ตั้ง</th>
                    <th>สถานะ</th>
                    <th>ซ่อมล่าสุด</th>
                    <th>ซ่อมครั้งต่อไป</th>
                    <th class="text-end pe-3">การกระทำ</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $facilities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $facility): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <?php
                    $type     = $typeMap[$facility->type]     ?? ['bi-box-fill', $facility->type, 'bg-secondary'];
                    $status   = $statusMap[$facility->status] ?? ['bi-question-circle-fill', $facility->status, 'bg-secondary'];
                    $nextDate = $facility->next_maintenance_date;
                    $isOverdue  = $nextDate && \Carbon\Carbon::parse($nextDate)->isPast();
                    $isUpcoming = $nextDate && !$isOverdue && \Carbon\Carbon::parse($nextDate)->diffInDays(now()) <= 30;
                ?>
                <tr>
                    <td class="ps-3">
                        
                        <span class="badge <?php echo e($type[2]); ?> px-2 py-1">
                            <i class="bi <?php echo e($type[0]); ?> me-1"></i><?php echo e($type[1]); ?>

                        </span>
                    </td>
                    <td class="fw-semibold"><?php echo e($facility->name); ?></td>
                    <td class="text-muted"><?php echo e($facility->location); ?></td>
                    <td>
                        <span class="badge <?php echo e($status[2]); ?> rounded-pill px-2 py-1">
                            <i class="bi <?php echo e($status[0]); ?> me-1"></i><?php echo e($status[1]); ?>

                        </span>
                    </td>
                    <td>
                        <small class="text-muted">
                            <?php echo e($facility->last_maintenance_date
                                ? \Carbon\Carbon::parse($facility->last_maintenance_date)->format('d/m/Y')
                                : '-'); ?>

                        </small>
                    </td>
                    <td>
                        <?php if($nextDate): ?>
                            <small class="<?php echo e($isOverdue ? 'text-danger fw-bold' : ($isUpcoming ? 'text-warning fw-semibold' : 'text-muted')); ?>">
                                <?php echo e(\Carbon\Carbon::parse($nextDate)->format('d/m/Y')); ?>

                                <?php if($isOverdue): ?> <i class="bi bi-exclamation-circle ms-1"></i> <?php endif; ?>
                            </small>
                        <?php else: ?>
                            <span class="text-muted">-</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-end pe-3">
                        <div class="btn-group">
                            <a href="<?php echo e(route('facilities.edit', $facility->id)); ?>"
                               class="btn btn-sm btn-outline-primary" title="แก้ไข">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="<?php echo e(route('facilities.destroy', $facility->id)); ?>"
                                  method="POST" class="d-inline"
                                  onsubmit="return confirm('ยืนยันการลบ?')">
                                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                <button type="submit"
                                        class="btn btn-sm btn-outline-danger" title="ลบ">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="7" class="text-center py-5 text-muted">
                        <i class="bi bi-inbox fs-2 d-block mb-2 opacity-25"></i>
                        ไม่พบข้อมูล
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
 
    <div class="card-footer bg-white border-0 py-3">
        <div class="d-flex justify-content-between align-items-center">
            <small class="text-muted">
                <?php if($facilities->total() > 0): ?>
                    แสดง <?php echo e($facilities->firstItem()); ?> ถึง <?php echo e($facilities->lastItem()); ?>

                    จาก <?php echo e($facilities->total()); ?> รายการ
                <?php endif; ?>
            </small>
            <?php echo e($facilities->appends(request()->query())->links()); ?>

        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
 
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Rm1\resources\views/facilities/index.blade.php ENDPATH**/ ?>