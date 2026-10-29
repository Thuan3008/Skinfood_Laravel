@extends('layouts.admin')

@section('title', 'Quản lý phiếu xuất')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">Phiếu xuất kho</h1>
        <a class="btn btn-primary" href="{{ route('admin.phieu-xuat.create') }}">
            <i class="fa-solid fa-plus me-1"></i> Tạo phiếu xuất
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
                        <th>Ngày xuất</th>
                        <th>Lý do xuất</th>
                        <th class="text-end">Tổng tiền</th>
                        <th class="text-end" width="160">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($phieus as $p)
                        <tr>
                            <td><strong>#{{ $p->MaPX }}</strong></td>
                            <td>{{ $p->NgayXuat ? \Carbon\Carbon::parse($p->NgayXuat)->format('d/m/Y') : '—' }}</td>
                            <td>{{ $p->LyDoXuat ?: 'Xuất bán / điều chuyển' }}</td>
                            <td class="text-end fw-bold text-danger">{{ number_format($p->TongTien ?? 0, 0, ',', '.') }} đ</td>
                            <td class="text-end">
                                <a class="btn btn-sm btn-outline-info" href="{{ route('admin.phieu-xuat.show', $p->MaPX) }}">
                                    <i class="fa-solid fa-eye me-1"></i> Chi tiết
                                </a>
                                <form class="d-inline" method="post" action="{{ route('admin.phieu-xuat.destroy', $p->MaPX) }}" onsubmit="return confirm('Xóa phiếu xuất này và hoàn tồn kho?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" title="Xóa">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center py-4 text-muted">Chưa có phiếu xuất nào</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $phieus->onEachSide(1)->links() }}
    </div>
</div>
@endsection
