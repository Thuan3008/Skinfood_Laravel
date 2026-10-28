<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function show(Request $request) { return view('profile.show', ['account' => $request->user()->load('nguoiDung')]); }

    public function avatar(int $maND)
    {
        abort_unless(auth()->user()->MaND === $maND, 403);
        $avatar = optional(auth()->user()->nguoiDung)->Avatar;
        $path = $avatar ? 'legacy/Content/avatars/' . basename($avatar) : 'legacy/Content/avatars/default.png';
        return redirect(asset($path));
    }

    public function update(Request $request)
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:100'], 'phone' => ['nullable', 'string', 'max:20'], 'address' => ['nullable', 'string', 'max:255'], 'gender' => ['nullable', 'string', 'max:10'], 'birth_date' => ['nullable', 'date']]);
        $request->user()->nguoiDung()->update(['HoTen' => $data['name'], 'SoDienThoai' => $data['phone'] ?? null, 'DiaChi' => $data['address'] ?? null, 'GioiTinh' => $data['gender'] ?? null, 'NgaySinh' => $data['birth_date'] ?? null]);
        return back()->with('success', 'Đã cập nhật thông tin.');
    }
}
