<?php
namespace App\Http\Controllers;
use App\Models\ThuongHieu;
use Illuminate\Http\Request;
class ThuongHieuController extends Controller
{
    public function index(){ $brands=ThuongHieu::orderByDesc('MaTH')->paginate(10); return view('admin.thuong-hieu.index',compact('brands')); }
    public function create(){return view('admin.thuong-hieu.create');}
    public function store(Request $request){$data=$request->validate(['TenTH'=>'required|string|max:100','QuocGia'=>'nullable|string|max:100']);ThuongHieu::create($data);return redirect()->route('admin.thuong-hieu.index')->with('success','Thêm thương hiệu thành công!');}
    public function show(int $id){return redirect()->route('admin.thuong-hieu.index');}
    public function edit(int $id){$brand=ThuongHieu::findOrFail($id);return view('admin.thuong-hieu.edit',compact('brand'));}
    public function update(Request $request,int $id){$data=$request->validate(['TenTH'=>'required|string|max:100','QuocGia'=>'nullable|string|max:100']);ThuongHieu::findOrFail($id)->update($data);return redirect()->route('admin.thuong-hieu.index')->with('success','Cập nhật thương hiệu thành công!');}
    public function destroy(int $id){try{ThuongHieu::destroy($id);return redirect()->route('admin.thuong-hieu.index')->with('success','Xóa thương hiệu thành công!');}catch(\Exception $e){return back()->with('error','Lỗi khi xóa: '.$e->getMessage());}}
}
