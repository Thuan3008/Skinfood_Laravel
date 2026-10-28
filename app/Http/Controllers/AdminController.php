<?php

namespace App\Http\Controllers;

use App\Models\DanhGia;
use App\Models\DanhMuc;
use App\Models\DonHang;
use App\Models\LoaiSP;
use App\Models\SanPham;
use App\Models\ThuongHieu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    public function dashboard() {
        $productCount = SanPham::count();
        return view('admin.dashboard', [
            'stats' => [
                'products' => $productCount,
                'orders' => DonHang::count(),
                'reviews' => DanhGia::where('DuocApprove', false)->count(),
                'revenue' => DonHang::where('TrangThaiThanhToan', 'Đã thanh toán')->sum('TongTien'),
                'categories' => DanhMuc::count(),
                'types' => LoaiSP::count(),
                'brands' => ThuongHieu::count(),
                'low_stock' => SanPham::where('SoLuongTon', '<=', 10)->count(),
            ],
            'recentProducts' => SanPham::with(['danhMuc', 'loai'])->latest('MaSP')->take(8)->get(),
            'categoryStats' => DanhMuc::withCount('sanPhams')->orderByDesc('san_phams_count')->get()
                ->map(function ($category) use ($productCount) {
                    $category->percentage = $productCount > 0 ? round($category->san_phams_count * 100 / $productCount) : 0;
                    return $category;
                }),
        ]);
    }

    public function products(Request $request) {
        $q = trim((string) $request->get('q', ''));
        $query = SanPham::with(['danhMuc', 'thuongHieu', 'loai']);
        if ($q !== '') {
            $query->where(function ($sub) use ($q) {
                $sub->where('TenSP', 'like', "%{$q}%")
                    ->orWhere('MaSP', $q);
            });
        }
        return view('admin.products.index', [
            'products' => $query->latest('MaSP')->paginate(10)->withQueryString(),
            'q' => $q,
        ]);
    }

    public function createProduct() {
        return view('admin.products.form', [
            'product' => new SanPham(),
            'categories' => DanhMuc::orderBy('TenDM')->get(),
            'brands' => ThuongHieu::orderBy('TenTH')->get(),
            'types' => LoaiSP::orderBy('TenLoai')->get(),
        ]);
    }

    public function storeProduct(Request $request) {
        $data = $request->validate([
            'TenSP' => 'required|string|max:200',
            'GiaBan' => 'required|numeric|min:0',
            'imageFile' => 'nullable|image|mimes:jpg,jpeg,png,webp,gif|max:5120',
            'MaDM' => 'nullable|integer|exists:DanhMuc,MaDM',
            'MaTH' => 'nullable|integer|exists:ThuongHieu,MaTH',
            'MaLoai' => 'nullable|integer|exists:LoaiSP,MaLoai',
            'MoTa' => 'nullable|string',
            'GiamGia' => 'nullable|numeric|min:0|max:99',
            'TrangThaiSanPham' => 'nullable|string|max:50',
        ]);
        unset($data['imageFile']);
        if ($request->hasFile('imageFile')) $data['HinhAnh'] = $this->storeProductImage($request);
        $data['SoLuongTon'] = 0;
        $data['GiamGia'] = $data['GiamGia'] ?? 0;
        SanPham::create($data);
        return redirect()->route('admin.products')->with('success', 'Đã thêm sản phẩm mới.');
    }

    public function editProduct(int $id) {
        return view('admin.products.form', [
            'product' => SanPham::findOrFail($id),
            'categories' => DanhMuc::orderBy('TenDM')->get(),
            'brands' => ThuongHieu::orderBy('TenTH')->get(),
            'types' => LoaiSP::orderBy('TenLoai')->get(),
        ]);
    }

    public function updateProduct(Request $request, int $id) {
        $product = SanPham::findOrFail($id);
        $data = $request->validate([
            'TenSP' => 'required|string|max:200',
            'GiaBan' => 'required|numeric|min:0',
            'imageFile' => 'nullable|image|mimes:jpg,jpeg,png,webp,gif|max:5120',
            'MaDM' => 'nullable|integer|exists:DanhMuc,MaDM',
            'MaTH' => 'nullable|integer|exists:ThuongHieu,MaTH',
            'MaLoai' => 'nullable|integer|exists:LoaiSP,MaLoai',
            'MoTa' => 'nullable|string',
            'GiamGia' => 'nullable|numeric|min:0|max:99',
            'TrangThaiSanPham' => 'nullable|string|max:50',
        ]);
        unset($data['imageFile']);
        if ($request->hasFile('imageFile')) {
            $oldImage = $product->HinhAnh;
            $data['HinhAnh'] = $this->storeProductImage($request);
            if ($oldImage && str_starts_with($oldImage, 'products/')) File::delete(public_path('legacy/Content/images/' . $oldImage));
        }
        $data['SoLuongTon'] = $product->SoLuongTon;
        $data['GiamGia'] = $data['GiamGia'] ?? 0;
        $product->update($data);
        return redirect()->route('admin.products')->with('success', 'Đã cập nhật sản phẩm.');
    }

    public function deleteProduct(int $id) {
        $product = SanPham::findOrFail($id);
        if (DB::table('ChiTietDonHangs')->where('MaSP', $id)->exists()) {
            return back()->with('error', 'Không thể xóa sản phẩm đã có trong đơn hàng.');
        }
        try {
            $product->delete();
            return back()->with('success', 'Đã xóa sản phẩm.');
        } catch (\Throwable $e) {
            return back()->with('error', 'Không thể xóa sản phẩm vì đã có dữ liệu liên quan (phiếu nhập, phiếu xuất hoặc đánh giá).');
        }
    }

    public function orders(Request $request) {
        $q = trim((string) $request->get('q', ''));
        $status = trim((string) $request->get('status', ''));
        $query = DonHang::with('nguoiDung');
        if ($q !== '') {
            $query->where(function ($sub) use ($q) {
                $sub->where('MaDH', $q)
                    ->orWhere('TenNguoiNhan', 'like', "%{$q}%")
                    ->orWhere('SoDienThoai', 'like', "%{$q}%");
            });
        }
        if ($status !== '') {
            $query->where('TrangThaiThanhToan', $status);
        }
        return view('admin.orders.index', [
            'orders' => $query->latest('NgayDat')->paginate(10)->withQueryString(),
            'q' => $q,
            'currentStatus' => $status,
        ]);
    }

    public function updateOrder(Request $request, int $id) {
        $data = $request->validate(['status' => 'required|string|max:50']);
        $order = DonHang::findOrFail($id);
        $order->update([
            'TrangThaiThanhToan' => $data['status'],
            'NgayThanhToan' => $data['status'] === 'Đã thanh toán' ? now() : $order->NgayThanhToan,
        ]);
        return back()->with('success', 'Đã cập nhật trạng thái đơn hàng.');
    }

    public function reviews() {
        return view('admin.reviews.index', [
            'reviews' => DanhGia::with(['sanPham', 'nguoiDung'])->latest('MaDG')->paginate(10)
        ]);
    }

    public function approveReview(int $id) {
        DanhGia::findOrFail($id)->update(['DuocApprove' => true]);
        return back()->with('success', 'Đã duyệt đánh giá.');
    }

    public function deleteReview(int $id) {
        DanhGia::findOrFail($id)->delete();
        return back()->with('success', 'Đã xóa đánh giá.');
    }

    private function storeProductImage(Request $request): string
    {
        $file = $request->file('imageFile');
        $directory = public_path('legacy/Content/images/products');
        File::ensureDirectoryExists($directory);
        $name = Str::uuid() . '.' . $file->extension();
        $file->move($directory, $name);
        return 'products/' . $name;
    }
}
