<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('SanPham')
            ->where(fn ($query) => $query->whereNull('TrangThaiSanPham')->orWhereIn('TrangThaiSanPham', ['', 'Còn hàng', 'Hết hàng']))
            ->update(['TrangThaiSanPham' => DB::raw("CASE WHEN COALESCE(SoLuongTon, 0) > 0 THEN 'Còn hàng' ELSE 'Hết hàng' END")]);
    }

    public function down(): void
    {
        // The old inconsistent status cannot be reconstructed from stock quantities.
    }
};
