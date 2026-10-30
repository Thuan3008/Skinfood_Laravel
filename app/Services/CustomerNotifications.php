<?php

namespace App\Services;

use App\Models\{LienHe, DonHang};

class CustomerNotifications
{
    public static function forUser(int $userId): array
    {
        $contacts = LienHe::where('MaND',$userId)->whereNotNull('PhanHoi');
        $orders = DonHang::where('MaND',$userId)->where('TrangThaiThanhToan','Đã thanh toán');
        $unread = (clone $contacts)->whereNull('DaDocPhanHoiLuc')->count() + (clone $orders)->whereNull('DaDocThongBaoThanhToanLuc')->count();
        $replies = $contacts->orderByDesc('NgayPhanHoi')->orderByDesc('MaLH')->take(20)->get()->map(fn ($contact) => [
            'id'=>$contact->MaLH, 'type'=>'contact', 'title'=>'SkinFood đã phản hồi liên hệ #'.$contact->MaLH,
            'message'=>$contact->PhanHoi, 'unread'=>$contact->DaDocPhanHoiLuc === null,
            'date'=>$contact->NgayPhanHoi?->format('d/m/Y H:i'), 'sort_at'=>$contact->NgayPhanHoi?->timestamp ?? 0,
            'url'=>route('contact.index',['conversation'=>$contact->MaLH]).'#contact-chat',
        ]);
        $payments = $orders->orderByDesc('NgayThanhToan')->orderByDesc('MaDH')->take(20)->get()->map(fn ($order) => [
            'id'=>$order->MaDH, 'type'=>'payment', 'title'=>'Đơn hàng #'.$order->MaDH.' đã được xác nhận thanh toán',
            'message'=>'SkinFood đã xác nhận thanh toán '.number_format($order->TongTien,0,',','.').' đ. Bấm để xem chi tiết đơn hàng.',
            'unread'=>$order->DaDocThongBaoThanhToanLuc === null,
            'date'=>($order->NgayThanhToan ?? $order->NgayDat)?->format('d/m/Y H:i'), 'sort_at'=>($order->NgayThanhToan ?? $order->NgayDat)?->timestamp ?? 0,
            'url'=>route('orders.show',$order->MaDH),
        ]);
        $items = $replies->concat($payments)->sortByDesc('sort_at')->take(20)->map(function ($item) { unset($item['sort_at']); return $item; })->values();
        return compact('unread','items');
    }
}
