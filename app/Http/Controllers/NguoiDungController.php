<?php

namespace App\Http\Controllers;

use App\Models\NguoiDung;
use Illuminate\Http\Request;

class NguoiDungController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->get('q', '');
        $query = NguoiDung::with('taiKhoans');
        if ($q) {
            $query->where(function ($sub) use ($q) {
                $sub->where('HoTen', 'like', "%{$q}%")
                    ->orWhere('SoDienThoai', 'like', "%{$q}%")
                    ->orWhereHas('taiKhoans', fn ($tq) => $tq->where('TenDangNhap', 'like', "%{$q}%"));
            });
        }
        $users = $query->orderByDesc('MaND')->paginate(10)->withQueryString();
        return view('admin.nguoi-dung.index', compact('users', 'q'));
    }

    public function destroy(int $id)
    {
        try {
            NguoiDung::destroy($id);
            return redirect()->route('admin.nguoi-dung.index')->with('success', 'Xóa người dùng thành công!');
        } catch (\Exception $e) {
            return back()->with('error', 'Lỗi khi xóa: ' . $e->getMessage());
        }
    }

    public function show(int $id)
    {
        return redirect()->route('admin.nguoi-dung.index');
    }
}
