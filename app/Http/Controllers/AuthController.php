<?php

namespace App\Http\Controllers;

use App\Models\NguoiDung;
use App\Models\TaiKhoan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login()
    {
        if (Auth::check()) {
            return redirect('/');
        }
        return view('auth.login');
    }

    public function authenticate(Request $request)
    {
        $username = $request->input('Email') ?? $request->input('TenDangNhap') ?? $request->input('username');
        $password = $request->input('MatKhau') ?? $request->input('password');

        if (!$username || !$password) {
            return back()->with('error', 'Vui lòng nhập đầy đủ thông tin đăng nhập.')->withInput();
        }

        $account = TaiKhoan::where('TenDangNhap', $username)->first();

        if ($account && ($account->TrangThai ?? 'active') !== 'active') {
            return back()->with('error', 'Tài khoản đang bị khóa.')->withInput();
        }

        // Legacy database uses plaintext passwords; new accounts use bcrypt
        $passwordMatches = $account && (
            hash_equals((string) $account->MatKhauHash, (string) $password)
            || Hash::check($password, (string) $account->MatKhauHash)
        );

        if (!$passwordMatches) {
            return back()->with('error', 'Thông tin đăng nhập hoặc mật khẩu không chính xác.')->withInput();
        }

        Auth::login($account, true);
        $request->session()->regenerate();
        return redirect()->intended(strtolower((string) $account->VaiTro) === 'admin' ? '/admin' : '/');
    }

    public function register()
    {
        if (Auth::check()) {
            return redirect('/');
        }
        return view('auth.register');
    }

    public function store(Request $request)
    {
        $hoTen = $request->input('HoTen') ?? $request->input('name');
        $tenDangNhap = $request->input('TenDangNhap') ?? $request->input('username') ?? $request->input('Email');
        $matKhau = $request->input('MatKhau') ?? $request->input('password');
        $nhapLaiMatKhau = $request->input('NhapLaiMatKhau') ?? $request->input('password_confirmation');
        $soDienThoai = $request->input('SoDienThoai') ?? $request->input('phone');
        $diaChi = $request->input('DiaChi') ?? $request->input('address');
        $gioiTinh = $request->input('GioiTinh', 'Nam');
        $ngaySinh = $request->input('NgaySinh');
        $avatar = $request->input('Avatar', '1.jpg');

        if (!$hoTen || !$tenDangNhap || !$matKhau) {
            return back()->with('error', 'Vui lòng điền đầy đủ các thông tin bắt buộc.')->withInput();
        }

        if ($matKhau !== $nhapLaiMatKhau) {
            return back()->with('error', 'Mật khẩu xác nhận không khớp.')->withInput();
        }

        if (TaiKhoan::where('TenDangNhap', $tenDangNhap)->exists()) {
            return back()->with('error', 'Tên đăng nhập / Email này đã tồn tại.')->withInput();
        }

        $person = NguoiDung::create([
            'HoTen' => $hoTen,
            'SoDienThoai' => $soDienThoai,
            'DiaChi' => $diaChi,
            'GioiTinh' => $gioiTinh,
            'NgaySinh' => $ngaySinh ? date('Y-m-d', strtotime($ngaySinh)) : null,
            'Avatar' => $avatar,
            'NgayTao' => now(),
        ]);

        $account = TaiKhoan::create([
            'TenDangNhap' => $tenDangNhap,
            'MatKhauHash' => Hash::make($matKhau),
            'VaiTro' => 'User',
            'TrangThai' => 'active',
            'MaND' => $person->MaND,
        ]);

        Auth::login($account);
        $request->session()->regenerate();
        return redirect('/')->with('success', 'Đăng ký tài khoản thành công.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
