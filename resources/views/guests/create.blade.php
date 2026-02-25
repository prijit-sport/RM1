<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เพิ่มแขกใหม่</title>
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
        input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1em;
            font-family: inherit;
        }
        input:focus {
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
            <h1>👥 เพิ่มแขกใหม่</h1>

            <form action="{{ route('guests.store') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label for="first_name">ชื่อ *</label>
                    <input type="text" id="first_name" name="first_name" placeholder="ชื่อ" value="{{ old('first_name') }}" required>
                    @error('first_name')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="last_name">นามสกุล *</label>
                    <input type="text" id="last_name" name="last_name" placeholder="นามสกุล" value="{{ old('last_name') }}" required>
                    @error('last_name')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="email">อีเมล *</label>
                    <input type="email" id="email" name="email" placeholder="example@domain.com" value="{{ old('email') }}" required>
                    @error('email')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="phone">เบอร์โทร *</label>
                    <input type="tel" id="phone" name="phone" placeholder="08xxxxxxxx" value="{{ old('phone') }}" required>
                    @error('phone')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="id_number">หมายเลขประจำตัว *</label>
                    <input type="text" id="id_number" name="id_number" placeholder="1234567890123" value="{{ old('id_number') }}" required>
                    @error('id_number')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="address">ที่อยู่</label>
                    <input type="text" id="address" name="address" placeholder="ที่อยู่" value="{{ old('address') }}">
                    @error('address')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="city">เมือง</label>
                    <input type="text" id="city" name="city" placeholder="เมือง" value="{{ old('city') }}">
                    @error('city')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="country">ประเทศ</label>
                    <input type="text" id="country" name="country" placeholder="ประเทศ" value="{{ old('country') }}">
                    @error('country')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="btn-group">
                    <button type="submit" class="btn btn-primary">✓ บันทึก</button>
                    <a href="{{ route('guests.index') }}" class="btn btn-secondary" style="text-decoration: none; display: flex; align-items: center; justify-content: center;">← ยกเลิก</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
