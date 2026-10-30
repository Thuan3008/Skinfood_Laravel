@extends('layouts.admin')
@section('title', 'Sửa '.$title)
@section('content')
<h1 class="h4 mb-4">Sửa {{ $title }} #{{ $record->getKey() }}</h1>
@php
    $locked = $resource === 'don-hang' && in_array($record->TrangThaiThanhToan, ['Đã thanh toán', 'Đã hủy'], true);
@endphp
@if($resource === 'don-hang')
    @include('admin.orders.details-content')
    <h2 class="h5 mb-3">Cập nhật trạng thái đơn hàng</h2>
@endif
@if($locked)
    <p class="text-secondary">Đơn hàng {{ $record->TrangThaiThanhToan }}. Trạng thái đã được khóa.</p>
@else
<form method="post" action="{{ route('admin.records.update', [$resource, $record->getKey()]) }}">
@csrf @method('put')
@foreach($fields as $name => [$label, $rules])
    @php
        $value = old($name, $name === 'status' ? $record->TrangThaiThanhToan : ($name === 'PhanHoi' ? ($record->PhanHoi ?? '') : $record->$name));
        $options = match($name) { 'status' => ['Chưa thanh toán','Đã chuyển khoản - chờ xác nhận','Đã thanh toán','Đã hủy'], 'GioiTinh' => ['Nam','Nữ','Khác'], default => null };
        $type = str_contains($rules, '|date') ? 'date' : (str_contains($rules, '|integer') || str_contains($rules, '|boolean') ? 'number' : 'text');
        if ($value instanceof \DateTimeInterface) $value = $value->format('Y-m-d');
    @endphp
    <div class="mb-3"><label class="form-label" for="record-{{ $name }}">{{ $label }}</label>
    @if($options)
        <select class="form-select" id="record-{{ $name }}" name="{{ $name }}">@foreach($options as $option)<option @selected($value === $option)>{{ $option }}</option>@endforeach</select>
    @elseif($name === 'MaNCC')
        <select class="form-select" id="record-{{ $name }}" name="MaNCC">@foreach(\App\Models\NhaCungCap::orderBy('TenNCC')->get() as $supplier)<option value="{{ $supplier->MaNCC }}" @selected($value == $supplier->MaNCC)>{{ $supplier->TenNCC }}</option>@endforeach</select>
    @elseif(in_array($name, ['NoiDung','PhanHoi','DiaChi','LyDoXuat']))
        <textarea class="form-control" id="record-{{ $name }}" name="{{ $name }}" rows="4">{{ $value }}</textarea>
    @else
        <input class="form-control" id="record-{{ $name }}" type="{{ $type }}" name="{{ $name }}" value="{{ $value }}" @required(str_starts_with($rules, 'required')) @if($name === 'Diem') min="1" max="5" @elseif($name === 'DuocApprove') min="0" max="1" @endif>
    @endif
    </div>
@endforeach
@if(in_array($resource, ['phieu-nhap', 'phieu-xuat'], true))
    @php
        $lines = $resource === 'phieu-nhap' ? $record->chiTietPhieuNhaps()->with('sanPham')->get() : $record->chiTietPhieuXuats()->with('sanPham')->get();
    @endphp
    <h2 class="h5 mt-4">Mặt hàng trong phiếu</h2>
    <div class="table-responsive"><table class="table"><thead><tr><th>Sản phẩm</th><th>Số lượng</th><th>{{ $resource === 'phieu-nhap' ? 'Giá nhập' : 'Đơn giá' }}</th></tr></thead><tbody>
    @foreach($lines as $index => $line)
        <tr><td>{{ $line->sanPham?->TenSP ?? '#'.$line->MaSP }}<input type="hidden" name="details[{{ $index }}][MaSP]" value="{{ $line->MaSP }}"></td>
        <td><input class="form-control" type="number" name="details[{{ $index }}][SoLuong]" value="{{ $line->SoLuong }}" min="1" required aria-label="Số lượng"></td>
        <td><input class="form-control" type="number" name="details[{{ $index }}][price]" value="{{ $line->GiaNhap ?? $line->DonGia ?? 0 }}" min="0" step="0.01" required aria-label="Đơn giá"></td></tr>
    @endforeach
    </tbody></table></div>
@endif
<button class="btn btn-primary" type="submit">Lưu thay đổi</button>
</form>
@endif
@endsection
