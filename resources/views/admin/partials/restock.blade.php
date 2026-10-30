<section class="card mb-4 admin-restock" aria-labelledby="restock-title">
    <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div><h2 class="h5 mb-1" id="restock-title"><i class="fa-solid fa-boxes-stacked me-2" aria-hidden="true"></i>Cần nhập thêm</h2><small class="text-muted">Ưu tiên hàng hết kho · Sắp hết khi còn 1–10 sản phẩm.</small></div>
        <a href="{{ route('admin.phieu-nhap.index') }}" class="btn btn-primary"><i class="fa-solid fa-arrow-right me-1" aria-hidden="true"></i>Đi tới trang nhập hàng</a>
    </div>
    <div class="card-body py-3 d-flex flex-wrap gap-2">
        <a href="{{ route('admin.products', ['stock' => 'out_of_stock']) }}" class="btn btn-outline-secondary">Hết hàng <span class="badge bg-danger ms-2">{{ $restock['empty'] }}</span></a>
        <a href="{{ route('admin.products', ['stock' => 'low_stock']) }}" class="btn btn-outline-secondary">Sắp hết <span class="badge bg-warning ms-2">{{ $restock['low'] }}</span></a>
        @if($restock['empty'] + $restock['low'] > 6)<small class="align-self-center ms-auto text-muted">Đang hiển thị 6 sản phẩm ưu tiên.</small>@endif
    </div>
    @if($restock['products']->isEmpty())
        <p class="text-muted px-4 pb-3 mb-0">Chưa có sản phẩm hết hàng hoặc sắp hết cần nhập thêm.</p>
    @else
        <div class="table-responsive"><table class="table mb-0"><thead><tr><th>Sản phẩm</th><th class="text-center">Tồn kho</th><th>Trạng thái</th><th class="text-end">Chi tiết</th></tr></thead><tbody>
        @foreach($restock['products'] as $item)
            <tr>
                <td><a href="{{ route('admin.records.show', ['san-pham', $item->MaSP]) }}" data-admin-popup class="d-flex align-items-center gap-3"><img src="{{ $item->HinhAnh ? asset('legacy/Content/images/'.$item->HinhAnh) : asset('favicon.ico') }}" alt="" width="42" height="46" loading="lazy" style="object-fit:cover;border-radius:6px"><span><strong>{{ $item->TenSP }}</strong><small class="text-muted d-block">#{{ $item->MaSP }}</small></span></a></td>
                <td class="text-center fw-bold">{{ max(0, (int) $item->SoLuongTon) }}</td>
                <td><span class="badge {{ ($item->SoLuongTon ?? 0) <= 0 ? 'bg-danger' : 'bg-warning' }}">{{ ($item->SoLuongTon ?? 0) <= 0 ? 'Hết hàng' : 'Sắp hết hàng' }}</span></td>
                <td class="text-end"><a href="{{ route('admin.records.show', ['san-pham', $item->MaSP]) }}" class="btn btn-sm btn-outline-primary" data-admin-popup title="Xem sản phẩm" aria-label="Xem sản phẩm"><i class="fa-solid fa-eye" aria-hidden="true"></i></a></td>
            </tr>
        @endforeach
        </tbody></table></div>
    @endif
</section>
