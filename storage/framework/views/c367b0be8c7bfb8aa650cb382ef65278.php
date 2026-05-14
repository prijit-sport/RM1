
 
<?php $__env->startSection('title', 'รายละเอียดใบแจ้งหนี้'); ?>
<?php $__env->startSection('page-title', 'รายละเอียดใบแจ้งหนี้'); ?>
 
<?php $__env->startPush('styles'); ?>
<style>
/* ============================================
   PAGE-SPECIFIC: invoices/show
   (CSS หลักอยู่ที่ public/css/romar-theme.css)
   ============================================ */
 
/* Back link */
.back-link {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    color: var(--text-secondary);
    text-decoration: none;
    font-size: 0.88rem;
    margin-bottom: 14px;
    transition: color 0.15s;
}
.back-link:hover {
    color: var(--romar-primary);
}
 
/* Invoice header (รวมเลขที่ + status) */
.invoice-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 16px;
    margin-bottom: 22px;
}
.invoice-header-left h1 {
    font-size: 1.6rem;
    font-weight: 700;
    margin: 0 0 4px 0;
    display: flex;
    align-items: center;
    gap: 10px;
    color: var(--text-primary);
}
.invoice-number-big {
    font-family: 'Inter', monospace;
    color: var(--romar-primary);
    font-weight: 700;
}
.invoice-meta {
    font-size: 0.85rem;
    color: var(--text-secondary);
    display: flex;
    align-items: center;
    gap: 14px;
    flex-wrap: wrap;
}
.invoice-meta .dot-sep {
    color: var(--border);
}
 
/* Mini info bar (4 metrics ด้านบน) */
.info-bar {
    background: white;
    border: 1px solid var(--border);
    border-radius: 14px;
    box-shadow: var(--shadow-sm);
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    margin-bottom: 22px;
    overflow: hidden;
}
.info-bar-item {
    padding: 18px 20px;
    border-right: 1px solid var(--border);
}
.info-bar-item:last-child { border-right: none; }
.info-bar-label {
    font-size: 0.75rem;
    color: var(--text-secondary);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 6px;
    font-weight: 500;
}
.info-bar-value {
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--text-primary);
}
.info-bar-value.amount-big {
    color: var(--romar-primary);
    font-family: 'Inter', sans-serif;
}
.info-bar-value .small-unit {
    font-size: 0.8rem;
    color: var(--text-secondary);
    font-weight: 500;
    margin-left: 3px;
}
 
/* Section cards */
.section-card {
    background: white;
    border-radius: 14px;
    border: 1px solid var(--border);
    box-shadow: var(--shadow-sm);
    margin-bottom: 18px;
    overflow: hidden;
}
.section-card-head {
    padding: 16px 20px;
    border-bottom: 1px solid var(--border);
    display: flex;
    align-items: center;
    gap: 10px;
    font-weight: 600;
    color: var(--text-primary);
    font-size: 0.95rem;
}
.section-card-head i {
    color: var(--romar-primary);
    font-size: 1.05rem;
}
.section-card-body { padding: 20px; }
 
/* Detail rows (label | value) */
.detail-row-romar {
    display: flex;
    padding: 12px 0;
    border-bottom: 1px solid var(--border);
    font-size: 0.9rem;
}
.detail-row-romar:last-child {
    border-bottom: none;
    padding-bottom: 0;
}
.detail-row-romar:first-child {
    padding-top: 0;
}
.detail-label-romar {
    color: var(--text-secondary);
    width: 160px;
    flex-shrink: 0;
    font-weight: 500;
}
.detail-value-romar {
    color: var(--text-primary);
    flex: 1;
    font-weight: 500;
}
 
/* Line items table (รายการในบิล) */
.items-table {
    width: 100%;
    border-collapse: collapse;
}
.items-table th {
    text-align: left;
    padding: 10px 0;
    font-size: 0.75rem;
    color: var(--text-secondary);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border-bottom: 2px solid var(--border);
    font-weight: 600;
}
.items-table th:last-child { text-align: right; }
.items-table td {
    padding: 14px 0;
    border-bottom: 1px solid var(--border);
    font-size: 0.9rem;
}
.items-table td:last-child {
    text-align: right;
    font-family: 'Inter', sans-serif;
    font-weight: 600;
}
.items-table tr:last-child td { border-bottom: none; }
 
/* Total summary */
.total-summary {
    margin-top: 8px;
    padding-top: 16px;
    border-top: 2px solid var(--border);
}
.total-row {
    display: flex;
    justify-content: space-between;
    padding: 6px 0;
    font-size: 0.9rem;
    color: var(--text-secondary);
}
.total-row.grand {
    margin-top: 10px;
    padding-top: 14px;
    border-top: 1px dashed var(--border);
    font-size: 1.15rem;
    color: var(--text-primary);
    font-weight: 700;
}
.total-row.grand .amount-big {
    color: var(--romar-primary);
    font-family: 'Inter', sans-serif;
}
 
