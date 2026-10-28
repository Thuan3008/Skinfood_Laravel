@extends('layouts.app')

@section('title', 'Trang Chủ - SkinFood Shop')

@section('content')
<main class="home-page">
    <section class="home-intro">
        <div class="home-hero-card">
            <img src="{{ asset('legacy/Content/images/banner.jpg') }}" alt="Mỹ phẩm chính hãng SkinFood">
            <div class="home-hero-overlay">
                <span>BEAUTY ROUTINE 2026</span>
                <h1>Đẹp theo cách<br>của riêng bạn</h1>
                <p>Khám phá sản phẩm phù hợp cho từng bước chăm sóc cá nhân.</p>
                <a href="{{ route('home.all') }}">Mua sắm ngay <i class="fa-solid fa-arrow-right"></i></a>
            </div>
        </div>

        <div class="home-brand-panel">
            <div class="home-panel-title">
                <div>
                    <span>THƯƠNG HIỆU ĐƯỢC YÊU THÍCH</span>
                    <h2>Chọn nhanh thương hiệu</h2>
                </div>
                <i class="fa-solid fa-tags"></i>
            </div>
            <div class="home-brand-list">
                @foreach($brands as $brand)
                    <a href="{{ route('home.brand', $brand->MaTH) }}"
                       class="{{ $selectedBrand?->MaTH === $brand->MaTH ? 'is-active' : '' }}">
                        {{ $brand->TenTH }}
                    </a>
                @endforeach
            </div>

            <div class="home-assurance-grid">
                <div><i class="fa-solid fa-certificate"></i><span><strong>Chính hãng</strong>Cam kết nguồn gốc</span></div>
                <div><i class="fa-solid fa-truck-fast"></i><span><strong>Giao nhanh</strong>Toàn quốc 2–4 ngày</span></div>
                <div><i class="fa-solid fa-rotate-left"></i><span><strong>Đổi trả</strong>Hỗ trợ trong 7 ngày</span></div>
            </div>
        </div>
    </section>

    <section class="home-discovery">
        <div class="home-discovery-heading">
            <span>KHÁM PHÁ THEO NHU CẦU</span>
            <h2>Danh mục làm đẹp dành cho bạn</h2>
        </div>
        <div class="home-category-grid">
            @foreach($categories as $category)
                @php
                    $representative = $category->sanPhams->first();
                    $categoryIcons = [1 => 'fa-droplet', 2 => 'fa-wand-magic-sparkles', 3 => 'fa-scissors', 4 => 'fa-spray-can-sparkles', 5 => 'fa-spa', 6 => 'fa-heart-pulse', 7 => 'fa-brush'];
                @endphp
                <a href="{{ route('home.category', $category->MaDM) }}" class="home-category-card">
                    <span class="home-category-icon"><i class="fa-solid {{ $categoryIcons[$category->MaDM] ?? 'fa-star' }}"></i></span>
                    <span class="home-category-copy">
                        <strong>{{ $category->TenDM }}</strong>
                        <small>{{ $category->san_phams_count }} sản phẩm</small>
                    </span>
                    @if($representative?->HinhAnh)
                        <img src="{{ asset('legacy/Content/images/' . $representative->HinhAnh) }}" alt="{{ $category->TenDM }}" loading="lazy">
                    @endif
                    <i class="fa-solid fa-arrow-right home-category-arrow"></i>
                </a>
            @endforeach
        </div>
    </section>

    @if($selectedBrand && $brandProducts->isNotEmpty())
        <section class="home-selected-brand">
            <div class="home-shelf-heading">
                <div>
                    <h2><i class="fa-solid fa-tag"></i> {{ $selectedBrand->TenTH }}</h2>
                    <p>Các sản phẩm nổi bật thuộc thương hiệu bạn vừa chọn</p>
                </div>
                <a href="{{ route('home') }}">Bỏ bộ lọc <i class="fa-solid fa-xmark"></i></a>
            </div>
            @include('home._products', ['products' => $brandProducts->take(8)])
        </section>
    @endif

    <section class="home-feature-row">
        <div class="home-feature-card home-feature-hot">
            <div><span>TOP LỰA CHỌN</span><h2>Sản phẩm HOT</h2><p>Những món mỹ phẩm được quan tâm nhiều nhất.</p><a href="{{ route('home.hot') }}">Khám phá ngay</a></div>
            <i class="fa-solid fa-fire-flame-curved"></i>
        </div>
        <div class="home-feature-card home-feature-sale">
            <div><span>ƯU ĐÃI HÔM NAY</span><h2>Deal đẹp giá xinh</h2><p>Săn ưu đãi cho chu trình làm đẹp tiết kiệm hơn.</p><a href="{{ route('home.sale') }}">Xem sản phẩm sale</a></div>
            <i class="fa-solid fa-bolt"></i>
        </div>
    </section>

    @foreach($productSections as $index => $section)
        @include('home._product-shelf', ['section' => $section, 'index' => $index])
    @endforeach

    <section class="home-bottom-cta">
        <div>
            <span>SKINFOOD BEAUTY CLUB</span>
            <h2>Hơn 45 sản phẩm, đủ cho mọi chu trình làm đẹp</h2>
            <p>Từ chăm sóc da, tóc và cơ thể đến trang điểm, nước hoa và dụng cụ cá nhân.</p>
        </div>
        <a href="{{ route('home.all') }}">Xem toàn bộ sản phẩm <i class="fa-solid fa-arrow-right"></i></a>
    </section>
</main>
@endsection

@push('scripts')
<script>
    function slideShelf(id, direction) {
        var track = document.getElementById(id);
        if (track) {
            track.scrollBy({ left: direction * Math.min(track.clientWidth * 0.82, 900), behavior: 'smooth' });
        }
    }

    document.querySelectorAll('.home-brand-list a').forEach(function (item) {
        item.addEventListener('click', function () {
            sessionStorage.setItem('homeScrollPos', window.scrollY);
        });
    });

    window.addEventListener('load', function () {
        var pos = sessionStorage.getItem('homeScrollPos');
        if (pos) {
            window.scrollTo(0, Number(pos));
            sessionStorage.removeItem('homeScrollPos');
        }
    });
</script>
@endpush
