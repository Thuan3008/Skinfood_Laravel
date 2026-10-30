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
            'restock' => $this->restockOverview(),
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
        $categoryId = $request->integer('category');
        $typeId = $request->integer('type');
        $brandId = $request->integer('brand');
        $stock = (string) $request->get('stock', '');
        $status = trim((string) $request->get('status', ''));
        $minPrice = $request->filled('min_price') ? max(0, (float) $request->get('min_price')) : null;
        $maxPrice = $request->filled('max_price') ? max(0, (float) $request->get('max_price')) : null;
        if ($minPrice !== null && $maxPrice !== null && $minPrice > $maxPrice) {
            [$minPrice, $maxPrice] = [$maxPrice, $minPrice];
        }
        $query = SanPham::visibleInAdmin()->with(['danhMuc', 'thuongHieu', 'loai']);

        if ($q !== '') {
            $query->where(function ($sub) use ($q) {
                $sub->where('TenSP', 'like', "%{$q}%")
                    ->orWhere('MaSP', $q);
            });
        }

        $query
            ->when($categoryId > 0, fn ($builder) => $builder->where('MaDM', $categoryId))
            ->when($typeId > 0, fn ($builder) => $builder->where('MaLoai', $typeId))
            ->when($brandId > 0, fn ($builder) => $builder->where('MaTH', $brandId))
            ->when($status !== '', fn ($builder) => $builder->where('TrangThaiSanPham', $status))
            ->when($minPrice !== null, fn ($builder) => $builder->where('GiaBan', '>=', $minPrice))
            ->when($maxPrice !== null, fn ($builder) => $builder->where('GiaBan', '<=', $maxPrice));

        match ($stock) {
            'in_stock' => $query->where('SoLuongTon', '>', 0),
            'out_of_stock' => $query->where('SoLuongTon', '<=', 0),
            'low_stock' => $query->whereBetween('SoLuongTon', [1, 10]),
            default => null,
        };

        return view('admin.products.index', [
            'restock' => $this->restockOverview(),
            'products' => \App\Support\AdminLists::paginate($query, 'san-pham', false),
            'q' => $q,
            'categories' => DanhMuc::orderBy('TenDM')->get(['MaDM', 'TenDM']),
            'types' => LoaiSP::orderBy('TenLoai')->get(['MaLoai', 'MaDM', 'TenLoai']),
            'brands' => ThuongHieu::orderBy('TenTH')->get(['MaTH', 'TenTH']),
            'filters' => compact('categoryId', 'typeId', 'brandId', 'stock', 'status', 'minPrice', 'maxPrice'),
        ]);
    }

    private function restockOverview(): array
    {
        $query = SanPham::visibleInAdmin()
            ->where(fn ($builder) => $builder->whereNull('TrangThaiSanPham')->orWhereNotIn('TrangThaiSanPham', ['Tạm ngưng', 'Ngừng kinh doanh']));
        return [
            'empty' => (clone $query)->whereRaw('COALESCE(SoLuongTon, 0) <= 0')->count(),
            'low' => (clone $query)->whereBetween('SoLuongTon', [1, 10])->count(),
            'products' => $query->whereRaw('COALESCE(SoLuongTon, 0) <= 10')
                ->orderByRaw('COALESCE(SoLuongTon, 0) ASC')->orderBy('MaSP')->take(6)->get(),
        ];
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
        return app(AdminRecordController::class)->hide(request(), 'san-pham', $id);
    }

    public function orders(Request $request) {
        $orders = \App\Support\AdminLists::paginate(DonHang::visibleInAdmin()->with('nguoiDung'), 'don-hang');
        return view('admin.orders.index', compact('orders'));
    }

    public function updateOrder(Request $request, int $id) {
        $data = $request->validate(['status' => 'required|string|max:50']);
        app(\App\Services\OrderPaymentService::class)->updateStatus($id, $data['status']);
        return back()->with('success', 'Đã cập nhật trạng thái đơn hàng.');
    }

    public function reviews() {
        return view('admin.reviews.index', [
            'reviews' => \App\Support\AdminLists::paginate(DanhGia::visibleInAdmin()->with(['sanPham', 'nguoiDung']), 'danh-gia')
        ]);
    }

    public function approveReview(int $id) {
        DanhGia::findOrFail($id)->update(['DuocApprove' => true]);
        return back()->with('success', 'Đã duyệt đánh giá.');
    }

    public function deleteReview(int $id) {
        return app(AdminRecordController::class)->hide(request(), 'danh-gia', $id);
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
