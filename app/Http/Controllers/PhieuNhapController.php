<?php
namespace App\Http\Controllers;
use App\Models\{PhieuNhap, ChiTietPhieuNhap, NhaCungCap, SanPham};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class PhieuNhapController extends Controller
{
    public function index(){
        $phieus = \App\Support\AdminLists::paginate(PhieuNhap::visibleInAdmin()->with('nhaCungCap'), 'phieu-nhap');
        return view('admin.phieu-nhap.index', ['phieus' => $phieus, 'q' => request('q', '')]);
    }
    public function create(){ $suppliers=NhaCungCap::orderBy('TenNCC')->get(); $products=SanPham::select('MaSP','TenSP','GiaBan')->orderBy('TenSP')->get(); return view('admin.phieu-nhap.create',compact('suppliers','products')); }
    public function store(Request $request){ $data=$request->validate(['MaNCC'=>'required|integer|exists:NhaCungCap,MaNCC','NgayNhap'=>'required|date','details'=>'required|array|min:1','details.*.MaSP'=>'required|integer|distinct|exists:SanPham,MaSP','details.*.SoLuong'=>'required|integer|min:1','details.*.GiaNhap'=>'required|numeric|min:0']); try{ DB::transaction(function()use($data,$request){$p=PhieuNhap::create(['MaNCC'=>$data['MaNCC'],'MaND'=>$request->user()->MaND,'NgayNhap'=>$data['NgayNhap']]);$total=0;foreach($data['details'] as $i){$sp=SanPham::where('MaSP',$i['MaSP'])->lockForUpdate()->firstOrFail();$line=$i['SoLuong']*$i['GiaNhap'];ChiTietPhieuNhap::create(['MaPN'=>$p->MaPN,'MaSP'=>$i['MaSP'],'SoLuong'=>$i['SoLuong'],'GiaNhap'=>$i['GiaNhap'],'ThanhTien'=>$line]);$sp->increment('SoLuongTon',$i['SoLuong']);$total+=$line;}$p->update(['TongTien'=>$total]);});return redirect()->route('admin.phieu-nhap.index')->with('success','Nhập hàng thành công!');}catch(\Throwable $e){return back()->with('error','Lỗi: '.$e->getMessage())->withInput();} }
    public function show(int $id){return $this->details($id);}
    public function details(int $id){$phieu=PhieuNhap::with(['nhaCungCap','chiTietPhieuNhaps.sanPham'])->findOrFail($id);return view('admin.phieu-nhap.details',compact('phieu'));}
    public function destroy(int $id){
        return app(AdminRecordController::class)->hide(request(), 'phieu-nhap', $id);
    }
}
