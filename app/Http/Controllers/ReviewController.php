<?php

namespace App\Http\Controllers;

use App\Models\DanhGia;
use App\Models\SanPham;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request, int $product)
    {
        $item = SanPham::findOrFail($product);
        $approved = $item->wasPurchasedBy($request->user()->MaND);
        $request->merge([
            'rating' => $request->input('Diem', $request->input('rating')),
            'content' => $request->input('NoiDung', $request->input('content')),
        ]);
        $data = $request->validate(['rating' => ['required', 'integer', 'between:1,5'], 'content' => ['required', 'string', 'max:500']]);
        DanhGia::create(['MaSP' => $product, 'MaND' => $request->user()->MaND, 'NoiDung' => $data['content'], 'Diem' => $data['rating'], 'NgayDanhGia' => now(), 'DuocApprove' => $approved]);
        return redirect()->to(route('home.product', $product).'#danhgia')->with('review_submitted', true)->with('review_pending', !$approved);
    }
}
