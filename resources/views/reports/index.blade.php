<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายงาน</title>
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
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            margin-bottom: 30px;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header h1 {
            color: #333;
            margin-bottom: 5px;
        }
        .header p {
            color: #666;
            font-size: 0.9em;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .stat-card.primary {
            border-left: 5px solid #667eea;
        }
        .stat-card.success {
            border-left: 5px solid #28a745;
        }
        .stat-card.warning {
            border-left: 5px solid #ffc107;
        }
        .stat-card.danger {
            border-left: 5px solid #dc3545;
        }
        .stat-card.info {
            border-left: 5px solid #17a2b8;
        }
        .stat-label {
            color: #666;
            font-size: 0.9em;
            margin-bottom: 8px;
        }
        .stat-value {
            font-size: 2em;
            font-weight: bold;
            color: #333;
        }
        .content-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(500px, 1fr));
            gap: 20px;
        }
        .card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .card h2 {
            color: #333;
            margin-bottom: 15px;
            font-size: 1.3em;
            border-bottom: 2px solid #667eea;
            padding-bottom: 10px;
        }
        .room-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        .room-item:last-child {
            border-bottom: none;
        }
        .room-name {
            font-weight: 600;
            color: #333;
        }
        .room-count {
            background: #667eea;
            color: white;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.9em;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }
        .btn:hover {
            background: #764ba2;
        }
        .btn-secondary {
            background: #6c757d;
        }
        .btn-secondary:hover {
            background: #5a6268;
        }
        @media (max-width: 768px) {
            .content-grid {
                grid-template-columns: 1fr;
            }
            .stat-value {
                font-size: 1.5em;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📊 รายงานสถิติและรายได้</h1>
            <p>วันที่: {{ now()->format('d/m/Y H:i') }}</p>
        </div>

        <!-- Key Statistics -->
        <div class="stats-grid">
            <!-- Rooms Stats -->
            <div class="stat-card primary">
                <div class="stat-label">🛏️ ห้องทั้งหมด</div>
                <div class="stat-value">{{ $total_rooms }}</div>
            </div>

            <div class="stat-card success">
                <div class="stat-label">✓ ห้องว่าง</div>
                <div class="stat-value">{{ $available_rooms }}</div>
            </div>

            <div class="stat-card warning">
                <div class="stat-label">👤 ห้องใช้งาน</div>
                <div class="stat-value">{{ $occupied_rooms }}</div>
            </div>

            <div class="stat-card danger">
                <div class="stat-label">🔧 ระหว่างซ่อม</div>
                <div class="stat-value">{{ $maintenance_rooms }}</div>
            </div>

            <!-- Occupancy Rate -->
            <div class="stat-card info">
                <div class="stat-label">📈 อัตราการเข้าพัก</div>
                <div class="stat-value">{{ $occupancy_rate }}%</div>
            </div>

            <!-- Today Stats -->
            <div class="stat-card primary">
                <div class="stat-label">📥 เช็คอินวันนี้</div>
                <div class="stat-value">{{ $today_check_ins }}</div>
            </div>

            <div class="stat-card success">
                <div class="stat-label">📤 เช็คเอาท์วันนี้</div>
                <div class="stat-value">{{ $today_check_outs }}</div>
            </div>

            <!-- Revenue -->
            <div class="stat-card warning">
                <div class="stat-label">💰 รายได้รวม</div>
                <div class="stat-value">฿{{ number_format($total_revenue, 0) }}</div>
            </div>

            <!-- Bookings This Month -->
            <div class="stat-card info">
                <div class="stat-label">📋 การจองเดือนนี้</div>
                <div class="stat-value">{{ $bookings_this_month }}</div>
            </div>

            <!-- Total Bookings -->
            <div class="stat-card primary">
                <div class="stat-label">📊 การจองทั้งหมด</div>
                <div class="stat-value">{{ $total_bookings }}</div>
            </div>
        </div>

        <!-- Status Breakdown -->
        <div class="content-grid">
            <div class="card">
                <h2>📝 สถานะการจอง</h2>
                <div class="room-item">
                    <span class="room-name">รอการยืนยัน</span>
                    <span class="room-count">{{ $pending_bookings }}</span>
                </div>
                <div class="room-item">
                    <span class="room-name">ยืนยันแล้ว</span>
                    <span class="room-count">{{ $confirmed_bookings }}</span>
                </div>
                <div class="room-item">
                    <span class="room-name">เช็คอินแล้ว</span>
                    <span class="room-count">{{ $checked_in }}</span>
                </div>
                <div class="room-item">
                    <span class="room-name">เช็คเอาท์แล้ว</span>
                    <span class="room-count">{{ $checked_out }}</span>
                </div>
                <div class="room-item">
                    <span class="room-name">ยกเลิก</span>
                    <span class="room-count">{{ $cancelled }}</span>
                </div>
            </div>

            <!-- Popular Rooms -->
            <div class="card">
                <h2>🏆 ห้องยอดนิยม</h2>
                @forelse ($popular_rooms as $room)
                    <div class="room-item">
                        <span class="room-name">#{{ $room->room_number }} - {{ $room->room_type }}</span>
                        <span class="room-count">{{ $room->bookings_count }} การจอง</span>
                    </div>
                @empty
                    <p style="color: #666; text-align: center; padding: 20px;">ยังไม่มีข้อมูล</p>
                @endforelse
            </div>
        </div>

        <div style="margin-top: 30px;">
            <a href="/" class="btn btn-secondary">← กลับไปแดชบอร์ด</a>
        </div>
    </div>
</body>
</html>
