<?php

namespace App\Http\Controllers;

use App\Models\DanhMuc;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DanhMucController extends Controller
{
    public function index()
    {
        $categories = DanhMuc::withCount(['loaiSanPhams', 'sanPhams'])->orderByDesc('MaDM')->paginate(10);
        return view('admin.danh-muc.index', compact('categories'));
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
        $category = DanhMuc::withCount(['loaiSanPhams', 'sanPhams'])->findOrFail($id);
        if ($category->loai_san_phams_count || $category->san_phams_count) {
            return back()->with('error', 'Không thể xóa danh mục đang có loại hoặc sản phẩm.');
        }
        $category->delete();
        return back()->with('success', 'Đã xóa danh mục.');
    }

    private function validated(Request $request, ?int $id = null): array
    {
        return $request->validate([
            'TenDM' => ['required', 'string', 'max:100', Rule::unique('DanhMuc', 'TenDM')->ignore($id, 'MaDM')],
        ]);
    }
}
