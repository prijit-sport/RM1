<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'ระบบจัดการอพาร์ทเมนท์')</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

    <!-- ✅ Bootstrap 5 CSS (CDN) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- ✅ Bootstrap Icons (เพียงไฟล์เดียว - ไม่ซ้ำ) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <!-- ✅ Google Fonts (with font-display=swap for faster loading) -->
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    <!-- ✅ Custom Theme CSS (check if exists) -->
    @if (file_exists(public_path('css/romar-theme.css')))
        <link rel="stylesheet"
            href="{{ asset('css/romar-theme.css?v=' . filemtime(public_path('css/romar-theme.css'))) }}">
    @endif

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
    <style>
        :root {
            --primary-color: #4f46e5;
            --secondary-color: #6366f1;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
            --info-color: #0ea5e9;
            --dark-color: #1f2937;
            --light-color: #f3f4f6;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Prompt', sans-serif;
            background-color: #f5f6fa;
        }

        .sidebar {
            min-height: 100vh;
            background: linear-gradient(180deg, #1e293b 0%, #0f172a 100%);
            box-shadow: 4px 0 15px rgba(0, 0, 0, 0.1);
            position: fixed;
            width: 260px;
            z-index: 1000;
        }

        .sidebar-brand {
            padding: 20px;
            font-size: 1.25rem;
            font-weight: 700;
            color: #fff;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .sidebar-menu {
            padding: 15px 0;
        }

        .sidebar-menu-item {
            padding: 12px 20px;
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 12px;
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
            font-size: 0.95rem;
        }

        .sidebar-menu-item:hover {
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
        }

        .sidebar-menu-item.active {
            background: rgba(79, 70, 229, 0.2);
            color: #fff;
            border-left-color: var(--primary-color);
        }

        .sidebar-menu-item i {
            font-size: 1.1rem;
            width: 24px;
            text-align: center;
        }

        .main-content {
            margin-left: 260px;
            min-height: 100vh;
        }

        .topbar {
            background: #fff;
            padding: 15px 30px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--primary-color);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .notification-badge {
            position: relative;
        }

        .notification-badge::after {
            content: '';
            position: absolute;
            top: -2px;
            right: -2px;
            width: 8px;
            height: 8px;
            background: var(--danger-color);
            border-radius: 50%;
            border: 2px solid #fff;
        }

        /* Notification Dropdown Styles */
        .notification-dropdown {
            min-width: 340px;
            padding: 0;
            border: none;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
            border-radius: 12px;
            overflow: hidden;
        }

        .notification-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 14px 18px;
            font-weight: 600;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .notification-item {
            padding: 12px 18px;
            border-bottom: 1px solid #f0f0f0;
            display: flex;
            align-items: flex-start;
            gap: 12px;
            text-decoration: none;
            color: #333;
            transition: background-color 0.2s ease;
        }

        .notification-item:hover {
            background-color: #f8f9fa;
            color: #333;
        }

        .notification-item:last-child {
            border-bottom: none;
        }

        .notification-icon {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 1.1rem;
        }

        .notification-icon.warning {
            background: #fff3cd;
            color: #f59e0b;
        }

        .notification-icon.danger {
            background: #fee2e2;
            color: #ef4444;
        }

        .notification-empty {
            text-align: center;
            padding: 30px 20px;
            color: #6c757d;
        }

        .stat-card {
            background: #fff;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .table-card {
            background: #fff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .table-card .table {
            margin-bottom: 0;
        }

        .table-card .table thead th {
            background: #f8f9fa;
            border-bottom: 2px solid #e9ecef;
            font-weight: 600;
            color: #495057;
            padding: 15px;
        }

        .table-card .table tbody td {
            padding: 15px;
            vertical-align: middle;
        }

        .status-badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .status-available {
            background: #d1fae5;
            color: #065f46;
        }

        .status-occupied {
            background: #dbeafe;
            color: #1e40af;
        }

        .status-maintenance {
            background: #fef3c7;
            color: #92400e;
        }

        .btn-primary-custom {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            color: #fff;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-primary-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(79, 70, 229, 0.4);
            color: #fff;
        }

        .alert {
            border: none;
            border-radius: 8px;
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }
        }
    </style>
