<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('DonHang', fn (Blueprint $table) => $table->boolean('TonKhoDaTru')->default(false));
        DB::transaction(function () {
            // Before this migration every placed order deducted stock, including unpaid orders.
            $orders = DB::table('DonHang')->lockForUpdate()->get();
            foreach ($orders as $order) {
                if ($order->TrangThaiThanhToan === 'Đã thanh toán') {
                    DB::table('DonHang')->where('MaDH', $order->MaDH)->update(['TonKhoDaTru' => true]);
                    continue;
                }
                foreach (DB::table('ChiTietDonHangs')->where('MaDH', $order->MaDH)->get() as $item) {
                    DB::table('SanPham')->where('MaSP', $item->MaSP)->increment('SoLuongTon', $item->SoLuong);
                }
            }
        });
    }

    public function down(): void
    {
        DB::transaction(function () {
            foreach (DB::table('DonHang')->where('TonKhoDaTru', false)->get() as $order) {
                foreach (DB::table('ChiTietDonHangs')->where('MaDH', $order->MaDH)->get() as $item) {
                    DB::table('SanPham')->where('MaSP', $item->MaSP)->decrement('SoLuongTon', $item->SoLuong);
                }
            }
        });
        Schema::table('DonHang', fn (Blueprint $table) => $table->dropColumn('TonKhoDaTru'));
    }
};
