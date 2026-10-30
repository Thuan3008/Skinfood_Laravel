<?php

namespace App\Http\Controllers;

use App\Models\{PhieuXuat, ChiTietPhieuXuat, SanPham};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PhieuXuatController extends Controller
{
    public function index()
    {
        $phieus = \App\Support\AdminLists::paginate(PhieuXuat::visibleInAdmin()->withSum('chiTietPhieuXuats', 'SoLuong'), 'phieu-xuat');
        return view('admin.phieu-xuat.index', ['phieus' => $phieus, 'q' => request('q', '')]);
    }
    public function create()
    {
        $products = SanPham::select('MaSP', 'TenSP', 'GiaBan', 'SoLuongTon')->orderBy('TenSP')->get();
        return view('admin.phieu-xuat.create', compact('products'));
    }
    public function store(Request $request)
    {
        $data = $request->validate([
            'NguoiNhanHang' => 'required|string|max:150', 'NgayXuat' => 'required|date',
            'SoDienThoaiNguoiNhan' => 'nullable|string|max:20', 'DiaChiNguoiNhan' => 'nullable|string|max:255',
            'GhiChu' => 'nullable|string|max:500', 'LyDoXuat' => 'nullable|string|max:255',
            'details' => 'required|array|min:1|max:100',
            'details.*.MaSP' => 'required|integer|distinct|exists:SanPham,MaSP',
            'details.*.SoLuong' => 'required|integer|min:1|max:2147483647',
            'details.*.DonGia' => 'required|numeric|min:0|max:999999999999',
        ], [
            'NguoiNhanHang.required' => 'Vui lòng nhập người nhận hàng.',
            'details.required' => 'Vui lòng thêm ít nhất một sản phẩm.',
            'details.*.MaSP.distinct' => 'Mỗi sản phẩm chỉ được chọn một lần trong phiếu xuất.',
            'details.*.SoLuong.min' => 'Số lượng xuất phải lớn hơn 0, không được bằng 0 hoặc âm.',
            'details.*.SoLuong.integer' => 'Số lượng xuất phải là số nguyên dương.',
        ]);
        DB::transaction(function () use ($data, $request) {
            $receipt = PhieuXuat::create([
                'MaND' => $request->user()->MaND, 'NgayXuat' => $data['NgayXuat'],
                'NguoiNhanHang' => $data['NguoiNhanHang'], 'GhiChu' => $data['GhiChu'] ?? null,
                'SoDienThoaiNguoiNhan' => $data['SoDienThoaiNguoiNhan'] ?? null, 'DiaChiNguoiNhan' => $data['DiaChiNguoiNhan'] ?? null,
                'LyDoXuat' => $data['LyDoXuat'] ?? null,
            ]);
            $total = 0;
            foreach (collect($data['details'])->sortBy('MaSP') as $index => $item) {
                $product = SanPham::whereKey($item['MaSP'])->lockForUpdate()->firstOrFail();
                if (($product->SoLuongTon ?? 0) < $item['SoLuong']) {
                    throw ValidationException::withMessages(["details.$index.SoLuong" => "Sản phẩm {$product->TenSP} chỉ còn ".max(0, (int) $product->SoLuongTon).' trong kho.']);
                }
                $price = round((float) $item['DonGia'], 2);
                $amount = round($price * (int) $item['SoLuong'], 2);
                if ($amount > 9999999999999999 || $total + $amount > 9999999999999999) {
                    throw ValidationException::withMessages(['details' => 'Tổng giá trị phiếu xuất vượt giới hạn cho phép.']);
                }
                ChiTietPhieuXuat::create(['MaPX' => $receipt->MaPX, 'MaSP' => $product->MaSP, 'SoLuong' => $item['SoLuong'], 'DonGia' => $price, 'ThanhTien' => $amount]);
                $product->decrement('SoLuongTon', $item['SoLuong']);
                $total += $amount;
            }
            $receipt->update(['TongTien' => $total]);
        });
        return redirect()->route('admin.phieu-xuat.index')->with('success', 'Đã lưu phiếu xuất kho.');
    }
    public function show(int $id) { return $this->details($id); }
    public function details(int $id)
    {
        $phieu = PhieuXuat::with('chiTietPhieuXuats.sanPham')->findOrFail($id);
        return view('admin.phieu-xuat.details', compact('phieu'));
    }
    public function destroy(int $id)
    {
        return app(AdminRecordController::class)->hide(request(), 'phieu-xuat', $id);
    }
}
