<?php

namespace App\Http\Controllers;

use App\Models\LienHe;
use App\Services\ContactConversation;
use Illuminate\Http\Request;

class ContactConversationController extends Controller
{
    private function contact(Request $request, int $id): LienHe
    {
        $query = LienHe::query();
        if (strtolower((string) $request->user()->VaiTro) !== 'admin') $query->where('MaND',$request->user()->MaND);
        return $query->findOrFail($id);
    }
    public function messages(Request $request, int $id)
    {
        $contact = $this->contact($request,$id);
        if ((int) $contact->MaND === (int) $request->user()->MaND && $contact->PhanHoi !== null && !$contact->DaDocPhanHoiLuc) $contact->update(['DaDocPhanHoiLuc'=>now()]);
        return response()->json(['messages'=>ContactConversation::messages($contact)])->header('Cache-Control','no-store');
    }
    public function send(Request $request, int $id)
    {
        $contact = $this->contact($request,$id);
        $data = $request->validate(['message'=>'required|string|max:5000']);
        $sender = strtolower((string) $request->user()->VaiTro) === 'admin' ? 'admin' : 'user';
        ContactConversation::send($contact->MaLH,$sender,$data['message']);
        if ($request->expectsJson()) return response()->json(['success'=>true,'messages'=>ContactConversation::messages($contact->fresh())]);
        return back()->with('success','Đã gửi tin nhắn.');
    }
}
