<?php

namespace App\Services;

use App\Models\LienHe;
use Illuminate\Support\Facades\DB;

class ContactConversation
{
    public static function ensureHistory(LienHe $contact): void
    {
        if (DB::table('ContactMessages')->where('MaLH',$contact->MaLH)->exists()) return;
        DB::table('ContactMessages')->insert(['MaLH'=>$contact->MaLH,'sender'=>'user','body'=>$contact->NoiDung ?? '', 'sent_at'=>$contact->NgayGui ?? now()]);
        if ($contact->PhanHoi !== null) DB::table('ContactMessages')->insert(['MaLH'=>$contact->MaLH,'sender'=>'admin','body'=>$contact->PhanHoi,'sent_at'=>$contact->NgayPhanHoi ?? $contact->NgayGui ?? now()]);
    }

    public static function send(int $id, string $sender, string $body): void
    {
        DB::transaction(function () use ($id,$sender,$body) {
            $contact = LienHe::whereKey($id)->lockForUpdate()->firstOrFail();
            self::ensureHistory($contact);
            DB::table('ContactMessages')->insert(['MaLH'=>$id,'sender'=>$sender,'body'=>trim($body),'sent_at'=>now()]);
            if ($sender === 'admin') $contact->update(['TrangThai'=>'Đã phản hồi','PhanHoi'=>trim($body),'NgayPhanHoi'=>now(),'DaDocPhanHoiLuc'=>null]);
            else $contact->update(['TrangThai'=>'Chờ phản hồi']);
        });
    }

    public static function messages(LienHe $contact)
    {
        $rows = DB::table('ContactMessages')->where('MaLH',$contact->MaLH)->orderBy('id')->get();
        if ($rows->isEmpty()) {
            $rows->push((object)['id'=>'initial','sender'=>'user','body'=>$contact->NoiDung ?? '', 'sent_at'=>$contact->NgayGui]);
            if ($contact->PhanHoi !== null) $rows->push((object)['id'=>'reply','sender'=>'admin','body'=>$contact->PhanHoi,'sent_at'=>$contact->NgayPhanHoi]);
        }
        return $rows->map(fn ($message) => ['id'=>$message->id,'sender'=>$message->sender,'body'=>$message->body,'date'=>$message->sent_at ? \Carbon\Carbon::parse($message->sent_at)->format('d/m/Y H:i') : '']);
    }
}