/* Tenant card (ขวา) */
.tenant-block {
    display: flex;
    align-items: center;
    gap: 14px;
    margin-bottom: 18px;
}
.tenant-block .person-avatar {
    width: 54px; height: 54px;
    font-size: 1.05rem;
}
.tenant-block-name {
    font-size: 1.02rem;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 2px;
}
.tenant-block-sub {
    font-size: 0.8rem;
    color: var(--text-secondary);
}
.contact-list {
    list-style: none;
    margin: 0;
    padding: 0;
}
.contact-list li {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 8px 0;
    font-size: 0.88rem;
    color: var(--text-primary);
    border-bottom: 1px solid var(--border);
}
.contact-list li:last-child { border-bottom: none; }
.contact-list li i {
    color: var(--romar-primary);
    width: 18px;
}
 
/* Notes box */
.notes-box {
    background: #fefce8;
    border-left: 3px solid #facc15;
    padding: 14px 16px;
    border-radius: 8px;
    color: #713f12;
    font-size: 0.88rem;
    line-height: 1.6;
    white-space: pre-wrap;
}
 
/* Status pill ที่ใหญ่กว่า list */
.status-pill-lg {
    padding: 6px 16px;
    font-size: 0.85rem;
}
 
/* Danger zone (ปุ่มลบ) */
.danger-zone {
    margin-top: 24px;
    padding: 18px 20px;
    background: #fef2f2;
    border: 1px dashed var(--danger);
    border-radius: 12px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 12px;
}
.danger-zone-text {
    font-size: 0.85rem;
    color: #7f1d1d;
}
.danger-zone-text strong {
    color: var(--danger);
    display: block;
    margin-bottom: 2px;
}
.btn-danger-romar {
    background: var(--danger);
    color: white;
    border: none;
    padding: 10px 18px;
    border-radius: 10px;
    font-family: inherit;
    font-weight: 500;
    font-size: 0.88rem;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
    transition: all 0.2s;
}
.btn-danger-romar:hover {
    background: #dc2626;
    transform: translateY(-1px);
    box-shadow: 0 4px 10px rgba(239, 68, 68, 0.3);
}
 
@media (max-width: 768px) {
    .info-bar { grid-template-columns: repeat(2, 1fr); }
    .info-bar-item:nth-child(2) { border-right: none; }
    .detail-label-romar { width: 130px; }
}
</style>
<?php $__env->stopPush(); ?>
 
<?php $__env->startSection('content'); ?>
 

<a href="<?php echo e(route('invoices.index')); ?>" class="back-link">
    <i class="bi bi-arrow-left"></i> กลับไปยังรายการใบแจ้งหนี้
</a>
 

<?php
    // ดึงข้อมูลผู้เช่าและห้องจาก relationship (null-safe)
    $guest = $invoice->booking?->guest;
    $room  = $invoice->booking?->room;
    $fullName = $guest ? trim(($guest->first_name ?? '') . ' ' . ($guest->last_name ?? '')) : '—';
    $initial  = $guest ? mb_substr($guest->first_name ?? '?', 0, 2, 'UTF-8') : '?';
 
    // เลือก variant สี avatar
    $variants = ['', 'v1', 'v2', 'v3', 'v4'];
    $avatarVariant = $variants[$guest ? (crc32((string) $guest->id) % 5) : 0];
 
    // คำนวณวันเกินกำหนด (ถ้ามี)
    $overdueDays = null;
    if ($invoice->status === 'overdue' && $invoice->due_date) {
        $overdueDays = (int) abs(now()->startOfDay()->diffInDays($invoice->due_date->startOfDay(), false));
    }
?>
 
