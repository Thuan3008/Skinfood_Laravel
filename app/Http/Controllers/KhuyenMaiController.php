<?php

namespace App\Http\Controllers;

use App\Models\KhuyenMai;
use App\Models\SanPham;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KhuyenMaiController extends Controller
{
    public function index()
    {
        $promotions = KhuyenMai::with('sanPhams')->orderByDesc('NgayBatDau')->paginate(10);
        return view('admin.khuyen-mai.index', compact('promotions'));
    }

    public function create()
    {
        $products = SanPham::select('MaSP', 'TenSP')->orderBy('TenSP')->get();
        return view('admin.khuyen-mai.create', compact('products'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'TenChuongTrinh' => 'required|string|max:100', 'MaCode' => 'nullable|string|max:50',
            'LoaiGiamGia' => 'required|string|in:PhanTram,TienMat', 'GiaTriGiam' => 'required|numeric|min:0.01',
            'NgayBatDau' => 'nullable|date', 'NgayKetThuc' => 'nullable|date|after_or_equal:NgayBatDau',
            'GiamToiDa' => 'nullable|numeric|min:0', 'DonHangToiThieu' => 'nullable|numeric|min:0',
            'SoLuongPhatHanh' => 'nullable|integer|min:0', 'TrangThai' => 'nullable', 'products' => 'nullable|array',
        ]);
        if ($data['LoaiGiamGia'] === 'PhanTram' && $data['GiaTriGiam'] > 100) return back()->with('error', 'Phần trăm giảm không được lớn hơn 100.')->withInput();
        try {
            DB::transaction(function () use ($data) {
                $km = KhuyenMai::create(array_merge($data, ['TrangThai' => isset($data['TrangThai']) ? (bool)$data['TrangThai'] : true]));
                if (!empty($data['products'])) $km->sanPhams()->attach($data['products']);
            });
            return redirect()->route('admin.khuyen-mai.index')->with('success', 'Thêm khuyến mãi thành công!');
        } catch (\Exception $e) { return back()->with('error', 'Lỗi: ' . $e->getMessage())->withInput(); }
    }

    public function show(int $id) { return redirect()->route('admin.khuyen-mai.index'); }

    public function edit(int $id)
    {
        $promo = KhuyenMai::with('sanPhams')->findOrFail($id);
        $products = SanPham::select('MaSP', 'TenSP')->orderBy('TenSP')->get();
        return view('admin.khuyen-mai.edit', compact('promo', 'products'));
    }

    public function update(Request $request, int $id)
    {
        $data = $request->validate([
            'TenChuongTrinh' => 'required|string|max:100', 'MaCode' => 'nullable|string|max:50',
            'LoaiGiamGia' => 'required|string|in:PhanTram,TienMat', 'GiaTriGiam' => 'required|numeric|min:0.01',
            'NgayBatDau' => 'nullable|date', 'NgayKetThuc' => 'nullable|date|after_or_equal:NgayBatDau',
            'GiamToiDa' => 'nullable|numeric|min:0', 'DonHangToiThieu' => 'nullable|numeric|min:0',
            'SoLuongPhatHanh' => 'nullable|integer|min:0', 'TrangThai' => 'nullable', 'products' => 'nullable|array',
        ]);
        try {
            DB::transaction(function () use ($data, $id) {
                $km = KhuyenMai::findOrFail($id);
                $km->update(array_merge($data, ['TrangThai' => isset($data['TrangThai']) ? (bool)$data['TrangThai'] : true]));
                $km->sanPhams()->sync($data['products'] ?? []);
            });
            return redirect()->route('admin.khuyen-mai.index')->with('success', 'Cập nhật thành công!');
        } catch (\Exception $e) { return back()->with('error', 'Lỗi: ' . $e->getMessage())->withInput(); }
    }

    public function destroy(int $id)
    {
        try { KhuyenMai::destroy($id); return redirect()->route('admin.khuyen-mai.index')->with('success', 'Xóa thành công!'); }
        catch (\Exception $e) { return back()->with('error', 'Lỗi khi xóa: ' . $e->getMessage()); }
    }
}
