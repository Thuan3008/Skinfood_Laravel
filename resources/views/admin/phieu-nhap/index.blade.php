@extends('layouts.admin')

@section('title', 'Quản lý phiếu nhập')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">Phiếu nhập hàng</h1>
        <a class="btn btn-primary" href="{{ route('admin.phieu-nhap.create') }}">
            <i class="fa-solid fa-plus me-1"></i> Tạo phiếu nhập
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="100">Mã phiếu</th>
                        <th>Nhà cung cấp</th>
                        <th>Ngày nhập</th>
                        <th class="text-end">Tổng tiền</th>
                        <th class="text-end" width="160">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($phieus as $p)
                        <tr>
                            <td><strong>#{{ $p->MaPN }}</strong></td>
                            <td>{{ $p->nhaCungCap->TenNCC ?? '—' }}</td>
                            <td>{{ $p->NgayNhap ? \Carbon\Carbon::parse($p->NgayNhap)->format('d/m/Y') : '—' }}</td>
                            <td class="text-end fw-bold text-danger">{{ number_format($p->TongTien ?? 0, 0, ',', '.') }} đ</td>
                            <td class="text-end">
                                <a class="btn btn-sm btn-outline-info" href="{{ route('admin.phieu-nhap.show', $p->MaPN) }}">
                                    <i class="fa-solid fa-eye me-1"></i> Chi tiết
                                </a>
                                <form class="d-inline" method="post" action="{{ route('admin.phieu-nhap.destroy', $p->MaPN) }}" onsubmit="return confirm('Xóa phiếu nhập này và hoàn tồn kho?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" title="Xóa">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center py-4 text-muted">Chưa có phiếu nhập nào</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $phieus->links() }}
    </div>
</div>
@endsection
