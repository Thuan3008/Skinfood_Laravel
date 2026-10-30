<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\{Schema, DB};

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('LienHe', function (Blueprint $table) {
            $table->text('PhanHoi')->nullable();
            $table->dateTime('NgayPhanHoi')->nullable();
            $table->dateTime('DaDocPhanHoiLuc')->nullable();
        });
        foreach (DB::table('LienHe')->where('TrangThai','like','PHANHOI|%')->get() as $contact) {
            DB::table('LienHe')->where('MaLH',$contact->MaLH)->update(['PhanHoi'=>substr($contact->TrangThai,8), 'NgayPhanHoi'=>$contact->NgayGui ?? now(), 'TrangThai'=>'Đã phản hồi']);
        }
    }
    public function down(): void { Schema::table('LienHe', fn (Blueprint $table) => $table->dropColumn(['PhanHoi','NgayPhanHoi','DaDocPhanHoiLuc'])); }
};
