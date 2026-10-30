<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void { Schema::table('DonHang', fn (Blueprint $table) => $table->dateTime('DaDocThongBaoThanhToanLuc')->nullable()); }
    public function down(): void { Schema::table('DonHang', fn (Blueprint $table) => $table->dropColumn('DaDocThongBaoThanhToanLuc')); }
};
