@extends('layouts.app')

@section('title', 'ตั้งค่า')
@section('page-title', 'ตั้งค่า')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white">
                <h5 class="mb-0">การตั้งค่าระบบ</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('settings.update') }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="locale" class="form-label">ภาษา</label>
                        <select id="locale" name="locale" class="form-select @error('locale') is-invalid @enderror">
                            <option value="th" @selected(old('locale', $settings['locale']) === 'th')>ไทย</option>
                            <option value="en" @selected(old('locale', $settings['locale']) === 'en')>English</option>
                        </select>
                        @error('locale')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="items_per_page" class="form-label">จำนวนรายการต่อหน้า</label>
                        <select id="items_per_page" name="items_per_page" class="form-select @error('items_per_page') is-invalid @enderror">
                            @foreach([10, 20, 50, 100] as $size)
                                <option value="{{ $size }}" @selected((int) old('items_per_page', $settings['items_per_page']) === $size)>{{ $size }} รายการ</option>
                            @endforeach
                        </select>
                        @error('items_per_page')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-check form-switch mb-4">
                        <input class="form-check-input" type="checkbox" role="switch" id="compact_mode" name="compact_mode" value="1"
                               @checked(old('compact_mode', $settings['compact_mode']))>
                        <label class="form-check-label" for="compact_mode">เปิดโหมดหน้าจอกระชับ (Compact)</label>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary-custom">บันทึกการตั้งค่า</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
