<?php
namespace App\Http\Controllers;
use App\Models\ThuongHieu;
use Illuminate\Http\Request;
class ThuongHieuController extends Controller
{
    public function index(){
        $brands = \App\Support\AdminLists::paginate(ThuongHieu::visibleInAdmin(), 'thuong-hieu');
        return view('admin.thuong-hieu.index', ['brands' => $brands, 'q' => request('q', '')]);
    }
    public function create(){return view('admin.thuong-hieu.create');}
    public function store(Request $request){$data=$request->validate(['TenTH'=>'required|string|max:100','QuocGia'=>'nullable|string|max:100']);ThuongHieu::create($data);return redirect()->route('admin.thuong-hieu.index')->with('success','Thêm thương hiệu thành công!');}
    public function show(int $id){return redirect()->route('admin.thuong-hieu.index');}
    public function edit(int $id){$brand=ThuongHieu::findOrFail($id);return view('admin.thuong-hieu.edit',compact('brand'));}
    public function update(Request $request,int $id){$data=$request->validate(['TenTH'=>'required|string|max:100','QuocGia'=>'nullable|string|max:100']);ThuongHieu::findOrFail($id)->update($data);return redirect()->route('admin.thuong-hieu.index')->with('success','Cập nhật thương hiệu thành công!');}
    public function destroy(int $id){
        return app(AdminRecordController::class)->hide(request(), 'thuong-hieu', $id);
    }
}
