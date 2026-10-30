<?php

namespace App\Http\Controllers;

use App\Models\LienHe;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index(Request $request, ?int $conversation = null)
    {
        $contact = null;
        $conversations = collect();
        if ($request->user()) {
            $request->validate(['conversation'=>'nullable|integer|min:1']);
            $query = LienHe::where('MaND',$request->user()->MaND);
            $conversations = (clone $query)->orderByDesc('MaLH')->get(['MaLH','NgayGui']);
            $id = $conversation ?? $request->input('conversation');
            if ($id) $contact = $query->findOrFail($id);
            elseif (!$request->boolean('new')) $contact = $query->orderByDesc('MaLH')->first();
            if ($contact && $contact->PhanHoi !== null && !$contact->DaDocPhanHoiLuc) $contact->update(['DaDocPhanHoiLuc'=>now()]);
        }
        return view('contact.index', compact('contact','conversations'));
    }
    public function store(Request $request)
    {
        $request->merge(['content' => $request->input('NoiDung', $request->input('content'))]);
        $data = $request->validate(['content' => ['required','string','max:1000']]);
        $user = $request->user();
        $contact = LienHe::create(['HoTen' => $user->nguoiDung?->HoTen ?? $user->TenDangNhap, 'Email' => $user->TenDangNhap, 'SoDienThoai' => $user->nguoiDung?->SoDienThoai, 'NoiDung' => $data['content'], 'NgayGui' => now(), 'MaND' => $user->MaND]);
        return redirect()->to(route('contact.index',['conversation'=>$contact->MaLH]).'#contact-chat')->with('success', 'Gửi liên hệ thành công. Chúng tôi sẽ phản hồi sớm nhất.');
    }
    public function checkLogin()
    {
        return auth()->check() ? redirect()->route('home') : redirect()->route('login');
    }

    public function replies(Request $request)
    {
        return response()->json(\App\Services\CustomerNotifications::forUser($request->user()->MaND))->header('Cache-Control','no-store');
    }

    public function reply(Request $request, int $id)
    {
        return $this->index($request,$id);
    }

    public function policy(string $page) { abort_unless(in_array($page, ['huong-dan-mua-hang','chinh-sach-doi-tra','chinh-sach-bao-mat'], true), 404); return view('contact.policy', compact('page')); }
}
