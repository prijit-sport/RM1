@extends('layouts.app')
 
@section('title', 'เพิ่มแขกหลายคน')
 
@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-11">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white py-3">
                    <h5 class="mb-0"><i class="bi bi-people-fill me-1"></i> เพิ่มแขกหลายคน</h5>
                </div>
                <div class="card-body">
 
                    {{-- แสดง validation errors --}}
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <strong><i class="bi bi-exclamation-triangle me-1"></i>กรุณาตรวจสอบข้อมูล:</strong>
                            <ul class="mb-0 mt-2">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
 
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-1"></i>
                        กรอกข้อมูลแขกได้หลายคนพร้อมกัน — กดปุ่ม <strong>"+ เพิ่มแถว"</strong> เพื่อเพิ่มผู้เช่าใหม่
                        <br>
                        <small><span class="text-danger">*</span> = จำเป็นต้องกรอก / เลขบัตรประชาชนห้ามซ้ำ</small>
                    </div>
 
                    <form action="{{ route('guests.bulk-store') }}" method="POST" id="bulkForm">
                        @csrf
 
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle" id="guestsTable">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width:50px;">#</th>
                                        <th>ชื่อ <span class="text-danger">*</span></th>
                                        <th>นามสกุล <span class="text-danger">*</span></th>
                                        <th>เลขบัตรประชาชน <span class="text-danger">*</span></th>
                                        <th>วันเกิด</th>
                                        <th>เบอร์โทร <span class="text-danger">*</span></th>
                                        <th style="width:60px;"></th>
                                    </tr>
                                </thead>
                                <tbody id="guestRows">
                                    @php
                                        // ถ้า validation fail → ดึงข้อมูลเดิมมาแสดง
                                        $oldGuests = old('guests', array_fill(0, 3, []));
                                    @endphp
 
                                    @foreach($oldGuests as $i => $row)
                                        <tr>
                                            <td class="text-center fw-bold row-num">{{ $i + 1 }}</td>
                                            <td>
                                                <input type="text" name="guests[{{ $i }}][first_name]"
                                                       class="form-control form-control-sm"
                                                       value="{{ $row['first_name'] ?? '' }}"
                                                       placeholder="ชื่อ">
                                            </td>
                                            <td>
                                                <input type="text" name="guests[{{ $i }}][last_name]"
                                                       class="form-control form-control-sm"
                                                       value="{{ $row['last_name'] ?? '' }}"
                                                       placeholder="นามสกุล">
                                            </td>
                                            <td>
                                                <input type="text" name="guests[{{ $i }}][id_number]"
                                                       class="form-control form-control-sm"
                                                       value="{{ $row['id_number'] ?? '' }}"
                                                       placeholder="1234567890123"
                                                       maxlength="50">
                                            </td>
                                            <td>
                                                <input type="date" name="guests[{{ $i }}][date_of_birth]"
                                                       class="form-control form-control-sm"
                                                       value="{{ $row['date_of_birth'] ?? '' }}">
                                            </td>
                                            <td>
                                                <input type="text" name="guests[{{ $i }}][phone]"
                                                       class="form-control form-control-sm"
                                                       value="{{ $row['phone'] ?? '' }}"
                                                       placeholder="08x-xxx-xxxx"
                                                       maxlength="20">
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-sm btn-outline-danger remove-row" title="ลบแถว">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
 
                        <div class="d-flex justify-content-between flex-wrap gap-2 mt-3">
                            <button type="button" id="addRowBtn" class="btn btn-outline-primary">
                                <i class="bi bi-plus-lg me-1"></i>เพิ่มแถว
                            </button>
 
                            <div>
                                <a href="{{ route('guests.index') }}" class="btn btn-secondary">
                                    <i class="bi bi-arrow-left"></i> ยกเลิก
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-circle"></i> บันทึกทั้งหมด
                                </button>
                            </div>
                        </div>
 
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
 
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const tbody  = document.getElementById('guestRows');
        const addBtn = document.getElementById('addRowBtn');
 
        // ── คำนวณเลขแถวใหม่ ──
        function renumberRows() {
            const rows = tbody.querySelectorAll('tr');
            rows.forEach((row, idx) => {
                row.querySelector('.row-num').textContent = idx + 1;
                row.querySelectorAll('input').forEach(inp => {
                    inp.name = inp.name.replace(/guests\[\d+\]/, `guests[${idx}]`);
                });
            });
        }
 
        // ── เพิ่มแถวใหม่ ──
        addBtn.addEventListener('click', function () {
            const idx = tbody.querySelectorAll('tr').length;
            const tr  = document.createElement('tr');
            tr.innerHTML = `
                <td class="text-center fw-bold row-num">${idx + 1}</td>
                <td><input type="text" name="guests[${idx}][first_name]"    class="form-control form-control-sm" placeholder="ชื่อ"></td>
                <td><input type="text" name="guests[${idx}][last_name]"     class="form-control form-control-sm" placeholder="นามสกุล"></td>
                <td><input type="text" name="guests[${idx}][id_number]"     class="form-control form-control-sm" placeholder="1234567890123" maxlength="50"></td>
                <td><input type="date" name="guests[${idx}][date_of_birth]" class="form-control form-control-sm"></td>
                <td><input type="text" name="guests[${idx}][phone]"         class="form-control form-control-sm" placeholder="08x-xxx-xxxx" maxlength="20"></td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-outline-danger remove-row" title="ลบแถว">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            `;
            tbody.appendChild(tr);
        });
 
        // ── ลบแถว (event delegation) ──
        tbody.addEventListener('click', function (e) {
            const removeBtn = e.target.closest('.remove-row');
            if (!removeBtn) return;
 
            if (tbody.querySelectorAll('tr').length <= 1) {
                alert('ต้องมีอย่างน้อย 1 แถว');
                return;
            }
 
            removeBtn.closest('tr').remove();
            renumberRows();
        });
 
        // ── ก่อน submit: ลบแถวที่ไม่ได้กรอก first_name + last_name ──
        document.getElementById('bulkForm').addEventListener('submit', function (e) {
            const rows = tbody.querySelectorAll('tr');
            rows.forEach(row => {
                const firstName = row.querySelector('input[name*="[first_name]"]').value.trim();
                const lastName  = row.querySelector('input[name*="[last_name]"]').value.trim();
                if (!firstName && !lastName) {
                    row.remove();
                }
            });
            renumberRows();
 
            if (tbody.querySelectorAll('tr').length === 0) {
                e.preventDefault();
                alert('กรุณากรอกข้อมูลแขกอย่างน้อย 1 คน');
            }
        });
    });
</script>
@endsection