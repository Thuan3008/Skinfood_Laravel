@extends('layouts.admin')

@section('title', 'Duyệt đánh giá')

@section('content')
    <div class="container py-4">
        <h1 class="mb-4">Đánh giá sản phẩm</h1>
@include('admin.partials.list-toolbar', ['resource' => 'danh-gia'])


        <div class="card mt-4"><div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead>
                <tr>
                    <th>Sản phẩm</th>
                    <th>Người dùng</th>
                    <th>Nội dung</th>
                    <th>Điểm</th>
                    <th>Trạng thái</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($reviews as $review)
                    <tr>
                        <td>{{ $review->sanPham?->TenSP ?? '—' }}</td>
                        <td>{{ $review->nguoiDung?->HoTen ?? '—' }}</td>
                        <td>{{ $review->NoiDung }}</td>
                        <td>{{ $review->Diem }}/5</td>
                        <td>{{ $review->DuocApprove ? 'Đã duyệt' : 'Chờ duyệt' }}</td>
                        <td class="text-end">@include('admin.records.actions', ['resource' => 'danh-gia', 'id' => $review->MaDG])</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        </div></div>

        @include('admin.partials.list-pagination', ['paginator' => $reviews])
    </div>
@endsection
