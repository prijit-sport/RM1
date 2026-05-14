 
<?php $__env->startSection('title', 'จัดการใบแจ้งหนี้'); ?>
<?php $__env->startSection('page-title', 'จัดการใบแจ้งหนี้'); ?>
 
<?php $__env->startPush('styles'); ?>
    
    
<?php $__env->stopPush(); ?>
 
<?php $__env->startSection('content'); ?>
 

<div class="page-header-romar">
    <div class="page-title-wrap">
        <h1><i class="bi bi-receipt"></i> จัดการใบแจ้งหนี้</h1>
        <p>ออก ตรวจสอบ และติดตามใบแจ้งหนี้ค่าเช่ารายเดือน</p>
    </div>
    <div class="page-actions">
        <a href="<?php echo e(route('invoices.bulk-create')); ?>" class="btn-outline-romar">
            <i class="bi bi-plus-circle"></i> สร้างหลายใบ
        </a>
        <a href="<?php echo e(route('invoices.export')); ?>" class="btn-outline-romar">
            <i class="bi bi-download"></i> <?php echo e(__('ui.export')); ?>

        </a>
        <a href="<?php echo e(route('invoices.create')); ?>" class="btn-romar">
            <i class="bi bi-plus-lg"></i> เพิ่มใบแจ้งหนี้
        </a>
    </div>
</div>
 

