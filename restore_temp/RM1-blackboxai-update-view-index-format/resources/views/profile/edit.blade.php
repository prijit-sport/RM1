@extends('layouts.app')

@section('title', 'โปรไฟล์ผู้ใช้')
@section('page-title', 'โปรไฟล์ผู้ใช้')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        {{-- ส่วนแสดงข้อความสำเร็จ --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold"><i class="bi bi-person-badge me-2 text-primary"></i>ข้อมูลบัญชี</h5>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('profile.update') }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="name" class="form-label fw-semibold">ชื่อผู้ใช้</label>
                        <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name', $user->name) }}" required placeholder="กรอกชื่อของคุณ">
                        @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label fw-semibold">อีเมล</label>
                        <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror"
                               value="{{ old('email', $user->email) }}" required placeholder="example@mail.com">
                        @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <hr class="my-4" style="border-top: 2px dashed #eee;">
                    
                    <div class="d-flex align-items-center mb-3">
                        <h6 class="mb-0 fw-bold text-secondary"><i class="bi bi-shield-lock me-2"></i>เปลี่ยนรหัสผ่าน (ไม่บังคับ)</h6>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="password" class="form-label fw-semibold">รหัสผ่านใหม่</label>
                                <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="ระบุหากต้องการเปลี่ยน">
                                @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-4">
                                <label for="password_confirmation" class="form-label fw-semibold">ยืนยันรหัสผ่านใหม่</label>
                                <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" placeholder="พิมพ์รหัสผ่านใหม่อีกครั้ง">
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 border-top pt-4">
                        <button type="reset" class="btn btn-light px-4">ยกเลิก</button>
                        <button type="submit" class="btn btn-primary px-4 shadow-sm">
                            <i class="bi bi-save me-2"></i>บันทึกโปรไฟล์
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection