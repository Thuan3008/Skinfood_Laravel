@extends('layouts.admin')

@section('title', 'Duyệt đánh giá')

@section('content')
    <div class="container py-4">
        <h1>Đánh giá sản phẩm</h1>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <table class="table align-middle">
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
                        <td>
                            @if(!$review->DuocApprove)
                                <form class="d-inline" method="post" action="{{ route('admin.reviews.approve', $review->MaDG) }}">
                                    @csrf
                                    <button class="btn btn-sm btn-success">Duyệt</button>
                                </form>
                            @endif
                            <form class="d-inline" method="post" action="{{ route('admin.reviews.delete', $review->MaDG) }}">
                                @csrf
                                @method('delete')
                                <button class="btn btn-sm btn-outline-danger">Xóa</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{ $reviews->links() }}
    </div>
@endsection
