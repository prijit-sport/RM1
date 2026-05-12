@extends('layouts.app')
 
@section('title', 'จัดการแขก')
 
@section('page-title', 'จัดการแขก')
 
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">รายการแขก</h4>
    <div class="d-flex gap-2">
        <a href="{{ route('guests.export') }}" class="btn btn-success">
            <i class="bi bi-download me-1"></i>{{ __("ui.export") }}
        </a>
        <a href="{{ route('guests.bulk-create') }}" class="btn btn-outline-primary">
            <i class="bi bi-plus-circle me-1"></i>เพิ่มหลายคน
        </a>
        <a href="{{ route('guests.create') }}" class="btn btn-primary-custom">
            <i class="bi bi-plus-lg me-1"></i>เพิ่มแขกใหม่
        </a>
    </div>
</div>
 
{{-- Flash --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
 
<!-- Search -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('guests.index') }}" class="row g-3">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="ค้นหาชื่อ, อีเมล, เบอร์โทร..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search me-1"></i>{{ __("ui.search") }}
                </button>
            </div>
            <div class="col-md-2">
                <a href="{{ route('guests.index') }}" class="btn btn-outline-secondary w-100">
                    รีเซ็ต
                </a>
            </div>
        </form>
    </div>
</div>
 
<!-- Guests Table -->
<div class="table-card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>ชื่อ-นามสกุล</th>
                    <th>อีเมล</th>
                    <th>เบอร์โทร</th>
                    <th>เลขบัตรประจำตัว</th>
                    <th>การกระทำ</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($guests as $guest)
                    <tr>
                        <td>
                            <strong>{{ $guest->first_name }} {{ $guest->last_name }}</strong>
                        </td>
                        <td>{{ $guest->email ?? '-' }}</td>
                        <td>{{ $guest->phone ?? '-' }}</td>
                        <td>{{ $guest->id_number ?? '-' }}</td>
                        <td>
                            <div class="d-flex gap-2">
                                <a href="{{ route('guests.show', $guest) }}" class="btn btn-sm btn-outline-info" title="ดู">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('guests.edit', $guest) }}" class="btn btn-sm btn-outline-warning" title="แก้ไข">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('guests.destroy', $guest) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="ลบ"
                                            onclick="return confirm('แน่ใจหรือไม่ที่จะลบแขกนี้?')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-4">
                            <i class="bi bi-inbox fs-1 text-muted"></i>
                            <p class="text-muted mt-2">ไม่มีข้อมูลแขก</p>
                            <div class="d-flex gap-2 justify-content-center flex-wrap mt-2">
                                <a href="{{ route('guests.bulk-create') }}" class="btn btn-outline-primary">เพิ่มหลายคน</a>
                                <a href="{{ route('guests.create') }}" class="btn btn-primary-custom">เพิ่มแขกใหม่</a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
 
<!-- Pagination -->
@if ($guests->hasPages())
    <div class="d-flex justify-content-center mt-4">
        {{ $guests->links('pagination::bootstrap-5') }}
    </div>
@endif
@endsection
 