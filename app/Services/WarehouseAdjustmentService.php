<?php

namespace App\Services;

use App\Models\{PhieuNhap, PhieuXuat, SanPham};
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class WarehouseAdjustmentService
{
    public function update(string $resource, int $id, array $data, array $details): void
    {
        DB::transaction(function () use ($resource, $id, $data, $details) {
            $import = $resource === 'phieu-nhap';
            $class = $import ? PhieuNhap::class : PhieuXuat::class;
            $record = $class::whereKey($id)->lockForUpdate()->firstOrFail();
            $relation = $import ? $record->chiTietPhieuNhaps() : $record->chiTietPhieuXuats();
            $old = $relation->orderBy('MaSP')->lockForUpdate()->get()->keyBy('MaSP');
            $new = collect($details)->keyBy('MaSP');
            if ($old->keys()->sort()->values()->all() !== $new->keys()->sort()->values()->all()) {
                throw ValidationException::withMessages(['details' => 'Danh sách sản phẩm đã thay đổi. Vui lòng mở lại phiếu.']);
            }
            $total = 0;
            foreach ($old as $productId => $line) {
                $item = $new[$productId];
                $product = SanPham::whereKey($productId)->lockForUpdate()->firstOrFail();
                $stock = (int) $product->SoLuongTon + ($import ? 1 : -1) * ((int) $item['SoLuong'] - (int) $line->SoLuong);
                if ($stock < 0) throw ValidationException::withMessages(['details' => "Sản phẩm {$product->TenSP} không đủ tồn kho để sửa phiếu."]);
                $product->update(['SoLuongTon' => $stock]);
                $amount = round((float) $item['price'] * (int) $item['SoLuong'], 2);
                DB::table($line->getTable())->where($record->getKeyName(), $id)->where('MaSP', $productId)->update([
                    'SoLuong' => $item['SoLuong'], $import ? 'GiaNhap' : 'DonGia' => $item['price'], 'ThanhTien' => $amount,
                ]);
                $total += $amount;
            }
            $record->update($data + ['TongTien' => $total]);
        });
    }
}
