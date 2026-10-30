@extends('layouts.admin')
@section('title', 'Quản lý thương hiệu')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Quản lý thương hiệu</h1>
        <a href="{{ route('admin.thuong-hieu.create') }}" class="btn btn-primary">
            <i class="fa-solid fa-plus"></i> Thêm mới
        </a>
    </div>
@include('admin.partials.list-toolbar', ['resource' => 'thuong-hieu'])


    <table class="table table-bordered table-hover bg-white">
        <thead class="table-light">
            <tr>
                <th>MaTH</th>
                <th>Tên thương hiệu</th>
                <th>Quốc gia</th>
                <th width="150">Hành động</th>
            </tr>
        </thead>
        <tbody>
            @forelse($brands as $brand)
            <tr>
                <td>{{ $brand->MaTH }}</td>
                <td>{{ $brand->TenTH }}</td>
                <td>{{ $brand->QuocGia ?: '—' }}</td>
                <td class="text-end">@include('admin.records.actions', ['resource' => 'thuong-hieu', 'id' => $brand->MaTH])</td>
            </tr>
            @empty
            <tr><td colspan="4" class="text-center text-muted">Chưa có thương hiệu nào</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="mt-3">@include('admin.partials.list-pagination', ['paginator' => $brands])</div>
</div>
@endsection
