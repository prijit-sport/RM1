@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-primary text-white py-3">
                        <h5 class="mb-0"><i class="bi bi-person-plus"></i> เพิ่มข้อมูลผู้เช่า</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('guests.store') }}" method="POST">
                            @csrf

                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label class="form-label required">ชื่อ</label>
                                    <input type="text" name="first_name"
                                        class="form-control @error('first_name') is-invalid @enderror" required>
                                    @error('first_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label required">นามสกุล</label>
                                    <input type="text" name="last_name"
                                        class="form-control @error('last_name') is-invalid @enderror" required>
                                    @error('last_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label class="form-label required">เลขบัตรประจำตัวประชาชน</label>
                                    <input type="text" name="id_number"
                                        class="form-control @error('id_number') is-invalid @enderror" required>
                                    @error('id_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">วันเกิด</label>
                                    <input type="date" name="date_of_birth" class="form-control">
                                </div>
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label class="form-label required">อีเมล</label>
                                    <input type="email" name="email"
                                        class="form-control @error('email') is-invalid @enderror" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label required">เบอร์โทรศัพท์</label>
                                    <input type="text" name="phone"
                                        class="form-control @error('phone') is-invalid @enderror" required>
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label">ที่อยู่</label>
                                <textarea name="address" class="form-control" rows="2"></textarea>
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label class="form-label">จังหวัด</label>
                                    <input type="text" name="city" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">ประเทศ</label>
                                    <input type="text" name="country" class="form-control" value="ไทย">
                                </div>
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="{{ route('guests.index') }}" class="btn btn-secondary">
                                    <i class="bi bi-arrow-left"></i> ยกเลิก
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-circle"></i> บันทึกข้อมูล
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
