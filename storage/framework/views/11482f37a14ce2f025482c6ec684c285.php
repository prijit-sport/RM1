

<?php $__env->startSection('title', 'บันทึกเลขมิเตอร์'); ?>
<?php $__env->startSection('page-title', 'บันทึกเลขมิเตอร์'); ?>

<?php $__env->startPush('styles'); ?>
    <style>
        /* ─── Meter Type Selector ─── */
        .meter-type-selector {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-bottom: 24px;
        }

        .meter-type-btn {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 16px 20px;
            border-radius: 12px;
            border: 2px solid #e2e8f0;
            background: white;
            text-decoration: none;
            color: #64748b;
            font-weight: 600;
            transition: all 0.2s;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.05);
        }

        .meter-type-btn:hover {
            border-color: #4f46e5;
            background: #f8faff;
            color: #4f46e5;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.12);
        }

        .meter-type-btn.active-electric {
            border-color: #f59e0b;
            background: #fffbeb;
            color: #92400e;
            box-shadow: 0 2px 8px rgba(245, 158, 11, 0.2);
        }

        .meter-type-btn.active-water {
            border-color: #0ea5e9;
            background: #f0f9ff;
            color: #0369a1;
            box-shadow: 0 2px 8px rgba(14, 165, 233, 0.2);
        }

        .meter-type-btn.disabled {
            opacity: 0.45;
            cursor: not-allowed;
            pointer-events: none;
        }

        .meter-type-icon {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            flex-shrink: 0;
        }

        .meter-type-icon.electric {
            background: #fef3c7;
        }

        .meter-type-icon.water {
            background: #e0f2fe;
        }

        .meter-type-info {
            flex: 1;
        }

        .meter-type-name {
            font-size: 1rem;
            font-weight: 700;
            line-height: 1.2;
        }

        .meter-type-code {
            font-size: 0.78rem;
            color: #94a3b8;
            font-weight: 400;
            margin-top: 2px;
        }

        /* ─── Info Banner ─── */
        .meter-info-banner {
            border-radius: 14px;
            padding: 20px 24px;
            margin-bottom: 20px;
            display: flex;
            gap: 32px;
            flex-wrap: wrap;
            align-items: center;
        }

        .meter-info-banner.electric {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        }

        .meter-info-banner.water {
            background: linear-gradient(135deg, #0ea5e9 0%, #0369a1 100%);
        }

        .banner-item {
            color: white;
        }

        .banner-label {
            font-size: 0.72rem;
            opacity: 0.8;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 3px;
        }

        .banner-value {
            font-size: 1.05rem;
            font-weight: 700;
        }

        .banner-badge {
            display: inline-block;
            padding: 3px 12px;
            border-radius: 20px;
            font-size: 0.82rem;
            font-weight: 700;
            background: rgba(255, 255, 255, 0.25);
            color: white;
        }

        /* ─── Tab Navigation ─── */
        .meter-tabs {
            display: flex;
            border-bottom: 2px solid #e2e8f0;
            margin-bottom: 0;
            background: #f8fafc;
            border-radius: 0;
        }

        .meter-tab-btn {
            flex: 1;
            padding: 14px 20px;
            border: none;
            background: none;
            cursor: pointer;
            font-size: 0.9rem;
            font-weight: 600;
            color: #94a3b8;
            border-bottom: 3px solid transparent;
            margin-bottom: -2px;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-family: inherit;
        }

        .meter-tab-btn:hover {
            color: #4f46e5;
            background: #f0f4ff;
        }

        .meter-tab-btn.active {
            color: #4f46e5;
            border-bottom-color: #4f46e5;
            background: white;
        }

        /* ─── Tab Content ─── */
        .meter-tab-content {
            display: none;
            padding: 28px;
        }

        .meter-tab-content.active {
            display: block;
        }

        /* ─── Form Fields ─── */
        .field-label {
            display: block;
            font-size: 0.85rem;
            font-weight: 600;
            color: #374151;
            margin-bottom: 7px;
        }

        .field-label .required {
            color: #ef4444;
        }

        .field-hint {
            font-size: 0.78rem;
            color: #94a3b8;
            margin-top: 5px;
        }

        /* ─── Preview Card ─── */
        .invoice-preview-card {
            background: linear-gradient(135deg, #f8faff 0%, #f0f4ff 100%);
            border: 1.5px dashed #a5b4fc;
            border-radius: 12px;
            padding: 18px 20px;
            margin-bottom: 20px;
        }

        .preview-title {
            font-size: 0.78rem;
            font-weight: 700;
            color: #4f46e5;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .preview-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 6px 0;
            font-size: 0.88rem;
            color: #64748b;
            border-bottom: 1px solid #e0e7ff;
        }

        .preview-row:last-child {
            border-bottom: none;
        }

        .preview-row.total {
            margin-top: 8px;
            padding-top: 10px;
            border-top: 2px solid #c7d2fe;
            border-bottom: none;
            font-weight: 700;
            font-size: 1rem;
            color: #312e81;
        }

        .preview-value {
            font-weight: 600;
            color: #1e293b;
        }

        /* ─── Alert Boxes ─── */
        .info-box {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            padding: 12px 16px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 0.88rem;
        }

        .info-box.info {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            color: #1e40af;
        }

        .info-box.warning {
            background: #fffbeb;
            border: 1px solid #fde68a;
            color: #92400e;
        }

        .info-box i {
            flex-shrink: 0;
            margin-top: 1px;
        }

        /* ─── Action Buttons ─── */
        .action-row {
            display: flex;
            gap: 10px;
            margin-top: 8px;
            padding-top: 20px;
            border-top: 1px solid #f1f5f9;
        }

        .btn-meter-save {
            flex: 1;
            padding: 12px 20px;
            border: none;
            border-radius: 10px;
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
            font-family: inherit;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.2s;
        }

        .btn-meter-save.primary {
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            color: white;
        }

        .btn-meter-save.success {
            background: linear-gradient(135deg, #059669, #047857);
            color: white;
        }

        .btn-meter-save:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .btn-meter-cancel {
            padding: 12px 20px;
            border-radius: 10px;
            border: 1.5px solid #e2e8f0;
            background: white;
            color: #64748b;
            font-weight: 600;
            font-size: 0.95rem;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s;
        }

        .btn-meter-cancel:hover {
            background: #f8fafc;
            color: #374151;
        }
    </style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
    <div class="row justify-content-center">
        <div class="col-lg-7 col-xl-6">

            
            <div class="d-flex align-items-center gap-3 mb-4">
                <a href="<?php echo e(route('meters.readings.index', $meter)); ?>" class="btn btn-light border rounded-3 px-3 py-2">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <div>
                    <h5 class="mb-0 fw-bold">บันทึกเลขมิเตอร์</h5>
                    <div class="text-muted small">ห้อง <?php echo e($meter->room->room_number ?? '-'); ?> · <?php echo e($meter->meter_number); ?>

                    </div>
                </div>
            </div>

            
            <?php
                $room = $meter->room;
                $electricMeter = $room->meters->where('type', 'electric')->first();
                $waterMeter = $room->meters->where('type', 'water')->first();
            ?>
            <div class="meter-type-selector">
                
                <?php if($electricMeter): ?>
                    <a href="<?php echo e(route('meters.readings.create', $electricMeter)); ?>"
                        class="meter-type-btn <?php echo e($meter->type === 'electric' ? 'active-electric' : ''); ?>">
                        <div class="meter-type-icon electric">⚡</div>
                        <div class="meter-type-info">
                            <div class="meter-type-name">ไฟฟ้า</div>
                            <div class="meter-type-code"><?php echo e($electricMeter->meter_number); ?></div>
                        </div>
                        <?php if($meter->type === 'electric'): ?>
                            <i class="bi bi-check-circle-fill text-warning ms-auto"></i>
                        <?php endif; ?>
                    </a>
                <?php else: ?>
                    <div class="meter-type-btn disabled">
                        <div class="meter-type-icon electric">⚡</div>
                        <div class="meter-type-info">
                            <div class="meter-type-name">ไฟฟ้า</div>
                            <div class="meter-type-code">ไม่มีมิเตอร์</div>
                        </div>
                    </div>
                <?php endif; ?>

                
                <?php if($waterMeter): ?>
                    <a href="<?php echo e(route('meters.readings.create', $waterMeter)); ?>"
                        class="meter-type-btn <?php echo e($meter->type === 'water' ? 'active-water' : ''); ?>">
                        <div class="meter-type-icon water">💧</div>
                        <div class="meter-type-info">
                            <div class="meter-type-name">น้ำประปา</div>
                            <div class="meter-type-code"><?php echo e($waterMeter->meter_number); ?></div>
                        </div>
                        <?php if($meter->type === 'water'): ?>
                            <i class="bi bi-check-circle-fill text-info ms-auto"></i>
                        <?php endif; ?>
                    </a>
                <?php else: ?>
                    <div class="meter-type-btn disabled">
                        <div class="meter-type-icon water">💧</div>
                        <div class="meter-type-info">
                            <div class="meter-type-name">น้ำประปา</div>
                            <div class="meter-type-code">ไม่มีมิเตอร์</div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <?php if($errors->any()): ?>
                <div class="alert alert-danger rounded-3 mb-3">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    กรุณาตรวจสอบข้อมูลที่กรอก
                </div>
            <?php endif; ?>

            
            <div class="card border-0 shadow-sm rounded-3 overflow-hidden">

                
                <div class="meter-info-banner <?php echo e($meter->type === 'electric' ? 'electric' : 'water'); ?>">
                    <div class="banner-item">
                        <div class="banner-label">ห้องพัก</div>
                        <div class="banner-value"><?php echo e($meter->room->room_number ?? '-'); ?></div>
                    </div>
                    <div class="banner-item">
                        <div class="banner-label">หมายเลขมิเตอร์</div>
                        <div class="banner-value"><?php echo e($meter->meter_number); ?></div>
                    </div>
                    <div class="banner-item">
                        <div class="banner-label">ประเภท</div>
                        <div class="banner-value">
                            <span class="banner-badge">
                                <?php echo e($meter->type === 'electric' ? '⚡ ไฟฟ้า' : '💧 น้ำประปา'); ?>

                            </span>
                        </div>
                    </div>
                    <div class="banner-item">
                        <div class="banner-label">อัตราค่าบริการ</div>
                        <div class="banner-value"><?php echo e(number_format($meter->rate_per_unit ?? 0, 2)); ?> ฿/หน่วย</div>
                    </div>
                    <?php if($meter->tax_rate > 0): ?>
                        <div class="banner-item">
                            <div class="banner-label">ภาษี</div>
                            <div class="banner-value"><?php echo e(number_format($meter->tax_rate, 0)); ?>%</div>
                        </div>
                    <?php endif; ?>
                </div>

                
                <div class="meter-tabs">
                    <button class="meter-tab-btn active" onclick="switchTab('general', this)">
                        <i class="bi bi-pencil-square"></i>
                        บันทึกทั่วไป
                    </button>
                    <button class="meter-tab-btn" onclick="switchTab('monthly', this)">
                        <i class="bi bi-receipt"></i>
                        รายเดือน + ใบแจ้งหนี้
                    </button>
                </div>

                
                <div id="tab-general" class="meter-tab-content active">
                    <div class="info-box info">
                        <i class="bi bi-info-circle-fill"></i>
                        <span>บันทึกเลขมิเตอร์ทั่วไป — <strong>ไม่สร้างใบแจ้งหนี้อัตโนมัติ</strong></span>
                    </div>

                    <form action="<?php echo e(route('meters.readings.store', $meter)); ?>" method="POST">
                        <?php echo csrf_field(); ?>

                        <div class="mb-3">
                            <label class="field-label" for="reading_date">
                                วันที่อ่านมิเตอร์ <span class="required">*</span>
                            </label>
                            <input class="form-control <?php $__errorArgs = ['reading_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="reading_date"
                                name="reading_date" type="date"
                                value="<?php echo e(old('reading_date', now()->format('Y-m-d'))); ?>" required>
                            <?php $__errorArgs = ['reading_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div class="mb-3">
                            <label class="field-label" for="reading_value">
                                เลขมิเตอร์ที่อ่านได้ <span class="required">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-white">
                                    <?php echo e($meter->type === 'electric' ? '⚡' : '💧'); ?>

                                </span>
                                <input class="form-control <?php $__errorArgs = ['reading_value'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="reading_value"
                                    name="reading_value" type="number" step="0.01" min="0"
                                    value="<?php echo e(old('reading_value')); ?>" placeholder="เช่น 1234.00" required>
                                <span class="input-group-text bg-white text-muted">
                                    <?php echo e($meter->type === 'electric' ? 'kWh' : 'Unit'); ?>

                                </span>
                                <?php $__errorArgs = ['reading_value'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <?php if($meter->latestReading): ?>
                                <div class="field-hint">
                                    <i class="bi bi-clock-history me-1"></i>
                                    เลขล่าสุด:
                                    <strong><?php echo e(number_format($meter->latestReading->reading_value, 2)); ?></strong>
                                    เมื่อ <?php echo e(\Carbon\Carbon::parse($meter->latestReading->reading_date)->diffForHumans()); ?>

                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label class="field-label" for="notes">หมายเหตุ</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="รายละเอียดเพิ่มเติม (ถ้ามี)"><?php echo e(old('notes')); ?></textarea>
                        </div>

                        <div class="action-row">
                            <a href="<?php echo e(route('meters.readings.index', $meter)); ?>" class="btn-meter-cancel">
                                <i class="bi bi-x"></i> ยกเลิก
                            </a>
                            <button type="submit" class="btn-meter-save primary">
                                <i class="bi bi-save"></i> บันทึกข้อมูล
                            </button>
                        </div>
                    </form>
                </div>

                
                <div id="tab-monthly" class="meter-tab-content">
                    <div class="info-box warning">
                        <i class="bi bi-lightning-charge-fill"></i>
                        <span>บันทึกเลขมิเตอร์ประจำเดือน และ
                            <strong>สร้างใบแจ้งหนี้ค่าน้ำ/ไฟอัตโนมัติ</strong>
                        </span>
                    </div>

                    <form action="<?php echo e(route('meters.readings.monthly', $meter)); ?>" method="POST" id="monthly-form">
                        <?php echo csrf_field(); ?>

                        <div class="row g-3 mb-3">
                            <div class="col-6">
                                <label class="field-label" for="period_month">
                                    เดือน <span class="required">*</span>
                                </label>
                                <select class="form-select <?php $__errorArgs = ['period_month'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="period_month"
                                    name="period_month" onchange="updatePreview()" required>
                                    <?php $__currentLoopData = range(1, 12); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($m); ?>"
                                            <?php echo e(old('period_month', now()->month) == $m ? 'selected' : ''); ?>>
                                            <?php echo e(\Carbon\Carbon::create()->month($m)->locale('th')->monthName); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <?php $__errorArgs = ['period_month'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div class="col-6">
                                <label class="field-label" for="period_year">
                                    ปี (พ.ศ.) <span class="required">*</span>
                                </label>
                                <select class="form-select <?php $__errorArgs = ['period_year'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="period_year"
                                    name="period_year" onchange="updatePreview()" required>
                                    <?php for($y = now()->year; $y >= now()->year - 3; $y--): ?>
                                        <option value="<?php echo e($y); ?>"
                                            <?php echo e(old('period_year', now()->year) == $y ? 'selected' : ''); ?>>
                                            <?php echo e($y + 543); ?> (<?php echo e($y); ?>)
                                        </option>
                                    <?php endfor; ?>
                                </select>
                                <?php $__errorArgs = ['period_year'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="field-label" for="m_reading_value">
                                เลขมิเตอร์ที่อ่านได้ <span class="required">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-white">
                                    <?php echo e($meter->type === 'electric' ? '⚡' : '💧'); ?>

                                </span>
                                <input class="form-control <?php $__errorArgs = ['reading_value'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    id="m_reading_value" name="reading_value" type="number" step="0.01"
                                    min="0" value="<?php echo e(old('reading_value')); ?>" placeholder="เช่น 1234.00"
                                    oninput="updatePreview()" required>
                                <span class="input-group-text bg-white text-muted">
                                    <?php echo e($meter->type === 'electric' ? 'kWh' : 'Unit'); ?>

                                </span>
                                <?php $__errorArgs = ['reading_value'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <?php if($meter->latestReading): ?>
                                <div class="field-hint">
                                    <i class="bi bi-clock-history me-1"></i>
                                    เลขล่าสุด:
                                    <strong><?php echo e(number_format($meter->latestReading->reading_value, 2)); ?></strong>
                                </div>
                            <?php endif; ?>
                        </div>

                        
                        <div class="invoice-preview-card" id="invoice-preview" style="display:none;">
                            <div class="preview-title">
                                <i class="bi bi-receipt"></i>
                                ตัวอย่างใบแจ้งหนี้ (ประมาณการ)
                            </div>
                            <div class="preview-row">
                                <span>เลขมิเตอร์ก่อนหน้า</span>
                                <span class="preview-value" id="prev-val">—</span>
                            </div>
                            <div class="preview-row">
                                <span>เลขมิเตอร์ปัจจุบัน</span>
                                <span class="preview-value" id="curr-val">—</span>
                            </div>
                            <div class="preview-row">
                                <span>จำนวนหน่วยที่ใช้</span>
                                <span class="preview-value" id="usage-val">—</span>
                            </div>
                            <div class="preview-row">
                                <span>อัตราค่าบริการ</span>
                                <span class="preview-value"><?php echo e(number_format($meter->rate_per_unit ?? 0, 2)); ?>

                                    ฿/หน่วย</span>
                            </div>
                            <?php if(($meter->tax_rate ?? 0) > 0): ?>
                                <div class="preview-row">
                                    <span>ยอดก่อนภาษี</span>
                                    <span class="preview-value" id="base-val">—</span>
                                </div>
                                <div class="preview-row">
                                    <span>ภาษี <?php echo e(number_format($meter->tax_rate, 0)); ?>%</span>
                                    <span class="preview-value" id="tax-val">—</span>
                                </div>
                            <?php endif; ?>
                            <div class="preview-row total">
                                <span>ยอดรวมประมาณการ</span>
                                <span id="total-val">—</span>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="field-label" for="m_notes">หมายเหตุ</label>
                            <textarea class="form-control" id="m_notes" name="notes" rows="2"
                                placeholder="หมายเหตุเพิ่มเติม (ถ้ามี)"><?php echo e(old('notes')); ?></textarea>
                        </div>

                        <div class="action-row">
                            <a href="<?php echo e(route('meters.readings.index', $meter)); ?>" class="btn-meter-cancel">
                                <i class="bi bi-x"></i> ยกเลิก
                            </a>
                            <button type="submit" class="btn-meter-save success">
                                <i class="bi bi-receipt"></i>
                                บันทึก + สร้างใบแจ้งหนี้
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>

    <script>
        const rate = <?php echo e((float) ($meter->rate_per_unit ?? 0)); ?>;
        const taxRate = <?php echo e((float) ($meter->tax_rate ?? 0)); ?>;
        const latestReading = <?php echo e($meter->latestReading?->reading_value ?? 'null'); ?>;

        function switchTab(name, btn) {
            document.querySelectorAll('.meter-tab-btn').forEach(b => b.classList.remove('active'));
            document.querySelectorAll('.meter-tab-content').forEach(c => c.classList.remove('active'));
            btn.classList.add('active');
            document.getElementById('tab-' + name).classList.add('active');
        }

        function updatePreview() {
            const currVal = parseFloat(document.getElementById('m_reading_value').value);
            if (isNaN(currVal)) {
                document.getElementById('invoice-preview').style.display = 'none';
                return;
            }

            const prevVal = latestReading !== null ? parseFloat(latestReading) : 0;
            const usage = Math.max(0, currVal - prevVal);
            const base = usage * rate;
            const tax = base * (taxRate / 100);
            const total = base + tax;

            const fmt = (n) => n.toLocaleString('th-TH', {
                minimumFractionDigits: 2
            });

            document.getElementById('prev-val').textContent = fmt(prevVal) + ' หน่วย';
            document.getElementById('curr-val').textContent = fmt(currVal) + ' หน่วย';
            document.getElementById('usage-val').textContent = fmt(usage) + ' หน่วย';
            document.getElementById('total-val').textContent = '฿ ' + fmt(total);

            const baseEl = document.getElementById('base-val');
            const taxEl = document.getElementById('tax-val');
            if (baseEl) baseEl.textContent = '฿ ' + fmt(base);
            if (taxEl) taxEl.textContent = '฿ ' + fmt(tax);

            document.getElementById('invoice-preview').style.display = 'block';
        }

        <?php if(old('period_month') || old('period_year')): ?>
            document.addEventListener('DOMContentLoaded', () => {
                switchTab('monthly', document.querySelectorAll('.meter-tab-btn')[1]);
            });
        <?php endif; ?>
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\Rm1\resources\views/meter_readings/create.blade.php ENDPATH**/ ?>