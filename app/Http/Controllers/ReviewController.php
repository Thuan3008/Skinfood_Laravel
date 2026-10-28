<?php

namespace App\Http\Controllers;

use App\Models\DanhGia;
use App\Models\SanPham;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request, int $product)
    {
        $request->merge([
            'rating' => $request->input('Diem', $request->input('rating')),
            'content' => $request->input('NoiDung', $request->input('content')),
        ]);
        $data = $request->validate(['rating' => ['required', 'integer', 'between:1,5'], 'content' => ['required', 'string', 'max:500']]);
        SanPham::findOrFail($product);
        DanhGia::create(['MaSP' => $product, 'MaND' => $request->user()->MaND, 'NoiDung' => $data['content'], 'Diem' => $data['rating'], 'NgayDanhGia' => now(), 'DuocApprove' => false]);
        return back()->with('success', 'Đánh giá đã gửi và đang chờ duyệt.');
    }
}
