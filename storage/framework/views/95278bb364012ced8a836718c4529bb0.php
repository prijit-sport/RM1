<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายละเอียดบทบาท</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI'; background: #f5f5f5; padding: 20px; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 40px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { margin-bottom: 30px; color: #333; }
        .detail-row { display: flex; border-bottom: 1px solid #eee; padding: 15px 0; }
        .detail-label { font-weight: 600; color: #555; width: 200px; }
        .detail-value { color: #333; flex: 1; }
        .permission-list { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 10px; }
        .permission-badge { display: inline-block; padding: 5px 12px; background: #e9ecef; border-radius: 15px; font-size: 13px; color: #495057; }
        .btn-group { display: flex; gap: 10px; margin-top: 30px; }
        .btn { padding: 12px 20px; border: none; border-radius: 5px; cursor: pointer; font-weight: bold; text-decoration: none; display: inline-block; }
        .btn-edit { background: #ffc107; color: #333; }
        .btn-edit:hover { background: #e0a800; }
        .btn-delete { background: #dc3545; color: white; }
        .btn-delete:hover { background: #c82333; }
        .btn-back { background: #6c757d; color: white; }
        .btn-back:hover { background: #5a6268; }
    </style>
</head>
<body>
    <div class="container">
        <h1>👤 รายละเอียดบทบาท</h1>

        <div class="detail-row">
            <div class="detail-label">ชื่อบทบาท:</div>
            <div class="detail-value"><?php echo e($role->name); ?></div>
        </div>

        <div class="detail-row">
            <div class="detail-label">คำอธิบาย:</div>
            <div class="detail-value"><?php echo e($role->description ?? '-'); ?></div>
        </div>

        <div class="detail-row">
            <div class="detail-label">สิทธิ์การใช้งาน:</div>
            <div class="detail-value">
                <?php if($role->permissions->count() > 0): ?>
                    <div class="permission-list">
                        <?php $__currentLoopData = $role->permissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $permission): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <span class="permission-badge"><?php echo e($permission->name); ?></span>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                <?php else: ?>
                    -
                <?php endif; ?>
            </div>
        </div>

        <div class="btn-group">
            <a href="<?php echo e(route('roles.edit', $role->id)); ?>" class="btn btn-edit">✏️ แก้ไข</a>
            <form method="POST" action="<?php echo e(route('roles.destroy', $role->id)); ?>" style="display:inline;" onsubmit="return confirm('คุณแน่ใจหรือไม่ที่จะลบบทบาทนี้?');">
                <?php echo csrf_field(); ?>
                <?php echo method_field('DELETE'); ?>
                <button type="submit" class="btn btn-delete">🗑️ ลบ</button>
            </form>
            <a href="<?php echo e(route('roles.index')); ?>" class="btn btn-back">⬅️ กลับ</a>
        </div>
    </div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\Rm1\resources\views/roles/show.blade.php ENDPATH**/ ?>