</head>

<body>
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-brand">
            <i class="bi bi-building"></i>
            <span>ระบบจัดการอพาร์ทเมนท์</span>
        </div>

        <nav class="sidebar-menu">
            <a href="{{ route('dashboard') }}"
                class="sidebar-menu-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i>
                <span>แดชบอร์ด</span>
            </a>

            <a href="{{ route('rooms.index') }}"
                class="sidebar-menu-item {{ request()->routeIs('rooms.*') ? 'active' : '' }}">
                <i class="bi bi-door-open"></i>
                <span>ห้องพัก</span>
            </a>

            <a href="{{ route('guests.index') }}"
                class="sidebar-menu-item {{ request()->routeIs('guests.*') ? 'active' : '' }}">
                <i class="bi bi-people"></i>
                <span>ผู้เช่า</span>
            </a>

            <a href="{{ route('bookings.index') }}"
                class="sidebar-menu-item {{ request()->routeIs('bookings.*') ? 'active' : '' }}">
                <i class="bi bi-calendar-check"></i>
                <span>เข้าพัก</span>
            </a>

            @if (auth()->check() && auth()->user() && auth()->user()->hasRole('Admin'))
                <a href="{{ route('contracts.index') }}"
                    class="sidebar-menu-item {{ request()->routeIs('contracts.*') ? 'active' : '' }}">
                    <i class="bi bi-file-earmark-text"></i>
                    <span>สัญญาเช่า</span>
                </a>

                <a href="{{ route('invoices.index') }}"
                    class="sidebar-menu-item {{ request()->routeIs('invoices.*') ? 'active' : '' }}">
                    <i class="bi bi-receipt"></i>
                    <span>ใบแจ้งหนี้</span>
                    @if (isset($pendingPayments) && $pendingPayments > 0)
                        <span class="badge bg-danger ms-auto">{{ $pendingPayments }}</span>
                    @endif
                </a>

                <a href="{{ route('meters.index') }}"
                    class="sidebar-menu-item {{ request()->routeIs('meters.*') ? 'active' : '' }}">
                    <i class="bi bi-lightning-charge"></i>
                    <span>มิเตอร์น้ำ/ไฟ</span>
                </a>

                <a href="{{ route('maintenances.index') }}"
                    class="sidebar-menu-item {{ request()->routeIs('maintenances.*') ? 'active' : '' }}">
                    <i class="bi bi-tools"></i>
                    <span>ซ่อมบำรุง</span>
                    @if (isset($pendingMaintenance) && $pendingMaintenance > 0)
                        <span class="badge bg-warning ms-auto">{{ $pendingMaintenance }}</span>
                    @endif
                </a>

                <a href="{{ route('facilities.index') }}"
                    class="sidebar-menu-item {{ request()->routeIs('facilities.*') ? 'active' : '' }}">
                    <i class="bi bi-building"></i>
                    <span>สิ่งอำนวยความสะดวก</span>
                </a>

                <a href="{{ route('roles.index') }}"
                    class="sidebar-menu-item {{ request()->routeIs('roles.*') ? 'active' : '' }}">
                    <i class="bi bi-shield-lock"></i>
                    <span>บทบาทและสิทธิ์</span>
                </a>
            @endif

            <a href="{{ route('reports.index') }}"
                class="sidebar-menu-item {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                <i class="bi bi-graph-up"></i>
                <span>รายงาน</span>
            </a>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Topbar -->
        <header class="topbar">
            <div class="d-flex align-items-center">
                <button class="btn d-lg-none me-3" id="sidebarToggle">
                    <i class="bi bi-list fs-4"></i>
                </button>
                <h5 class="mb-0">@yield('page-title', 'หน้าหลัก')</h5>
            </div>

            <div class="d-flex align-items-center gap-3">

                {{-- Notifications --}}
                @php
                    $pendingPayments = $pendingPayments ?? 0;
                    $pendingMaintenance = $pendingMaintenance ?? 0;
                    $totalNotifications = $pendingPayments + $pendingMaintenance;
                @endphp

                <div class="dropdown">
                    <button
                        class="btn btn-light position-relative {{ $totalNotifications > 0 ? 'notification-badge' : '' }}"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-bell"></i>
                        @if ($totalNotifications > 0)
                            <span
                                class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                {{ $totalNotifications > 99 ? '99+' : $totalNotifications }}
                            </span>
                        @endif
                    </button>

                    <ul class="dropdown-menu dropdown-menu-end notification-dropdown">
                        <li>
                            <div class="notification-header">
                                <span><i class="bi bi-bell-fill me-2"></i>การแจ้งเตือน</span>
                                @if ($totalNotifications > 0)
                                    <span class="badge bg-light text-dark">{{ $totalNotifications }} รายการ</span>
                                @endif
                            </div>
                        </li>

                        @if ($totalNotifications === 0)
                            <li>
                                <div class="notification-empty">
                                    <i class="bi bi-check-circle-fill text-success" style="font-size: 2rem;"></i>
                                    <div class="mt-2">ไม่มีการแจ้งเตือนใหม่</div>
                                    <small class="text-muted">ทุกอย่างเรียบร้อยดี ✨</small>
                                </div>
                            </li>
                        @else
                            @if ($pendingMaintenance > 0)
                                <li>
                                    <a href="{{ route('maintenances.index', ['status' => 'pending']) }}"
                                        class="notification-item">
                                        <div class="notification-icon warning">
                                            <i class="bi bi-tools"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="fw-semibold">งานซ่อมบำรุงรอดำเนินการ</div>
                                            <small class="text-muted">
                                                มีรายการซ่อมบำรุง <strong>{{ $pendingMaintenance }}</strong>
                                                รายการที่รอดำเนินการ
                                            </small>
                                        </div>
                                        <i class="bi bi-chevron-right text-muted"></i>
                                    </a>
                                </li>
                            @endif

                            @if ($pendingPayments > 0)
                                <li>
                                    <a href="{{ route('invoices.index') }}" class="notification-item">
                                        <div class="notification-icon danger">
                                            <i class="bi bi-receipt"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="fw-semibold">ใบแจ้งหนี้รอชำระเงิน</div>
                                            <small class="text-muted">
                                                มีใบแจ้งหนี้ <strong>{{ $pendingPayments }}</strong> ใบที่ยังไม่ชำระ
                                            </small>
                                        </div>
                                        <i class="bi bi-chevron-right text-muted"></i>
                                    </a>
                                </li>
                            @endif
                        @endif
                    </ul>
                </div>

                <!-- User Menu -->
                <div class="dropdown">
                    <button class="btn d-flex align-items-center gap-2" data-bs-toggle="dropdown">
                        <div class="user-avatar">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                        <span class="d-none d-md-inline">{{ auth()->user()->name }}</span>
                        <i class="bi bi-chevron-down"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><span class="dropdown-item-text">
                                <small class="text-muted">{{ auth()->user()->role->name ?? 'User' }}</small>
                            </span></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item" href="{{ route('profile.edit') }}"><i
                                    class="bi bi-person me-2"></i>โปรไฟล์</a></li>
                        <li><a class="dropdown-item" href="{{ route('settings.edit') }}"><i
                                    class="bi bi-gear me-2"></i>ตั้งค่า</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="bi bi-box-arrow-right me-2"></i>ออกจากระบบ
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </header>

        <!-- Content -->
        <div class="container-fluid p-4">
            <!-- Flash Messages -->
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if (session('warning'))
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i>{{ session('warning') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if (session('info'))
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <i class="bi bi-info-circle me-2"></i>{{ session('info') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <!-- ✅ Bootstrap JS (ใช้ defer เพื่อ async loading) -->
    <script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Sidebar toggle for mobile
        document.getElementById('sidebarToggle')?.addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('show');
        });

        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    </script>

    @stack('scripts')
</body>

</html>
