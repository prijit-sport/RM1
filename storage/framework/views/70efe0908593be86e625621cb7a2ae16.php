

<?php $__env->startSection('title', 'จัดการการจอง'); ?>

<?php $__env->startSection('page-title', 'จัดการการจอง'); ?>

<?php $__env->startSection('content'); ?>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">รายการการจอง</h4>
        <div class="d-flex gap-2">
            <a href="<?php echo e(route('bookings.export')); ?>" class="btn btn-success">
                <i class="bi bi-download me-1"></i><?php echo e(__('ui.export')); ?>

            </a>
            <a href="<?php echo e(route('bookings.create')); ?>" class="btn btn-primary-custom">
                <i class="bi bi-plus-lg me-1"></i>เพิ่มการจองใหม่
            </a>
        </div>
    </div>

    <style>
        .room-chip {
            display: inline-block;
            padding: 8px 14px;
            margin: 4px 4px 4px 0;
            background: rgba(255, 255, 255, 0.25);
            border: 1.5px solid rgba(255, 255, 255, 0.5);
            border-radius: 10px;
            color: white;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.92rem;
            transition: all 0.2s ease;
            backdrop-filter: blur(4px);
        }

        .room-chip:hover {
            background: white;
            color: #2c3e50 !important;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }

        .room-chip small {
            opacity: 0.85;
            font-weight: 400;
            margin-left: 4px;
        }

        .room-chip:hover small {
            opacity: 0.7;
        }

        .summary-card {
            transition: transform 0.2s ease;
        }

        .summary-card:hover {
            transform: translateY(-2px);
        }

        .stat-number {
            font-size: 2.8rem;
            font-weight: 700;
            line-height: 1;
        }
    </style>

    
    <div class="row g-3 mb-4">
        
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100 summary-card"
                style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                <div class="card-body text-white">

                    
                    <div class="d-flex align-items-center justify-content-between mb-3 pb-3"
                        style="border-bottom: 1px solid rgba(255,255,255,0.3);">
                        <div>
                            <h5 class="mb-1 fw-bold">
                                <i class="bi bi-fan"></i> ห้องพัดลม
                            </h5>
                            <div class="opacity-75 small">
                                ว่าง <?php echo e($availableFanRooms->count()); ?> ห้อง / ทั้งหมด <?php echo e($fanTotal); ?> ห้อง
                            </div>
                        </div>
                        <div class="text-end">
                            <div class="stat-number"><?php echo e($fanTotal); ?></div>
                            <small class="opacity-75">ห้องทั้งหมด</small>
                        </div>
                    </div>

                    
                    <div class="mb-3">
                        <div class="small mb-2 opacity-90">
                            <i class="bi bi-check2-circle"></i>
                            <strong>ห้องที่ว่าง — คลิกเพื่อจอง:</strong>
                        </div>
                        <?php if($availableFanRooms->isEmpty()): ?>
                            <div class="text-center py-3 opacity-75">
                                <i class="bi bi-x-circle"></i> ไม่มีห้องว่างในขณะนี้
                            </div>
                        <?php else: ?>
                            <div>
                                <?php $__currentLoopData = $availableFanRooms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $room): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <a href="<?php echo e(route('bookings.create', ['room_id' => $room->id])); ?>" class="room-chip"
                                        title="จองห้อง #<?php echo e($room->room_number); ?> - ฿<?php echo e(number_format($room->price_per_month, 0)); ?>/เดือน">
                                        #<?php echo e($room->room_number); ?>

                                        <?php if($room->zone): ?>
                                            <small>(<?php echo e($room->zone); ?>)</small>
                                        <?php endif; ?>
                                    </a>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    
                    <a href="<?php echo e(route('bookings.create', ['room_type' => 'fan'])); ?>"
                        class="btn btn-light btn-sm w-100 fw-bold">
                        <i class="bi bi-plus-lg"></i> เพิ่มการจองห้องพัดลม
                    </a>
                </div>
            </div>
        </div>

        
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100 summary-card"
                style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="card-body text-white">

                    <div class="d-flex align-items-center justify-content-between mb-3 pb-3"
                        style="border-bottom: 1px solid rgba(255,255,255,0.3);">
                        <div>
                            <h5 class="mb-1 fw-bold">
                                <i class="bi bi-snow"></i> ห้องแอร์
                            </h5>
                            <div class="opacity-75 small">
                                ว่าง <?php echo e($availableAcRooms->count()); ?> ห้อง / ทั้งหมด <?php echo e($acTotal); ?> ห้อง
                            </div>
                        </div>
                        <div class="text-end">
                            <div class="stat-number"><?php echo e($acTotal); ?></div>
                            <small class="opacity-75">ห้องทั้งหมด</small>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="small mb-2 opacity-90">
                            <i class="bi bi-check2-circle"></i>
                            <strong>ห้องที่ว่าง — คลิกเพื่อจอง:</strong>
                        </div>
                        <?php if($availableAcRooms->isEmpty()): ?>
                            <div class="text-center py-3 opacity-75">
                                <i class="bi bi-x-circle"></i> ไม่มีห้องว่างในขณะนี้
                            </div>
                        <?php else: ?>
                            <div>
                                <?php $__currentLoopData = $availableAcRooms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $room): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <a href="<?php echo e(route('bookings.create', ['room_id' => $room->id])); ?>" class="room-chip"
                                        title="จองห้อง #<?php echo e($room->room_number); ?> - ฿<?php echo e(number_format($room->price_per_month, 0)); ?>/เดือน">
                                        #<?php echo e($room->room_number); ?>

                                        <?php if($room->zone): ?>
                                            <small>(<?php echo e($room->zone); ?>)</small>
                                        <?php endif; ?>
                                    </a>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <a href="<?php echo e(route('bookings.create', ['room_type' => 'air'])); ?>"
                        class="btn btn-light btn-sm w-100 fw-bold">
                        <i class="bi bi-plus-lg"></i> เพิ่มการจองห้องแอร์
                    </a>
                </div>
            </div>
        </div>
    </div>

    
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="<?php echo e(route('bookings.index')); ?>" class="row g-3">
                <div class="col-md-3">
                    <input type="text" name="search" class="form-control" placeholder="ค้นหาชื่อแขก / เลขห้อง..."
                        value="<?php echo e(request('search')); ?>">
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">ทุกสถานะ</option>
                        <option value="pending" <?php echo e(request('status') == 'pending' ? 'selected' : ''); ?>>รอการยืนยัน</option>
                        <option value="confirmed" <?php echo e(request('status') == 'confirmed' ? 'selected' : ''); ?>>ยืนยันแล้ว
                        </option>
                        <option value="checked_in" <?php echo e(request('status') == 'checked_in' ? 'selected' : ''); ?>>เช็คอินแล้ว
                        </option>
                        <option value="checked_out" <?php echo e(request('status') == 'checked_out' ? 'selected' : ''); ?>>
                            เช็คเอาท์แล้ว</option>
                        <option value="cancelled" <?php echo e(request('status') == 'cancelled' ? 'selected' : ''); ?>>ยกเลิก</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="room_type" class="form-select">
                        <option value="">ทุกประเภทห้อง</option>
                        <option value="fan" <?php echo e(request('room_type') == 'fan' ? 'selected' : ''); ?>>🌀 ห้องพัดลม</option>
                        <option value="air" <?php echo e(request('room_type') == 'air' ? 'selected' : ''); ?>>❄️ ห้องแอร์</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1">
                        <i class="bi bi-search me-1"></i><?php echo e(__('ui.search')); ?>

                    </button>
                    <a href="<?php echo e(route('bookings.index')); ?>" class="btn btn-outline-secondary" title="ล้างตัวกรอง">
                        <i class="bi bi-arrow-clockwise"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    
    <div class="table-card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>ห้อง</th>
                        <th>ประเภท</th>
                        <th>แขก</th>
                        <th>ราคา</th>
                        <th>สถานะการจอง</th>
                        <th>การกระทำ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $bookings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $booking): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td>
                                <strong>
                                    <?php if($booking->room): ?>
                                        #<?php echo e($booking->room->room_number); ?>

                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </strong>
                            </td>
                            <td>
                                <?php if($booking->room): ?>
                                    <?php
                                        $type = $booking->room->room_type;
                                        $typeLabel = match ($type) {
                                            'fan' => ['🌀 พัดลม', 'bg-info text-white'],
                                            'air' => ['❄️ แอร์', 'bg-primary text-white'],
                                            'air_conditioning' => ['❄️ แอร์', 'bg-primary text-white'],
                                            default => [$type ?: '-', 'bg-secondary'],
                                        };
                                    ?>
                                    <span class="badge <?php echo e($typeLabel[1]); ?>"><?php echo e($typeLabel[0]); ?></span>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($booking->guest): ?>
                                    <?php echo e($booking->guest->first_name); ?> <?php echo e($booking->guest->last_name); ?>

                                <?php else: ?>
                                    <span class="text-muted fst-italic">ไม่ระบุผู้เช่า</span>
                                <?php endif; ?>
                            </td>
                            <td>฿<?php echo e(number_format($booking->total_price ?? 0, 2)); ?></td>
                            <td>
                                <?php
                                    $statusClasses = [
                                        'pending' => 'bg-warning',
                                        'confirmed' => 'bg-info',
                                        'checked_in' => 'bg-success',
                                        'checked_out' => 'bg-secondary',
                                        'cancelled' => 'bg-danger',
                                    ];
                                ?>
                                <span class="badge <?php echo e($statusClasses[$booking->status] ?? 'bg-light text-dark'); ?>">
                                    <?php echo e(enum_th('booking_status', $booking->status)); ?>

                                </span>
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    
                                    <a href="<?php echo e(route('bookings.show', $booking)); ?>" class="btn btn-sm btn-outline-info">
                                        <i class="bi bi-eye"></i> ดู
                                    </a>

                                    
                                    <?php if($booking->status == 'pending'): ?>
                                        <form action="<?php echo e(route('bookings.confirm', $booking)); ?>" method="POST"
                                            class="d-inline">
                                            <?php echo csrf_field(); ?>
                                            <button type="submit" class="btn btn-sm btn-outline-success"
                                                onclick="return confirm('ยืนยันการจองนี้?')" title="ยืนยันการจอง">
                                                <i class="bi bi-check-lg"></i> ยืนยัน
                                            </button>
                                        </form>
                                    <?php endif; ?>

                                    
                                    <?php if($booking->status != 'cancelled' && $booking->status != 'checked_out'): ?>
                                        <form action="<?php echo e(route('bookings.cancel', $booking)); ?>" method="POST"
                                            class="d-inline">
                                            <?php echo csrf_field(); ?>
                                            <button type="submit" class="btn btn-sm btn-outline-danger"
                                                onclick="return confirm('ยกเลิกการจองนี้?')" title="ยกเลิกการจอง">
                                                <i class="bi bi-x-lg"></i> ยกเลิก
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="6" class="text-center py-4">
                                <i class="bi bi-inbox fs-1 text-muted"></i>
                                <p class="text-muted mt-2">ไม่มีข้อมูลการจอง</p>
                                <a href="<?php echo e(route('bookings.create')); ?>"
                                    class="btn btn-primary-custom mt-2">เพิ่มการจองใหม่</a>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php if($bookings->hasPages()): ?>
        <div class="d-flex justify-content-center mt-4">
            <?php echo e($bookings->links('pagination::bootstrap-5')); ?>

        </div>
    <?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Rm1\resources\views/bookings/index.blade.php ENDPATH**/ ?>