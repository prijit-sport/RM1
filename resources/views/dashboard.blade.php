<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ระบบจัดการห้องพัก</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            padding: 0;
        }
        .navbar {
            width: 100%;
            background: rgba(0, 0, 0, 0.1);
            padding: 20px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: white;
        }
        .navbar-logo {
            font-size: 24px;
            font-weight: bold;
        }
        .navbar-user {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        .logout-btn {
            background: linear-gradient(135deg, #f85032 0%, #e73827 100%);
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            text-decoration: none;
            display: inline-block;
        }
        .logout-btn:hover {
            opacity: 0.9;
            color: white;
        }
        .content-wrapper {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            padding: 40px 20px;
        }
        .container {
            max-width: 1200px;
            width: 100%;
        }
        .header {
            text-align: center;
            color: white;
            margin-bottom: 50px;
        }
        .header h1 {
            font-size: 3em;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }
        .header p {
            font-size: 1.2em;
            opacity: 0.9;
        }
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
        }
        .card {
            background: white;
            border-radius: 15px;
            padding: 40px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            transition: transform 0.3s, box-shadow 0.3s;
            cursor: pointer;
        }
        .card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.3);
        }
        .card i {
            font-size: 3em;
            margin-bottom: 20px;
            display: block;
        }
        .card h2 {
            color: #333;
            margin-bottom: 15px;
            font-size: 1.5em;
        }
        .card p {
            color: #666;
            margin-bottom: 20px;
            line-height: 1.6;
        }
        .btn {
            display: inline-block;
            padding: 12px 30px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            border-radius: 50px;
            transition: transform 0.3s;
            border: none;
            cursor: pointer;
            font-size: 1em;
        }
        .btn:hover {
            transform: scale(1.05);
        }
        .stats {
            background: white;
            border-radius: 15px;
            padding: 30px;
            margin-top: 40px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        .stat-item {
            text-align: center;
            padding: 20px;
            display: inline-block;
            width: 25%;
        }
        .stat-number {
            font-size: 2.5em;
            font-weight: bold;
            color: #667eea;
        }
        .stat-label {
            color: #666;
            margin-top: 10px;
        }
        @media (max-width: 768px) {
            .header h1 {
                font-size: 2em;
            }
            .stat-item {
                width: 50%;
            }
        }
    </style>
</head>
<body>
    <div class="navbar">
        <div class="navbar-logo">🏢 ระบบจัดการอพาร์ทเมนต์</div>
        <div class="navbar-user">
            <span>👤 {{ auth()->user()->name }} <small>( {{ auth()->user()->role->name ?? 'User' }} )</small></span>
            <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                @csrf
                <button type="submit" class="logout-btn">ออกจากระบบ</button>
            </form>
        </div>
    </div>
    
    <div class="content-wrapper">
        <div class="container">
            <div class="header">
            <h1>🏢 ระบบจัดการอพาร์ทเมนต์</h1>
            <p>ยินดีต้อนรับสู่ระบบจัดการพนักงาน</p>
        </div>

        <div class="grid">
            <div class="card">
                <div style="font-size: 3em; margin-bottom: 20px;">🛏️</div>
                <h2>จัดการห้อง</h2>
                <p>เพิ่ม แก้ไข และลบข้อมูลห้องพัก</p>
                <a href="/rooms" class="btn">ดูห้องทั้งหมด</a>
            </div>

            <div class="card">
                <div style="font-size: 3em; margin-bottom: 20px;">👥</div>
                <h2>ข้อมูลผู้เช่า</h2>
                <p>จัดการข้อมูลผู้เช่าและลูกค้า</p>
                <a href="/guests" class="btn">ดูผู้เช่าทั้งหมด</a>
            </div>

            <div class="card">
                <div style="font-size: 3em; margin-bottom: 20px;">📅</div>
                <h2>การเช่าห้อง</h2>
                <p>ตรวจสอบและจัดการการเช่าห้อง</p>
                <a href="/bookings" class="btn">ดูการเช่าทั้งหมด</a>
            </div>

            <div class="card">
                <div style="font-size: 3em; margin-bottom: 20px;">📊</div>
                <h2>รายงาน</h2>
                <p>ดูรายงานสถิติและรายได้</p>
                <a href="/reports" class="btn">ดูรายงาน</a>
            </div>

            @if(auth()->user()->hasRole('Manager') || auth()->user()->hasRole('Admin'))
            <div class="card">
                <div style="font-size: 3em; margin-bottom: 20px;">💼</div>
                <h2>จัดการสินค้า</h2>
                <p>จัดการวัสดุอุปกรณ์และสินค้า</p>
                <a href="/items" class="btn">ดูสินค้า</a>
            </div>

            <div class="card">
                <div style="font-size: 3em; margin-bottom: 20px;">⚡</div>
                <h2>มิเตอร์น้ำ/ไฟ</h2>
                <p>จัดการมิเตอร์ และบันทึกเลขรายงวด</p>
                <a href="/meters" class="btn">ดูมิเตอร์</a>
            </div>

            <div class="card">
                <div style="font-size: 3em; margin-bottom: 20px;">📝</div>
                <h2>สัญญา</h2>
                <p>จัดการสัญญาและข้อตกลง</p>
                <a href="/contracts" class="btn">ดูสัญญา</a>
            </div>

            <div class="card">
                <div style="font-size: 3em; margin-bottom: 20px;">💰</div>
                <h2>ใบแจ้งหนี้</h2>
                <p>จัดการการเรียกเก็บเงิน</p>
                <a href="/invoices" class="btn">ดูใบแจ้งหนี้</a>
            </div>

            <div class="card">
                <div style="font-size: 3em; margin-bottom: 20px;">🔧</div>
                <h2>บำรุงรักษา</h2>
                <p>จัดการการซ่อมบำรุงและบ้านทั่ว</p>
                <a href="/maintenances" class="btn">ดูการบำรุงรักษา</a>
            </div>

            <div class="card">
                <div style="font-size: 3em; margin-bottom: 20px;">🏗️</div>
                <h2>สิ่งอำนวยความสะดวก</h2>
                <p>จัดการสิ่งอำนวยความสะดวก</p>
                <a href="/facilities" class="btn">ดูสิ่งอำนวยความสะดวก</a>
            </div>
            @endif

            @if(auth()->user()->hasRole('Admin'))
            <div class="card">
                <div style="font-size: 3em; margin-bottom: 20px;">👨‍💼</div>
                <h2>บทบาท</h2>
                <p>จัดการบทบาทและผู้ใช้</p>
                <a href="/roles" class="btn">ดูบทบาท</a>
            </div>
            @endif

        <div class="stats">
            <div class="stat-item">
                <div class="stat-number">{{ $roomCount ?? 0 }}</div>
                <div class="stat-label">ห้องทั้งหมด</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">{{ $guestCount ?? 0 }}</div>
                <div class="stat-label">แขกทั้งหมด</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">{{ $bookingCount ?? 0 }}</div>
                <div class="stat-label">การจองทั้งหมด</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">{{ $occupiedCount ?? 0 }}</div>
                <div class="stat-label">ห้องที่ใช้งาน</div>
            </div>
        </div>
        </div>
    </div>
</body>
</html>
