@extends('layouts.app')

@section('title', 'เพิ่มห้องหลายห้อง')
@section('page-title', 'เพิ่มห้องหลายห้อง')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">เพิ่มห้องหลายห้อง</h4>
    <a href="{{ route('rooms.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>กลับ
    </a>
</div>

@if ($errors->any())
    <div class="alert alert-danger">
        <strong>กรุณาตรวจสอบข้อมูล:</strong>
        <ul class="mb-0 mt-2">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('rooms.bulk-store') }}" id="bulkRoomForm">
            @csrf

            <div class="table-responsive">
                <table class="table align-middle" id="roomsTable">
                    <thead>
                        <tr>
                            <th>หมายเลขห้อง</th>
                            <th>ประเภท</th>
                            <th>ราคา/คืน</th>
                            <th>ความจุ</th>
                            <th>สถานะ</th>
                            <th>คำอธิบาย</th>
                            <th style="width: 80px;">ลบ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $oldRooms = old('rooms', [
                                ['room_number' => '', 'room_type' => 'Single', 'price_per_night' => '', 'capacity' => 1, 'status' => 'available', 'description' => ''],
                            ]);
                        @endphp

                        @foreach ($oldRooms as $i => $room)
                            <tr>
                                <td><input type="text" class="form-control" name="rooms[{{ $i }}][room_number]" value="{{ $room['room_number'] ?? '' }}" required></td>
                                <td>
                                    <select class="form-select" name="rooms[{{ $i }}][room_type]" required>
                                        <option value="Single" @selected(($room['room_type'] ?? '') === 'Single')>{{ enum_bi('room_type', 'Single') }}</option>
                                        <option value="Double" @selected(($room['room_type'] ?? '') === 'Double')>{{ enum_bi('room_type', 'Double') }}</option>
                                        <option value="Twin" @selected(($room['room_type'] ?? '') === 'Twin')>{{ enum_bi('room_type', 'Twin') }}</option>
                                        <option value="Suite" @selected(($room['room_type'] ?? '') === 'Suite')>{{ enum_bi('room_type', 'Suite') }}</option>
                                        <option value="Deluxe" @selected(($room['room_type'] ?? '') === 'Deluxe')>{{ enum_bi('room_type', 'Deluxe') }}</option>
                                    </select>
                                </td>
                                <td><input type="number" min="0" step="0.01" class="form-control" name="rooms[{{ $i }}][price_per_night]" value="{{ $room['price_per_night'] ?? '' }}" required></td>
                                <td><input type="number" min="1" class="form-control" name="rooms[{{ $i }}][capacity]" value="{{ $room['capacity'] ?? 1 }}" required></td>
                                <td>
                                    <select class="form-select" name="rooms[{{ $i }}][status]">
                                        <option value="available" @selected(($room['status'] ?? 'available') === 'available')>ว่าง</option>
                                        <option value="occupied" @selected(($room['status'] ?? '') === 'occupied')>ใช้งาน</option>
                                        <option value="maintenance" @selected(($room['status'] ?? '') === 'maintenance')>ซ่อมบำรุง</option>
                                    </select>
                                </td>
                                <td><input type="text" class="form-control" name="rooms[{{ $i }}][description]" value="{{ $room['description'] ?? '' }}"></td>
                                <td>
                                    <button type="button" class="btn btn-outline-danger btn-sm remove-row">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
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

        function rowTemplate(i) {
            return `
                <tr>
                    <td><input type="text" class="form-control" name="rooms[${i}][room_number]" required></td>
                    <td>
                        <select class="form-select" name="rooms[${i}][room_type]" required>
                            <option value="Single">{{ enum_bi('room_type', 'Single') }}</option>
                            <option value="Double">{{ enum_bi('room_type', 'Double') }}</option>
                            <option value="Twin">{{ enum_bi('room_type', 'Twin') }}</option>
                            <option value="Suite">{{ enum_bi('room_type', 'Suite') }}</option>
                            <option value="Deluxe">{{ enum_bi('room_type', 'Deluxe') }}</option>
                        </select>
                    </td>
                    <td><input type="number" min="0" step="0.01" class="form-control" name="rooms[${i}][price_per_night]" required></td>
                    <td><input type="number" min="1" class="form-control" name="rooms[${i}][capacity]" value="1" required></td>
                    <td>
                        <select class="form-select" name="rooms[${i}][status]">
                            <option value="available">ว่าง</option>
                            <option value="occupied">ใช้งาน</option>
                            <option value="maintenance">ซ่อมบำรุง</option>
                        </select>
                    </td>
                    <td><input type="text" class="form-control" name="rooms[${i}][description]"></td>
                    <td>
                        <button type="button" class="btn btn-outline-danger btn-sm remove-row">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
        }

        addRowBtn.addEventListener('click', function () {
            tableBody.insertAdjacentHTML('beforeend', rowTemplate(nextIndex()));
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
@endsection
