@php
    /** @var \App\Models\Maintenance $maintenance */
    /** @var \Illuminate\Database\Eloquent\Collection|\App\Models\Room[] $rooms */
@endphp
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แก้ไขรายการซ่อมบำรุง</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Tahoma, sans-serif; }
        body { background-color: #f4f7f6; padding: 40px; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 12px; box-shadow: 0 5px 20px rgba(0,0,0,0.05); }
        h1 { color: #333; margin-bottom: 25px; font-size: 24px; display: flex; align-items: center; gap: 10px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; font-weight: 600; color: #555; }
        .form-control { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 15px; outline: none; transition: border-color 0.3s; }
        .form-control:focus { border-color: #4a90e2; }
        textarea.form-control { min-height: 100px; resize: vertical; }
        .row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .btn-group { display: flex; gap: 12px; margin-top: 30px; border-top: 1px solid #eee; padding-top: 20px; }
        .btn { padding: 12px 25px; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; font-size: 15px; transition: opacity 0.2s; }
        .btn-save { background-color: #28a745; color: white; }
        .btn-back { background-color: #6c757d; color: white; }
        .btn:hover { opacity: 0.9; }
    </style>
</head>
<body>

<div class="container">
    <h1><i class="fas fa-tools"></i> แก้ไขรายการซ่อมบำรุง</h1>

    <form action="{{ route('maintenances.update', $maintenance->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row">
            <div class="form-group">
                <label for="room_id">ห้องพัก</label>
                <select name="room_id" id="room_id" class="form-control" required>
                    @foreach($rooms as $room)
                        <option value="{{ $room->id }}" {{ $maintenance->room_id == $room->id ? 'selected' : '' }}>
                            ห้อง {{ $room->room_number }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="maintenance_type">ประเภทงานซ่อม</label>
                <select name="maintenance_type" id="maintenance_type" class="form-control" required>
                    <option value="" disabled>-- กรุณาเลือกประเภท --</option>
                    <option value="ระบบไฟฟ้า" {{ $maintenance->maintenance_type == 'ระบบไฟฟ้า' ? 'selected' : '' }}>💡 ระบบไฟฟ้า</option>
                    <option value="ระบบประปา" {{ $maintenance->maintenance_type == 'ระบบประปา' ? 'selected' : '' }}>🚰 ระบบประปา</option>
                    <option value="เครื่องปรับอากาศ" {{ $maintenance->maintenance_type == 'เครื่องปรับอากาศ' ? 'selected' : '' }}>❄️ เครื่องปรับอากาศ</option>
                    <option value="เฟอร์นิเจอร์" {{ $maintenance->maintenance_type == 'เฟอร์นิเจอร์' ? 'selected' : '' }}>🪑 เฟอร์นิเจอร์</option>
                    <option value="อินเทอร์เน็ต/เครือข่าย" {{ $maintenance->maintenance_type == 'อินเทอร์เน็ต/เครือข่าย' ? 'selected' : '' }}>🌐 อินเทอร์เน็ต/เครือข่าย</option>
                    <option value="อื่นๆ" {{ $maintenance->maintenance_type == 'อื่นๆ' ? 'selected' : '' }}>🛠️ อื่นๆ</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label for="description">รายละเอียดปัญหา</label>
            <textarea name="description" id="description" class="form-control" placeholder="ระบุรายละเอียดเพิ่มเติม...">{{ old('description', $maintenance->description) }}</textarea>
        </div>

        <div class="row">
            <div class="form-group">
                <label for="assigned_to"><i class="fas fa-user-cog"></i> ผู้รับผิดชอบ (ช่าง)</label>
                <input type="text" name="assigned_to" id="assigned_to" class="form-control" 
                       value="{{ old('assigned_to', $maintenance->assigned_to) }}" placeholder="ชื่อช่างผู้รับผิดชอบ">
            </div>

            <div class="form-group">
                <label for="cost"><i class="fas fa-baht-sign"></i> ค่าใช้จ่าย (บาท)</label>
                <input type="number" step="0.01" name="cost" id="cost" class="form-control" 
                       value="{{ old('cost', $maintenance->cost) }}" placeholder="0.00">
            </div>
        </div>

        <div class="row">
            <div class="form-group">
                <label for="request_date">วันที่แจ้งซ่อม</label>
                <input type="date" name="request_date" id="request_date" class="form-control" 
                       value="{{ $maintenance->request_date ? $maintenance->request_date->format('Y-m-d') : '' }}" required>
            </div>

            <div class="form-group">
                <label for="status">สถานะ</label>
                <select name="status" id="status" class="form-control">
                    <option value="pending" {{ $maintenance->status == 'pending' ? 'selected' : '' }}>⏳ รอดำเนินการ</option>
                    <option value="in_progress" {{ $maintenance->status == 'in_progress' ? 'selected' : '' }}>⚙️ กำลังดำเนินการ</option>
                    <option value="completed" {{ $maintenance->status == 'completed' ? 'selected' : '' }}>✅ เสร็จสิ้น</option>
                    <option value="cancelled" {{ $maintenance->status == 'cancelled' ? 'selected' : '' }}>❌ ยกเลิก</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label for="notes">หมายเหตุ</label>
            <textarea name="notes" id="notes" class="form-control" rows="2" placeholder="หมายเหตุเพิ่มเติม...">{{ old('notes', $maintenance->notes) }}</textarea>
        </div>

        <div class="btn-group">
            <button type="submit" class="btn btn-save">
                <i class="fas fa-save"></i> บันทึกการแก้ไข
            </button>
            <a href="{{ route('maintenances.index') }}" class="btn btn-back">
                ยกเลิก
            </a>
        </div>
    </form>
</div>

</body>
</html>