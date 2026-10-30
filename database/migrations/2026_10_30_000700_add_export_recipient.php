<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void { Schema::table('PhieuXuat', fn (Blueprint $table) => $table->string('NguoiNhanHang', 150)->nullable()); }
    public function down(): void { Schema::table('PhieuXuat', fn (Blueprint $table) => $table->dropColumn('NguoiNhanHang')); }
};
