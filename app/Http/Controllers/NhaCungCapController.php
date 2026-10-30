<?php

namespace App\Http\Controllers;

use App\Models\NhaCungCap;
use Illuminate\Http\Request;

class NhaCungCapController extends Controller
{
    public function index()
    {
        $suppliers = \App\Support\AdminLists::paginate(NhaCungCap::visibleInAdmin(), 'nha-cung-cap');
        return view('admin.nha-cung-cap.index', ['suppliers' => $suppliers, 'q' => request('q', '')]);
    }

    public function create() { return view('admin.nha-cung-cap.create'); }

    public function store(Request $request)
    {
        $data = $request->validate([
            'TenNCC' => 'required|string|max:100', 'Email' => 'nullable|email',
            'SoDienThoai' => 'nullable|string|max:20', 'DiaChi' => 'nullable|string|max:255',
            'NguoiLienHe' => 'nullable|string|max:100',
        ]);
        try {
            NhaCungCap::create($data);
            return redirect()->route('admin.nha-cung-cap.index')->with('success', 'Thêm nhà cung cấp thành công!');
        } catch (\Exception $e) { return back()->with('error', 'Lỗi: ' . $e->getMessage())->withInput(); }
    }

    public function show(int $id) { return redirect()->route('admin.nha-cung-cap.index'); }

    public function edit(int $id)
    {
        $supplier = NhaCungCap::findOrFail($id);
        return view('admin.nha-cung-cap.edit', compact('supplier'));
    }

    public function update(Request $request, int $id)
    {
        $data = $request->validate([
            'TenNCC' => 'required|string|max:100', 'Email' => 'nullable|email',
            'SoDienThoai' => 'nullable|string|max:20', 'DiaChi' => 'nullable|string|max:255',
            'NguoiLienHe' => 'nullable|string|max:100',
        ]);
        try {
            NhaCungCap::findOrFail($id)->update($data);
            return redirect()->route('admin.nha-cung-cap.index')->with('success', 'Cập nhật thành công!');
        } catch (\Exception $e) { return back()->with('error', 'Lỗi: ' . $e->getMessage())->withInput(); }
    }

    public function destroy(int $id)
    {
        return app(AdminRecordController::class)->hide(request(), 'nha-cung-cap', $id);
    }
}
