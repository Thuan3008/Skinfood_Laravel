@php
    $shelfId = 'shelf-' . ($index ?? 0);
    $viewAllUrl = isset($section['type_ids'])
        ? route('home.category', $section['category_id'])
        : route('home.category', $section['category_id'] ?? $section['category_ids'][0]);
@endphp

<section class="home-shelf" style="--shelf-color: {{ $section['color'] }};">
    <div class="home-shelf-heading">
        <div>
            <h2><i class="fa-solid {{ $section['icon'] }}"></i> {{ $section['title'] }}</h2>
            <p>{{ $section['subtitle'] }}</p>
        </div>
        <a href="{{ $viewAllUrl }}">Xem tất cả <i class="fa-solid fa-arrow-right"></i></a>
    </div>

    <div class="home-shelf-frame">
        <button type="button" class="home-shelf-arrow home-shelf-prev" onclick="slideShelf('{{ $shelfId }}', -1)" aria-label="Xem sản phẩm trước">
            <i class="fa-solid fa-chevron-left"></i>
        </button>

        <div id="{{ $shelfId }}" class="home-shelf-track">
            @foreach($section['products'] as $product)
                @php
                    $outOfStock = (int) ($product->SoLuongTon ?? 0) <= 0;
                    $discount = (float) ($product->GiamGia ?? 0);
                    $originalPrice = (float) ($product->GiaBan ?? 0);
                    $salePrice = $originalPrice * (1 - $discount / 100);
                @endphp
                <article class="home-product-card {{ $outOfStock ? 'is-disabled' : '' }}">
                    <a href="{{ route('home.product', $product->MaSP) }}" class="home-product-link">
                        <div class="home-product-media">
                            @if($outOfStock)
                                <span class="home-stock-label">Hết hàng</span>
                            @elseif($discount > 0)
                                <span class="home-discount-label">-{{ (int) $discount }}%</span>
                            @else
                                <span class="home-new-label">Mới</span>
                            @endif
                            <img src="{{ $product->HinhAnh ? asset('legacy/Content/images/' . $product->HinhAnh) : 'https://via.placeholder.com/400x400?text=SkinFood' }}"
                                 alt="{{ $product->TenSP }}" loading="lazy">
                        </div>
                        <div class="home-product-info">
                            <span class="home-product-brand">{{ $product->thuongHieu?->TenTH ?? $product->loai?->TenLoai ?? 'SkinFood' }}</span>
                            <h3 title="{{ $product->TenSP }}">{{ $product->TenSP }}</h3>
                            <div class="home-product-price">
                                <strong>{{ number_format($salePrice, 0, ',', '.') }}đ</strong>
                                @if($discount > 0)
                                    <del>{{ number_format($originalPrice, 0, ',', '.') }}đ</del>
                                @endif
                            </div>
                        </div>
                    </a>
                    @if(!$outOfStock)
                        <form method="post" action="{{ route('cart.add', $product->MaSP) }}" class="home-product-action">
                            @csrf
                            <button type="submit"><i class="fa-solid fa-cart-plus"></i> Thêm vào giỏ</button>
                        </form>
                    @endif
                </article>
            @endforeach
        </div>

        <button type="button" class="home-shelf-arrow home-shelf-next" onclick="slideShelf('{{ $shelfId }}', 1)" aria-label="Xem sản phẩm tiếp theo">
            <i class="fa-solid fa-chevron-right"></i>
        </button>
    </div>
</section>
