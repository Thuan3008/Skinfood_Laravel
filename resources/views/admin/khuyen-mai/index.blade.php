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

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

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
                            <td>
                                <a href="{{ route('admin.khuyen-mai.edit', $promo->MaKM) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fa-solid fa-pen"></i>
                                </a>
                                <form class="d-inline" method="post" action="{{ route('admin.khuyen-mai.destroy', $promo->MaKM) }}" onsubmit="return confirm('Bạn có chắc muốn xóa khuyến mãi này?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </td>
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
        {{ $promotions->links() }}
    @endif
</div>
@endsection