<div class="invoice-header">
    <div class="invoice-header-left">
        <h1>
            <i class="bi bi-receipt" style="color: var(--romar-primary);"></i>
            ใบแจ้งหนี้
            <span class="invoice-number-big"><?php echo e($invoice->invoice_number); ?></span>
        </h1>
        <div class="invoice-meta">
            <span><i class="bi bi-calendar-event me-1"></i>
                ออกเมื่อ <?php echo e(optional($invoice->issue_date)->format('d/m/Y')); ?>

            </span>
            <span class="dot-sep">•</span>
            <span class="status-pill status-<?php echo e($invoice->status); ?> status-pill-lg">
                <?php if($invoice->status === 'overdue' && $overdueDays): ?>
                    เกินกำหนด <?php echo e($overdueDays); ?> วัน
                <?php else: ?>
                    <?php echo e(enum_bi('invoice_status', $invoice->status, ucfirst($invoice->status))); ?>

                <?php endif; ?>
            </span>
        </div>
    </div>
 
    <div class="page-actions">
        <?php if(in_array($invoice->status, ['sent', 'overdue'])): ?>
            <form action="<?php echo e(route('invoices.markAsPaid', $invoice)); ?>" method="POST" class="d-inline">
                <?php echo csrf_field(); ?>
                <button type="submit" class="btn-outline-romar"
                        style="border-color: var(--success); color: var(--success);"
                        onclick="return confirm('ยืนยันการชำระเงิน?')">
                    <i class="bi bi-check-circle"></i> ทำเครื่องหมายชำระแล้ว
                </button>
            </form>
        <?php endif; ?>
        <a href="<?php echo e(route('invoices.edit', $invoice)); ?>" class="btn-romar">
            <i class="bi bi-pencil"></i> แก้ไข
        </a>
    </div>
</div>
 

