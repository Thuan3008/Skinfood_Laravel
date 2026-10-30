<?php
namespace App\Http\Controllers;
use App\Models\LoaiSP;
use App\Models\DanhMuc;
use Illuminate\Http\Request;
class LoaiSPController extends Controller
{
    public function index(){
        $types = \App\Support\AdminLists::paginate(LoaiSP::visibleInAdmin()->with('danhMuc'), 'loai-sp');
        return view('admin.loai-sp.index', ['types' => $types, 'q' => request('q', '')]);
    }
    public function create(){ $categories=DanhMuc::orderBy('TenDM')->get();return view('admin.loai-sp.create',compact('categories')); }
    public function store(Request $request){$data=$request->validate(['TenLoai'=>'required|string|max:100','MaDM'=>'required|integer|exists:DanhMuc,MaDM']);LoaiSP::create($data);return redirect()->route('admin.loai-sp.index')->with('success','Thêm loại sản phẩm thành công!');}
    public function show(int $id){return redirect()->route('admin.loai-sp.index');}
    public function edit(int $id){$type=LoaiSP::findOrFail($id);$categories=DanhMuc::orderBy('TenDM')->get();return view('admin.loai-sp.edit',compact('type','categories'));}
    public function update(Request $request,int $id){$data=$request->validate(['TenLoai'=>'required|string|max:100','MaDM'=>'required|integer|exists:DanhMuc,MaDM']);LoaiSP::findOrFail($id)->update($data);return redirect()->route('admin.loai-sp.index')->with('success','Cập nhật thành công!');}
    public function destroy(int $id){
        return app(AdminRecordController::class)->hide(request(), 'loai-sp', $id);
    }
}
