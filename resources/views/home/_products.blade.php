<div class="row g-3 w-100">
@forelse($products as $product)
    @php
        $outOfStock = ((int)($product->SoLuongTon ?? 0) <= 0);
        $giamGia = (float)($product->GiamGia ?? 0);
        $giaGoc = (float)($product->GiaBan ?? 0);
        $giaBan = $giamGia > 0 ? $giaGoc * (1 - $giamGia / 100) : $giaGoc;
    @endphp
    <div class="col-6 col-md-4 col-lg-3">
        <div class="product-card-grid h-100 {{ $outOfStock ? 'card-disabled' : '' }}">
            <a href="{{ route('home.product', $product->MaSP) }}" class="text-decoration-none text-dark d-block">
                <div class="position-relative">
                    @if($outOfStock)
                        <div class="overlay-hethang">HẾT HÀNG</div>
                    @endif
                    @if($giamGia > 0)
                        <span class="badge-sale">-{{ (int)$giamGia }}%</span>
                    @endif
                    <img src="{{ $product->HinhAnh ? asset('legacy/Content/images/' . $product->HinhAnh) : 'https://via.placeholder.com/400x300?text=SkinFood' }}" class="product-img" alt="{{ $product->TenSP }}">
                </div>
                <div class="p-3">
                    <h6 class="fw-bold text-truncate" title="{{ $product->TenSP }}">{{ $product->TenSP }}</h6>
                    @if($giamGia > 0)
                        <span class="text-muted text-decoration-line-through small">{{ number_format($giaGoc, 0, ',', '.') }} đ</span><br>
                    @else
                        <br>
                    @endif
                    <span class="fw-bold text-danger fs-5">{{ number_format($giaBan, 0, ',', '.') }} đ</span>
                </div>
            </a>
            @if(!$outOfStock)
                <form method="post" action="{{ route('cart.add', $product->MaSP) }}" class="px-3 pb-3">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger w-100 btn-sm">
                        <i class="fas fa-cart-plus me-1"></i> Thêm vào giỏ
                    </button>
                </form>
            @endif
        </div>
    </div>
@empty
    <div class="col-12 py-4">
        <div class="alert alert-warning text-center">
            Hiện chưa có sản phẩm nào.
        </div>
    </div>
@endforelse
</div>
