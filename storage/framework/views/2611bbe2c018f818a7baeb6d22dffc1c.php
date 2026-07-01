<?php
    /** @var \App\Models\Maintenance $maintenance */
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายละเอียดการซ่อมบำรุง</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, sans-serif; background: #f0f2f5; padding: 40px 20px; }
        .container { max-width: 700px; margin: 0 auto; background: white; padding: 35px; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); }
        h2 { border-bottom: 2px solid #f0f0f0; padding-bottom: 15px; margin-bottom: 25px; color: #1a1a1a; display: flex; align-items: center; gap: 12px; }
        .row { display: flex; padding: 16px 0; border-bottom: 1px solid #f8f9fa; align-items: center; }
        .label { width: 200px; font-weight: 600; color: #64748b; font-size: 15px; }
        .value { flex: 1; color: #1e293b; font-size: 16px; }
        .btn-group { display: flex; gap: 12px; margin-top: 30px; }
        .btn { display: inline-flex; align-items: center; gap: 8px; padding: 12px 24px; border-radius: 10px; font-weight: 600; text-decoration: none; transition: all 0.2s; font-size: 15px; }
        .btn-back { background: #f1f5f9; color: #475569; }
        .btn-back:hover { background: #e2e8f0; }
        .btn-edit { background: #fef9c3; color: #a16207; border: 1px solid #fef08a; }
        .btn-edit:hover { background: #fef08a; }
        .badge { padding: 6px 16px; border-radius: 30px; font-size: 13px; font-weight: 700; text-transform: uppercase; }
        .status-pending { background: #fef3c7; color: #d97706; }
        .status-in_progress { background: #dbeafe; color: #2563eb; }
        .status-completed { background: #d1fae5; color: #059669; }
        .status-cancelled { background: #fee2e2; color: #dc2626; }
    </style>
</head>
<body>
<div class="container">
    <h2><i class="fas fa-file-invoice text-primary"></i> รายละเอียดการซ่อมบำรุง</h2>
    
    <div class="row">
        <div class="label">หมายเลขห้อง:</div>
        <div class="value"><strong>ห้อง <?php echo e($maintenance->room->room_number ?? '-'); ?></strong></div>
    </div>

    <div class="row">
        <div class="label">ประเภทปัญหา:</div>
        <div class="value">
            <span class="fw-bold"><?php echo e($maintenance->maintenance_type); ?></span>
        </div>
    </div>

    <div class="row">
        <div class="label">สถานะปัจจุบัน:</div>
        <div class="value">
            <span class="badge status-<?php echo e($maintenance->status); ?>">
                <?php switch($maintenance->status):
                    case ('pending'): ?> รอดำเนินการ <?php break; ?>
                    <?php case ('in_progress'): ?> กำลังดำเนินการ <?php break; ?>
                    <?php case ('completed'): ?> เสร็จสิ้น <?php break; ?>
                    <?php case ('cancelled'): ?> ยกเลิก <?php break; ?>
                    <?php default: ?> <?php echo e($maintenance->status); ?>

                <?php endswitch; ?>
            </span>
        </div>
    </div>

    <div class="row">
        <div class="label">ผู้รับผิดชอบ (ช่าง):</div>
        <div class="value"><i class="fas fa-user-gear me-1"></i> <?php echo e($maintenance->assigned_to ?? 'ยังไม่ระบุ'); ?></div>
    </div>

    <div class="row">
        <div class="label">ค่าใช้จ่าย:</div>
        <div class="value" style="color: #ef4444; font-weight: 700;">
            <?php echo e($maintenance->cost ? number_format($maintenance->cost, 2) . ' บาท' : '0.00 บาท'); ?>

        </div>
    </div>

    <div class="row">
        <div class="label">วันที่แจ้งซ่อม:</div>
        <div class="value"><?php echo e(optional($maintenance->request_date)->format('d/m/Y') ?? '-'); ?></div>
    </div>

    <div class="row">
        <div class="label">รายละเอียดปัญหา:</div>
        <div class="value"><?php echo e($maintenance->description ?? '-'); ?></div>
    </div>

    <div class="row">
        <div class="label">หมายเหตุเพิ่มเติม:</div>
        <div class="value"><?php echo e($maintenance->notes ?? '-'); ?></div>
    </div>

    <div class="btn-group">
        <a href="<?php echo e(route('maintenances.index')); ?>" class="btn btn-back">
            <i class="fas fa-arrow-left"></i> กลับหน้าหลัก
        </a>
        <a href="<?php echo e(route('maintenances.edit', $maintenance->id)); ?>" class="btn btn-edit">
            <i class="fas fa-edit"></i> แก้ไขข้อมูล
        </a>
    </div>
</div>
</body>
</html><?php /**PATH F:\xampp\htdocs\Rm1\resources\views/maintenances/show.blade.php ENDPATH**/ ?>