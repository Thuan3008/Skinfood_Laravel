<?php

namespace App\Http\Controllers;

use App\Models\DanhMuc;
use App\Models\KhuyenMai;
use App\Models\LoaiSP;
use App\Models\SanPham;
use App\Models\ThuongHieu;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    private function publicProducts()
    {
        return SanPham::query()->where(fn ($query) => $query
            ->whereNull('TrangThaiSanPham')
            ->orWhere(fn ($query) => $query
                ->where('TrangThaiSanPham', 'not like', '%tạm%')
                ->where('TrangThaiSanPham', 'not like', '%ngừng%')));
    }

    private function applyStorefrontFilters($query, Request $request, bool $allowTypeFilter = true)
    {
        $typeIds = $allowTypeFilter
            ? array_values(array_filter(array_map('intval', (array) $request->query('type', []))))
            : [];
        $brandIds = array_values(array_filter(array_map('intval', (array) $request->query('brand', []))));
        $availability = (string) $request->query('availability', '');
        $sort = (string) $request->query('sort', 'newest');
        $priceRange = (string) $request->query('price_range', '');
        [$minPrice, $maxPrice] = match ($priceRange) {
            'under_100' => [0, 100000],
            '100_300' => [100000, 300000],
            '300_600' => [300000, 600000],
            'over_600' => [600000, null],
            default => [null, null],
        };

        $query
            ->when($typeIds !== [], fn ($builder) => $builder->whereIn('MaLoai', $typeIds))
            ->when($brandIds !== [], fn ($builder) => $builder->whereIn('MaTH', $brandIds))
            ->when($minPrice !== null, fn ($builder) => $builder->where('GiaBan', '>=', $minPrice))
            ->when($maxPrice !== null, fn ($builder) => $builder->where('GiaBan', '<=', $maxPrice));

        match ($availability) {
            'in_stock' => $query->where('SoLuongTon', '>', 0),
            'sale' => $query->where('GiamGia', '>', 0),
            default => null,
        };

        match ($sort) {
            'price_asc' => $query->orderBy('GiaBan'),
            'price_desc' => $query->orderByDesc('GiaBan'),
            'name_asc' => $query->orderBy('TenSP'),
            'oldest' => $query->orderBy('MaSP'),
            default => $query->latest('MaSP'),
        };

        return [
            'typeIds' => $typeIds,
            'brandIds' => $brandIds,
            'availability' => $availability,
            'sort' => $sort,
            'priceRange' => $priceRange,
        ];
    }

    private function productListView(string $title, $query, Request $request, $filterTypes, bool $allowTypeFilter = true)
    {
        $filters = $this->applyStorefrontFilters($query, $request, $allowTypeFilter);

        return view('home.products', [
            'title' => $title,
            'products' => $query->with(['thuongHieu', 'loai'])->paginate(12)->withQueryString(),
            'filterTypes' => $filterTypes,
            'filterBrands' => ThuongHieu::orderBy('TenTH')->get(['MaTH', 'TenTH']),
            'filters' => $filters,
        ]);
    }

    private function homeData(?int $maTH = null): array
    {
        $now = now();
        $categories = DanhMuc::withCount('sanPhams')
            ->with(['sanPhams' => fn ($query) => $query->latest('MaSP')])
            ->orderBy('MaDM')
            ->get();
        $activePromotionIds = KhuyenMai::where('TrangThai', true)
            ->where(fn ($query) => $query->whereNull('NgayBatDau')->orWhere('NgayBatDau', '<=', $now))
            ->where(fn ($query) => $query->whereNull('NgayKetThuc')->orWhere('NgayKetThuc', '>=', $now))
            ->with('sanPhams:MaSP')
            ->get()
            ->flatMap(fn ($promotion) => $promotion->sanPhams->pluck('MaSP'))
            ->unique();
        $brand = $maTH ? ThuongHieu::find($maTH) : null;

        $sectionDefinitions = [
            ['title' => 'Chăm sóc da chuyên sâu', 'subtitle' => 'Serum, kem dưỡng và làm sạch cho chu trình da khỏe', 'icon' => 'fa-droplet', 'color' => '#268f8b', 'category_id' => 1],
            ['title' => 'Toner & mặt nạ', 'subtitle' => 'Cân bằng, cấp ẩm và phục hồi nhanh tại nhà', 'icon' => 'fa-spa', 'color' => '#a864a8', 'type_ids' => [13, 14], 'category_id' => 1],
            ['title' => 'Trang điểm nổi bật', 'subtitle' => 'Điểm tô vẻ đẹp với những gam màu dễ dùng', 'icon' => 'fa-wand-magic-sparkles', 'color' => '#d84f78', 'category_id' => 2],
            ['title' => 'Nước hoa cuốn hút', 'subtitle' => 'Hương thơm dành cho mọi phong cách và khoảnh khắc', 'icon' => 'fa-spray-can-sparkles', 'color' => '#7357a6', 'category_id' => 4],
            ['title' => 'Chăm sóc tóc', 'subtitle' => 'Làm sạch dịu nhẹ, nuôi dưỡng mái tóc chắc khỏe', 'icon' => 'fa-scissors', 'color' => '#a8743e', 'category_id' => 3],
            ['title' => 'Cơ thể & sức khỏe', 'subtitle' => 'Chăm sóc toàn diện để đẹp khỏe mỗi ngày', 'icon' => 'fa-heart-pulse', 'color' => '#de744f', 'category_ids' => [5, 6]],
            ['title' => 'Dụng cụ làm đẹp', 'subtitle' => 'Phụ kiện tiện ích nâng cấp chu trình chăm sóc cá nhân', 'icon' => 'fa-brush', 'color' => '#347caf', 'category_id' => 7],
        ];

        $productSections = collect($sectionDefinitions)->map(function (array $section) {
            $query = $this->publicProducts()->with(['thuongHieu:MaTH,TenTH', 'loai:MaLoai,TenLoai']);
            if (isset($section['type_ids'])) {
                $query->whereIn('MaLoai', $section['type_ids']);
            } else {
                $query->whereIn('MaDM', $section['category_ids'] ?? [$section['category_id']]);
            }
            $section['products'] = $query->latest('MaSP')->take(8)->get();
            return $section;
        })->filter(fn (array $section) => $section['products']->isNotEmpty())->values();

        return [
            'navCategories' => DanhMuc::with('loaiSanPhams')->get(),
            'categories' => $categories,
            'products' => $this->publicProducts()->latest('MaSP')->take(12)->get(),
            'hotProducts' => $this->publicProducts()->orderByDesc('GiaBan')->take(10)->get(),
            'saleProducts' => $this->publicProducts()->where(fn ($query) => $query->where('GiamGia', '>', 0)->orWhereIn('MaSP', $activePromotionIds))->take(10)->get(),
            'newProducts' => $this->publicProducts()->latest('MaSP')->take(10)->get(),
            'productSections' => $productSections,
            'brands' => ThuongHieu::orderBy('TenTH')->get(),
            'brandProducts' => $maTH ? $this->publicProducts()->where('MaTH', $maTH)->get() : collect(),
            'selectedBrand' => $brand,
        ];
    }
    public function index(?int $maTH = null) { return view('home.index', $this->homeData($maTH)); }
    public function search(Request $request) { $keyword=trim((string)$request->query('keyword','')); $products=$this->publicProducts()->when($keyword!=='',fn($q)=>$q->where(fn($q)=>$q->where('TenSP','like',"%{$keyword}%")->orWhere('MoTa','like',"%{$keyword}%")))->orderBy('TenSP')->paginate(12)->withQueryString(); return view('home.search',compact('products','keyword')); }
    public function categories() { return view('home.categories',['categories'=>DanhMuc::with('loaiSanPhams')->get()]); }
    public function types() { return view('home.types',['types'=>LoaiSP::with('danhMuc')->get()]); }
    public function category(Request $request, int $id)
    {
        $category = DanhMuc::findOrFail($id);
        $types = LoaiSP::where('MaDM', $id)->orderBy('TenLoai')->get(['MaLoai', 'TenLoai']);

        return $this->productListView(
            $category->TenDM,
            $this->publicProducts()->where('MaDM', $id),
            $request,
            $types
        );
    }

    public function type(Request $request, int $id)
    {
        $type = LoaiSP::findOrFail($id);

        return $this->productListView(
            $type->TenLoai,
            $this->publicProducts()->where('MaLoai', $id),
            $request,
            collect(),
            false
        );
    }
    public function product(int $id) { $product=$this->publicProducts()->with(['danhMuc','loai','thuongHieu','danhGias.nguoiDung'])->findOrFail($id); $relatedProducts=$this->publicProducts()->where('MaDM',$product->MaDM)->where('MaSP','!=',$id)->take(4)->get(); return view('home.product',compact('product','relatedProducts')); }
    public function variant(Request $request, string $variant)
    {
        $query = $this->publicProducts();
        $title = match ($variant) {
            'hot' => 'Sản phẩm HOT',
            'sale' => 'Sản phẩm SALE',
            default => 'Tất cả sản phẩm',
        };

        if ($variant === 'sale') {
            $query->where('GiamGia', '>', 0);
        }

        return $this->productListView(
            $title,
            $query,
            $request,
            LoaiSP::orderBy('TenLoai')->get(['MaLoai', 'TenLoai'])
        );
    }
}
