<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายละเอียดมิเตอร์</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f5f5f5; }
        .container { max-width: 1000px; margin: 0 auto; padding: 20px; }
        .card { background: white; border-radius: 10px; padding: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin-bottom: 16px; }
        .header { display:flex; justify-content: space-between; align-items: center; gap: 12px; }
        .header h1 { color: #333; }
        .btn { display: inline-block; padding: 10px 16px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; border: none; cursor: pointer; transition: background 0.2s; font-weight: 700; }
        .btn:hover { background: #764ba2; color:white; }
        .btn-secondary { background: #6c757d; }
        .btn-secondary:hover { background: #5a6268; }
        .btn-danger { background: #dc3545; }
        .btn-danger:hover { background: #c82333; }
        .btn-small { padding: 8px 12px; font-size: 0.9em; }
        .grid { display:grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-top: 12px; }
        @media (max-width: 800px) { .grid { grid-template-columns: 1fr; } }
        .label { color:#666; font-size: 0.9em; margin-bottom: 4px; }
        .value { font-weight: 800; color:#222; }
        .badge { display:inline-block; padding: 4px 10px; border-radius: 999px; font-size: 0.85em; font-weight: 800; }
        .badge-water { background: #d9f2ff; color: #0b5ed7; }
        .badge-electric { background: #fff3cd; color: #856404; }
        .badge-active { background: #d4edda; color: #155724; }
        .badge-inactive { background: #f8d7da; color: #721c24; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #667eea; color: white; padding: 12px 15px; text-align:left; }
        td { padding: 12px 15px; border-bottom: 1px solid #eee; }
        .alert { padding: 15px; margin-bottom: 15px; border-radius: 5px; }
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .actions { display:flex; flex-wrap: wrap; gap: 8px; }
    </style>
</head>
<body>
    <div class="container">
        <?php if(session('success')): ?>
            <div class="alert alert-success"><?php echo e(session('success')); ?></div>
        <?php endif; ?>

        <div class="card">
            <div class="header">
                <h1>รายละเอียดมิเตอร์</h1>
                <div class="actions">
                    <a class="btn btn-secondary btn-small" href="<?php echo e(route('meters.index')); ?>">← กลับ</a>
                    <a class="btn btn-secondary btn-small" href="<?php echo e(route('meters.edit', $meter)); ?>">แก้ไข</a>
                    <a class="btn btn-small" href="<?php echo e(route('meters.readings.index', $meter)); ?>">บันทึกเลข</a>
                    <form action="<?php echo e(route('meters.destroy', $meter)); ?>" method="POST" style="display:inline;">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('DELETE'); ?>
                        <button type="submit" class="btn btn-danger btn-small" onclick="return confirm('แน่ใจหรือ?')">ลบ</button>
                    </form>
                </div>
            </div>

            <div class="grid">
                <div>
                    <div class="label">ห้อง</div>
                    <div class="value">ห้อง <?php echo e($meter->room->room_number ?? '-'); ?></div>
                </div>
                <div>
                    <div class="label">ประเภท</div>
                    <div class="value">
                        <?php if($meter->type === 'water'): ?>
                            <span class="badge badge-water">น้ำ</span>
                        <?php else: ?>
                            <span class="badge badge-electric">ไฟ</span>
                        <?php endif; ?>
                    </div>
                </div>
                <div>
                    <div class="label">เลขมิเตอร์</div>
                    <div class="value"><?php echo e($meter->meter_number); ?></div>
                </div>
                <div>
                    <div class="label">หน่วย</div>
                    <div class="value"><?php echo e($meter->unit ?? '-'); ?></div>
                </div>
                <div>
                    <div class="label">วันที่ติดตั้ง</div>
                    <div class="value"><?php echo e($meter->installed_at ? $meter->installed_at->format('d/m/Y') : '-'); ?></div>
                </div>
                <div>
                    <div class="label">สถานะ</div>
                    <div class="value">
                        <?php if($meter->is_active): ?>
                            <span class="badge badge-active">ใช้งาน</span>
                        <?php else: ?>
                            <span class="badge badge-inactive">ปิดใช้งาน</span>
                        <?php endif; ?>
                    </div>
                </div>
        </div>
        </div>

        <div class="card mb-4">
            <div class="header" style="margin-bottom: 12px;">
                <h1 style="font-size: 1.1em;">สรุปการใช้งานล่าสุด</h1>
                <span class="badge badge-water" style="background:#dbeafe;color:#1d4ed8;padding:6px 12px;">
                    <?php echo e($meter->type === 'water' ? 'น้ำ' : 'ไฟฟ้า'); ?>

                </span>
            </div>
            <?php if($billing['has_reading']): ?>
                <table style="width:100%;border-collapse:collapse;">
                    <tbody>
                        <tr>
                            <th style="text-align:left;width:45%;padding:10px;border-bottom:1px solid #eee;">เลขก่อนหน้า</th>
                            <td style="padding:10px;border-bottom:1px solid #eee;"><?php echo e(number_format($billing['previous'], 2)); ?> (<?php echo e($billing['previous_date'] ?? '-'); ?>)</td>
                        </tr>
                        <tr>
                            <th style="text-align:left;padding:10px;border-bottom:1px solid #eee;">เลขปัจจุบัน</th>
                            <td style="padding:10px;border-bottom:1px solid #eee;"><?php echo e(number_format($billing['current'], 2)); ?> (<?php echo e($billing['current_date'] ?? '-'); ?>)</td>
                        </tr>
                        <tr>
                            <th style="text-align:left;padding:10px;border-bottom:1px solid #eee;">หน่วยที่ใช้</th>
                            <td style="padding:10px;border-bottom:1px solid #eee;"><?php echo e(number_format($billing['usage'], 2)); ?> <?php echo e($meter->unit ?? ''); ?></td>
                        </tr>
                        <tr>
                            <th style="text-align:left;padding:10px;border-bottom:1px solid #eee;">อัตราต่อหน่วย</th>
                            <td style="padding:10px;border-bottom:1px solid #eee;"><?php echo e(number_format($billing['rate'], 2)); ?> บาท</td>
                        </tr>
                        <tr>
                            <th style="text-align:left;padding:10px;border-bottom:1px solid #eee;">ภาษี/ค่าธรรมเนียม (<?php echo e(number_format($billing['tax_rate'], 2)); ?>%)</th>
                            <td style="padding:10px;border-bottom:1px solid #eee;"><?php echo e(number_format($billing['tax'], 2)); ?> บาท</td>
                        </tr>
                        <tr>
                            <th style="text-align:left;padding:10px;">ยอดรวม</th>
                            <td style="padding:10px;font-weight:700;"><?php echo e(number_format($billing['total'], 2)); ?> บาท</td>
                        </tr>
                    </tbody>
                </table>
                <div class="actions" style="margin-top:8px;">
                    <span class="badge badge-active" style="background:#e0f2fe;color:#0c4a6e;padding:6px 12px;font-size:0.8em;">
                        <?php echo e($billing['formula']); ?>

                    </span>
                    <span class="badge badge-inactive" style="background:#fef9c3;color:#92400e;padding:6px 12px;font-size:0.8em;">
                        รับบันทึกโดย <?php echo e($billing['recorder'] ?? 'ไม่ระบุ'); ?>

                    </span>
                </div>
            <?php else: ?>
                <div style="padding:16px;color:#475569;">ยังไม่มีข้อมูลเลขอ่านล่าสุด กรุณาบันทึกเลขก่อนเพื่อแสดงผลคำนวณ</div>
            <?php endif; ?>
        </div>

        <?php if($meter->notes): ?>
                <div style="margin-top: 14px;">
                    <div class="label">หมายเหตุ</div>
                    <div class="value" style="font-weight: 500; white-space: pre-wrap;"><?php echo e($meter->notes); ?></div>
                </div>
            <?php endif; ?>
        </div>

        <div class="card">
            <div class="header" style="margin-bottom: 10px;">
                <h1 style="font-size: 1.2em;">เลขมิเตอร์ล่าสุด</h1>
                <a class="btn btn-small" href="<?php echo e(route('meters.readings.create', $meter)); ?>">➕ เพิ่มเลข</a>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>วันที่</th>
                        <th>เลขมิเตอร์</th>
                        <th>ผู้บันทึก</th>
                        <th>หมายเหตุ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $meter->readings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $reading): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><?php echo e($reading->reading_date?->format('d/m/Y') ?? '-'); ?></td>
                            <td><strong><?php echo e(number_format((float)$reading->reading_value, 2)); ?></strong></td>
                            <td><?php echo e($reading->recordedBy->name ?? '-'); ?></td>
                            <td><?php echo e($reading->notes ?? '-'); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="4" style="text-align:center; padding: 20px;">
                                ยังไม่มีรายการเลขมิเตอร์
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <div style="margin-top: 14px;">
                <a class="btn btn-secondary btn-small" href="<?php echo e(route('meters.readings.index', $meter)); ?>">ดูทั้งหมด →</a>
            </div>
        </div>
    </div>
</body>
</html>

<?php /**PATH F:\xampp\htdocs\Rm1\resources\views/meters/show.blade.php ENDPATH**/ ?>