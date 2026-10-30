<?php

namespace App\Http\Controllers;

use App\Models\TaiKhoan;
use App\Models\NguoiDung;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class TaiKhoanController extends Controller
{
    public function index()
    {
        $accounts = \App\Support\AdminLists::paginate(TaiKhoan::visibleInAdmin()->with('nguoiDung'), 'tai-khoan');
        return view('admin.tai-khoan.index', ['accounts' => $accounts, 'q' => request('q', '')]);
    }

    public function create()
    {
        $users = NguoiDung::orderBy('HoTen')->get();
        return view('admin.tai-khoan.create', compact('users'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'TenDangNhap' => 'required|string|max:50|unique:TaiKhoan,TenDangNhap',
            'MatKhau' => 'required|string|min:6', 'VaiTro' => 'required|in:Admin,User',
            'TrangThai' => 'required|in:active,inactive', 'MaND' => 'nullable|integer|exists:NguoiDung,MaND',
        ]);
        try {
            if (empty($data['MaND'])) {
                $user = NguoiDung::create(['HoTen' => $data['TenDangNhap'], 'NgayTao' => now()]);
                $data['MaND'] = $user->MaND;
            }
            TaiKhoan::create([
                'TenDangNhap' => $data['TenDangNhap'], 'MatKhauHash' => Hash::make($data['MatKhau']),
                'VaiTro' => $data['VaiTro'], 'TrangThai' => $data['TrangThai'] ?? 'active', 'MaND' => $data['MaND'],
            ]);
            return redirect()->route('admin.tai-khoan.index')->with('success', 'Tạo tài khoản thành công!');
        } catch (\Exception $e) { return back()->with('error', 'Lỗi: ' . $e->getMessage())->withInput(); }
    }

    public function show(int $id) { return redirect()->route('admin.tai-khoan.index'); }

    public function edit(int $id)
    {
        $account = TaiKhoan::with('nguoiDung')->findOrFail($id);
        $users = NguoiDung::orderBy('HoTen')->get();
        return view('admin.tai-khoan.edit', compact('account', 'users'));
    }

    public function update(Request $request, int $id)
    {
        $account = TaiKhoan::findOrFail($id);
        $data = $request->validate([
            'MatKhau' => 'nullable|string|min:6', 'VaiTro' => 'required|in:Admin,User',
            'TrangThai' => 'required|in:active,inactive', 'MaND' => 'required|integer|exists:NguoiDung,MaND',
        ]);
        $update = ['VaiTro' => $data['VaiTro'], 'TrangThai' => $data['TrangThai'] ?? 'active', 'MaND' => $data['MaND'] ?? null];
        if (!empty($data['MatKhau'])) $update['MatKhauHash'] = Hash::make($data['MatKhau']);
        $account->update($update);
        return redirect()->route('admin.tai-khoan.index')->with('success', 'Cập nhật thành công!');
    }

    public function destroy(int $id)
    {
        return app(AdminRecordController::class)->hide(request(), 'tai-khoan', $id);
    }
}
