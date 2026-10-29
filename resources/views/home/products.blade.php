@extends('layouts.app')

@section('title', $title)

@section('content')
@php
    $activeFilterCount = count($filters['typeIds'] ?? [])
        + count($filters['brandIds'] ?? [])
        + (($filters['priceRange'] ?? '') !== '' ? 1 : 0)
        + (($filters['availability'] ?? '') !== '' ? 1 : 0);
@endphp
<div class="container-xl py-4 storefront-catalog-page">
    <div class="d-flex align-items-center justify-content-between gap-3 mb-4">
        <div>
            <h3 class="section-title text-primary mb-0">{{ $title }}</h3>
        </div>
        <button class="btn btn-outline-primary d-lg-none" type="button" data-bs-toggle="collapse" data-bs-target="#storefrontFilters" aria-expanded="false" aria-controls="storefrontFilters">
            <i class="fa-solid fa-sliders me-1"></i> Bộ lọc
            @if($activeFilterCount > 0)<span class="badge bg-danger ms-1">{{ $activeFilterCount }}</span>@endif
        </button>
    </div>

    <div class="row g-4 align-items-start">
        <aside class="col-lg-4 col-xl-3">
            <div class="collapse d-lg-block" id="storefrontFilters">
                <form id="storefrontFilterForm" method="GET" action="{{ url()->current() }}" class="storefront-filter-sidebar">
                    <div class="storefront-filter-heading">
                        <span><i class="fa-solid fa-filter me-2"></i>Bộ lọc sản phẩm</span>
                        @if($activeFilterCount > 0)<span class="badge rounded-pill bg-primary">{{ $activeFilterCount }}</span>@endif
                    </div>

                    @if(($filterTypes ?? collect())->isNotEmpty())
                        <section class="storefront-filter-group">
                            <h6>Loại sản phẩm <i class="fa-solid fa-chevron-up"></i></h6>
                            <div class="storefront-filter-options storefront-filter-scroll">
                                @foreach($filterTypes as $type)
                                    <label class="storefront-check" for="storeType{{ $type->MaLoai }}">
                                        <input id="storeType{{ $type->MaLoai }}" type="checkbox" name="type[]" value="{{ $type->MaLoai }}" @checked(in_array($type->MaLoai, $filters['typeIds'] ?? [], true))>
                                        <span>{{ $type->TenLoai }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </section>
                    @endif

                    <section class="storefront-filter-group">
                        <h6>Khoảng giá <i class="fa-solid fa-chevron-up"></i></h6>
                        <div class="storefront-filter-options">
                            @foreach([
                                '' => 'Tất cả mức giá',
                                'under_100' => 'Dưới 100.000đ',
                                '100_300' => '100.000đ – 300.000đ',
                                '300_600' => '300.000đ – 600.000đ',
                                'over_600' => 'Trên 600.000đ',
                            ] as $value => $label)
                                <label class="storefront-check" for="storePrice{{ $loop->index }}">
                                    <input id="storePrice{{ $loop->index }}" type="radio" name="price_range" value="{{ $value }}" @checked(($filters['priceRange'] ?? '') === $value)>
                                    <span>{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                    </section>

                    <section class="storefront-filter-group">
                        <h6>Tình trạng <i class="fa-solid fa-chevron-up"></i></h6>
                        <div class="storefront-filter-options">
                            @foreach(['' => 'Tất cả sản phẩm', 'in_stock' => 'Còn hàng', 'sale' => 'Đang giảm giá'] as $value => $label)
                                <label class="storefront-check" for="storeAvailability{{ $loop->index }}">
                                    <input id="storeAvailability{{ $loop->index }}" type="radio" name="availability" value="{{ $value }}" @checked(($filters['availability'] ?? '') === $value)>
                                    <span>{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                    </section>

                    <section class="storefront-filter-group">
                        <h6>Thương hiệu <i class="fa-solid fa-chevron-up"></i></h6>
                        <div class="storefront-filter-options storefront-filter-scroll">
                            @foreach($filterBrands as $brand)
                                <label class="storefront-check" for="storeBrand{{ $brand->MaTH }}">
                                    <input id="storeBrand{{ $brand->MaTH }}" type="checkbox" name="brand[]" value="{{ $brand->MaTH }}" @checked(in_array($brand->MaTH, $filters['brandIds'] ?? [], true))>
                                    <span>{{ $brand->TenTH }}</span>
                                </label>
                            @endforeach
                        </div>
                    </section>

                    <input type="hidden" name="sort" value="{{ $filters['sort'] ?? 'newest' }}" id="storeSortHidden">
                    <div class="storefront-filter-actions">
                        <button type="submit" class="btn btn-primary w-100"><i class="fa-solid fa-check me-1"></i> Áp dụng</button>
                        <a href="{{ url()->current() }}" class="btn btn-link text-secondary w-100">Xóa tất cả bộ lọc</a>
                    </div>
                </form>
            </div>
        </aside>

        <main class="col-lg-8 col-xl-9">
            <div class="storefront-catalog-toolbar mb-3">
                <span class="text-muted">Tìm thấy <strong>{{ $products->total() }}</strong> sản phẩm</span>
                <div class="d-flex align-items-center gap-2">
                    <label for="storeSort" class="text-muted text-nowrap">Sắp xếp:</label>
                    <select id="storeSort" class="form-select form-select-sm" aria-label="Sắp xếp sản phẩm">
                        <option value="newest" @selected(($filters['sort'] ?? '') === 'newest')>Mới nhất</option>
                        <option value="oldest" @selected(($filters['sort'] ?? '') === 'oldest')>Cũ nhất</option>
                        <option value="price_asc" @selected(($filters['sort'] ?? '') === 'price_asc')>Giá thấp đến cao</option>
                        <option value="price_desc" @selected(($filters['sort'] ?? '') === 'price_desc')>Giá cao đến thấp</option>
                        <option value="name_asc" @selected(($filters['sort'] ?? '') === 'name_asc')>Tên A–Z</option>
                    </select>
                </div>
            </div>

            @include('home._products', ['products' => $products])
            @if(method_exists($products, 'links'))
                <div class="mt-4">{{ $products->onEachSide(1)->links('pagination::bootstrap-5') }}</div>
            @endif
        </main>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('storeSort')?.addEventListener('change', function () {
    document.getElementById('storeSortHidden').value = this.value;
    document.getElementById('storefrontFilterForm').submit();
});
</script>
@endpush
