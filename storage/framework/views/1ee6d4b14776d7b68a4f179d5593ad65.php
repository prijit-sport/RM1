<?php $__env->startSection('title', 'แดชบอร์ด'); ?>

<?php $__env->startSection('page-title', 'แดชบอร์ด'); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .stat-card {
        border: none;
        overflow: hidden;
    }
    
    .stat-card .stat-icon {
        width: 60px;
        height: 60px;
    }
    
    .chart-container {
        position: relative;
        height: 300px;
    }
    
    .progress-card {
        transition: all 0.3s ease;
    }
    
    .progress-card:hover {
        transform: translateY(-3px);
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<?php
    /** @var \Illuminate\Support\Collection|\App\Models\Booking[] $recentBookings */
    /** @var \Illuminate\Support\Collection|\App\Models\Invoice[] $pendingInvoices */
?>
<div class="row g-4 mb-4">
    <!-- KPI Cards -->
    <div class="col-md-6 col-xl-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <p class="text-muted mb-1">ห้องทั้งหมด</p>
                    <h3 class="mb-0"><?php echo e(number_format($roomCount ?? 0)); ?></h3>
                    <small class="text-success">
                        <i class="bi bi-arrow-up"></i> <?php echo e(number_format((($occupiedCount ?? 0) / max($roomCount ?? 1, 1)) * 100, 1)); ?>% ถูกเช่า
                    </small>
                </div>
                <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                    <i class="bi bi-door-open"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 col-xl-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <p class="text-muted mb-1">ห้องว่าง</p>
                    <h3 class="mb-0"><?php echo e(number_format(($roomCount ?? 0) - ($occupiedCount ?? 0))); ?></h3>
                    <small class="text-info">
                        <i class="bi bi-info-circle"></i> พร้อมให้เช่า
                    </small>
                </div>
                <div class="stat-icon bg-success bg-opacity-10 text-success">
                    <i class="bi bi-check-circle"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 col-xl-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <p class="text-muted mb-1">ผู้เช่า</p>
                    <h3 class="mb-0"><?php echo e(number_format($guestCount ?? 0)); ?></h3>
                    <small class="text-warning">
                        <i class="bi bi-people"></i> ลูกค้าทั้งหมด
                    </small>
                </div>
                <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                    <i class="bi bi-people-fill"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 col-xl-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <p class="text-muted mb-1">การจอง</p>
                    <h3 class="mb-0"><?php echo e(number_format($bookingCount ?? 0)); ?></h3>
                    <small class="text-danger">
                        <i class="bi bi-clock"></i> รอตรวจสอบ
                    </small>
                </div>
                <div class="stat-icon bg-danger bg-opacity-10 text-danger">
                    <i class="bi bi-calendar-check"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Room Status by Type -->
<div class="row g-4 mb-4">
    <?php
        $roomTypeStatsFillingSafe = $roomTypeStatsFilling ?? [];
        $types = ['fan' => 'fan', 'air_conditioning' => 'air_conditioning'];
        $statuses = ['available' => ['label' => 'ว่าง', 'class' => 'success'], 'occupied' => ['label' => 'ไม่ว่าง', 'class' => 'primary'], 'maintenance' => ['label' => 'ซ่อม', 'class' => 'warning']];
    ?>

    <?php $__currentLoopData = $types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $typeKey => $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php
            $stats = $roomTypeStatsFillingSafe[$typeKey] ?? ['available' => 0, 'occupied' => 0, 'maintenance' => 0];
            $total = ($stats['available'] ?? 0) + ($stats['occupied'] ?? 0) + ($stats['maintenance'] ?? 0);
        ?>

        <div class="col-lg-6">
            <div class="table-card p-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">สรุปโซนสถานะ: <?php echo e(enum_bi('room_type', $typeKey)); ?></h5>
                    <span class="text-muted">ทั้งหมด <?php echo e(number_format($total)); ?> ห้อง</span>
                </div>

                <div class="mb-4">
                    <?php $__currentLoopData = $statuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $statusKey => $meta): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $count = (int) ($stats[$statusKey] ?? 0);
                            $pct = $total > 0 ? ($count / $total) * 100 : 0;
                        ?>

                        <div class="d-flex justify-content-between mb-2">
                            <span><?php echo e($meta['label']); ?></span>
                            <span class="fw-bold"><?php echo e(number_format($count)); ?> ห้อง (<?php echo e(number_format($pct, 1)); ?>%)</span>
                        </div>
                        <div class="progress" style="height: 10px; margin-bottom: 14px;">
                            <div class="progress-bar bg-<?php echo e($meta['class']); ?>" role="progressbar" style="width: <?php echo e(min($pct, 100)); ?>%"></div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>

