<!doctype html>
<html lang="th">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>คุณไม่มีสิทธิ์เข้าถึง</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-7">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="rounded-circle bg-danger-subtle text-danger-emphasis d-flex align-items-center justify-content-center"
                                style="width:56px;height:56px;">
                                <span class="fw-bold fs-4">403</span>
                            </div>
                            <div>
                                <h3 class="mb-1">คุณไม่มีสิทธิ์เข้าถึง</h3>
                                <p class="text-muted mb-0">โปรดตรวจสอบบทบาทผู้ใช้งาน หรือกลับไปที่หน้า Dashboard</p>
                            </div>
                        </div>

                        <div class="d-flex gap-2 flex-wrap">
                            <a href="{{ route('dashboard') }}" class="btn btn-primary px-4">กลับหน้า Dashboard</a>
                            <button class="btn btn-outline-secondary" onclick="history.back()">ย้อนกลับ</button>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
