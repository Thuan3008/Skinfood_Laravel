@extends('layouts.app')
@section('title', 'Loại sản phẩm')
@section('content')
<div class="container py-4"><h1>Loại sản phẩm</h1><div class="row g-3">@foreach ($types as $type)<div class="col-md-4"><a href="{{ route('home.type', $type->MaLoai) }}" class="card p-3 text-decoration-none text-dark"><strong>{{ $type->TenLoai }}</strong><small>{{ $type->danhMuc?->TenDM }}</small></a></div>@endforeach</div></div>
@endsection