<!-- Charts Row -->
<div class="row g-4 mb-4">
    <div class="col-lg-8">
        <div class="table-card p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="mb-0">สถิติการเช่าห้อง</h5>
                <select class="form-select form-select-sm w-auto">
                    <option value="7">7 วันที่ผ่านมา</option>
                    <option value="30" selected>30 วันที่ผ่านมา</option>
                    <option value="90">90 วันที่ผ่านมา</option>
                </select>
            </div>
            <div class="chart-container">
                <canvas id="bookingChart"></canvas>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="table-card p-4">
            <h5 class="mb-4">สถานะห้อง</h5>
            
            <?php
            $totalRooms = max($roomCount ?? 1, 1);
            $occupiedPercent = ($occupiedCount ?? 0) / $totalRooms * 100;
            $availablePercent = (($roomCount ?? 0) - ($occupiedCount ?? 0)) / $totalRooms * 100;
            $maintenancePercent = ($maintenanceCount ?? 0) / $totalRooms * 100;
            $hasRooms = ($roomCount ?? 0) > 0;
            ?>
            
            <?php if($hasRooms): ?>
            <div class="mb-4">
                <div class="d-flex justify-content-between mb-2">
                    <span>ห้องที่ถูกเช่า</span>
                    <span class="fw-bold"><?php echo e(number_format($occupiedPercent, 1)); ?>%</span>
                </div>
                <div class="progress" style="height: 10px;">
                    <div class="progress-bar bg-primary" role="progressbar" 
                         style="width: <?php echo e(min($occupiedPercent, 95)); ?>%"></div>
                </div>
            </div>
            
            <div class="mb-4">
                <div class="d-flex justify-content-between mb-2">
                    <span>ห้องว่าง</span>
                    <span class="fw-bold"><?php echo e(number_format($availablePercent, 1)); ?>%</span>
                </div>
                <div class="progress" style="height: 10px;">
                    <div class="progress-bar bg-success" role="progressbar" 
                         style="width: <?php echo e(min($availablePercent, 95)); ?>%"></div>
                </div>
            </div>
            
            <div class="mb-4">
                <div class="d-flex justify-content-between mb-2">
                    <span>ห้องปรับปรุง</span>
                    <span class="fw-bold"><?php echo e(number_format($maintenancePercent, 1)); ?>%</span>
                </div>
                <div class="progress" style="height: 10px;">
                    <div class="progress-bar bg-warning" role="progressbar" 
                         style="width: <?php echo e(min($maintenancePercent, 95)); ?>%"></div>
                </div>
            </div>
            <?php else: ?>
            <div class="text-center text-muted py-4">
                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                ไม่มีข้อมูลห้อง
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Tables Row -->
<div class="row g-4">
    <div class="col-lg-6">
        <div class="table-card">
            <div class="p-4 border-bottom">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">การจองล่าสุด</h5>
                    <a href="<?php echo e(route('bookings.index')); ?>" class="btn btn-sm btn-outline-primary">
                        ดูทั้งหมด <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>ห้อง</th>
                            <th>ผู้เช่า</th>
                            <th>วันที่</th>
                            <th>สถานะ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $recentBookings ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $booking): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php /** @var \App\Models\Booking $booking */ ?>
                        <tr>
                            <td>
                                <?php if($booking->room_id): ?>
                                <a href="<?php echo e(route('rooms.show', $booking->room_id)); ?>" class="text-decoration-none">
                                    <?php echo e(optional($booking->room)->room_number ?? '-'); ?>

                                </a>
                                <?php else: ?>
                                -
                                <?php endif; ?>
                            </td>
                            <td><?php echo e(optional($booking->guest)->full_name ?? '-'); ?></td>
                            <td><?php echo e(\Carbon\Carbon::parse($booking->check_in_date)->format('d/m/Y')); ?></td>
                            <td>
                                <?php
                                $statusClass = match($booking->status) {
                                    'confirmed' => 'success',
                                    'pending' => 'warning',
                                    'cancelled' => 'danger',
                                    default => 'secondary'
                                };
                                ?>
                                <span class="status-badge bg-<?php echo e($statusClass); ?> bg-opacity-10 text-<?php echo e($statusClass); ?>">
                                    <?php echo e(enum_bi('booking_status', $booking->status, $booking->status)); ?>

                                </span>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                ไม่มีการจองล่าสุด
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <div class="col-lg-6">
        <div class="table-card">
            <div class="p-4 border-bottom">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">ใบแจ้งหนี้ที่รอชำระ</h5>
                    <a href="<?php echo e(route('invoices.index')); ?>" class="btn btn-sm btn-outline-primary">
                        ดูทั้งหมด <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>Invoice</th>
                            <th>Guest</th>
                            <th>Total</th>
                            <th>Due Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $pendingInvoices ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $invoice): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php /** @var \App\Models\Invoice $invoice */ ?>
                        <tr>
                            <td>
                                <a href="<?php echo e(route('invoices.show', $invoice->id)); ?>" class="text-decoration-none">
                                    <?php echo e($invoice->invoice_number); ?>

                                </a>
                            </td>
                            <td><?php echo e(optional(optional($invoice->booking)->guest)->full_name ?? '-'); ?></td>
                            <td><?php echo e(number_format($invoice->total ?? 0, 2)); ?> &#3647;</td>
                            <td>
                                <?php
                                $dueDate = \Carbon\Carbon::parse($invoice->due_date);
                                $isOverdue = $dueDate->isPast();
                                ?>
                                <span class="<?php echo e($isOverdue ? 'text-danger' : 'text-muted'); ?>">
                                    <?php echo e($dueDate->format('d/m/Y')); ?>

                                </span>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">
                                <i class="bi bi-check-circle fs-1 d-block mb-2 text-success"></i>
                                ไม่มีบิลที่รอชำระ
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Booking Chart
    const ctx = document.getElementById('bookingChart').getContext('2d');
    const bookingChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($chartLabels ?? ['ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.']); ?>,
            datasets: [{
                label: 'จำนวนการจอง',
                data: <?php echo json_encode($chartData ?? [12, 19, 8, 15, 22, 18]); ?>,
                backgroundColor: 'rgba(79, 70, 229, 0.8)',
                borderColor: 'rgba(79, 70, 229, 1)',
                borderWidth: 1,
                borderRadius: 6,
                maxBarThickness: 50
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Rm1\resources\views/dashboard/index.blade.php ENDPATH**/ ?>