<?php if(session('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i><?php echo e(session('success')); ?>

        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>
 

<div class="info-bar">
    <div class="info-bar-item">
        <div class="info-bar-label">ยอดรวม</div>
        <div class="info-bar-value amount-big">
            ฿ <?php echo e(number_format($invoice->total, 2)); ?>

        </div>
    </div>
    <div class="info-bar-item">
        <div class="info-bar-label">วันที่ออก</div>
        <div class="info-bar-value">
            <?php echo e(optional($invoice->issue_date)->format('d/m/Y') ?? '—'); ?>

        </div>
    </div>
    <div class="info-bar-item">
        <div class="info-bar-label">วันครบกำหนด</div>
        <div class="info-bar-value">
            <?php echo e(optional($invoice->due_date)->format('d/m/Y') ?? '—'); ?>

        </div>
    </div>
    <div class="info-bar-item">
        <div class="info-bar-label">สถานะ</div>
        <div class="info-bar-value">
            <span class="status-pill status-<?php echo e($invoice->status); ?>">
                <?php echo e(enum_bi('invoice_status', $invoice->status, ucfirst($invoice->status))); ?>

            </span>
        </div>
    </div>
</div>
 

<div class="row g-3">
 
    
    <div class="col-lg-8">
 
        
        <div class="section-card">
            <div class="section-card-head">
                <i class="bi bi-list-ul"></i>
                รายการในใบแจ้งหนี้
            </div>
            <div class="section-card-body">
                <table class="items-table">
                    <thead>
                        <tr>
                            <th>รายการ</th>
                            <th>จำนวนเงิน</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <div style="font-weight: 500;">ค่าเช่ารายเดือน</div>
                                <div class="doc-num-sub">
                                    Booking #<?php echo e($invoice->booking_id); ?>

                                    <?php if($room): ?> — ห้อง <?php echo e($room->room_number); ?> <?php endif; ?>
                                </div>
                            </td>
                            <td>฿ <?php echo e(number_format($invoice->amount, 2)); ?></td>
                        </tr>
                        <?php if($invoice->tax > 0): ?>
                            <tr>
                                <td>
                                    <div style="font-weight: 500;">ภาษี</div>
                                    <div class="doc-num-sub">VAT</div>
                                </td>
                                <td>฿ <?php echo e(number_format($invoice->tax, 2)); ?></td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
 
                
                <div class="total-summary">
                    <div class="total-row">
                        <span>ยอดก่อนภาษี</span>
                        <span>฿ <?php echo e(number_format($invoice->amount, 2)); ?></span>
                    </div>
                    <?php if($invoice->tax > 0): ?>
                        <div class="total-row">
                            <span>ภาษี</span>
                            <span>฿ <?php echo e(number_format($invoice->tax, 2)); ?></span>
                        </div>
                    <?php endif; ?>
                    <div class="total-row grand">
                        <span>ยอดรวมทั้งสิ้น</span>
                        <span class="amount-big">฿ <?php echo e(number_format($invoice->total, 2)); ?></span>
                    </div>
                </div>
            </div>
        </div>
 
        
        <?php if(!empty($invoice->notes)): ?>
            <div class="section-card">
                <div class="section-card-head">
                    <i class="bi bi-sticky"></i>
                    หมายเหตุ
                </div>
                <div class="section-card-body">
                    <div class="notes-box"><?php echo e($invoice->notes); ?></div>
                </div>
            </div>
        <?php endif; ?>
 
    </div>
 
    
    <div class="col-lg-4">
 
        
        <div class="section-card">
            <div class="section-card-head">
                <i class="bi bi-person"></i>
                ผู้เช่า
            </div>
            <div class="section-card-body">
                <?php if($guest): ?>
                    <div class="tenant-block">
                        <div class="person-avatar <?php echo e($avatarVariant); ?>"><?php echo e($initial); ?></div>
                        <div>
                            <div class="tenant-block-name"><?php echo e($fullName); ?></div>
                            <div class="tenant-block-sub">รหัสผู้เช่า #<?php echo e($guest->id); ?></div>
                        </div>
                    </div>
                    <ul class="contact-list">
                        <?php if(!empty($guest->phone)): ?>
                            <li>
                                <i class="bi bi-telephone"></i>
                                <a href="tel:<?php echo e($guest->phone); ?>" style="color: inherit; text-decoration: none;">
                                    <?php echo e($guest->phone); ?>

                                </a>
                            </li>
                        <?php endif; ?>
                        <?php if(!empty($guest->email)): ?>
                            <li>
                                <i class="bi bi-envelope"></i>
                                <a href="mailto:<?php echo e($guest->email); ?>" style="color: inherit; text-decoration: none;">
                                    <?php echo e($guest->email); ?>

                                </a>
                            </li>
                        <?php endif; ?>
                        <?php if(!empty($guest->id_number)): ?>
                            <li>
                                <i class="bi bi-credit-card-2-front"></i>
                                <span class="doc-num" style="font-size:0.85rem;"><?php echo e($guest->id_number); ?></span>
                            </li>
                        <?php endif; ?>
                    </ul>
                    <a href="<?php echo e(route('guests.show', $guest)); ?>" class="btn-outline-romar w-100 justify-content-center mt-3">
                        <i class="bi bi-arrow-right-circle"></i> ดูข้อมูลผู้เช่า
                    </a>
                <?php else: ?>
                    <div class="text-muted text-center py-3" style="font-size: 0.9rem;">
                        <i class="bi bi-person-x" style="font-size: 1.5rem; display: block; margin-bottom: 6px;"></i>
                        ไม่พบข้อมูลผู้เช่า
                    </div>
                <?php endif; ?>
            </div>
        </div>
 
        
        <div class="section-card">
            <div class="section-card-head">
                <i class="bi bi-door-closed"></i>
                ห้องพัก / การจอง
            </div>
            <div class="section-card-body">
                <?php if($room): ?>
                    <div style="text-align:center; padding: 8px 0 14px;">
                        <span class="room-badge" style="font-size: 1.05rem; padding: 8px 18px;">
                            <i class="bi bi-door-closed"></i> <?php echo e($room->room_number); ?>

                        </span>
                    </div>
                <?php endif; ?>
                <div class="detail-row-romar">
                    <div class="detail-label-romar">รหัสการจอง</div>
                    <div class="detail-value-romar">
                        <span class="doc-num">#<?php echo e($invoice->booking_id); ?></span>
                    </div>
                </div>
                <?php if($room && !empty($room->room_type)): ?>
                    <div class="detail-row-romar">
                        <div class="detail-label-romar">ประเภทห้อง</div>
                        <div class="detail-value-romar"><?php echo e($room->room_type); ?></div>
                    </div>
                <?php endif; ?>
                <?php if($room && !empty($room->zone)): ?>
                    <div class="detail-row-romar">
                        <div class="detail-label-romar">โซน</div>
                        <div class="detail-value-romar"><?php echo e($room->zone); ?></div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
 
    </div>
</div>
 

<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delete', $invoice)): ?>
    <div class="danger-zone">
        <div class="danger-zone-text">
            <strong><i class="bi bi-exclamation-triangle"></i> ลบใบแจ้งหนี้</strong>
            การลบจะนำใบแจ้งหนี้นี้ออกจากระบบอย่างถาวร ไม่สามารถกู้คืนได้
        </div>
        <form method="POST" action="<?php echo e(route('invoices.destroy', $invoice->id)); ?>"
              onsubmit="return confirm('คุณแน่ใจหรือไม่ที่จะลบใบแจ้งหนี้ <?php echo e($invoice->invoice_number); ?> ?\nการกระทำนี้ไม่สามารถยกเลิกได้');">
            <?php echo csrf_field(); ?>
            <?php echo method_field('DELETE'); ?>
            <button type="submit" class="btn-danger-romar">
                <i class="bi bi-trash"></i> ลบใบแจ้งหนี้นี้
            </button>
        </form>
    </div>
<?php else: ?>
    
    
<?php endif; ?>
 
<?php $__env->stopSection(); ?>
 
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Rm1\resources\views/invoices/show.blade.php ENDPATH**/ ?>