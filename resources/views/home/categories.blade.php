@extends('layouts.app')
@section('title', 'Danh mục sản phẩm')
@section('content')
<div class="container py-4"><h1>Danh mục sản phẩm</h1><div class="list-group">@foreach ($categories as $category)<a href="{{ route('home.category', $category->MaDM) }}" class="list-group-item list-group-item-action d-flex justify-content-between"><span>{{ $category->TenDM }}</span><span>{{ $category->loaiSanPhams->count() }} loại</span></a>@endforeach</div></div>
@endsection
