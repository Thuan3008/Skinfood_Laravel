@extends('layouts.admin')

@section('title', 'Quản lý khuyến mãi')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Khuyến mãi</h1>
        <a href="{{ route('admin.khuyen-mai.create') }}" class="btn btn-danger">
            <i class="fa-solid fa-plus"></i> Tạo mới
        </a>
    </div>
@include('admin.partials.list-toolbar', ['resource' => 'khuyen-mai'])


    <div class="card shadow-sm">
        <div class="card-body">
            <table class="table align-middle table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>MaKM</th>
                        <th>Tên chương trình</th>
                        <th>Mã code</th>
                        <th>Giảm</th>
                        <th>Ngày bắt đầu</th>
                        <th>Ngày kết thúc</th>
                        <th>Trạng thái</th>
                        <th style="width: 120px;">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($promotions as $promo)
                        <tr>
                            <td>{{ $promo->MaKM }}</td>
                            <td>{{ $promo->TenChuongTrinh }}</td>
                            <td><code>{{ $promo->MaCode }}</code></td>
                            <td>
                                @if($promo->LoaiGiamGia === 'PhanTram')
                                    {{ $promo->GiaTriGiam }}%
                                @else
                                    {{ number_format($promo->GiaTriGiam, 0, ',', '.') }} đ
                                @endif
                            </td>
                            <td>{{ $promo->NgayBatDau ? \Carbon\Carbon::parse($promo->NgayBatDau)->format('d/m/Y') : '-' }}</td>
                            <td>{{ $promo->NgayKetThuc ? \Carbon\Carbon::parse($promo->NgayKetThuc)->format('d/m/Y') : '-' }}</td>
                            <td>
                                @if($promo->TrangThai)
                                    <span class="badge bg-success">Hoạt động</span>
                                @else
                                    <span class="badge bg-secondary">Tắt</span>
                                @endif
                            </td>
                            <td class="text-end">@include('admin.records.actions', ['resource' => 'khuyen-mai', 'id' => $promo->MaKM])</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">Chưa có khuyến mãi nào.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if(isset($promotions) && method_exists($promotions, 'links'))
        @include('admin.partials.list-pagination', ['paginator' => $promotions])
    @endif
</div>
@endsection
