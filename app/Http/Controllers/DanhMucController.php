<?php

namespace App\Http\Controllers;

use App\Models\DanhMuc;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DanhMucController extends Controller
{
    public function index()
    {
        $categories = \App\Support\AdminLists::paginate(DanhMuc::visibleInAdmin()->withCount(['loaiSanPhams', 'sanPhams']), 'danh-muc');
        return view('admin.danh-muc.index', ['categories' => $categories, 'q' => request('q', '')]);
    }

    public function create() { return view('admin.danh-muc.form', ['category' => new DanhMuc()]); }

    public function store(Request $request)
    {
        DanhMuc::create($this->validated($request));
        return redirect()->route('admin.danh-muc.index')->with('success', 'Đã thêm danh mục.');
    }

    public function edit(int $id) { return view('admin.danh-muc.form', ['category' => DanhMuc::findOrFail($id)]); }

    public function update(Request $request, int $id)
    {
        $category = DanhMuc::findOrFail($id);
        $category->update($this->validated($request, $id));
        return redirect()->route('admin.danh-muc.index')->with('success', 'Đã cập nhật danh mục.');
    }

    public function destroy(int $id)
    {
        return app(AdminRecordController::class)->hide(request(), 'danh-muc', $id);
    }

    private function validated(Request $request, ?int $id = null): array
    {
        return $request->validate([
            'TenDM' => ['required', 'string', 'max:100', Rule::unique('DanhMuc', 'TenDM')->ignore($id, 'MaDM')],
        ]);
    }
}