<?php if(session('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i><?php echo e(session('success')); ?>

        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>
 

<div class="stats-grid">
    <div class="stat-card" style="--stat-color: var(--romar-primary); --stat-bg: var(--romar-soft);">
        <div class="stat-icon"><i class="bi bi-receipt-cutoff"></i></div>
        <div class="stat-label">ใบแจ้งหนี้ทั้งหมด</div>
        <div class="stat-value"><?php echo e(number_format($stats['total'] ?? 0)); ?></div>
        <?php if(($stats['monthly_diff'] ?? 0) !== 0): ?>
            <div class="stat-meta <?php echo e($stats['monthly_diff'] > 0 ? 'up' : 'down'); ?>">
                <i class="bi bi-arrow-<?php echo e($stats['monthly_diff'] > 0 ? 'up' : 'down'); ?>-short"></i>
                <?php echo e($stats['monthly_diff'] > 0 ? '+' : ''); ?><?php echo e($stats['monthly_diff']); ?> จากเดือนที่แล้ว
            </div>
        <?php endif; ?>
    </div>
 
    <div class="stat-card" style="--stat-color: var(--success); --stat-bg: var(--success-soft);">
        <div class="stat-icon"><i class="bi bi-check-circle"></i></div>
        <div class="stat-label">ชำระแล้ว</div>
        <div class="stat-value"><?php echo e(number_format($stats['paid_count'] ?? 0)); ?></div>
        <div class="stat-meta">มูลค่ารวม ฿ <?php echo e(number_format($stats['paid_amount'] ?? 0)); ?></div>
    </div>
 
    <div class="stat-card" style="--stat-color: var(--warning); --stat-bg: var(--warning-soft);">
        <div class="stat-icon"><i class="bi bi-clock-history"></i></div>
        <div class="stat-label">รอชำระ</div>
        <div class="stat-value"><?php echo e(number_format($stats['sent_count'] ?? 0)); ?></div>
        <div class="stat-meta">มูลค่ารวม ฿ <?php echo e(number_format($stats['sent_amount'] ?? 0)); ?></div>
    </div>
 
    <div class="stat-card" style="--stat-color: var(--danger); --stat-bg: var(--danger-soft);">
        <div class="stat-icon"><i class="bi bi-exclamation-triangle"></i></div>
        <div class="stat-label">เกินกำหนด</div>
        <div class="stat-value"><?php echo e(number_format($stats['overdue_count'] ?? 0)); ?></div>
        <?php if(($stats['overdue_count'] ?? 0) > 0): ?>
            <div class="stat-meta down"><i class="bi bi-bell"></i> ต้องติดตามด่วน</div>
        <?php else: ?>
            <div class="stat-meta">ไม่มีรายการเกินกำหนด</div>
        <?php endif; ?>
    </div>
</div>
 

<form method="GET" action="<?php echo e(route('invoices.index')); ?>" class="filter-bar">
    <div class="search-input-romar">
        <i class="bi bi-search"></i>
        <input type="text"
               name="search"
               placeholder="ค้นหาเลขที่ใบแจ้งหนี้, ชื่อผู้เช่า, ห้อง..."
               value="<?php echo e(request('search')); ?>">
    </div>
 
    <select name="status" class="filter-select-romar" onchange="this.form.submit()">
        <option value="">ทุกสถานะ</option>
        <option value="draft"     <?php echo e(request('status') == 'draft'     ? 'selected' : ''); ?>>Draft</option>
        <option value="sent"      <?php echo e(request('status') == 'sent'      ? 'selected' : ''); ?>>รอชำระ</option>
        <option value="paid"      <?php echo e(request('status') == 'paid'      ? 'selected' : ''); ?>>ชำระแล้ว</option>
        <option value="overdue"   <?php echo e(request('status') == 'overdue'   ? 'selected' : ''); ?>>เกินกำหนด</option>
        <option value="cancelled" <?php echo e(request('status') == 'cancelled' ? 'selected' : ''); ?>>ยกเลิก</option>
    </select>
 
    <button type="submit" class="btn-romar">
        <i class="bi bi-funnel"></i> กรอง
    </button>
 
    <?php if(request()->hasAny(['search', 'status'])): ?>
        <a href="<?php echo e(route('invoices.index')); ?>" class="btn-outline-romar">
            <i class="bi bi-x-lg"></i> ล้าง
        </a>
    <?php endif; ?>
</form>
 

<div class="data-card">
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>เลขที่ใบแจ้งหนี้</th>
                    <th>ผู้เช่า / ห้อง</th>
                    <th>ยอดรวม</th>
                    <th>วันออก</th>
                    <th>ครบกำหนด</th>
                    <th>สถานะ</th>
                    <th style="text-align:right;">การกระทำ</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $invoices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $invoice): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php
                        // ดึงข้อมูลผู้เช่า/ห้องจาก relationship
                        $guest = $invoice->booking?->guest;
                        $room  = $invoice->booking?->room;
                        $fullName = $guest ? trim($guest->first_name . ' ' . $guest->last_name) : 'ไม่ระบุ';
 
                        // ตัวอักษรย่อสำหรับ avatar (รับทั้งไทยและอังกฤษ)
                        $initial = $guest ? mb_substr($guest->first_name ?? '?', 0, 2, 'UTF-8') : '?';
 
                        // เลือก variant สีจาก hash ของชื่อ เพื่อให้แต่ละคนสีไม่ซ้ำ
                        $variants = ['', 'v1', 'v2', 'v3', 'v4'];
                        $avatarVariant = $variants[$guest ? (crc32($guest->id) % 5) : 0];
                    ?>
                    <tr>
                        
                        <td>
                            <div class="doc-num"><?php echo e($invoice->invoice_number); ?></div>
                            <div class="doc-num-sub">Booking #<?php echo e($invoice->booking_id); ?></div>
                        </td>
 
                        
                        <td>
                            <div class="person">
                                <div class="person-avatar <?php echo e($avatarVariant); ?>">
                                    <?php echo e($initial); ?>

                                </div>
                                <div>
                                    <div class="person-name"><?php echo e($fullName); ?></div>
                                    <?php if($room): ?>
                                        <span class="room-badge">
                                            <i class="bi bi-door-closed"></i> <?php echo e($room->room_number); ?>

                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </td>
 
                        
                        <td>
                            <span class="amount">
                                <span class="currency">฿</span><?php echo e(number_format($invoice->total, 2)); ?>

                            </span>
                        </td>
 
                        
                        <td><?php echo e(optional($invoice->issue_date)->format('d/m/Y')); ?></td>
 
                        
                        <td><?php echo e(optional($invoice->due_date)->format('d/m/Y')); ?></td>
 
                        
                        <td>
                            <?php
                                // กรณี overdue คำนวณวันที่เกิน
                                $overdueDays = null;
                                if ($invoice->status === 'overdue' && $invoice->due_date) {
                                    $overdueDays = now()->startOfDay()->diffInDays($invoice->due_date->startOfDay(), false);
                                    $overdueDays = abs($overdueDays);
                                }
                            ?>
                            <span class="status-pill status-<?php echo e($invoice->status); ?>">
                                <?php if($invoice->status === 'overdue' && $overdueDays): ?>
                                    เกินกำหนด <?php echo e($overdueDays); ?> วัน
                                <?php else: ?>
                                    <?php echo e(enum_bi('invoice_status', $invoice->status, ucfirst($invoice->status))); ?>

                                <?php endif; ?>
                            </span>
                        </td>
 
                        
                        <td>
                            <div class="action-bar">
                                <a href="<?php echo e(route('invoices.show', $invoice)); ?>"
                                   class="action-btn-romar view" title="ดูรายละเอียด">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="<?php echo e(route('invoices.edit', $invoice)); ?>"
                                   class="action-btn-romar edit" title="แก้ไข">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <?php if(in_array($invoice->status, ['sent', 'overdue'])): ?>
                                    <form action="<?php echo e(route('invoices.markAsPaid', $invoice)); ?>"
                                          method="POST" class="d-inline">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" class="action-btn-romar paid"
                                                title="ทำเครื่องหมายว่าชำระแล้ว"
                                                onclick="return confirm('ยืนยันการชำระเงิน?')">
                                            <i class="bi bi-check-lg"></i>
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="7">
                            <div class="empty-state">
                                <div class="empty-state-icon">
                                    <i class="bi bi-inbox"></i>
                                </div>
                                <div class="empty-state-title">ยังไม่มีข้อมูลใบแจ้งหนี้</div>
                                <div class="empty-state-text">
                                    <?php if(request()->hasAny(['search', 'status'])): ?>
                                        ไม่พบรายการตามเงื่อนไขที่เลือก ลองเปลี่ยนคำค้นหา
                                    <?php else: ?>
                                        เริ่มออกใบแจ้งหนี้ใบแรกของคุณ
                                    <?php endif; ?>
                                </div>
                                <?php if(!request()->hasAny(['search', 'status'])): ?>
                                    <a href="<?php echo e(route('invoices.create')); ?>" class="btn-romar">
                                        <i class="bi bi-plus-lg"></i> เพิ่มใบแจ้งหนี้
                                    </a>
                                <?php else: ?>
                                    <a href="<?php echo e(route('invoices.index')); ?>" class="btn-outline-romar">
                                        <i class="bi bi-arrow-counterclockwise"></i> ล้างตัวกรอง
                                    </a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
 
    
    <?php if($invoices->hasPages()): ?>
        <div class="pagination-wrap">
            <div class="pagination-info">
                แสดง <?php echo e($invoices->firstItem()); ?>-<?php echo e($invoices->lastItem()); ?>

                จาก <?php echo e(number_format($invoices->total())); ?> รายการ
            </div>
            <div>
                <?php echo e($invoices->withQueryString()->links('pagination::bootstrap-5')); ?>

            </div>
        </div>
    <?php endif; ?>
</div>
 
<?php $__env->stopSection(); ?>
 
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Rm1\resources\views/invoices/index.blade.php ENDPATH**/ ?>