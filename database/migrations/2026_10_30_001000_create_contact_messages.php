<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\{DB, Schema};

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ContactMessages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('MaLH');
            $table->string('sender', 10);
            $table->text('body');
            $table->dateTime('sent_at');
            $table->foreign('MaLH')->references('MaLH')->on('LienHe');
            $table->index(['MaLH','id']);
        });
        DB::table('LienHe')->orderBy('MaLH')->chunk(100, function ($contacts) {
            foreach ($contacts as $contact) {
                DB::table('ContactMessages')->insert(['MaLH'=>$contact->MaLH,'sender'=>'user','body'=>$contact->NoiDung ?? '', 'sent_at'=>$contact->NgayGui ?? now()]);
                if ($contact->PhanHoi !== null) DB::table('ContactMessages')->insert(['MaLH'=>$contact->MaLH,'sender'=>'admin','body'=>$contact->PhanHoi,'sent_at'=>$contact->NgayPhanHoi ?? $contact->NgayGui ?? now()]);
            }
        });
    }
    public function down(): void { Schema::dropIfExists('ContactMessages'); }
};
