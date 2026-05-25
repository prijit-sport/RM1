<?php $__env->startSection('title', 'เพิ่มห้องหลายห้อง'); ?>
<?php $__env->startSection('page-title', 'เพิ่มห้องหลายห้อง'); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">เพิ่มห้องหลายห้อง</h4>
    <a href="<?php echo e(route('rooms.index')); ?>" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>กลับ
    </a>
</div>

<?php if($errors->any()): ?>
    <div class="alert alert-danger">
        <strong>กรุณาตรวจสอบข้อมูล:</strong>
        <ul class="mb-0 mt-2">
            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li><?php echo e($error); ?></li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-body">
        <form method="POST" action="<?php echo e(route('rooms.bulk-store')); ?>" id="bulkRoomForm">
            <?php echo csrf_field(); ?>

            <div class="table-responsive">
                <table class="table align-middle" id="roomsTable">
                    <thead>
                        <tr>
                            <th>หมายเลขห้อง</th>
                            <th>ประเภท</th>
                            <th>ราคา/เดือน</th>
                            <th>ความจุ</th>
                            <th>สถานะ</th>
                            <th>ชั้น</th>
                            <th>คำอธิบาย</th>
                            <th style="width: 80px;">ลบ</th>

                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $oldRooms = old('rooms', [
                                ['room_number' => '', 'room_type' => 'fan', 'price_per_month' => '', 'capacity' => 1, 'floor' => 1, 'status' => 'available', 'description' => ''],
                            ]);
                        ?>


                        <?php $__currentLoopData = $oldRooms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $room): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><input type="text" class="form-control" name="rooms[<?php echo e($i); ?>][room_number]" value="<?php echo e($room['room_number'] ?? ''); ?>" required></td>

                                <td>

                                    <select class="form-select room-type" name="rooms[<?php echo e($i); ?>][room_type]" data-room-index="<?php echo e($i); ?>" required>
                                        <option value="fan" <?php if(($room['room_type'] ?? '') === 'fan'): echo 'selected'; endif; ?>><?php echo e(enum_bi('room_type', 'fan')); ?></option>
                                        <option value="air_conditioning" <?php if(($room['room_type'] ?? '') === 'air_conditioning'): echo 'selected'; endif; ?>><?php echo e(enum_bi('room_type', 'air_conditioning')); ?></option>
                                    </select>
                                </td>
                                <td><input type="number" min="0" step="0.01" class="form-control room-price" name="rooms[<?php echo e($i); ?>][price_per_month]" data-room-index="<?php echo e($i); ?>" value="<?php echo e($room['price_per_month'] ?? ''); ?>" required></td>
                                <td><input type="number" min="1" class="form-control" name="rooms[<?php echo e($i); ?>][capacity]" value="<?php echo e($room['capacity'] ?? 1); ?>" required></td>
                                <td>
                                    <select class="form-select" name="rooms[<?php echo e($i); ?>][status]">
                                        <option value="available" <?php if(($room['status'] ?? 'available') === 'available'): echo 'selected'; endif; ?>>ว่าง</option>
                                        <option value="occupied" <?php if(($room['status'] ?? '') === 'occupied'): echo 'selected'; endif; ?>>ใช้งาน</option>
                                        <option value="maintenance" <?php if(($room['status'] ?? '') === 'maintenance'): echo 'selected'; endif; ?>>ซ่อมบำรุง</option>
                                    </select>
                                </td>
                                <td><input type="number" min="1" class="form-control" name="rooms[<?php echo e($i); ?>][floor]" value="<?php echo e($room['floor'] ?? 1); ?>" required></td>
                                <td><input type="text" class="form-control" name="rooms[<?php echo e($i); ?>][description]" value="<?php echo e($room['description'] ?? ''); ?>"></td>


                                <td>
                                    <button type="button" class="btn btn-outline-danger btn-sm remove-row">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>

            <div class="d-flex gap-2 mt-3">
                <button type="button" class="btn btn-outline-primary" id="addRowBtn">
                    <i class="bi bi-plus-circle me-1"></i>เพิ่มแถว
                </button>
                <button type="submit" class="btn btn-primary-custom">
                    <i class="bi bi-save me-1"></i>บันทึกทั้งหมด
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    (function () {
        const tableBody = document.querySelector('#roomsTable tbody');
        const addRowBtn = document.getElementById('addRowBtn');

        function nextIndex() {
            return tableBody.querySelectorAll('tr').length;
        }

        const PRICE_BY_TYPE = {
            fan: 2800,
            air_conditioning: 3500,
        };

        function priceInputForIndex(i) {
            return tableBody.querySelector(`input.room-price[data-room-index="${i}"]`);
        }

        function applyPriceForSelect(selectEl) {
            if (!selectEl) return;
            const i = selectEl.dataset.roomIndex;
            const priceInput = priceInputForIndex(i);


            // fallback: find by closest row (safer if indexes change)
            if (!priceInput) {
                const row = selectEl.closest('tr');
                if (row) {
                    const fallbackInput = row.querySelector('input.room-price');
                    if (fallbackInput) priceInput = fallbackInput;
                }
            }
            if (!priceInput) return;

            const roomType = selectEl.value;
            const nextPrice = PRICE_BY_TYPE[roomType];
            if (nextPrice === undefined) return;

            priceInput.value = nextPrice;
        }

        function rowTemplate(i) {
            return `
                <tr>
                    <td><input type="text" class="form-control" name="rooms[${i}][room_number]" required></td>
                    <td>
                        <select class="form-select room-type" name="rooms[${i}][room_type]" data-room-index="${i}" required>
                            <option value="fan"><?php echo e(enum_bi('room_type', 'fan')); ?></option>
                            <option value="air_conditioning"><?php echo e(enum_bi('room_type', 'air_conditioning')); ?></option>
                        </select>
                    </td>
                    <td><input type="number" min="0" step="0.01" class="form-control room-price" name="rooms[${i}][price_per_month]" data-room-index="${i}" required></td>
                    <td><input type="number" min="1" class="form-control" name="rooms[${i}][capacity]" value="1" required></td>
                    <td>
                        <select class="form-select" name="rooms[${i}][status]">

                            <option value="available">ว่าง</option>
                            <option value="occupied">ใช้งาน</option>
                            <option value="maintenance">ซ่อมบำรุง</option>
                        </select>
                    </td>
                    <td><input type="number" min="1" class="form-control" name="rooms[${i}][floor]" value="1" required></td>
                    <td><input type="text" class="form-control" name="rooms[${i}][description]"></td>

                    <td>
                        <button type="button" class="btn btn-outline-danger btn-sm remove-row">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
        }

        // init for existing rows
        tableBody.querySelectorAll('select.room-type').forEach(selectEl => {
            applyPriceForSelect(selectEl);
        });

        addRowBtn.addEventListener('click', function () {
            tableBody.insertAdjacentHTML('beforeend', rowTemplate(nextIndex()));
            const newRow = tableBody.querySelectorAll('tr')[tableBody.querySelectorAll('tr').length - 1];
            const selectEl = newRow.querySelector('select.room-type');
            applyPriceForSelect(selectEl);
        });

        tableBody.addEventListener('change', function (e) {
            const selectEl = e.target.closest('select.room-type');
            if (!selectEl) return;
            applyPriceForSelect(selectEl);
        });

        tableBody.addEventListener('click', function (e) {
            const button = e.target.closest('.remove-row');
            if (!button) return;
            const rows = tableBody.querySelectorAll('tr');
            if (rows.length <= 1) return;
            button.closest('tr').remove();
        });
    })();
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Rm1\resources\views/rooms/bulk-create.blade.php ENDPATH**/ ?>