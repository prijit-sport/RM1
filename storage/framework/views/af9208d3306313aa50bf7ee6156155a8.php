<?php $__env->startSection('title', 'จัดการใบแจ้งหนี้'); ?>
<?php $__env->startSection('page-title', 'จัดการใบแจ้งหนี้'); ?>

<?php $__env->startPush('styles'); ?>
    
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>

    
    <div class="page-header-romar">
        <div class="page-title-wrap">
            <h1><i class="bi bi-receipt"></i> จัดการใบแจ้งหนี้</h1>
            <p>จัดการค่าเช่าห้องและค่าน้ำ/ค่าไฟแยกประเภท</p>
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

    
    <div class="mb-4">
        <ul class="nav nav-tabs" style="border-bottom: 2px solid #e2e8f0;">
            <li class="nav-item">
                <a class="nav-link <?php echo e(request('invoice_type', 'all') === 'all' ? 'active' : ''); ?>"
                    href="<?php echo e(route('invoices.index', array_merge(request()->except('invoice_type', 'page'), []))); ?>"
                    style="<?php echo e(request('invoice_type', 'all') === 'all' ? 'border-bottom: 3px solid var(--romar-primary); color: var(--romar-primary); font-weight:600;' : 'color:#64748b;'); ?>">
                    <i class="bi bi-receipt me-1"></i> ทั้งหมด
                    <span class="badge bg-secondary ms-1"><?php echo e($stats['total'] ?? 0); ?></span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo e(request('invoice_type') === 'rent' ? 'active' : ''); ?>"
                    href="<?php echo e(route('invoices.index', array_merge(request()->except('invoice_type', 'page'), ['invoice_type' => 'rent']))); ?>"
                    style="<?php echo e(request('invoice_type') === 'rent' ? 'border-bottom: 3px solid #4f46e5; color: #4f46e5; font-weight:600;' : 'color:#64748b;'); ?>">
                    <i class="bi bi-house-door me-1"></i> ค่าห้องพัก
                    <span class="badge ms-1" style="background:#4f46e5;"><?php echo e($stats['rent_count'] ?? 0); ?></span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo e(request('invoice_type') === 'utility' ? 'active' : ''); ?>"
                    href="<?php echo e(route('invoices.index', array_merge(request()->except('invoice_type', 'page'), ['invoice_type' => 'utility']))); ?>"
                    style="<?php echo e(request('invoice_type') === 'utility' ? 'border-bottom: 3px solid #0ea5e9; color: #0ea5e9; font-weight:600;' : 'color:#64748b;'); ?>">
                    <i class="bi bi-lightning-charge me-1"></i> ค่าน้ำ/ค่าไฟ
                    <span class="badge ms-1" style="background:#0ea5e9;"><?php echo e($stats['utility_count'] ?? 0); ?></span>
                </a>
            </li>
        </ul>
    </div>

    
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
        <?php if(request('invoice_type')): ?>
            <input type="hidden" name="invoice_type" value="<?php echo e(request('invoice_type')); ?>">
        <?php endif; ?>

        <div class="search-input-romar">
            <i class="bi bi-search"></i>
            <input type="text" name="search" placeholder="ค้นหาเลขที่ใบแจ้งหนี้, ผู้เช่า, ห้อง..."
                value="<?php echo e(request('search')); ?>">
        </div>

        <select name="status" class="filter-select-romar" onchange="this.form.submit()">
            <option value="">ทุกสถานะ</option>
            <option value="draft" <?php echo e(request('status') == 'draft' ? 'selected' : ''); ?>>Draft</option>
            <option value="sent" <?php echo e(request('status') == 'sent' ? 'selected' : ''); ?>>รอชำระ</option>
            <option value="paid" <?php echo e(request('status') == 'paid' ? 'selected' : ''); ?>>ชำระแล้ว</option>
            <option value="overdue" <?php echo e(request('status') == 'overdue' ? 'selected' : ''); ?>>เกินกำหนด</option>
            <option value="cancelled" <?php echo e(request('status') == 'cancelled' ? 'selected' : ''); ?>>ยกเลิก</option>
        </select>

        <button type="submit" class="btn-romar">
            <i class="bi bi-funnel"></i> กรอง
        </button>

        <?php if(request()->hasAny(['search', 'status'])): ?>
            <a href="<?php echo e(route('invoices.index', request('invoice_type') ? ['invoice_type' => request('invoice_type')] : [])); ?>"
                class="btn-outline-romar">
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
                        <th>ประเภท</th>
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
                            $guest = $invoice->booking?->guest;
                            $room = $invoice->booking?->room;
                            $fullName = $guest ? trim($guest->first_name . ' ' . $guest->last_name) : 'ไม่ระบุ';
                            $initial = $guest ? mb_substr($guest->first_name ?? '?', 0, 2, 'UTF-8') : '?';
                            $variants = ['', 'v1', 'v2', 'v3', 'v4'];
                            $avatarVariant = $variants[$guest ? crc32($guest->id) % 5 : 0];
                            $invoiceType = $invoice->invoice_type ?? 'rent';
                        ?>
                        <tr>
                            
                            <td>
                                <div class="doc-num"><?php echo e($invoice->invoice_number); ?></div>
                                <div class="doc-num-sub">Booking #<?php echo e($invoice->booking_id); ?></div>
                            </td>

                            
                            <td>
                                <?php if($invoiceType === 'utility'): ?>
                                    <span class="badge rounded-pill"
                                        style="background:#e0f2fe; color:#0369a1; font-size:0.78rem; padding:5px 10px;">
                                        <i class="bi bi-lightning-charge-fill me-1"></i>ค่าน้ำ/ไฟ
                                    </span>
                                <?php else: ?>
                                    <span class="badge rounded-pill"
                                        style="background:#ede9fe; color:#4f46e5; font-size:0.78rem; padding:5px 10px;">
                                        <i class="bi bi-house-door-fill me-1"></i>ค่าห้อง
                                    </span>
                                <?php endif; ?>
                            </td>

                            
                            <td>
                                <div class="person">
                                    <div class="person-avatar <?php echo e($avatarVariant); ?>"><?php echo e($initial); ?></div>
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
                                    $overdueDays = null;
                                    if ($invoice->status === 'overdue' && $invoice->due_date) {
                                        $overdueDays = abs(
                                            now()
                                                ->startOfDay()
                                                ->diffInDays($invoice->due_date->startOfDay(), false),
                                        );
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
                                    <a href="<?php echo e(route('invoices.show', $invoice)); ?>" class="action-btn-romar view"
                                        title="ดูรายละเอียด">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="<?php echo e(route('invoices.edit', $invoice)); ?>" class="action-btn-romar edit"
                                        title="แก้ไข">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <?php if(in_array($invoice->status, ['sent', 'overdue'])): ?>
                                        <form action="<?php echo e(route('invoices.markAsPaid', $invoice)); ?>" method="POST"
                                            class="d-inline">
                                            <?php echo csrf_field(); ?>
                                            <button type="submit" class="action-btn-romar paid"
                                                title="ทำเครื่องหมายชำระแล้ว"
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
                            <td colspan="8">
                                <div class="empty-state">
                                    <div class="empty-state-icon"><i class="bi bi-inbox"></i></div>
                                    <div class="empty-state-title">ยังไม่มีข้อมูลใบแจ้งหนี้</div>
                                    <div class="empty-state-text">
                                        <?php if(request()->hasAny(['search', 'status'])): ?>
                                            ไม่พบรายการที่ตรงกับเงื่อนไขที่เลือก ลองล้างตัวกรองใหม่
                                        <?php else: ?>
                                            เพิ่มใบแจ้งหนี้ใหม่ได้ทันที
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

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\Rm1\resources\views/invoices/index.blade.php ENDPATH**/ ?>