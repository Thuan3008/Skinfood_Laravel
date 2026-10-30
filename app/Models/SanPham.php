<?php

namespace App\Models;

class SanPham extends LegacyModel
{
    protected $table = 'SanPham';
    protected $primaryKey = 'MaSP';
    protected static function booted(): void
    {
        static::saving(function (SanPham $product) {
            if (!in_array($product->TrangThaiSanPham, ['Tạm ngưng', 'Ngừng kinh doanh'], true)) {
                $product->TrangThaiSanPham = ($product->SoLuongTon ?? 0) > 0 ? 'Còn hàng' : 'Hết hàng';
            }
        });
        // Eloquent increment/decrement fire updated, but do not fire saving.
        static::updated(function (SanPham $product) {
            if ($product->isDirty('SoLuongTon')) {
                static::whereKey($product->getKey())
                    ->where(fn ($query) => $query->whereNull('TrangThaiSanPham')->orWhereIn('TrangThaiSanPham', ['', 'Còn hàng', 'Hết hàng']))
                    ->update(['TrangThaiSanPham' => \Illuminate\Support\Facades\DB::raw("CASE WHEN COALESCE(SoLuongTon, 0) > 0 THEN 'Còn hàng' ELSE 'Hết hàng' END")]);
            }
        });
    }
    protected $casts = [
        'GiamGia' => 'decimal:2',
        'GiaBan' => 'decimal:2',
        'DoTinCay' => 'decimal:1',
        'NgayNhapKho' => 'datetime',
    ];

    public function danhMuc() { return $this->belongsTo(DanhMuc::class, 'MaDM', 'MaDM'); }
    public function loai() { return $this->belongsTo(LoaiSP::class, 'MaLoai', 'MaLoai'); }
    public function thuongHieu() { return $this->belongsTo(ThuongHieu::class, 'MaTH', 'MaTH'); }
    public function khuyenMais() { return $this->belongsToMany(KhuyenMai::class, 'KhuyenMai_SanPham', 'MaSP', 'MaKM'); }
    public function danhGias() { return $this->hasMany(DanhGia::class, 'MaSP', 'MaSP'); }

    public function scopeInStockFirst($query)
    {
        return $query->orderByRaw('CASE WHEN COALESCE(SoLuongTon, 0) > 0 THEN 0 ELSE 1 END');
    }

    public function wasPurchasedBy(?int $userId): bool
    {
        return $userId !== null && DonHang::where('MaND', $userId)
            ->where('TrangThaiThanhToan', 'Đã thanh toán')
            ->whereHas('chiTiet', fn ($query) => $query->where('MaSP', $this->MaSP))
            ->exists();
    }
}
