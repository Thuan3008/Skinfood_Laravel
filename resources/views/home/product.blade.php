@extends('layouts.app')
@section('title', $product->TenSP)
@section('styles')
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
@endsection
@section('content')
@php
$hasDiscount = (float)($product->GiamGia ?? 0) > 0;
$finalPrice = $hasDiscount ? $product->GiaBan * (1 - $product->GiamGia / 100) : $product->GiaBan;
$canBuy = ($product->SoLuongTon ?? 0) > 0;
$canReview = $product->wasPurchasedBy(auth()->user()?->MaND);
$reviewTabActive = session('review_submitted') || $errors->hasAny(['rating', 'content']);
@endphp
<div class="container my-5 product-detail-page">
    {{-- PHẦN 1: THÔNG TIN SẢN PHẨM --}}
    <div class="card shadow-sm border-0 mb-5">
        <div class="card-body p-4">
            <div class="row">
                {{-- Cột Ảnh --}}
                <div class="col-md-5 text-center">
                    <div class="p-3 border rounded bg-light d-flex align-items-center justify-content-center" style="min-height:400px">
                        <img src="{{ $product->HinhAnh ? asset('legacy/Content/images/' . $product->HinhAnh) : 'https://via.placeholder.com/700x500?text=SkinFood' }}" alt="{{ $product->TenSP }}" class="img-fluid" style="max-height:400px;object-fit:contain">
                    </div>
                </div>
                {{-- Cột Thông Tin --}}
                <div class="col-md-7 mt-4 mt-md-0 product-info">
                    <nav aria-label="breadcrumb" class="mb-3">
                        <ol class="breadcrumb bg-transparent p-0">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-muted">Trang chủ</a></li>
                            <li class="breadcrumb-item text-muted">{{ $product->danhMuc?->TenDM ?? 'Sản phẩm' }}</li>
                            <li class="breadcrumb-item active" aria-current="page">{{ $product->TenSP }}</li>
                        </ol>
                    </nav>
                    <h2 class="font-weight-bold text-dark mb-3">{{ $product->TenSP }}</h2>
                    @if($product->MoTa)
                        <p class="text-muted mb-3" style="font-size:1rem">{{ Str::limit(strip_tags($product->MoTa), 240) }}</p>
                    @endif
                    <div class="mb-3">
                        @if($product->thuongHieu)
                            <span class="badge bg-info text-white me-2 p-2">{{ $product->thuongHieu->TenTH }}</span>
                        @endif
                        @if($product->loai)
                            <span class="badge bg-secondary text-white p-2">{{ $product->loai->TenLoai }}</span>
                        @endif
                    </div>
                    {{-- Giá bán --}}
                    <div class="mb-4">
                        @if($hasDiscount)
                            <div class="d-flex align-items-center">
                                <h3 class="text-danger font-weight-bold mb-0 me-3">{{ number_format($finalPrice, 0, ',', '.') }} đ</h3>
                                <span class="text-muted text-decoration-line-through me-2">{{ number_format($product->GiaBan, 0, ',', '.') }} đ</span>
                                <span class="badge bg-danger">-{{ $product->GiamGia }}%</span>
                            </div>
                        @else
                            <h3 class="text-primary font-weight-bold">{{ number_format($product->GiaBan, 0, ',', '.') }} đ</h3>
                        @endif
                    </div>
                    {{-- Tình trạng --}}
                    <div class="mb-4">
                        <p class="mb-1">Tình trạng:</p>
                        @if($canBuy)
                            <span class="text-success font-weight-bold"><i class="bi bi-check-circle-fill"></i> Còn hàng ({{ $product->SoLuongTon }} sản phẩm)</span>
                        @else
                            <span class="text-danger font-weight-bold"><i class="bi bi-x-circle-fill"></i> Hết hàng</span>
                        @endif
                    </div>
                    <hr>
                    {{-- Nút mua hàng --}}
                    <div class="d-flex gap-2">
                        @if($canBuy)
                            <form method="post" action="{{ route('cart.add', $product->MaSP) }}">
                                @csrf
                                <button type="submit" class="btn btn-primary btn-lg shadow-sm px-5">
                                    <i class="bi bi-cart-plus me-2"></i> THÊM VÀO GIỎ
                                </button>
                            </form>
                        @else
                            <button class="btn btn-secondary btn-lg px-5" disabled>
                                <i class="bi bi-slash-circle me-2"></i> TẠM HẾT HÀNG
                            </button>
                        @endif
                        <a href="{{ route('home') }}" class="btn btn-outline-secondary btn-lg" title="Quay lại">
                            <i class="bi bi-arrow-return-left"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- PHẦN 2: TABS MÔ TẢ & ĐÁNH GIÁ --}}
    <div class="card shadow-sm border-0 mb-5">
        <div class="card-header bg-white border-bottom-0">
            <ul class="nav nav-tabs" id="productTab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link {{ $reviewTabActive ? '' : 'active' }} font-weight-bold" id="mota-tab" data-bs-toggle="tab" href="#mota" role="tab">Mô Tả Sản Phẩm</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $reviewTabActive ? 'active' : '' }} font-weight-bold" id="danhgia-tab" data-bs-toggle="tab" href="#danhgia" role="tab">
                        Đánh Giá ({{ $product->danhGias->where('DuocApprove', true)->count() }})
                    </a>
                </li>
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content" id="myTabContent">
                {{-- Tab Mô tả --}}
                <div class="tab-pane fade {{ $reviewTabActive ? '' : 'show active' }} py-3 px-2" id="mota" role="tabpanel">
                    <div class="product-description">@if($product->MoTa){!! $product->MoTa !!}@else<em>Chưa có mô tả sản phẩm.</em>@endif</div>
                </div>
                {{-- Tab Đánh giá --}}
                <div class="tab-pane fade {{ $reviewTabActive ? 'show active' : '' }} py-3 px-2" id="danhgia" role="tabpanel">

                    <div class="mb-4">
                        @forelse($product->danhGias->where('DuocApprove', true) as $review)
                            <div class="border-bottom py-3">
                                <strong>{{ $review->nguoiDung?->HoTen ?? 'Khách hàng' }}</strong>
                                <span class="text-warning">{{ str_repeat('★', $review->Diem) }}{{ str_repeat('☆', 5 - $review->Diem) }}</span>
                                <p class="mb-0 mt-1 text-muted">{{ $review->NoiDung }}</p>
                            </div>
                        @empty
                            <p class="text-muted">Chưa có đánh giá nào cho sản phẩm này.</p>
                        @endforelse
                    </div>
                    <hr class="my-4">
                    @auth
                        @if(!$canReview)<p class="text-muted">Bạn chưa mua sản phẩm này. Đánh giá sẽ hiển thị sau khi được quản trị viên duyệt.</p>@endif
                        <div class="bg-light p-4 rounded border review-form-wrap">
                            <h5 class="font-weight-bold mb-3">Viết đánh giá của bạn</h5>
                            <form method="post" action="{{ route('reviews.store', $product->MaSP) }}">
                                @csrf
                                <div class="mb-3 d-flex align-items-center">
                                    <div class="star-rating me-3">
                                        @for($i = 5; $i >= 1; $i--)
                                            <input type="radio" id="star{{ $i }}" name="Diem" value="{{ $i }}" @checked((int) old('Diem', 5) === $i) required>
                                            <label for="star{{ $i }}" title="{{ $i }} sao"><span aria-hidden="true">★</span><span class="visually-hidden">{{ $i }} sao</span></label>
                                        @endfor
                                    </div>
                                    <span class="fw-bold mb-0">Mức độ hài lòng:</span>
                                </div>
                                <div class="mb-3 review-content-field">
                                    <label class="fw-bold mb-2" for="reviewContent">Nội dung đánh giá:</label>
                                    <textarea id="reviewContent" name="NoiDung" class="form-control" rows="4" maxlength="500" placeholder="Chia sẻ cảm nhận về sản phẩm..." required>{{ old('NoiDung') }}</textarea>
                                </div>
                                <button class="btn btn-primary px-4">Gửi đánh giá</button>
                            </form>
                        </div>
                    @else
                        <div class="alert alert-info text-center">
                            Vui lòng <a href="{{ route('login') }}" class="alert-link fw-bold">Đăng nhập</a> để viết đánh giá.
                        </div>
                    @endauth
                </div>
            </div>
        </div>
    </div>
    {{-- PHẦN 3: SẢN PHẨM LIÊN QUAN --}}
    @if(isset($relatedProducts) && $relatedProducts->count() > 0)
        <div class="related-products mt-5">
            <h4 class="font-weight-bold mb-4 border-bottom pb-2">Sản Phẩm Liên Quan</h4>
            <div class="row">
                @foreach($relatedProducts as $sp)
                    @php $hetHang = (int)($sp->SoLuongTon ?? 0) <= 0; @endphp
                    <div class="col-6 col-md-3 mb-4">
                        <div class="card h-100 shadow-sm border-0">
                            <a href="{{ route('home.product', $sp->MaSP) }}" class="text-decoration-none text-dark">
                                <div class="position-relative">
                                    @if($hetHang)
                                        <div class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center" style="background:rgba(0,0,0,0.5);color:white;font-weight:bold;z-index:2">HẾT HÀNG</div>
                                    @endif
                                    @if((float)($sp->GiamGia ?? 0) > 0)
                                        <span class="position-absolute top-0 start-0 badge bg-danger m-2" style="z-index:1">-{{ $sp->GiamGia }}%</span>
                                    @endif
                                    <img src="{{ $sp->HinhAnh ? asset('legacy/Content/images/' . $sp->HinhAnh) : 'https://via.placeholder.com/200' }}" class="card-img-top p-3" alt="{{ $sp->TenSP }}" style="height:200px;object-fit:contain">
                                </div>
                                <div class="card-body text-start d-flex flex-column related-product-info">
                                    <h6 class="card-title text-truncate fw-bold" title="{{ $sp->TenSP }}">{{ $sp->TenSP }}</h6>
                                    <div class="mt-auto">
                                        @if((float)($sp->GiamGia ?? 0) > 0)
                                            <div class="text-danger fw-bold">{{ number_format($sp->GiaBan * (1 - $sp->GiamGia / 100), 0, ',', '.') }} đ</div>
                                            <div class="text-muted small text-decoration-line-through">{{ number_format($sp->GiaBan, 0, ',', '.') }} đ</div>
                                        @else
                                            <div class="text-primary fw-bold">{{ number_format($sp->GiaBan, 0, ',', '.') }} đ</div>
                                        @endif
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
<style>
.star-rating{display:inline-flex;flex-direction:row-reverse}.star-rating input{position:absolute;opacity:0;width:1px;height:1px}.star-rating label{cursor:pointer;font-size:1.5rem;color:#ccc;transition:color .2s}.star-rating label:hover,.star-rating label:hover ~ label,.star-rating input:checked ~ label{color:#ffc107}.star-rating input:focus-visible + label{outline:2px solid #0d6efd}.product-description img{max-width:100%;height:auto}.card-title{font-size:.95rem}

.product-detail-page .product-info,
.product-detail-page .product-info h2,
.product-detail-page .product-info p,
.product-detail-page .product-info .breadcrumb,
.product-detail-page .product-description,
.product-detail-page #danhgia,
.product-detail-page .related-products,
.product-detail-page .related-product-info {
    text-align: left !important;
}

.product-detail-page .product-description {
    width: 100%;
    line-height: 1.7;
}

.product-detail-page .review-form-wrap,
.product-detail-page .review-form-wrap h5,
.product-detail-page .review-form-wrap form,
.product-detail-page .review-form-wrap label {
    text-align: left !important;
}

.product-detail-page .review-content-field,
.product-detail-page .review-content-field label {
    display: block;
    width: 100%;
}

.product-detail-page .review-content-field textarea {
    display: block;
    width: 100% !important;
    max-width: none !important;
    min-height: 120px;
    resize: vertical;
    text-align: left;
}

.product-detail-page .related-product-info .card-title {
    width: 100%;
    text-align: left !important;
}
</style>
@endsection
