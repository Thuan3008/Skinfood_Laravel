<?php

namespace App\Services;

use App\Models\DonHang;
use App\Models\SanPham;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderPaymentService
{
    public function updateStatus(int $orderId, string $status, ?callable $validate = null): DonHang
    {
        return DB::transaction(function () use ($orderId, $status, $validate) {
            $order = DonHang::where('MaDH', $orderId)->lockForUpdate()->firstOrFail();
            $allowed = ['Chưa thanh toán', 'Đã chuyển khoản - chờ xác nhận', 'Đã thanh toán', 'Đã hủy'];
            if (!in_array($status, $allowed, true)) {
                throw ValidationException::withMessages(['status' => 'Trạng thái đơn hàng không hợp lệ.']);
            }
            if (in_array($order->TrangThaiThanhToan, ['Đã thanh toán', 'Đã hủy'], true) && $status !== $order->TrangThaiThanhToan) {
                throw ValidationException::withMessages(['status' => 'Đơn hàng '.$order->TrangThaiThanhToan.' không thể đổi trạng thái hoặc hủy.']);
            }
            if ($status === 'Đã hủy' && !in_array($order->TrangThaiThanhToan, ['Chưa thanh toán', 'Đã chuyển khoản - chờ xác nhận', 'Đã hủy'], true)) {
                throw ValidationException::withMessages(['status' => 'Chỉ được hủy đơn chưa thanh toán hoặc đang chờ xác nhận chuyển khoản.']);
            }
            if ($validate) $validate($order);

            if ($status === 'Đã thanh toán' && !$order->TonKhoDaTru) {
                foreach ($order->chiTiet()->orderBy('MaSP')->get() as $item) {
                    $product = SanPham::where('MaSP', $item->MaSP)->lockForUpdate()->firstOrFail();
                    if ($item->SoLuong < 1 || ($product->SoLuongTon ?? 0) < $item->SoLuong) {
                        throw ValidationException::withMessages(['stock' => "Sản phẩm {$product->TenSP} không đủ tồn kho để xác nhận thanh toán."]);
                    }
                    $product->decrement('SoLuongTon', $item->SoLuong);
                }
                $order->TonKhoDaTru = true;
            }

            $order->TrangThaiThanhToan = $status;
            if ($status === 'Đã thanh toán') $order->NgayThanhToan ??= now();
            $order->save();
            return $order;
        });
    }
}
