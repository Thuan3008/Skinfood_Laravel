<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('PhieuXuat', function (Blueprint $table) {
            $table->string('SoDienThoaiNguoiNhan', 20)->nullable();
            $table->string('DiaChiNguoiNhan', 255)->nullable();
        });
    }
    public function down(): void
    {
        Schema::table('PhieuXuat', fn (Blueprint $table) => $table->dropColumn(['SoDienThoaiNguoiNhan', 'DiaChiNguoiNhan']));
    }
};
