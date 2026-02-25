@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1>จัดการบทบาท (Coming Soon)</h1>
        </div>
        <div class="col-md-4 text-right">
            <a href="{{ route('roles.create') }}" class="btn btn-primary">เพิ่มบทบาทใหม่</a>
        </div>
    </div>
    
    <div class="alert alert-info">
        <strong>Coming Soon</strong> - ฟีเจอร์นี้อยู่ระหว่างการพัฒนา
    </div>
</div>
@endsection
