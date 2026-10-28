@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="container py-4">
    <h3 class="section-title text-primary mb-4">{{ $title }}</h3>
    @include('home._products', ['products' => $products])
    @if(method_exists($products, 'links'))
        <div class="mt-4 d-flex justify-content-center">{{ $products->links('pagination::bootstrap-5') }}</div>
    @endif
</div>
@endsection
