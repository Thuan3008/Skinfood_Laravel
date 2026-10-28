<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('PhieuXuat', function (Blueprint $table) {
            $table->decimal('TongTien', 18, 2)->default(0)->after('NgayXuat');
        });
        Schema::table('ChiTietPhieuXuat', function (Blueprint $table) {
            $table->decimal('DonGia', 18, 2)->default(0)->after('SoLuong');
            $table->decimal('ThanhTien', 18, 2)->default(0)->after('DonGia');
        });
        Schema::table('DonHang', function (Blueprint $table) {
            $table->string('MaThamChieuThanhToan', 40)->nullable()->index();
            $table->dateTime('QrHetHanLuc')->nullable();
        });
        Schema::create('SavedCarts', function (Blueprint $table) {
            $table->unsignedInteger('MaND')->primary();
            $table->longText('CartJson')->nullable();
            $table->dateTime('Updated')->nullable();
            $table->foreign('MaND')->references('MaND')->on('NguoiDung')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('SavedCarts');
        Schema::table('DonHang', function (Blueprint $table) {
            $table->dropIndex(['MaThamChieuThanhToan']);
            $table->dropColumn(['MaThamChieuThanhToan', 'QrHetHanLuc']);
        });
        Schema::table('ChiTietPhieuXuat', function (Blueprint $table) {
            $table->dropColumn(['DonGia', 'ThanhTien']);
        });
        Schema::table('PhieuXuat', function (Blueprint $table) {
            $table->dropColumn('TongTien');
        });
    }
};
