<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เพิ่มห้องใหม่</title>
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
            <h1>🛏️ เพิ่มห้องใหม่</h1>

            <form action="{{ route('rooms.store') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label for="room_number">หมายเลขห้อง *</label>
                    <input type="text" id="room_number" name="room_number" placeholder="เช่น 101" value="{{ old('room_number') }}" required>
                    @error('room_number')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="room_type">ประเภทห้อง *</label>
                    <select id="room_type" name="room_type" required>
                        <option value="">-- เลือกประเภท --</option>
                        <option value="Single" {{ old('room_type') == 'Single' ? 'selected' : '' }}>Single</option>
                        <option value="Double" {{ old('room_type') == 'Double' ? 'selected' : '' }}>Double</option>
                        <option value="Twin" {{ old('room_type') == 'Twin' ? 'selected' : '' }}>Twin</option>
                        <option value="Suite" {{ old('room_type') == 'Suite' ? 'selected' : '' }}>Suite</option>
                        <option value="Deluxe" {{ old('room_type') == 'Deluxe' ? 'selected' : '' }}>Deluxe</option>
                    </select>
                    @error('room_type')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="price_per_night">ราคา/คืน (บาท) *</label>
                    <input type="number" id="price_per_night" name="price_per_night" placeholder="1000" step="0.01" value="{{ old('price_per_night') }}" required>
                    @error('price_per_night')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="capacity">ความจุ (คน) *</label>
                    <input type="number" id="capacity" name="capacity" placeholder="1" min="1" value="{{ old('capacity', 1) }}" required>
                    @error('capacity')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="status">สถานะ *</label>
                    <select id="status" name="status" required>
                        <option value="available" {{ old('status', 'available') == 'available' ? 'selected' : '' }}>ว่าง</option>
                        <option value="occupied" {{ old('status') == 'occupied' ? 'selected' : '' }}>ใช้งาน</option>
                        <option value="maintenance" {{ old('status') == 'maintenance' ? 'selected' : '' }}>ซ่อมบำรุง</option>
                    </select>
                    @error('status')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="description">คำอธิบาย</label>
                    <textarea id="description" name="description" placeholder="เขียนรายละเอียดห้อง..." rows="4">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="btn-group">
                    <button type="submit" class="btn btn-primary">✓ บันทึก</button>
                    <a href="{{ route('rooms.index') }}" class="btn btn-secondary" style="text-decoration: none; display: flex; align-items: center; justify-content: center;">← ยกเลิก</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
