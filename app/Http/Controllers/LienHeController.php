<?php

namespace App\Http\Controllers;

use App\Models\LienHe;
use Illuminate\Http\Request;

class LienHeController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->get('q', '');
        $query = LienHe::with('nguoiDung');
        if ($q) {
            $query->where(function ($sub) use ($q) {
                $sub->where('HoTen', 'like', "%{$q}%")
                    ->orWhere('Email', 'like', "%{$q}%")
                    ->orWhere('NoiDung', 'like', "%{$q}%");
            });
        }
        $contacts = $query->orderByDesc('NgayGui')->paginate(10)->withQueryString();
        return view('admin.lien-he.index', compact('contacts', 'q'));
    }

    public function details(int $id)
    {
        $contact = LienHe::with('nguoiDung')->findOrFail($id);
        return view('admin.lien-he.details', compact('contact'));
    }

    public function reply(Request $request, int $id)
    {
        $request->validate(['PhanHoi' => 'required|string']);

        try {
            $contact = LienHe::findOrFail($id);
            $contact->TrangThai = 'PHANHOI|' . trim($request->PhanHoi);
            $contact->save();
            return redirect()->route('admin.lien-he.details', $id)->with('success', 'Đã gửi phản hồi thành công!');
        } catch (\Exception $e) {
            return back()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }

    public function destroy(int $id)
    {
        try {
            LienHe::destroy($id);
            return redirect()->route('admin.lien-he.index')->with('success', 'Xóa liên hệ thành công!');
        } catch (\Exception $e) {
            return back()->with('error', 'Lỗi khi xóa: ' . $e->getMessage());
        }
    }
}
