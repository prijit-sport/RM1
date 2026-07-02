<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายละเอียดสิ่งอำนวยความสะดวก</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI'; background: #f5f5f5; padding: 20px; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 40px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { margin-bottom: 30px; color: #333; display: flex; align-items: center; gap: 10px; }
        .detail-group { margin-bottom: 25px; border-bottom: 1px solid #eee; padding-bottom: 15px; }
        .detail-row { display: flex; margin-bottom: 10px; }
        .detail-label { width: 200px; font-weight: 600; color: #555; }
        .detail-value { flex: 1; color: #333; }
        
        /* Badge Styles */
        .badge { padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: bold; display: inline-block; }
        .status-good { background: #d1fae5; color: #065f46; }
        .status-fair { background: #e0f2fe; color: #0369a1; }
        .status-repair { background: #fef3c7; color: #92400e; }
        .status-maintenance { background: #e0e7ff; color: #4338ca; }
        .status-damaged { background: #fee2e2; color: #991b1b; }
        .status-retired { background: #f3f4f6; color: #374151; }

        .btn-group { display: flex; gap: 10px; margin-top: 30px; flex-wrap: wrap; }
        .btn { padding: 12px 20px; border: none; border-radius: 5px; cursor: pointer; font-weight: bold; text-decoration: none; display: inline-block; text-align: center; transition: opacity 0.2s; }
        .btn:hover { opacity: 0.85; }
        
        .btn-edit { background: #667eea; color: white; }
        .btn-maintenance { background: #f59e0b; color: white; } /* สีส้มสำหรับแจ้งซ่อม */
        .btn-back { background: #6c757d; color: white; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 รายละเอียด: <?php echo e($facility->name); ?></h1>

        <div class="detail-group">
            <div class="detail-row">
                <div class="detail-label">ชื่อ:</div>
                <div class="detail-value"><?php echo e($facility->name); ?></div>
            </div>
            <div class="detail-row">
                <div class="detail-label">ประเภท:</div>
                <div class="detail-value">
                    <?php
                        $types = [
                            'bed' => '🛏️ เตียง',
                            'mattress' => '🛌 ที่นอน',
                            'wardrobe' => '🚪 ตู้เสื้อผ้า',
                            'dressing_table' => '💄 โต๊ะเครื่องแป้ง',
                            'tv_stand' => '📺 ชั้นวางทีวี',
                            'clothes_rack' => '👔 ราวแขวนผ้า'
                        ];
                    ?>
                    <?php echo e($types[$facility->type] ?? $facility->type); ?>

                </div>
            </div>
            <div class="detail-row">
                <div class="detail-label">ที่ตั้ง:</div>
                <div class="detail-value">ห้อง <?php echo e($facility->location); ?></div>
            </div>
            <div class="detail-row">
                <div class="detail-label">สถานะ:</div>
                <div class="detail-value">
                    <?php
                        $statusClasses = [
                            'good' => 'status-good',
                            'fair' => 'status-fair',
                            'needs_repair' => 'status-repair',
                            'maintenance' => 'status-maintenance',
                            'damaged' => 'status-damaged',
                            'retired' => 'status-retired'
                        ];
                        $statusLabels = [
                            'good' => '✅ ใช้งานได้',
                            'fair' => '🟡 สภาพปานกลาง',
                            'needs_repair' => '⚠️ ต้องซ่อม',
                            'maintenance' => '🔧 กำลังซ่อมบำรุง',
                            'damaged' => '❌ ชำรุด',
                            'retired' => '🗑️ ปลดประจำการ'
                        ];
                    ?>
                    <span class="badge <?php echo e($statusClasses[$facility->status] ?? 'status-retired'); ?>">
                        <?php echo e($statusLabels[$facility->status] ?? $facility->status); ?>

                    </span>
                </div>
            </div>
        </div>

        <div class="detail-group">
            <div class="detail-row">
                <div class="detail-label">คำอธิบาย:</div>
                <div class="detail-value"><?php echo e($facility->description ?? '-'); ?></div>
            </div>
            <div class="detail-row">
                <div class="detail-label">ตารางบำรุงรักษา:</div>
                <div class="detail-value"><?php echo e($facility->maintenance_schedule ?? '-'); ?></div>
            </div>
            
            <div class="detail-row">
                <div class="detail-label">วันที่บำรุงรักษาล่าสุด:</div>
                <div class="detail-value">
                    <?php echo e($facility->last_maintenance_date ? \Carbon\Carbon::parse($facility->last_maintenance_date)->format('d/m/Y') : '-'); ?>

                </div>
            </div>
            <div class="detail-row">
                <div class="detail-label">วันที่บำรุงรักษาครั้งถัดไป:</div>
                <div class="detail-value">
                    <?php echo e($facility->next_maintenance_date ? \Carbon\Carbon::parse($facility->next_maintenance_date)->format('d/m/Y') : '-'); ?>

                </div>
            </div>
        </div>

        <div class="btn-group">
            
            <a href="<?php echo e(route('facilities.edit', $facility->id)); ?>" class="btn btn-edit">✏️ แก้ไขข้อมูล</a>

            
            <a href="<?php echo e(route('maintenances.create', ['facility_id' => $facility->id])); ?>" class="btn btn-maintenance">
                🛠️ แจ้งส่งซ่อมบำรุง
            </a>

            
            <a href="<?php echo e(route('facilities.index')); ?>" class="btn btn-back">⬅️ กลับหน้ารวม</a>
        </div>
    </div>
</body>
</html><?php /**PATH F:\xampp\htdocs\Rm1\resources\views/facilities/show.blade.php ENDPATH**/ ?>