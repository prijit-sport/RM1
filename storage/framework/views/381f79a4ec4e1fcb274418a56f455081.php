

<?php $__env->startSection('title', 'รายละเอียดใบแจ้งหนี้'); ?>
<?php $__env->startSection('page-title', 'รายละเอียดใบแจ้งหนี้'); ?>

<?php $__env->startPush('styles'); ?>
    <style>
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

        .info-bar-item:last-child {
            border-right: none;
        }

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

        .section-card-body {
            padding: 20px;
        }

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

        .items-table th:last-child {
            text-align: right;
        }

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

        .items-table tr:last-child td {
            border-bottom: none;
        }

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

        .tenant-block {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 18px;
        }

        .tenant-block .person-avatar {
            width: 54px;
            height: 54px;
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

        .contact-list li:last-child {
            border-bottom: none;
        }

        .contact-list li i {
            color: var(--romar-primary);
            width: 18px;
        }

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

        .status-pill-lg {
            padding: 6px 16px;
            font-size: 0.85rem;
        }

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

        /* ✅ Meter breakdown card */
        .meter-breakdown-card {
            border-radius: 12px;
            border: 1.5px solid #bfdbfe;
            background: #f0f9ff;
            padding: 16px 20px;
            margin-bottom: 16px;
        }

        .meter-breakdown-title {
            font-size: 0.8rem;
            font-weight: 700;
            color: #0369a1;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .meter-type-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-bottom: 12px;
        }

        .meter-type-box {
            border-radius: 10px;
            padding: 12px 14px;
        }

        .meter-type-box.electric {
            background: #fffbeb;
            border: 1px solid #fde68a;
        }

        .meter-type-box.water {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
        }

        .meter-type-label {
            font-size: 0.8rem;
            font-weight: 600;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .meter-type-box.electric .meter-type-label {
            color: #92400e;
        }

        .meter-type-box.water .meter-type-label {
            color: #1e40af;
        }

        .meter-stat-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4px;
            margin-bottom: 6px;
        }

        .meter-stat-item {
            font-size: 0.78rem;
        }

        .meter-stat-item .stat-label {
            color: #6b7280;
        }

        .meter-stat-item .stat-value {
            font-weight: 600;
            color: #1f2937;
        }

        .meter-type-total {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 8px;
            padding-top: 8px;
            border-top: 1px solid rgba(0, 0, 0, 0.08);
            font-size: 0.85rem;
            font-weight: 700;
        }

        .meter-type-box.electric .meter-type-total {
            color: #92400e;
        }

        .meter-type-box.water .meter-type-total {
            color: #1e40af;
        }

        .meter-grand-total {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 14px;
            background: white;
            border-radius: 8px;
            border: 1px solid #bfdbfe;
            font-weight: 600;
            color: #0369a1;
        }

        @media (max-width: 768px) {
            .info-bar {
                grid-template-columns: repeat(2, 1fr);
            }

            .info-bar-item:nth-child(2) {
                border-right: none;
            }

            .detail-label-romar {
                width: 130px;
            }

            .meter-type-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>

    <?php
        $guest = $invoice->booking?->guest;
        $room = $invoice->booking?->room;
        $fullName = $guest ? trim(($guest->first_name ?? '') . ' ' . ($guest->last_name ?? '')) : '—';
        $initial = $guest ? mb_substr($guest->first_name ?? '?', 0, 2, 'UTF-8') : '?';
        $variants = ['', 'v1', 'v2', 'v3', 'v4'];
        $avatarVariant = $variants[$guest ? crc32((string) $guest->id) % 5 : 0];
        $overdueDays = null;
        if ($invoice->status === 'overdue' && $invoice->due_date) {
            $overdueDays = (int) abs(
                now()
                    ->startOfDay()
                    ->diffInDays($invoice->due_date->startOfDay(), false),
            );
        }

        // ✅ ดึง meter readings ของห้องนี้ในเดือนที่ออก invoice
        $meterBreakdown = [];
        if ($invoice->booking && $invoice->issue_date) {
            $issueMonth = $invoice->issue_date->month;
            $issueYear = $invoice->issue_date->year;
            $booking = $invoice->booking;

            foreach (['electric', 'water'] as $type) {
                $meter = \App\Models\Meter::where('room_id', $booking->room_id)->where('type', $type)->first();

                if (!$meter) {
                    continue;
                }

                // reading เดือนนี้
                $current = \App\Models\MeterReading::where('meter_id', $meter->id)
                    ->where('period_month', $issueMonth)
                    ->where('period_year', $issueYear)
                    ->first();

                if (!$current) {
                    continue;
                }

                // reading ก่อนหน้า
                $previous = \App\Models\MeterReading::where('meter_id', $meter->id)
                    ->whereDate('reading_date', '<', $current->reading_date)
                    ->orderByDesc('reading_date')
                    ->first();

                $currentVal = (float) $current->reading_value;
                $previousVal = $previous ? (float) $previous->reading_value : null;
                $usage = $previousVal !== null ? max(0, $currentVal - $previousVal) : 0;
                $rate = (float) ($meter->rate_per_unit ?? 0);
                $cost = round($usage * $rate, 2);

                $meterBreakdown[$type] = [
                    'meter_number' => $meter->meter_number ?? '-',
                    'current_value' => $currentVal,
                    'previous_value' => $previousVal,
                    'usage' => $usage,
                    'rate' => $rate,
                    'cost' => $cost,
                ];
            }
        }
    ?>

    <a href="<?php echo e(route('invoices.index')); ?>" class="back-link">
        <i class="bi bi-arrow-left"></i> กลับไปยังรายการใบแจ้งหนี้
    </a>

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
            <div class="info-bar-value amount-big">฿ <?php echo e(number_format($invoice->total, 2)); ?></div>
        </div>
        <div class="info-bar-item">
            <div class="info-bar-label">วันที่ออก</div>
            <div class="info-bar-value"><?php echo e(optional($invoice->issue_date)->format('d/m/Y') ?? '—'); ?></div>
        </div>
        <div class="info-bar-item">
            <div class="info-bar-label">วันครบกำหนด</div>
            <div class="info-bar-value"><?php echo e(optional($invoice->due_date)->format('d/m/Y') ?? '—'); ?></div>
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

            
            <?php if(!empty($meterBreakdown)): ?>
                <div class="section-card">
                    <div class="section-card-head">
                        <i class="bi bi-lightning-charge"></i>
                        รายละเอียดค่าน้ำ / ค่าไฟ
                    </div>
                    <div class="section-card-body">
                        <div class="meter-breakdown-card">
                            <div class="meter-breakdown-title">
                                <i class="bi bi-speedometer2"></i>
                                ข้อมูลมิเตอร์ประจำเดือน <?php echo e(optional($invoice->issue_date)->isoFormat('MMMM YYYY')); ?>

                            </div>

                            <div class="meter-type-grid">
                                
                                <?php if(isset($meterBreakdown['electric'])): ?>
                                    <?php $e = $meterBreakdown['electric']; ?>
                                    <div class="meter-type-box electric">
                                        <div class="meter-type-label">
                                            ⚡ ค่าไฟฟ้า
                                            <span class="badge bg-warning text-dark ms-1"
                                                style="font-size:0.7rem;"><?php echo e($e['meter_number']); ?></span>
                                        </div>
                                        <div class="meter-stat-row">
                                            <div class="meter-stat-item">
                                                <div class="stat-label">มิเตอร์ก่อนหน้า</div>
                                                <div class="stat-value">
                                                    <?php echo e($e['previous_value'] !== null ? number_format($e['previous_value'], 2) : '-'); ?>

                                                </div>
                                            </div>
                                            <div class="meter-stat-item">
                                                <div class="stat-label">มิเตอร์ปัจจุบัน</div>
                                                <div class="stat-value"><?php echo e(number_format($e['current_value'], 2)); ?></div>
                                            </div>
                                            <div class="meter-stat-item">
                                                <div class="stat-label">จำนวนหน่วย</div>
                                                <div class="stat-value"><?php echo e(number_format($e['usage'], 2)); ?> หน่วย</div>
                                            </div>
                                            <div class="meter-stat-item">
                                                <div class="stat-label">อัตรา/หน่วย</div>
                                                <div class="stat-value">฿<?php echo e(number_format($e['rate'], 2)); ?></div>
                                            </div>
                                        </div>
                                        <div class="meter-type-total">
                                            <span>รวมค่าไฟ</span>
                                            <span>฿<?php echo e(number_format($e['cost'], 2)); ?></span>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                
                                <?php if(isset($meterBreakdown['water'])): ?>
                                    <?php $w = $meterBreakdown['water']; ?>
                                    <div class="meter-type-box water">
                                        <div class="meter-type-label">
                                            💧 ค่าน้ำประปา
                                            <span class="badge bg-primary ms-1"
                                                style="font-size:0.7rem;"><?php echo e($w['meter_number']); ?></span>
                                        </div>
                                        <div class="meter-stat-row">
                                            <div class="meter-stat-item">
                                                <div class="stat-label">มิเตอร์ก่อนหน้า</div>
                                                <div class="stat-value">
                                                    <?php echo e($w['previous_value'] !== null ? number_format($w['previous_value'], 2) : '-'); ?>

                                                </div>
                                            </div>
                                            <div class="meter-stat-item">
                                                <div class="stat-label">มิเตอร์ปัจจุบัน</div>
                                                <div class="stat-value"><?php echo e(number_format($w['current_value'], 2)); ?></div>
                                            </div>
                                            <div class="meter-stat-item">
                                                <div class="stat-label">จำนวนหน่วย</div>
                                                <div class="stat-value"><?php echo e(number_format($w['usage'], 2)); ?> หน่วย</div>
                                            </div>
                                            <div class="meter-stat-item">
                                                <div class="stat-label">อัตรา/หน่วย</div>
                                                <div class="stat-value">฿<?php echo e(number_format($w['rate'], 2)); ?></div>
                                            </div>
                                        </div>
                                        <div class="meter-type-total">
                                            <span>รวมค่าน้ำ</span>
                                            <span>฿<?php echo e(number_format($w['cost'], 2)); ?></span>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <?php
                                $meterGrand =
                                    ($meterBreakdown['electric']['cost'] ?? 0) +
                                    ($meterBreakdown['water']['cost'] ?? 0);
                            ?>
                            <div class="meter-grand-total">
                                <span><i class="bi bi-calculator me-1"></i> รวมค่าน้ำ + ค่าไฟ</span>
                                <span>฿<?php echo e(number_format($meterGrand, 2)); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            
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
                            <?php if(($invoice->invoice_type ?? 'rent') === 'utility'): ?>
                                
                                <?php if(isset($meterBreakdown['electric']) && $meterBreakdown['electric']['cost'] > 0): ?>
                                    <?php $e = $meterBreakdown['electric']; ?>
                                    <tr>
                                        <td>
                                            <div style="font-weight:500;">⚡ ค่าไฟฟ้า</div>
                                            <div class="doc-num-sub">
                                                มิเตอร์ <?php echo e($e['meter_number']); ?> ·
                                                <?php echo e(number_format($e['usage'], 2)); ?> หน่วย ×
                                                ฿<?php echo e(number_format($e['rate'], 2)); ?>

                                                <?php if($room): ?>
                                                    — ห้อง <?php echo e($room->room_number); ?>

                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td>฿ <?php echo e(number_format($e['cost'], 2)); ?></td>
                                    </tr>
                                <?php endif; ?>

                                <?php if(isset($meterBreakdown['water']) && $meterBreakdown['water']['cost'] > 0): ?>
                                    <?php $w = $meterBreakdown['water']; ?>
                                    <tr>
                                        <td>
                                            <div style="font-weight:500;">💧 ค่าน้ำประปา</div>
                                            <div class="doc-num-sub">
                                                มิเตอร์ <?php echo e($w['meter_number']); ?> ·
                                                <?php echo e(number_format($w['usage'], 2)); ?> หน่วย ×
                                                ฿<?php echo e(number_format($w['rate'], 2)); ?>

                                                <?php if($room): ?>
                                                    — ห้อง <?php echo e($room->room_number); ?>

                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td>฿ <?php echo e(number_format($w['cost'], 2)); ?></td>
                                    </tr>
                                <?php endif; ?>

                                
                                <?php if(empty($meterBreakdown)): ?>
                                    <tr>
                                        <td>
                                            <div style="font-weight:500;">⚡💧 ค่าน้ำ/ค่าไฟ</div>
                                            <div class="doc-num-sub">
                                                <?php if($room): ?>
                                                    ห้อง <?php echo e($room->room_number); ?>

                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td>฿ <?php echo e(number_format($invoice->amount, 2)); ?></td>
                                    </tr>
                                <?php endif; ?>

                                <?php if($invoice->tax > 0): ?>
                                    <tr>
                                        <td>
                                            <div style="font-weight:500;">ภาษี</div>
                                            <div class="doc-num-sub">VAT</div>
                                        </td>
                                        <td>฿ <?php echo e(number_format($invoice->tax, 2)); ?></td>
                                    </tr>
                                <?php endif; ?>
                            <?php else: ?>
                                
                                <tr>
                                    <td>
                                        <div style="font-weight:500;">ค่าเช่ารายเดือน</div>
                                        <div class="doc-num-sub">
                                            Booking #<?php echo e($invoice->booking_id); ?>

                                            <?php if($room): ?>
                                                — ห้อง <?php echo e($room->room_number); ?>

                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td>฿ <?php echo e(number_format($invoice->amount, 2)); ?></td>
                                </tr>
                                <?php if($invoice->tax > 0): ?>
                                    <tr>
                                        <td>
                                            <div style="font-weight:500;">ภาษี</div>
                                            <div class="doc-num-sub">VAT</div>
                                        </td>
                                        <td>฿ <?php echo e(number_format($invoice->tax, 2)); ?></td>
                                    </tr>
                                <?php endif; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                    <div class="total-summary">
                        <?php if(($invoice->invoice_type ?? 'rent') === 'utility' && !empty($meterBreakdown)): ?>
                            <?php if(isset($meterBreakdown['electric']) && $meterBreakdown['electric']['cost'] > 0): ?>
                                <div class="total-row">
                                    <span>⚡ รวมค่าไฟฟ้า</span>
                                    <span>฿ <?php echo e(number_format($meterBreakdown['electric']['cost'], 2)); ?></span>
                                </div>
                            <?php endif; ?>
                            <?php if(isset($meterBreakdown['water']) && $meterBreakdown['water']['cost'] > 0): ?>
                                <div class="total-row">
                                    <span>💧 รวมค่าน้ำประปา</span>
                                    <span>฿ <?php echo e(number_format($meterBreakdown['water']['cost'], 2)); ?></span>
                                </div>
                            <?php endif; ?>
                            <?php if($invoice->tax > 0): ?>
                                <div class="total-row">
                                    <span>ภาษี (VAT)</span>
                                    <span>฿ <?php echo e(number_format($invoice->tax, 2)); ?></span>
                                </div>
                            <?php endif; ?>
                        <?php else: ?>
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
                                    <a href="tel:<?php echo e($guest->phone); ?>"
                                        style="color:inherit;text-decoration:none;"><?php echo e($guest->phone); ?></a>
                                </li>
                            <?php endif; ?>
                            <?php if(!empty($guest->email)): ?>
                                <li>
                                    <i class="bi bi-envelope"></i>
                                    <a href="mailto:<?php echo e($guest->email); ?>"
                                        style="color:inherit;text-decoration:none;"><?php echo e($guest->email); ?></a>
                                </li>
                            <?php endif; ?>
                            <?php if(!empty($guest->id_number)): ?>
                                <li>
                                    <i class="bi bi-credit-card-2-front"></i>
                                    <span class="doc-num" style="font-size:0.85rem;"><?php echo e($guest->id_number); ?></span>
                                </li>
                            <?php endif; ?>
                        </ul>
                        <a href="<?php echo e(route('guests.show', $guest)); ?>"
                            class="btn-outline-romar w-100 justify-content-center mt-3">
                            <i class="bi bi-arrow-right-circle"></i> ดูข้อมูลผู้เช่า
                        </a>
                    <?php else: ?>
                        <div class="text-muted text-center py-3" style="font-size:0.9rem;">
                            <i class="bi bi-person-x" style="font-size:1.5rem;display:block;margin-bottom:6px;"></i>
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
                        <div style="text-align:center;padding:8px 0 14px;">
                            <span class="room-badge" style="font-size:1.05rem;padding:8px 18px;">
                                <i class="bi bi-door-closed"></i> <?php echo e($room->room_number); ?>

                            </span>
                        </div>
                    <?php endif; ?>
                    <div class="detail-row-romar">
                        <div class="detail-label-romar">รหัสการจอง</div>
                        <div class="detail-value-romar"><span class="doc-num">#<?php echo e($invoice->booking_id); ?></span></div>
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
    <?php endif; ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\Rm1\resources\views/invoices/show.blade.php ENDPATH**/ ?>