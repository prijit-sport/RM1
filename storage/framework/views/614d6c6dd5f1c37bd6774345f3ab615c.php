<?php $__env->startSection('content'); ?>
<div class="container-fluid py-4">

    
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-0 fw-bold">
                <i class="bi bi-tools text-primary me-2"></i>จัดการการบำรุงรักษา
            </h4>
            <small class="text-muted">ภาพรวมและรายการแจ้งซ่อมทั้งหมด</small>
        </div>
        <div class="d-flex gap-2">
            <a href="<?php echo e(route('maintenances.export')); ?>" class="btn btn-outline-success">
                <i class="bi bi-download me-1"></i>Export
            </a>
            <a href="<?php echo e(route('maintenances.create')); ?>" class="btn btn-primary">
                <i class="bi bi-plus-circle me-1"></i>เพิ่มการบำรุงรักษา
            </a>
        </div>
    </div>

    
    <?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i><?php echo e(session('success')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body py-4">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle d-flex align-items-center justify-content-center me-3 flex-shrink-0"
                             style="width:60px; height:60px; background:#fef3c7;">
                            <i class="bi bi-clock-history fs-3" style="color:#d97706;"></i>
                        </div>
                        <div>
                            <div class="fs-1 fw-bold text-dark lh-1"><?php echo e($pendingCount); ?></div>
                            <div class="text-muted mt-1">รอดำเนินการ</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body py-4">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle d-flex align-items-center justify-content-center me-3 flex-shrink-0"
                             style="width:60px; height:60px; background:#dbeafe;">
                            <i class="bi bi-gear-fill fs-3" style="color:#2563eb;"></i>
                        </div>
                        <div>
                            <div class="fs-1 fw-bold text-dark lh-1"><?php echo e($inProgressCount); ?></div>
                            <div class="text-muted mt-1">กำลังดำเนินการ</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body py-4">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle d-flex align-items-center justify-content-center me-3 flex-shrink-0"
                             style="width:60px; height:60px; background:#d1fae5;">
                            <i class="bi bi-check-circle-fill fs-3" style="color:#059669;"></i>
                        </div>
                        <div>
                            <div class="fs-1 fw-bold text-dark lh-1"><?php echo e($completedThisMonth); ?></div>
                            <div class="text-muted mt-1">เสร็จแล้วเดือนนี้</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
    <div class="row g-3 mb-4">
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 pt-3 pb-2">
                    <h6 class="fw-bold mb-0">
                        <i class="bi bi-exclamation-circle text-warning me-2"></i>
                        รอดำเนินการ (<?php echo e($pending->count()); ?>)
                    </h6>
                </div>
                <div class="card-body pt-2">
                    <?php $__empty_1 = true; $__currentLoopData = $pending; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                            // แก้ไข: ใช้ maintenance_type
                            $issue = $item->maintenance_type ?? '';
                            $style = match(true) {
                                str_contains($issue, 'ไฟ') => ['icon' => 'bi-lightning-charge-fill', 'bg' => '#fef3c7', 'color' => '#d97706'],
                                str_contains($issue, 'น้ำ') || str_contains($issue, 'ท่อ') || str_contains($issue, 'ประปา') => ['icon' => 'bi-droplet-fill', 'bg' => '#dbeafe', 'color' => '#2563eb'],
                                str_contains($issue, 'กุญแจ') || str_contains($issue, 'ล็อค') => ['icon' => 'bi-key-fill', 'bg' => '#ede9fe', 'color' => '#7c3aed'],
                                str_contains($issue, 'แอร์') || str_contains($issue, 'พัดลม') => ['icon' => 'bi-wind', 'bg' => '#cffafe', 'color' => '#0891b2'],
                                default => ['icon' => 'bi-tools', 'bg' => '#f3f4f6', 'color' => '#4b5563'],
                            };

                            $progress = match($item->status) {
                                'pending'     => 20,
                                'in_progress' => 65,
                                'completed'   => 100,
                                default       => 10,
                            };
                        ?>

                        <div class="d-flex align-items-start gap-3 p-3 mb-2 rounded-3"
                             style="background:#f8fafc; border:1px solid #f1f5f9;">

                            <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0"
                                 style="width:48px; height:48px; background:<?php echo e($style['bg']); ?>;">
                                <i class="bi <?php echo e($style['icon']); ?> fs-5" style="color:<?php echo e($style['color']); ?>;"></i>
                            </div>

                            <div class="flex-grow-1" style="min-width:0;">
                                <div class="fw-semibold text-dark">
                                    <a href="<?php echo e(route('maintenances.show', $item)); ?>" class="text-decoration-none text-dark">
                                        <?php echo e($item->maintenance_type); ?> 
                                    </a>
                                    <span class="text-muted fw-normal ms-1">
                                        ห้อง <?php echo e($item->room->room_number ?? '-'); ?>

                                    </span>
                                </div>
                                <div class="text-muted small mt-1">
                                    <?php if(!empty($item->assigned_to)): ?>
                                        <i class="bi bi-person-check me-1"></i>
                                        ผู้รับผิดชอบ: <?php echo e($item->assigned_to); ?>

                                        <span class="mx-1">·</span>
                                    <?php endif; ?>
                                    <i class="bi bi-calendar-event me-1"></i>
                                    แจ้งเมื่อ <?php echo e(optional($item->request_date)->translatedFormat('j M Y') ?? '-'); ?> 
                                    <?php if($item->request_date): ?>
                                        <span class="ms-1">(<?php echo e(\Carbon\Carbon::parse($item->request_date)->diffForHumans()); ?>)</span>
                                    <?php endif; ?>
                                </div>
                                <div class="progress mt-2" style="height: 4px; background:#e5e7eb; border-radius:999px;">
                                    <div style="width: <?php echo e($progress); ?>%; height:100%; background:<?php echo e($style['color']); ?>; border-radius:999px; transition:width 0.6s ease;"></div>
                                </div>
                            </div>

                            <div class="flex-shrink-0">
                                <?php if($item->status === 'pending'): ?>
                                    <form action="<?php echo e(route('maintenances.start', $item)); ?>" method="POST" class="d-inline">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" class="btn btn-primary btn-sm px-3">รับงาน</button>
                                    </form>
                                <?php elseif($item->status === 'in_progress'): ?>
                                    <div class="d-flex gap-1">
                                        <a href="<?php echo e(route('maintenances.edit', $item)); ?>" class="btn btn-outline-warning btn-sm px-2" title="อัพเดท">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <form action="<?php echo e(route('maintenances.complete', $item)); ?>" method="POST" class="d-inline">
                                            <?php echo csrf_field(); ?>
                                            <button type="submit" class="btn btn-success btn-sm px-2" title="ทำเสร็จแล้ว">
                                                <i class="bi bi-check2"></i>
                                            </button>
                                        </form>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-check2-circle fs-1 d-block mb-2 opacity-25"></i>
                            <small>ไม่มีงานรอดำเนินการ</small>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 pt-3 pb-2">
                    <h6 class="fw-bold mb-0">
                        <i class="bi bi-check-circle text-success me-2"></i>
                        เสร็จแล้วล่าสุด
                    </h6>
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover mb-0 align-middle">
                        <thead>
                            <tr class="text-muted small">
                                <th class="ps-4 py-3 fw-normal">ห้อง</th>
                                <th class="py-3 fw-normal">ประเภท</th>
                                <th class="pe-4 py-3 text-end fw-normal">วันที่เสร็จ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $recentlyCompleted; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td class="ps-4 fw-semibold text-dark"><?php echo e($item->room->room_number ?? '-'); ?></td>
                                    <td>
                                        <a href="<?php echo e(route('maintenances.show', $item)); ?>" class="text-decoration-none text-dark">
                                            <?php echo e($item->maintenance_type); ?> 
                                        </a>
                                    </td>
                                    <td class="pe-4 text-end text-muted small">
                                        <?php echo e(optional($item->completion_date)->translatedFormat('j M') ?? '-'); ?> 
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="3" class="text-center py-5 text-muted">
                                        <i class="bi bi-inbox fs-1 d-block mb-2 opacity-25"></i>
                                        <small>ยังไม่มีงานที่เสร็จแล้ว</small>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 py-3 d-flex align-items-center justify-content-between">
            <h6 class="fw-bold mb-0">
                <i class="bi bi-list-ul text-primary me-2"></i>
                รายการการบำรุงรักษาทั้งหมด
            </h6>
        </div>

        <div class="card-body py-3 border-bottom">
            <form method="GET" action="<?php echo e(route('maintenances.index')); ?>" class="row g-2 align-items-end">
                <div class="col-md-6">
                    <input type="text" name="search" class="form-control form-control-sm"
                           placeholder="ค้นหาประเภทปัญหา / ห้อง / ผู้รับผิดชอบ..."
                           value="<?php echo e(request('search')); ?>">
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select form-select-sm">
                        <option value="">ทุกสถานะ</option>
                        <option value="pending"     <?php echo e(request('status') === 'pending'     ? 'selected' : ''); ?>>รอดำเนินการ</option>
                        <option value="in_progress" <?php echo e(request('status') === 'in_progress' ? 'selected' : ''); ?>>กำลังดำเนินการ</option>
                        <option value="completed"   <?php echo e(request('status') === 'completed'   ? 'selected' : ''); ?>>เสร็จสิ้น</option>
                        <option value="cancelled"   <?php echo e(request('status') === 'cancelled'   ? 'selected' : ''); ?>>ยกเลิก</option>
                    </select>
                </div>
                <div class="col-md-auto">
                    <button type="submit" class="btn btn-primary btn-sm px-4">
                        <i class="bi bi-search me-1"></i>Search
                    </button>
                    <a href="<?php echo e(route('maintenances.index')); ?>" class="btn btn-outline-secondary btn-sm">รีเซ็ต</a>
                </div>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr class="small text-muted">
                        <th class="ps-4 py-3">ห้อง</th>
                        <th class="py-3">ประเภทปัญหา</th>
                        <th class="py-3">วันที่แจ้ง</th>
                        <th class="py-3">ผู้รับผิดชอบ</th>
                        <th class="py-3">สถานะ</th>
                        <th class="py-3 text-end">ค่าใช้จ่าย</th>
                        <th class="pe-4 py-3 text-center">การกระทำ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $maintenances; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td class="ps-4 fw-semibold"><?php echo e($m->room->room_number ?? '-'); ?></td>
                            <td><?php echo e($m->maintenance_type); ?></td> 
                            <td class="text-muted small"><?php echo e(optional($m->request_date)->format('d/m/Y') ?? '-'); ?></td> 
                            <td><?php echo e($m->assigned_to ?? '-'); ?></td>
                            <td>
                                <?php switch($m->status):
                                    case ('pending'): ?>
                                        <span class="badge" style="background:#fef3c7; color:#d97706;">รอดำเนินการ</span>
                                        <?php break; ?>
                                    <?php case ('in_progress'): ?>
                                        <span class="badge" style="background:#dbeafe; color:#2563eb;">กำลังดำเนินการ</span>
                                        <?php break; ?>
                                    <?php case ('completed'): ?>
                                        <span class="badge" style="background:#d1fae5; color:#059669;">เสร็จสิ้น</span>
                                        <?php break; ?>
                                    <?php case ('cancelled'): ?>
                                        <span class="badge bg-secondary">ยกเลิก</span>
                                        <?php break; ?>
                                <?php endswitch; ?>
                            </td>
                            <td class="text-end">
                                <?php if($m->cost): ?>
                                    <?php echo e(number_format($m->cost, 2)); ?> ฿
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td class="pe-4 text-center">
                                <div class="d-inline-flex gap-1">
                                    <a href="<?php echo e(route('maintenances.show', $m)); ?>"
                                       class="btn btn-outline-success btn-sm py-0 px-2" title="ดูรายละเอียด">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="<?php echo e(route('maintenances.edit', $m)); ?>"
                                       class="btn btn-outline-primary btn-sm py-0 px-2" title="แก้ไข">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="<?php echo e(route('maintenances.destroy', $m)); ?>" method="POST" class="d-inline"
                                          onsubmit="return confirm('ยืนยันการลบรายการนี้?');">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="btn btn-outline-danger btn-sm py-0 px-2" title="ลบ">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-2 opacity-25"></i>
                                <small>ไม่มีข้อมูล</small>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if($maintenances->hasPages()): ?>
            <div class="card-footer bg-white border-0 d-flex justify-content-center py-3">
                <?php echo e($maintenances->links()); ?>

            </div>
        <?php endif; ?>
    </div>
</div>

<style>
    .table > :not(caption) > * > * { padding: 0.85rem 0.75rem; }
    .badge { padding: 0.45em 0.75em; font-weight: 500; }
</style>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\Rm1\resources\views/maintenances/index.blade.php ENDPATH**/ ?>