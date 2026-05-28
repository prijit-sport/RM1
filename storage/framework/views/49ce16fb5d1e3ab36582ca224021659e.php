

<?php $__env->startSection('content'); ?>
    <div class="container py-4">

        
        <div class="d-flex align-items-center gap-2 mb-4">
            <a href="<?php echo e(route('bookings.index')); ?>" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-arrow-left"></i>
            </a>
            <h5 class="mb-0"><i class="bi bi-calendar-check text-success me-2"></i>รายละเอียดการจอง #<?php echo e($booking->id); ?>

            </h5>
        </div>

        <div class="row g-4">

            
            <div class="col-lg-6">

                
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="mb-0 text-primary">
                            <i class="bi bi-person-circle me-2"></i>ผู้เช่า
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-muted">ชื่อ-สกุล</label>
                            <p class="fs-5 fw-bold mb-0">
                                <?php if($booking->guest): ?>
                                    <?php echo e($booking->guest->first_name); ?> <?php echo e($booking->guest->last_name); ?>

                                <?php else: ?>
                                    <span class="text-muted fst-italic">ไม่ระบุผู้เช่า</span>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                </div>

                
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="mb-0 text-primary">
                            <i class="bi bi-door-open me-2"></i>ข้อมูลห้องพัก
                        </h6>
                    </div>
                    <div class="card-body">

                        
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-muted">ห้องหมายเลข</label>
                            <p class="fs-5 fw-bold mb-0">
                                <?php if($booking->room): ?>
                                    <span class="badge bg-dark">#<?php echo e($booking->room->room_number); ?></span>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </p>
                        </div>

                        
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-muted">ประเภทห้อง</label>
                            <p class="fs-5 fw-bold mb-0">
                                <?php if($booking->room): ?>
                                    <?php
                                        $type = $booking->room->room_type;
                                        $typeLabel = match ($type) {
                                            'fan' => ['🌀 พัดลม', 'bg-info'],
                                            'air' => ['❄️ แอร์', 'bg-primary'],
                                            'air_conditioning' => ['❄️ แอร์', 'bg-primary'],
                                            default => [$type ?: '-', 'bg-secondary'],
                                        };
                                    ?>
                                    <span class="badge <?php echo e($typeLabel[1]); ?> text-white"><?php echo e($typeLabel[0]); ?></span>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </p>
                        </div>

                        
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-muted">โซน</label>
                            <p class="fs-5 fw-bold mb-0">
                                <?php if($booking->room?->zone): ?>
                                    <?php echo e($booking->room->zone); ?>

                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </p>
                        </div>

                        
                        <div class="mb-0">
                            <label class="form-label fw-semibold text-muted">วันที่เข้าพัก</label>
                            <p class="fs-5 fw-bold mb-0">
                                <?php if($booking->check_in_date): ?>
                                    <i class="bi bi-calendar me-1"></i><?php echo e($booking->check_in_date->format('d/m/Y')); ?>

                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </p>
                        </div>

                    </div>
                </div>

            </div>


            
            <div class="col-lg-6">

                
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="mb-0 text-primary">
                            <i class="bi bi-cash-coin me-2"></i>มัดจำและค่าใช้จ่ายแรกเข้า
                        </h6>
                    </div>
                    <div class="card-body">

                        
                        <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                            <div>
                                <div class="fw-semibold">ค่าเช่าเดือนแรก</div>
                                <small class="text-muted">ห้องที่เลือก</small>
                            </div>
                            <div class="text-end">
                                <span class="fw-bold fs-5 text-dark"><?php echo e(number_format($booking->rent_amount ?? 0)); ?></span>
                                <span class="text-muted ms-1">฿</span>
                            </div>
                        </div>

                        
                        <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                            <div>
                                <div class="fw-semibold">เงินมัดจำ</div>
                                <small class="text-muted">1 เดือน</small>
                            </div>
                            <div class="text-end">
                                <span
                                    class="fw-bold fs-5 text-dark"><?php echo e(number_format($booking->deposit_amount ?? 0)); ?></span>
                                <span class="text-muted ms-1">฿</span>
                            </div>
                        </div>

                        
                        <div class="d-flex justify-content-between align-items-center pt-3 mt-1">
                            <div class="fw-bold fs-6">ยอดรวมที่ต้องชำระ</div>
                            <div class="text-end">
                                <span class="fw-bold text-primary"
                                    style="font-size:1.5rem"><?php echo e(number_format(($booking->rent_amount ?? 0) + ($booking->deposit_amount ?? 0))); ?></span>
                                <span class="text-primary ms-1 fw-semibold">฿</span>
                            </div>
                        </div>

                    </div>
                </div>

                
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="mb-0 text-primary">
                            <i class="bi bi-speedometer2 me-2"></i>เลขมิเตอร์เริ่มต้น
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-6">
                                <label class="form-label fw-semibold text-muted">
                                    <i class="bi bi-lightning-charge text-warning me-1"></i>มิเตอร์ไฟ
                                </label>
                                <p class="fs-5 fw-bold mb-0">
                                    <?php echo e($booking->electric_meter_start ?? '—'); ?> หน่วย
                                </p>
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-semibold text-muted">
                                    <i class="bi bi-droplet text-info me-1"></i>มิเตอร์น้ำ
                                </label>
                                <p class="fs-5 fw-bold mb-0">
                                    <?php echo e($booking->water_meter_start ?? '—'); ?> หน่วย
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <label class="form-label fw-semibold text-muted mb-2">สถานะการจอง</label>
                        <?php
                            $statusClasses = [
                                'pending' => 'bg-warning',
                                'confirmed' => 'bg-info',
                                'checked_in' => 'bg-success',
                                'checked_out' => 'bg-secondary',
                                'cancelled' => 'bg-danger',
                            ];
                        ?>
                        <p class="mb-0">
                            <span class="badge <?php echo e($statusClasses[$booking->status] ?? 'bg-light text-dark'); ?> fs-6">
                                <?php echo e(enum_th('booking_status', $booking->status)); ?>

                            </span>
                        </p>
                    </div>
                </div>

                
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <label class="form-label fw-semibold text-muted mb-2">หมายเหตุ</label>
                        <p class="mb-0">
                            <?php if($booking->notes): ?>
                                <span class="text-dark"><?php echo e($booking->notes); ?></span>
                            <?php else: ?>
                                <span class="text-muted fst-italic">ไม่มีหมายเหตุ</span>
                            <?php endif; ?>
                        </p>
                    </div>
                </div>

            </div>

        </div>

        
        <div class="d-flex justify-content-between mt-4">
            <a href="<?php echo e(route('bookings.index')); ?>" class="btn btn-outline-secondary px-4">
                <i class="bi bi-arrow-left me-1"></i> กลับ
            </a>

            <div class="d-flex gap-2">
                
                <?php if($booking->status == 'pending'): ?>
                    <form action="<?php echo e(route('bookings.confirm', $booking)); ?>" method="POST" class="d-inline">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="btn btn-success px-4" onclick="return confirm('ยืนยันการจองนี้?')">
                            <i class="bi bi-check-circle me-1"></i> ยืนยัน
                        </button>
                    </form>
                <?php endif; ?>

                
                <?php if($booking->status != 'cancelled' && $booking->status != 'checked_out'): ?>
                    <form action="<?php echo e(route('bookings.cancel', $booking)); ?>" method="POST" class="d-inline">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="btn btn-danger px-4" onclick="return confirm('ยกเลิกการจองนี้?')">
                            <i class="bi bi-x-circle me-1"></i> ยกเลิก
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </div>

    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Rm1\resources\views/bookings/show.blade.php ENDPATH**/ ?>