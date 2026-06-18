<?php $__env->startSection('page-title', 'เพิ่มใบแจ้งหนี้ใหม่'); ?>

<?php $__env->startSection('content'); ?>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <!-- Header Section -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="fw-bold text-dark mb-0">สร้างใบแจ้งหนี้ (Create Invoice)</h4>
                    <a href="<?php echo e(route('invoices.index')); ?>" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-arrow-left"></i> กลับสู่หน้ารายการ
                    </a>
                </div>

                
                <?php if(isset($meterData) && !empty($meterData)): ?>
                    <div class="card border-0 mb-4 shadow-sm rounded-3"
                        style="border-left: 4px solid #4a90d9 !important; border-left-width: 4px !important;">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center gap-2 mb-3">
                                <span class="badge bg-primary rounded-pill fs-6 px-3 py-2">
                                    <i class="bi bi-lightning-charge-fill me-1"></i> มิเตอร์น้ำ / ไฟ
                                </span>
                                <span class="text-muted small">
                                    ห้อง <?php echo e($meterData['room'] ?? '-'); ?>

                                    <?php if(!empty($meterData['tenant'])): ?>
                                        · <?php echo e($meterData['tenant']); ?>

                                    <?php endif; ?>
                                    <?php if(!empty($meterData['period'])): ?>
                                        · ประจำเดือน <?php echo e($meterData['period']); ?>

                                    <?php endif; ?>
                                </span>
                            </div>

                            <div class="row g-3">
                                
                                <?php if(isset($meterData['electric'])): ?>
                                    <?php $e = $meterData['electric']; ?>
                                    <div class="col-md-6">
                                        <div class="rounded-3 p-3 h-100"
                                            style="background: #fffbeb; border: 1px solid #fde68a;">
                                            <div class="d-flex align-items-center gap-2 mb-2">
                                                <span style="font-size:1.4rem;">⚡</span>
                                                <span class="fw-semibold text-warning-emphasis">ค่าไฟฟ้า</span>
                                                <span
                                                    class="badge bg-warning text-dark ms-auto"><?php echo e($e['meter_number']); ?></span>
                                            </div>
                                            <div class="row g-2 text-sm">
                                                <div class="col-6">
                                                    <div class="text-muted small">เลขมิเตอร์ก่อนหน้า</div>
                                                    <div class="fw-semibold">
                                                        <?php echo e($e['previous_value'] !== null ? number_format($e['previous_value'], 2) : '-'); ?>

                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="text-muted small">เลขมิเตอร์ปัจจุบัน</div>
                                                    <div class="fw-semibold"><?php echo e(number_format($e['current_value'], 2)); ?>

                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="text-muted small">จำนวนหน่วย</div>
                                                    <div class="fw-semibold"><?php echo e(number_format($e['usage'], 2)); ?> หน่วย</div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="text-muted small">อัตรา/หน่วย</div>
                                                    <div class="fw-semibold">฿<?php echo e(number_format($e['rate'], 2)); ?></div>
                                                </div>
                                            </div>
                                            <hr class="my-2">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="text-muted small">รวมค่าไฟ</span>
                                                <span
                                                    class="fw-bold text-warning-emphasis fs-5">฿<?php echo e(number_format($e['cost'], 2)); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                
                                <?php if(isset($meterData['water'])): ?>
                                    <?php $w = $meterData['water']; ?>
                                    <div class="col-md-6">
                                        <div class="rounded-3 p-3 h-100"
                                            style="background: #eff6ff; border: 1px solid #bfdbfe;">
                                            <div class="d-flex align-items-center gap-2 mb-2">
                                                <span style="font-size:1.4rem;">💧</span>
                                                <span class="fw-semibold text-primary">ค่าน้ำประปา</span>
                                                <span class="badge bg-primary ms-auto"><?php echo e($w['meter_number']); ?></span>
                                            </div>
                                            <div class="row g-2 text-sm">
                                                <div class="col-6">
                                                    <div class="text-muted small">เลขมิเตอร์ก่อนหน้า</div>
                                                    <div class="fw-semibold">
                                                        <?php echo e($w['previous_value'] !== null ? number_format($w['previous_value'], 2) : '-'); ?>

                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="text-muted small">เลขมิเตอร์ปัจจุบัน</div>
                                                    <div class="fw-semibold"><?php echo e(number_format($w['current_value'], 2)); ?>

                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="text-muted small">จำนวนหน่วย</div>
                                                    <div class="fw-semibold"><?php echo e(number_format($w['usage'], 2)); ?> หน่วย
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="text-muted small">อัตรา/หน่วย</div>
                                                    <div class="fw-semibold">฿<?php echo e(number_format($w['rate'], 2)); ?></div>
                                                </div>
                                            </div>
                                            <hr class="my-2">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="text-muted small">รวมค่าน้ำ</span>
                                                <span
                                                    class="fw-bold text-primary fs-5">฿<?php echo e(number_format($w['cost'], 2)); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>

                            
                            <?php
                                $meterTotal =
                                    ($meterData['electric']['cost'] ?? 0) + ($meterData['water']['cost'] ?? 0);
                            ?>
                            <div class="mt-3 p-3 rounded-3 d-flex justify-content-between align-items-center"
                                style="background: #f0f9ff; border: 1px solid #bae6fd;">
                                <span class="fw-semibold text-primary">
                                    <i class="bi bi-calculator me-1"></i>
                                    รวมค่าน้ำ + ค่าไฟ (ดึงอัตโนมัติจากมิเตอร์)
                                </span>
                                <span class="fw-bold fs-4 text-primary">฿<?php echo e(number_format($meterTotal, 2)); ?></span>
                            </div>

                            <div class="mt-2 text-muted small">
                                <i class="bi bi-info-circle me-1"></i>
                                ยอดข้างต้นถูกกรอกลงในช่อง "จำนวนเงินก่อนภาษี" โดยอัตโนมัติ
                                — สามารถแก้ไขได้ก่อนบันทึก
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-body p-4 p-md-5">
                        <form action="<?php echo e(route('invoices.store')); ?>" method="POST" id="invoiceForm">
                            <?php echo csrf_field(); ?>

                            
                            <?php if(isset($draftInvoice)): ?>
                                <input type="hidden" name="draft_invoice_id" value="<?php echo e($draftInvoice->id); ?>">
                            <?php endif; ?>

                            <!-- ส่วนที่ 1: ข้อมูลอ้างอิง -->
                            <h6 class="text-uppercase text-muted fw-bold mb-3 border-bottom pb-2">
                                <i class="bi bi-info-circle me-1"></i> ข้อมูลอ้างอิง (Reference Information)
                            </h6>
                            <div class="row g-4 mb-4">
                                <div class="col-md-6">
                                    <label for="booking_id" class="form-label fw-semibold required">รายการจองอ้างอิง</label>
                                    <select id="booking_id" name="booking_id"
                                        class="form-select <?php $__errorArgs = ['booking_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required
                                        <?php echo e(isset($draftInvoice) ? 'disabled' : ''); ?>>
                                        <option value="">-- ระบุรายการจอง --</option>
                                        <?php $__currentLoopData = $bookings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $booking): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($booking->id); ?>"
                                                data-amount="<?php echo e(number_format($booking->total_price ?? 0, 2, '.', '')); ?>"
                                                <?php echo e((old('booking_id') ?: $draftInvoice->booking_id ?? null) == $booking->id ? 'selected' : ''); ?>>
                                                ห้อง <?php echo e($booking->room->room_number ?? '-'); ?> · ผู้เช่า:
                                                <?php echo e(trim(($booking->guest->first_name ?? '') . ' ' . ($booking->guest->last_name ?? '')) ?: 'ไม่ระบุชื่อ'); ?>

                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                    
                                    <?php if(isset($draftInvoice)): ?>
                                        <input type="hidden" name="booking_id" value="<?php echo e($draftInvoice->booking_id); ?>">
                                    <?php endif; ?>
                                    <?php $__errorArgs = ['booking_id'];
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
                                <div class="col-md-6">
                                    <label for="invoice_number"
                                        class="form-label fw-semibold required">เลขที่ใบแจ้งหนี้</label>
                                    <input type="text" id="invoice_number" name="invoice_number"
                                        class="form-control <?php $__errorArgs = ['invoice_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                        value="<?php echo e(old('invoice_number', $draftInvoice->invoice_number ?? ($invoiceNumber ?? ''))); ?>"
                                        placeholder="INV-YYYYMM-XXXX" required>
                                    <?php $__errorArgs = ['invoice_number'];
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

                            <!-- ส่วนที่ 2: รายละเอียดจำนวนเงิน -->
                            <h6 class="text-uppercase text-muted fw-bold mb-3 border-bottom pb-2">
                                <i class="bi bi-currency-dollar me-1"></i> รายละเอียดทางการเงิน (Financial Details)
                            </h6>
                            <div class="row g-4 mb-4 bg-light p-3 rounded border border-light-subtle mx-0">
                                <div class="col-md-4">
                                    <label for="amount" class="form-label fw-semibold required">จำนวนเงินก่อนภาษี
                                        (THB)</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white">฿</span>
                                        <input id="amount" name="amount" type="number" min="0"
                                            step="0.01"
                                            class="form-control text-end <?php $__errorArgs = ['amount'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                            value="<?php echo e(old('amount', isset($meterData) ? ($meterData['electric']['cost'] ?? 0) + ($meterData['water']['cost'] ?? 0) : $draftInvoice->amount ?? '')); ?>"
                                            placeholder="0.00" required>
                                        <?php $__errorArgs = ['amount'];
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
                                    <?php if(isset($meterData) && !empty($meterData)): ?>
                                        <div class="form-text text-primary">
                                            <i class="bi bi-lightning-fill"></i> คำนวณจากค่าน้ำ+ค่าไฟอัตโนมัติ
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-4">
                                    <label for="tax" class="form-label fw-semibold required">ภาษีมูลค่าเพิ่ม
                                        (THB)</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white">฿</span>
                                        <input id="tax" name="tax" type="number" min="0"
                                            step="0.01"
                                            class="form-control text-end <?php $__errorArgs = ['tax'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                            value="<?php echo e(old('tax', $draftInvoice->tax ?? '0.00')); ?>" placeholder="0.00"
                                            required>
                                        <?php $__errorArgs = ['tax'];
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
                                <div class="col-md-4">
                                    <label for="total"
                                        class="form-label fw-semibold required text-primary">ยอดชำระสุทธิ (THB)</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-primary text-white border-primary">฿</span>
                                        <input id="total" name="total" type="number" min="0"
                                            step="0.01"
                                            class="form-control text-end fw-bold bg-white <?php $__errorArgs = ['total'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                            value="<?php echo e(old('total', $draftInvoice->total ?? '')); ?>" placeholder="0.00"
                                            required readonly>
                                        <?php $__errorArgs = ['total'];
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
                            </div>

                            <!-- ส่วนที่ 3: กำหนดเวลาและสถานะ -->
                            <h6 class="text-uppercase text-muted fw-bold mb-3 border-bottom pb-2">
                                <i class="bi bi-calendar-check me-1"></i> กำหนดเวลาและเงื่อนไข (Schedule & Terms)
                            </h6>
                            <div class="row g-4 mb-4">
                                <div class="col-md-4">
                                    <label for="issue_date"
                                        class="form-label fw-semibold required">วันที่ออกเอกสาร</label>
                                    <input id="issue_date" name="issue_date" type="date"
                                        class="form-control <?php $__errorArgs = ['issue_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                        value="<?php echo e(old('issue_date', isset($draftInvoice->issue_date) ? \Carbon\Carbon::parse($draftInvoice->issue_date)->format('Y-m-d') : '')); ?>"
                                        required>
                                    <?php $__errorArgs = ['issue_date'];
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
                                <div class="col-md-4">
                                    <label for="due_date" class="form-label fw-semibold required">วันครบกำหนดชำระ</label>
                                    <input id="due_date" name="due_date" type="date"
                                        class="form-control <?php $__errorArgs = ['due_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                        value="<?php echo e(old('due_date', isset($draftInvoice->due_date) ? \Carbon\Carbon::parse($draftInvoice->due_date)->format('Y-m-d') : '')); ?>"
                                        required>
                                    <?php $__errorArgs = ['due_date'];
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
                                <div class="col-md-4">
                                    <label for="status" class="form-label fw-semibold required">สถานะเอกสาร</label>
                                    <select id="status" name="status"
                                        class="form-select <?php $__errorArgs = ['status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                                        <?php $currentStatus = old('status', $draftInvoice->status ?? 'draft'); ?>
                                        <option value="draft" <?php echo e($currentStatus === 'draft' ? 'selected' : ''); ?>>ร่าง
                                            (Draft)</option>
                                        <option value="sent" <?php echo e($currentStatus === 'sent' ? 'selected' : ''); ?>>
                                            ส่งมอบแล้ว (Sent)</option>
                                        <option value="paid" <?php echo e($currentStatus === 'paid' ? 'selected' : ''); ?>>
                                            ชำระเงินเรียบร้อย (Paid)</option>
                                        <option value="overdue" <?php echo e($currentStatus === 'overdue' ? 'selected' : ''); ?>>
                                            เกินกำหนด (Overdue)</option>
                                        <option value="cancelled" <?php echo e($currentStatus === 'cancelled' ? 'selected' : ''); ?>>
                                            ยกเลิก (Cancelled)</option>
                                    </select>
                                    <?php $__errorArgs = ['status'];
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

                            <!-- ส่วนที่ 4: หมายเหตุเพิ่มเติม -->
                            <div class="row mb-5">
                                <div class="col-12">
                                    <label for="notes" class="form-label fw-semibold">หมายเหตุเพิ่มเติม
                                        (Remarks)</label>
                                    <textarea id="notes" name="notes" class="form-control bg-light" rows="3"
                                        placeholder="ระบุเงื่อนไขการชำระเงิน หรือรายละเอียดเพิ่มเติมสำหรับเอกสารฉบับนี้..."><?php echo e(old('notes', $draftInvoice->notes ?? '')); ?></textarea>
                                </div>
                            </div>

                            <!-- Button Actions -->
                            <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                                <button type="button" class="btn btn-light border px-4"
                                    onclick="window.history.back();">
                                    ยกเลิก
                                </button>
                                <button type="submit" class="btn btn-primary px-5">
                                    <i class="bi bi-save me-1"></i>
                                    <?php echo e(isset($draftInvoice) ? 'ยืนยันใบแจ้งหนี้' : 'บันทึกข้อมูล'); ?>

                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php echo $__env->make('invoices._total-calculator', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    
    <?php if(isset($meterData) && !empty($meterData)): ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // trigger การคำนวณ total ทันทีที่หน้าโหลด
                const amountInput = document.getElementById('amount');
                const taxInput = document.getElementById('tax');
                const totalInput = document.getElementById('total');

                function recalcTotal() {
                    const amount = parseFloat(amountInput.value) || 0;
                    const tax = parseFloat(taxInput.value) || 0;
                    totalInput.value = (amount + tax).toFixed(2);
                }

                amountInput.addEventListener('input', recalcTotal);
                taxInput.addEventListener('input', recalcTotal);

                // คำนวณครั้งแรกเมื่อ load
                recalcTotal();
            });
        </script>
    <?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\Rm1\resources\views/invoices/create.blade.php ENDPATH**/ ?>