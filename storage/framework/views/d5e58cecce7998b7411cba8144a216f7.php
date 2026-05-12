<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แก้ไขเลขมิเตอร์</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f5f5f5; }
        .container { max-width: 700px; margin: 50px auto; padding: 20px; }
        .card { background: white; border-radius: 10px; padding: 30px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #333; margin-bottom: 16px; }
        .subtitle { color:#666; margin-bottom: 18px; }
        .form-group { margin-bottom: 16px; }
        label { display:block; margin-bottom: 6px; font-weight: 700; color: #333; }
        input, textarea { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 1em; font-family: inherit; }
        textarea { resize: vertical; }
        .btn-group { display:flex; gap: 10px; margin-top: 20px; }
        .btn { flex: 1; padding: 12px; border: none; border-radius: 5px; cursor: pointer; font-size: 1em; font-weight: 700; transition: background 0.2s; text-decoration:none; display:flex; align-items:center; justify-content:center; }
        .btn-primary { background: #667eea; color: white; }
        .btn-primary:hover { background: #764ba2; }
        .btn-secondary { background: #6c757d; color: white; }
        .btn-secondary:hover { background: #5a6268; }
        .error { color: #dc3545; font-size: 0.9em; margin-top: 6px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <h1>แก้ไขเลขมิเตอร์</h1>
            <div class="subtitle">
                ห้อง <strong><?php echo e($meter->room->room_number ?? '-'); ?></strong>
                | เลขมิเตอร์: <strong><?php echo e($meter->meter_number); ?></strong>
            </div>

            <form action="<?php echo e(route('meters.readings.update', [$meter, $reading])); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>

                <div class="form-group">
                    <label for="reading_date">วันที่ *</label>
                    <input id="reading_date" name="reading_date" type="date" value="<?php echo e(old('reading_date', optional($reading->reading_date)->format('Y-m-d'))); ?>" required>
                    <?php $__errorArgs = ['reading_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="error"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div class="form-group">
                    <label for="reading_value">เลขมิเตอร์ *</label>
                    <input id="reading_value" name="reading_value" type="number" step="0.01" min="0" value="<?php echo e(old('reading_value', $reading->reading_value)); ?>" required>
                    <?php $__errorArgs = ['reading_value'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="error"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div class="form-group">
                    <label for="notes">หมายเหตุ</label>
                    <textarea id="notes" name="notes" rows="4" placeholder="รายละเอียดเพิ่มเติม..."><?php echo e(old('notes', $reading->notes)); ?></textarea>
                    <?php $__errorArgs = ['notes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="error"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div class="btn-group">
                    <button type="submit" class="btn btn-primary">✓ บันทึก</button>
                    <a href="<?php echo e(route('meters.readings.index', $meter)); ?>" class="btn btn-secondary">← ยกเลิก</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>

<?php /**PATH C:\xampp\htdocs\Rm1\resources\views/meter_readings/edit.blade.php ENDPATH**/ ?>