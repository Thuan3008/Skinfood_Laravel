<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Models\NguoiDung;
use App\Models\TaiKhoan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

    public function store(RegisterRequest $request)
    {
        $data = $request->validated();
        $account = DB::transaction(function () use ($data) {
            $person = NguoiDung::create([
                'HoTen' => $data['HoTen'],
                'SoDienThoai' => $data['SoDienThoai'] ?? null,
                'DiaChi' => $data['DiaChi'] ?? null,
                'GioiTinh' => $data['GioiTinh'],
                'NgaySinh' => $data['NgaySinh'] ?? null,
                'Avatar' => $data['Avatar'],
                'NgayTao' => now(),
            ]);

            return TaiKhoan::create([
                'TenDangNhap' => $data['TenDangNhap'],
                'MatKhauHash' => Hash::make($data['MatKhau']),
                'VaiTro' => 'User',
                'TrangThai' => 'active',
                'MaND' => $person->MaND,
            ]);
        });

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
