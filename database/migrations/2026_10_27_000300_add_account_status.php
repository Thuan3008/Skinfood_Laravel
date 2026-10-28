<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('TaiKhoan', function (Blueprint $table) {
            $table->string('TrangThai', 20)->default('active')->after('VaiTro');
        });
    }

    public function down(): void
    {
        Schema::table('TaiKhoan', fn (Blueprint $table) => $table->dropColumn('TrangThai'));
    }
};
