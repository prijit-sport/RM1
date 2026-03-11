{{-- Form Layout Component - สไตล์ทางการ --}}
@props(['title', 'action' => '', 'method' => 'POST'])

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body { background-color: #f5f7fa; }
        .form-header {
            background: linear-gradient(135deg, #1e3a5f 0%, #2c5282 100%);
            color: white;
            padding: 20px;
            border-radius: 8px 8px 0 0;
            margin: -20px -20px 20px -20px;
        }
        .form-header h4 { margin: 0; font-weight: 600; }
        .form-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            padding: 20px;
        }
        .form-label {
            font-weight: 600;
            color: #2c3e50;
            font-size: 0.9rem;
        }
        .form-control, .form-select {
            border: 1px solid #d1d5db;
            border-radius: 6px;
            padding: 10px 12px;
        }
        .form-control:focus, .form-select:focus {
            border-color: #2c5282;
            box-shadow: 0 0 0 3px rgba(44, 82, 130, 0.1);
        }
        .btn-primary {
            background-color: #2c5282;
            border-color: #2c5282;
            padding: 10px 24px;
            font-weight: 600;
        }
        .btn-primary:hover {
            background-color: #1e3a5f;
            border-color: #1e3a5f;
        }
        .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
            padding: 10px 24px;
        }
        .required::after { content: " *"; color: #dc3545; }
        .section-title {
            color: #2c5282;
            font-weight: 600;
            border-bottom: 2px solid #e9ecef;
            padding-bottom: 8px;
            margin-bottom: 16px;
        }
        .is-invalid { border-color: #dc3545 !important; }
        .invalid-feedback { color: #dc3545; font-size: 0.85rem; }
    </style>
</head>
<body>
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="form-card">
                    <div class="form-header">
                        <h4><i class="bi bi-{{ $icon ?? 'document-text' }}"></i> {{ $title }}</h4>
                    </div>
                    
                    <form action="{{ $action }}" method="{{ $method === 'POST' ? 'POST' : 'POST' }}" {{ $attributes }}>
                        @csrf
                        @if($method !== 'POST' && $method !== 'GET')
                            @method($method)
                        @endif
                        
                        {{ $slot }}
                        
                        <hr class="my-4">
                        <div class="d-flex justify-content-between">
                            <a href="{{ $backUrl ?? '#' }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> ยกเลิก
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> บันทึก
                            </button>
                        </div>
                    </form>
                </div>
        </div>
</body>
</html>
