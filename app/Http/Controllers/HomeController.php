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
    public function category(int $id) { $category=DanhMuc::findOrFail($id); return view('home.products',['title'=>$category->TenDM,'products'=>$this->publicProducts()->where('MaDM',$id)->paginate(12)]); }
    public function type(int $id) { $type=LoaiSP::findOrFail($id); return view('home.products',['title'=>$type->TenLoai,'products'=>$this->publicProducts()->where('MaLoai',$id)->paginate(12)]); }
    public function product(int $id) { $product=$this->publicProducts()->with(['danhMuc','loai','thuongHieu','danhGias.nguoiDung'])->findOrFail($id); $relatedProducts=$this->publicProducts()->where('MaDM',$product->MaDM)->where('MaSP','!=',$id)->take(4)->get(); return view('home.product',compact('product','relatedProducts')); }
    public function variant(string $variant) { $query=$this->publicProducts(); $title=match($variant){ 'hot'=>'Sản phẩm HOT','sale'=>'Sản phẩm SALE',default=>'Tất cả sản phẩm'}; if($variant==='hot')$query->orderByDesc('GiaBan'); elseif($variant==='sale')$query->where('GiamGia','>',0); else $query->latest('MaSP'); return view('home.products',['title'=>$title,'products'=>$query->paginate(12)]); }
}
