@extends('layouts.app')
@section('title','Không tìm thấy trang')
@section('content')<div class="container my-5 text-center"><h1>Không tìm thấy trang</h1><p class="text-muted">Trang bạn đang tìm không tồn tại hoặc đã được thay đổi.</p><a class="btn btn-danger" href="{{ route('home') }}">Về trang chủ</a></div>@endsection
