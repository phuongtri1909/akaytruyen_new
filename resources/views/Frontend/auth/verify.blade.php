@extends('Frontend.layouts.default')

@section('title', 'Đăng nhập')
@section('description', 'Đăng nhập AKAY TRUYỆN')
@section('keywords', 'Đăng nhập AKAY TRUYỆN')
@push('custom_schema')
{{-- {!! SEOMeta::generate() !!} --}}
{{-- {!! JsonLd::generate() !!} --}}
{!! SEO::generate() !!}
@endpush
@section('content')
<div class="container">
    <h2>Xác thực tài khoản</h2>
    <form method="POST" action="{{ route('auth.verify') }}">
        @csrf
        <input type="hidden" name="email" value="{{ session('email') }}">
        <div class="mb-3">
            <label for="verification_code" class="form-label">Nhập mã xác thực:</label>
            <input type="text" class="form-control" name="verification_code" required>
        </div>
        <button type="submit" class="btn btn-primary">Xác thực</button>
    </form>
</div>
@endsection