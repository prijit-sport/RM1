<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เพิ่มการจองใหม่</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f5f5;
        }
        .container {
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
        }
        .form-card {
            background: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            margin-bottom: 30px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 600;
        }
        input, select, textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1em;
            font-family: inherit;
        }
        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 5px rgba(102, 126, 234, 0.3);
        }
        .btn-group {
            display: flex;
            gap: 10px;
            margin-top: 30px;
        }
        .btn {
            flex: 1;
            padding: 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1em;
            font-weight: 600;
            transition: background 0.3s;
        }
        .btn-primary {
            background: #667eea;
            color: white;
        }
        .btn-primary:hover {
            background: #764ba2;
        }
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        .btn-secondary:hover {
            background: #5a6268;
        }
        .error {
            color: #dc3545;
            font-size: 0.9em;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="form-card">
            <h1>📅 เพิ่มการจองใหม่</h1>

            <form action="{{ route('bookings.store') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label for="room_id">ห้อง *</label>
                                        <select id="room_id" name="room_id" required>
                        <option value="">-- เลือกห้อง --</option>
                        @foreach ($rooms as $room)
                            <option value="{{ $room->id }}" {{ old('room_id') == $room->id ? 'selected' : '' }}>
                                #{{ $room->room_number }} - {{ enum_bi('room_type', $room->room_type) }} (฿{{ $room->price_per_night }}/คืน)
                            </option>
                        @endforeach
                    </select>
                    @error('room_id')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="guest_id">แขก *</label>
                    <select id="guest_id" name="guest_id" required>
                        <option value="">-- เลือกแขก --</option>
                        @foreach ($guests as $guest)
                            <option value="{{ $guest->id }}" {{ old('guest_id') == $guest->id ? 'selected' : '' }}>
                                {{ $guest->first_name }} {{ $guest->last_name }} ({{ $guest->phone }})
                            </option>
                        @endforeach
                    </select>
                    @error('guest_id')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="check_in_date">วันเช็คอิน *</label>
                    <input type="date" id="check_in_date" name="check_in_date" value="{{ old('check_in_date') }}" required>
                    @error('check_in_date')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="check_out_date">วันเช็คเอาท์ *</label>
                    <input type="date" id="check_out_date" name="check_out_date" value="{{ old('check_out_date') }}" required>
                    @error('check_out_date')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="status">สถานะ *</label>
                    <select id="status" name="status" required>
                        <option value="pending" {{ old('status', 'pending') == 'pending' ? 'selected' : '' }}>รอการยืนยัน</option>
                        <option value="confirmed" {{ old('status') == 'confirmed' ? 'selected' : '' }}>ยืนยันแล้ว</option>
                        <option value="checked_in" {{ old('status') == 'checked_in' ? 'selected' : '' }}>เช็คอินแล้ว</option>
                        <option value="checked_out" {{ old('status') == 'checked_out' ? 'selected' : '' }}>เช็คเอาท์แล้ว</option>
                        <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>ยกเลิก</option>
                    </select>
                    @error('status')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="notes">หมายเหตุ</label>
                    <textarea id="notes" name="notes" placeholder="เขียนหมายเหตุ..." rows="4">{{ old('notes') }}</textarea>
                    @error('notes')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="btn-group">
                    <button type="submit" class="btn btn-primary">✓ บันทึก</button>
                    <a href="{{ route('bookings.index') }}" class="btn btn-secondary" style="text-decoration: none; display: flex; align-items: center; justify-content: center;">← ยกเลิก</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>



