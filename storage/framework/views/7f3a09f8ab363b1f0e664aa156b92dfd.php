<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เพิ่มสิ่งอำนวยความสะดวกใหม่</title>
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
    </style>
</head>
<body>
    <div class="container">
        <h1>🏢 เพิ่มสิ่งอำนวยความสะดวกใหม่</h1>
        
        <?php if($errors->any()): ?>
            <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div><?php echo e($error); ?></div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?php echo e(route('facilities.store')); ?>">
            <?php echo csrf_field(); ?>

            <div class="form-group">
                <label for="name">ชื่อ *</label>
                <input type="text" id="name" name="name" value="<?php echo e(old('name')); ?>" required>
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
                <label for="type">ประเภท *</label>
                <input type="text" id="type" name="type" value="<?php echo e(old('type')); ?>" required>
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

            <div class="form-group">
                <label for="location">ที่ตั้ง *</label>
                <input type="text" id="location" name="location" value="<?php echo e(old('location')); ?>" required>
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
                <textarea id="description" name="description"><?php echo e(old('description')); ?></textarea>
            </div>

            <div class="form-group">
                <label for="status">สถานะ *</label>
                <select id="status" name="status" required>
                    <option value="">-- เลือกสถานะ --</option>
                    <option value="active" <?php echo e(old('status') === 'active' ? 'selected' : ''); ?>>ใช้งาน</option>
                    <option value="inactive" <?php echo e(old('status') === 'inactive' ? 'selected' : ''); ?>>ไม่ใช้งาน</option>
                    <option value="maintenance" <?php echo e(old('status') === 'maintenance' ? 'selected' : ''); ?>>บำรุงรักษา</option>
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
                <input type="text" id="maintenance_schedule" name="maintenance_schedule" value="<?php echo e(old('maintenance_schedule')); ?>">
            </div>

            <div class="form-group">
                <label for="last_maintenance_date">วันที่บำรุงรักษาล่าสุด</label>
                <input type="date" id="last_maintenance_date" name="last_maintenance_date" value="<?php echo e(old('last_maintenance_date')); ?>">
            </div>

            <div class="form-group">
                <label for="next_maintenance_date">วันที่บำรุงรักษาครั้งถัดไป</label>
                <input type="date" id="next_maintenance_date" name="next_maintenance_date" value="<?php echo e(old('next_maintenance_date')); ?>">
            </div>

            <div class="btn-group">
                <button type="submit" class="btn btn-submit">💾 บันทึก</button>
                <a href="<?php echo e(route('facilities.index')); ?>" class="btn btn-cancel">❌ ยกเลิก</a>
            </div>
        </form>
    </div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\Rm1\resources\views/facilities/create.blade.php ENDPATH**/ ?>