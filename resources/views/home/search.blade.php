@extends('layouts.app')

@section('title', 'Tìm kiếm sản phẩm')
@section('content-class', 'product-list-content')

@section('content')
<div class="container pb-4">
    <h3 class="section-title text-primary mb-4">Kết quả tìm kiếm cho: "{{ $keyword }}"</h3>
    @include('home._products', ['products' => $products])
    @if(method_exists($products, 'links'))
        <div class="mt-4 d-flex justify-content-center">{{ $products->onEachSide(1)->links('pagination::bootstrap-5') }}</div>
    @endif
</div>
@endsection
