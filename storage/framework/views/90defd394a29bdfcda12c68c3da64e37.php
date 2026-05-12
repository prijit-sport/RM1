<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แก้ไขบทบาท</title>
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
        .checkbox-group { display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px; margin-top: 10px; }
        .checkbox-item { display: flex; align-items: center; gap: 8px; }
        .checkbox-item input { width: auto; }
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
        <h1>✏️ แก้ไขบทบาท</h1>
        
        <?php if($errors->any()): ?>
            <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div><?php echo e($error); ?></div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?php echo e(route('roles.update', $role->id)); ?>">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>

            <div class="form-group">
                <label for="name">ชื่อบทบาท *</label>
                <input type="text" id="name" name="name" value="<?php echo e(old('name', $role->name)); ?>" required>
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
                <label for="description">คำอธิบาย</label>
                <textarea id="description" name="description"><?php echo e(old('description', $role->description)); ?></textarea>
            </div>

            <div class="form-group">
                <label>สิทธิ์การใช้งาน</label>
                <div class="checkbox-group">
                    <?php $__currentLoopData = $permissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $permission): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="checkbox-item">
                            <input type="checkbox" id="permission_<?php echo e($permission->id); ?>" name="permissions[]" value="<?php echo e($permission->id); ?>" <?php echo e($role->permissions->contains($permission->id) || in_array($permission->id, old('permissions', [])) ? 'checked' : ''); ?>>
                            <label for="permission_<?php echo e($permission->id); ?>"><?php echo e($permission->name); ?></label>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>

            <div class="btn-group">
                <button type="submit" class="btn btn-submit">💾 บันทึก</button>
                <a href="<?php echo e(route('roles.show', $role->id)); ?>" class="btn btn-cancel">❌ ยกเลิก</a>
            </div>
        </form>
    </div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\Rm1\resources\views/roles/edit.blade.php ENDPATH**/ ?>