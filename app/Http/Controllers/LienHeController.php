<?php

namespace App\Http\Controllers;

use App\Models\LienHe;
use Illuminate\Http\Request;

class LienHeController extends Controller
{
    public function index(Request $request)
    {
        $contacts = \App\Support\AdminLists::paginate(LienHe::visibleInAdmin()->with('nguoiDung'), 'lien-he');
        return view('admin.lien-he.index', ['contacts' => $contacts, 'q' => request('q', '')]);
    }

    public function details(int $id)
    {
        $contact = LienHe::with('nguoiDung')->findOrFail($id);
        return view('admin.lien-he.details', compact('contact'));
    }

    public function reply(Request $request, int $id)
    {
        $request->validate(['PhanHoi' => 'required|string|max:5000']);

        try {
            \App\Services\ContactConversation::send($id,'admin',$request->PhanHoi);
            return redirect()->route('admin.lien-he.details', $id)->with('success', 'Đã gửi phản hồi thành công!');
        } catch (\Exception $e) {
            return back()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }

    public function destroy(int $id)
    {
        return app(AdminRecordController::class)->hide(request(), 'lien-he', $id);
    }
}
