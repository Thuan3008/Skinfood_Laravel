<?php
namespace App\Http\Controllers;
use App\Models\{PhieuXuat, ChiTietPhieuXuat, SanPham};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class PhieuXuatController extends Controller
{
    public function index(){ $phieus=PhieuXuat::orderByDesc('NgayXuat')->paginate(10); return view('admin.phieu-xuat.index',compact('phieus')); }
    public function create(){ $products=SanPham::select('MaSP','TenSP','GiaBan','SoLuongTon')->orderBy('TenSP')->get(); return view('admin.phieu-xuat.create',compact('products')); }
    public function store(Request $request){$data=$request->validate(['NgayXuat'=>'required|date','LyDoXuat'=>'nullable|string|max:255','details'=>'required|array|min:1','details.*.MaSP'=>'required|integer|distinct|exists:SanPham,MaSP','details.*.SoLuong'=>'required|integer|min:1']);try{DB::transaction(function()use($data,$request){$p=PhieuXuat::create(['MaND'=>$request->user()->MaND,'NgayXuat'=>$data['NgayXuat'],'LyDoXuat'=>$data['LyDoXuat']??null]);$total=0;foreach($data['details'] as $i){$sp=SanPham::where('MaSP',$i['MaSP'])->lockForUpdate()->firstOrFail();if($sp->SoLuongTon<$i['SoLuong'])throw new \RuntimeException("Sản phẩm {$sp->TenSP} không đủ hàng.");$line=$i['SoLuong']*$sp->GiaBan;ChiTietPhieuXuat::create(['MaPX'=>$p->MaPX,'MaSP'=>$i['MaSP'],'SoLuong'=>$i['SoLuong'],'DonGia'=>$sp->GiaBan,'ThanhTien'=>$line]);$sp->decrement('SoLuongTon',$i['SoLuong']);$total+=$line;}$p->update(['TongTien'=>$total]);});return redirect()->route('admin.phieu-xuat.index')->with('success','Xuất hàng thành công!');}catch(\Throwable $e){return back()->with('error','Lỗi: '.$e->getMessage())->withInput();}}
    public function show(int $id){return $this->details($id);}
    public function details(int $id){$phieu=PhieuXuat::with('chiTietPhieuXuats.sanPham')->findOrFail($id);return view('admin.phieu-xuat.details',compact('phieu'));}
    public function destroy(int $id){try{DB::transaction(function()use($id){$p=PhieuXuat::with('chiTietPhieuXuats')->lockForUpdate()->findOrFail($id);foreach($p->chiTietPhieuXuats as $i)SanPham::where('MaSP',$i->MaSP)->lockForUpdate()->firstOrFail()->increment('SoLuongTon',$i->SoLuong);$p->delete();});return redirect()->route('admin.phieu-xuat.index')->with('success','Đã xóa phiếu và hoàn tồn kho.');}catch(\Throwable $e){return back()->with('error','Lỗi: '.$e->getMessage());}}
}
