
 
<?php
    /** @var \App\Models\Contract $contract */
    /** @var \Illuminate\Support\Collection<\App\Models\Room> $rooms */
    /** @var \Illuminate\Support\Collection<\App\Models\Guest> $guests */
?>
 
<?php $__env->startSection('content'); ?>
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-warning text-dark py-3">
                    <h5 class="mb-0"><i class="bi bi-pencil-square"></i> แก้ไขสัญญาเช่า</h5>
                </div>
                <div class="card-body">
                    <form action="<?php echo e(route('contracts.update', $contract->id)); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PUT'); ?>
                        
                        <!-- ข้อมูลห้องและผู้เช่า -->
                        <h6 class="section-title mt-0"><i class="bi bi-info-circle"></i> ข้อมูลการเช่า</h6>
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label required">ห้องพัก</label>
                                <select name="room_id" class="form-select <?php $__errorArgs = ['room_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                                    <option value="">-- เลือกห้องพัก --</option>
                                    <?php $__currentLoopData = $rooms ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $room): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($room->id); ?>" <?php echo e($contract->room_id == $room->id ? 'selected' : ''); ?>>
                                            <?php echo e($room->room_number); ?> - <?php echo e($room->room_type); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <?php $__errorArgs = ['room_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback d-block"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label required">ผู้เช่า</label>
                                <select name="guest_id" class="form-select <?php $__errorArgs = ['guest_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                                    <option value="">-- เลือกผู้เช่า --</option>
                                    <?php $__currentLoopData = $guests ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $guest): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($guest->id); ?>" <?php echo e($contract->guest_id == $guest->id ? 'selected' : ''); ?>>
                                            <?php echo e($guest->first_name); ?> <?php echo e($guest->last_name); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <?php $__errorArgs = ['guest_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback d-block"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>
 
                        <!-- ข้อมูลสัญญา -->
                        <h6 class="section-title"><i class="bi bi-file-earmark-ruled"></i> ข้อมูลสัญญา</h6>
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <label class="form-label">เลขที่สัญญา</label>
                                <input type="text" class="form-control" value="<?php echo e(old('contract_number', $contract->contract_number)); ?>" readonly>
                                <small class="text-muted">ระบบสร้างอัตโนมัติ</small>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">ชื่อสัญญา</label>
                                <input type="text" name="title" class="form-control <?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('title', $contract->title)); ?>">
                                <?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback d-block"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">วันที่ทำสัญญา</label>
                                <input type="date" name="contract_date" class="form-control <?php $__errorArgs = ['contract_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('contract_date', $contract->contract_date)); ?>">
                                <?php $__errorArgs = ['contract_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback d-block"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>
 
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label required">สถานะ</label>
                                <select name="status" class="form-select <?php $__errorArgs = ['status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                                    <option value="draft" <?php echo e($contract->status == 'draft' ? 'selected' : ''); ?>>ร่าง</option>
                                    <option value="pending" <?php echo e($contract->status == 'pending' ? 'selected' : ''); ?>>รออนุมัติ</option>
                                    <option value="active" <?php echo e($contract->status == 'active' ? 'selected' : ''); ?>>ใช้งาน</option>
                                    <option value="completed" <?php echo e($contract->status == 'completed' ? 'selected' : ''); ?>>เสร็จสิ้น</option>
                                    <option value="cancelled" <?php echo e($contract->status == 'cancelled' ? 'selected' : ''); ?>>ยกเลิก</option>
                                </select>
                                <?php $__errorArgs = ['status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback d-block"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>
 
                        <!-- ระยะเวลาเช่า -->
                        <h6 class="section-title"><i class="bi bi-calendar-range"></i> ระยะเวลาเช่า</h6>
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <label class="form-label required">วันที่เริ่มเช่า</label>
                                <input type="date" name="start_date" class="form-control <?php $__errorArgs = ['start_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('start_date', $contract->start_date)); ?>" required>
                                <?php $__errorArgs = ['start_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback d-block"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label required">วันที่สิ้นสุด</label>
                                <input type="date" name="end_date" class="form-control <?php $__errorArgs = ['end_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('end_date', $contract->end_date)); ?>" required>
                                <?php $__errorArgs = ['end_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback d-block"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">ระยะเวลา (เดือน)</label>
                                <input type="text" class="form-control" value="<?php echo e(old('duration', $contract->duration)); ?>" readonly>
                                <small class="text-muted">คำนวณอัตโนมัติ</small>
                            </div>
                        </div>
 
                        <!-- ค่าเช่าและค่าธรรมเนียม -->
                        <h6 class="section-title"><i class="bi bi-currency-dollar"></i> ค่าเช่าและค่าธรรมเนียม</h6>
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label required">ค่าเช่าต่อเดือน (บาท)</label>
                                <input type="number" name="monthly_rent" class="form-control <?php $__errorArgs = ['monthly_rent'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('monthly_rent', $contract->monthly_rent)); ?>" required min="0" step="0.01">
                                <?php $__errorArgs = ['monthly_rent'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback d-block"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">ค่าเช่าเป็นตัวอักษร</label>
                                <input type="text" name="monthly_rent_text" class="form-control" value="<?php echo e(old('monthly_rent_text', $contract->monthly_rent_text)); ?>">
                            </div>
                        </div>
 
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <label class="form-label">เงินประกัน (บาท)</label>
                                <input type="number" name="deposit" class="form-control <?php $__errorArgs = ['deposit'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" min="0" step="0.01" value="<?php echo e(old('deposit', $contract->deposit ?? 0)); ?>">
                                <small class="text-muted">หากไม่กรอก = ค่าเช่ารายเดือน</small>
                                <?php $__errorArgs = ['deposit'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback d-block"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">จ่ายล่วงหน้า (เดือน)</label>
                                <input type="number" name="advance_payment_months" class="form-control <?php $__errorArgs = ['advance_payment_months'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" min="1" step="1" value="1" readonly>
                                <small class="text-muted">💡 คิดแค่ 1 เดือนเท่านั้น (ระบบตั้งอัตโนมัติ)</small>
                                <?php $__errorArgs = ['advance_payment_months'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback d-block"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">จ่ายล่วงหน้า (บาท)</label>
                                <input type="text" class="form-control" value="฿<?php echo e(number_format($contract->advance_payment ?? 0, 2)); ?>" readonly>
                                <small class="text-muted">อ่านอย่างเดียว</small>
                            </div>
                        </div>
 
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <label class="form-label">ค่าไฟฟ้าต่อหน่วย (บาท)</label>
                                <input type="number" name="electricity_rate" class="form-control <?php $__errorArgs = ['electricity_rate'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" min="0" step="0.01" value="<?php echo e(old('electricity_rate', $contract->electricity_rate ?? 0)); ?>">
                                <?php $__errorArgs = ['electricity_rate'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback d-block"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">ค่าน้ำต่อหน่วย (บาท)</label>
                                <input type="number" name="water_rate" class="form-control <?php $__errorArgs = ['water_rate'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" min="0" step="0.01" value="<?php echo e(old('water_rate', $contract->water_rate ?? 0)); ?>">
                                <?php $__errorArgs = ['water_rate'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback d-block"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">ค่าปรับล่าช้า (%)</label>
                                <input type="number" name="late_fee" class="form-control <?php $__errorArgs = ['late_fee'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" min="0" step="0.01" value="<?php echo e(old('late_fee', $contract->late_fee ?? 0)); ?>">
                                <?php $__errorArgs = ['late_fee'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback d-block"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>
 
                        <div class="mb-4">
                            <label class="form-label">ค่าธรรมเนียมอื่น ๆ</label>
                            <input type="text" name="other_fees" class="form-control" value="<?php echo e(old('other_fees', $contract->other_fees)); ?>">
                        </div>
 
                        <!-- ข้อมูลผู้ให้เช่า -->
                        <h6 class="section-title"><i class="bi bi-person-badge"></i> ข้อมูลผู้ให้เช่า</h6>
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <label class="form-label">ชื่อผู้ให้เช่า</label>
                                <input type="text" name="landlord_name" class="form-control" value="<?php echo e(old('landlord_name', $contract->landlord_name)); ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">เลขบัตรประชาชน</label>
                                <input type="text" name="landlord_id_number" class="form-control" value="<?php echo e(old('landlord_id_number', $contract->landlord_id_number)); ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">เบอร์โทร</label>
                                <input type="text" name="landlord_phone" class="form-control" value="<?php echo e(old('landlord_phone', $contract->landlord_phone)); ?>">
                            </div>
                        </div>
 
                        <div class="mb-4">
                            <label class="form-label">ที่อยู่ผู้ให้เช่า</label>
                            <textarea name="landlord_address" class="form-control" rows="2"><?php echo e(old('landlord_address', $contract->landlord_address)); ?></textarea>
                        </div>
 
                        <!-- เงื่อนไขและลายเซ็น -->
                        <h6 class="section-title"><i class="bi bi-pen"></i> เงื่อนไขและลายเซ็น</h6>
                        <div class="mb-4">
                            <label class="form-label">เงื่อนไขสัญญา</label>
                            <textarea name="terms" class="form-control" rows="3"><?php echo e(old('terms', $contract->terms)); ?></textarea>
                        </div>
 
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <label class="form-label">ลายเซ็นผู้เช่า</label>
                                <input type="text" name="tenant_signature" class="form-control" value="<?php echo e(old('tenant_signature', $contract->tenant_signature)); ?>">
                                <small class="text-muted">วันที่ลงนาม</small>
                                <input type="date" name="tenant_sign_date" class="form-control mt-2" value="<?php echo e(old('tenant_sign_date', $contract->tenant_sign_date)); ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">ลายเซ็นผู้ให้เช่า</label>
                                <input type="text" name="landlord_signature" class="form-control" value="<?php echo e(old('landlord_signature', $contract->landlord_signature)); ?>">
                                <small class="text-muted">วันที่ลงนาม</small>
                                <input type="date" name="landlord_sign_date" class="form-control mt-2" value="<?php echo e(old('landlord_sign_date', $contract->landlord_sign_date)); ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">ลายเซ็นพยาน</label>
                                <input type="text" name="witness_signature" class="form-control" value="<?php echo e(old('witness_signature', $contract->witness_signature)); ?>">
                                <small class="text-muted">วันที่ลงนาม</small>
                                <input type="date" name="witness_sign_date" class="form-control mt-2" value="<?php echo e(old('witness_sign_date', $contract->witness_sign_date)); ?>">
                            </div>
                        </div>
 
                        <!-- หมายเหตุ -->
                        <h6 class="section-title"><i class="bi bi-card-text"></i> หมายเหตุเพิ่มเติม</h6>
                        <div class="mb-4">
                            <label class="form-label">คำอธิบาย</label>
                            <textarea name="description" class="form-control" rows="2"><?php echo e(old('description', $contract->description)); ?></textarea>
                        </div>
 
                        <div class="mb-4">
                            <label class="form-label">หมายเหตุ</label>
                            <textarea name="notes" class="form-control" rows="3"><?php echo e(old('notes', $contract->notes)); ?></textarea>
                        </div>
 
                        <!-- Buttons -->
                        <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                            <a href="<?php echo e(route('contracts.index')); ?>" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> ยกเลิก
                            </a>
                            <button type="submit" class="btn btn-warning">
                                <i class="bi bi-check-circle"></i> บันทึกการแก้ไข
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
 
<style>
.section-title {
    color: #1e3a5f;
    font-weight: 600;
    border-bottom: 2px solid #e9ecef;
    padding-bottom: 8px;
    margin-bottom: 16px;
}
 
.required::after {
    content: ' *';
    color: #dc3545;
}
 
.invalid-feedback {
    display: none;
    font-size: 0.875rem;
    color: #dc3545;
}
 
.form-control.is-invalid,
.form-select.is-invalid {
    border-color: #dc3545;
}
 
.form-control.is-invalid:focus,
.form-select.is-invalid:focus {
    border-color: #dc3545;
    box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
}
</style>
<?php $__env->stopSection(); ?>
 
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Rm1\resources\views/contracts/edit.blade.php ENDPATH**/ ?>