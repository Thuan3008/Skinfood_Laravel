<?php

namespace App\Http\Controllers;

use App\Models\LienHe;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index() { return view('contact.index'); }
    public function store(Request $request)
    {
        $request->merge(['content' => $request->input('NoiDung', $request->input('content'))]);
        $data = $request->validate(['content' => ['required','string','max:1000']]);
        $user = $request->user();
        LienHe::create(['HoTen' => $user->nguoiDung?->HoTen ?? $user->TenDangNhap, 'Email' => $user->TenDangNhap, 'SoDienThoai' => $user->nguoiDung?->SoDienThoai, 'NoiDung' => $data['content'], 'NgayGui' => now(), 'MaND' => $user->MaND]);
        return back()->with('success', 'Gửi liên hệ thành công. Chúng tôi sẽ phản hồi sớm nhất.');
    }
    public function checkLogin()
    {
        return auth()->check() ? redirect()->route('home') : redirect()->route('login');
    }

    public function policy(string $page) { abort_unless(in_array($page, ['huong-dan-mua-hang','chinh-sach-doi-tra','chinh-sach-bao-mat'], true), 404); return view('contact.policy', compact('page')); }
}
