<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แก้ไขสิ่งอำนวยความสะดวก</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI'; background: #f5f5f5; padding: 20px; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 40px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { margin-bottom: 30px; color: #333; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; font-weight: 600; color: #555; }
        input, textarea, select { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Segoe UI'; font-size: 14px; }
        input:focus, textarea:focus, select:focus { outline: none; border-color: #667eea; box-shadow: 0 0 5px rgba(102, 126, 234, 0.3); }
        textarea { resize: vertical; min-height: 100px; }
        .btn-group { display: flex; gap: 10px; margin-top: 30px; }
        .btn { padding: 12px 20px; border: none; border-radius: 5px; cursor: pointer; font-weight: bold; text-decoration: none; display: inline-block; }
        .btn-submit { background: #667eea; color: white; }
        .btn-submit:hover { background: #764ba2; }
        .btn-cancel { background: #6c757d; color: white; }
        .btn-cancel:hover { background: #5a6268; }
        .error-message { color: #dc3545; font-size: 12px; margin-top: 5px; }
        .required::after { content: ' *'; color: #dc3545; }
        .info-box { background: #e8f4f8; border-left: 4px solid #0284c7; padding: 12px 15px; border-radius: 5px; margin-bottom: 20px; font-size: 0.9em; color: #075985; }
        .two-cols { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
        @media (max-width: 600px) { .two-cols { grid-template-columns: 1fr; } }
        select:disabled { background-color: #f0f0f0; cursor: not-allowed; color: #666; }
    </style>
</head>
<body>
    <div class="container">
        <h1>✏️ แก้ไขสิ่งอำนวยความสะดวก</h1>
        
        <?php if($errors->any()): ?>
            <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div><?php echo e($error); ?></div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php endif; ?>
 
        <form method="POST" action="<?php echo e(route('facilities.update', $facility->id)); ?>">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>
 
            <!-- ✅ Lock Dropdown Room -->
            <div class="form-group">
                <label for="room_id">ห้องพัก <span class="required"></span></label>
                <select name="room_id" id="room_id" disabled required>
                    <option value="">-- เลือกห้องพัก --</option>
                    <?php $__currentLoopData = $rooms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $room): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($room->id); ?>" 
                            <?php echo e(old('room_id', $facility->room_id) == $room->id ? 'selected' : ''); ?>>
                            ห้อง <?php echo e($room->room_number); ?> (<?php echo e($room->room_type); ?>)
                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <div class="info-box">🔒 ล็อค: ไม่สามารถเปลี่ยนห้องได้ (ติดต่อแอดมินหากต้องการเปลี่ยน)</div>
                
                <input type="hidden" name="room_id" value="<?php echo e($facility->room_id); ?>">
                <?php $__errorArgs = ['room_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <div class="error-message"><?php echo e($message); ?></div>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
 
            <div class="form-group">
                <label for="name">ชื่อ <span class="required"></span></label>
                <input type="text" id="name" name="name" value="<?php echo e(old('name', $facility->name)); ?>" required>
                <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <div class="error-message"><?php echo e($message); ?></div>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
 
            <div class="form-group">
                <label for="type">ประเภท <span class="required"></span></label>
                <select name="type" id="type" required>
                    <option value="">-- เลือกประเภท --</option>
                    <?php $currentType = old('type', $facility->type); ?>
                    <option value="bed"            <?php echo e($currentType == 'bed'            ? 'selected' : ''); ?>>🛏️ เตียง</option>
                    <option value="mattress"       <?php echo e($currentType == 'mattress'       ? 'selected' : ''); ?>>🛌 ที่นอน</option>
                    <option value="wardrobe"       <?php echo e($currentType == 'wardrobe'       ? 'selected' : ''); ?>>🚪 ตู้เสื้อผ้า</option>
                    <option value="dressing_table" <?php echo e($currentType == 'dressing_table' ? 'selected' : ''); ?>>💄 โต๊ะเครื่องแป้ง</option>
                    <option value="tv_stand"       <?php echo e($currentType == 'tv_stand'       ? 'selected' : ''); ?>>📺 ชั้นวางทีวี</option>
                    <option value="clothes_rack"   <?php echo e($currentType == 'clothes_rack'   ? 'selected' : ''); ?>>👔 ราวแขวนผ้า</option>
                </select>
                <?php $__errorArgs = ['type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <div class="error-message"><?php echo e($message); ?></div>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
 
            <!-- ✅ แปลง location → ชั้นและโซน (auto-display) -->
            <div class="two-cols">
                <div class="form-group">
                    <label for="floor">ชั้น</label>
                    <input type="text" id="floor" name="floor" 
                        value="<?php echo e(old('floor', $facility->room?->floor ?? '')); ?>" 
                        placeholder="ชั้น" readonly style="background-color: #f0f0f0; cursor: not-allowed;">
                </div>
                <div class="form-group">
                    <label for="zone">โซน</label>
                    <input type="text" id="zone" name="zone" 
                        value="<?php echo e(old('zone', $facility->room?->zone ?? '')); ?>" 
                        placeholder="โซน" readonly style="background-color: #f0f0f0; cursor: not-allowed;">
                </div>
            </div>
 
            <div class="form-group">
                <label for="location">หมายเหตุเพิ่มเติม</label>
                <input type="text" id="location" name="location" value="<?php echo e(old('location', $facility->location)); ?>" placeholder="เช่น มุมซ้ายห้องนอน, ใกล้หน้าต่าง">
                <?php $__errorArgs = ['location'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <div class="error-message"><?php echo e($message); ?></div>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
 
            <div class="form-group">
                <label for="description">คำอธิบาย</label>
                <textarea id="description" name="description"><?php echo e(old('description', $facility->description)); ?></textarea>
            </div>
 
            <div class="form-group">
                <label for="status">สถานะ <span class="required"></span></label>
                <select name="status" id="status" required>
                    <?php $currentStatus = old('status', $facility->status); ?>
                    <option value="good"         <?php echo e($currentStatus == 'good'         ? 'selected' : ''); ?>>✅ ใช้งานได้</option>
                    <option value="fair"         <?php echo e($currentStatus == 'fair'         ? 'selected' : ''); ?>>🟡 สภาพปานกลาง</option>
                    <option value="needs_repair" <?php echo e($currentStatus == 'needs_repair' ? 'selected' : ''); ?>>⚠️ ต้องซ่อม</option>
                    <option value="maintenance"  <?php echo e($currentStatus == 'maintenance'  ? 'selected' : ''); ?>>🔧 กำลังซ่อมบำรุง</option>
                    <option value="damaged"      <?php echo e($currentStatus == 'damaged'      ? 'selected' : ''); ?>>❌ ชำรุด</option>
                    <option value="retired"      <?php echo e($currentStatus == 'retired'      ? 'selected' : ''); ?>>🗑️ ปลดประจำการ</option>
                </select>
                <?php $__errorArgs = ['status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <div class="error-message"><?php echo e($message); ?></div>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
 
            <div class="form-group">
                <label for="maintenance_schedule">ตารางบำรุงรักษา</label>
                <input type="text" id="maintenance_schedule" name="maintenance_schedule" value="<?php echo e(old('maintenance_schedule', $facility->maintenance_schedule)); ?>" placeholder="เช่น ทุก 3 เดือน">
            </div>
 
            <div class="form-group">
                <label for="last_maintenance_date">วันที่บำรุงรักษาล่าสุด</label>
                <input type="date" id="last_maintenance_date" name="last_maintenance_date" 
                    value="<?php echo e(old('last_maintenance_date', $facility->last_maintenance_date ? \Carbon\Carbon::parse($facility->last_maintenance_date)->format('Y-m-d') : '')); ?>">
            </div>
 
            <div class="form-group">
                <label for="next_maintenance_date">วันที่บำรุงรักษาครั้งถัดไป</label>
                <input type="date" id="next_maintenance_date" name="next_maintenance_date" 
                    value="<?php echo e(old('next_maintenance_date', $facility->next_maintenance_date ? \Carbon\Carbon::parse($facility->next_maintenance_date)->format('Y-m-d') : '')); ?>">
            </div>
 
            <div class="btn-group">
                <button type="submit" class="btn btn-submit">💾 บันทึก</button>
                <a href="<?php echo e(route('facilities.show', $facility->id)); ?>" class="btn btn-cancel">❌ ยกเลิก</a>
            </div>
        </form>
    </div>
</body>
</html>
 <?php /**PATH C:\xampp\htdocs\Rm1\resources\views/facilities/edit.blade.php ENDPATH**/ ?>