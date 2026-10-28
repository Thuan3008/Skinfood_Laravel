<?php

namespace App\Http\Controllers;

use App\Models\DonHang;
use App\Models\ChiTietDonHang;
use App\Models\SanPham;
use App\Models\DanhMuc;
use Illuminate\Http\Request;

class BaoCaoController extends Controller
{
    public function doanhThuChiTiet(Request $request)
    {
        $from = (string) $request->get('from', now()->subMonth()->format('Y-m-d'));
        $to = (string) $request->get('to', now()->format('Y-m-d'));
        $paidOnly = filter_var($request->get('paidOnly', false), FILTER_VALIDATE_BOOLEAN);

        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $from) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $to) || $from > $to) {
            $from = now()->subMonth()->format('Y-m-d');
            $to = now()->format('Y-m-d');
        }

        $orderQuery = DonHang::with(['chiTiet.sanPham.danhMuc'])
            ->whereDate('NgayDat', '>=', $from)
            ->whereDate('NgayDat', '<=', $to);

        if ($paidOnly) {
            $orderQuery->whereIn('TrangThaiThanhToan', ['Đã thanh toán', 'Paid']);
        }

        $orders = $orderQuery->get();
        $totalRevenue = (float) $orders->sum('TongTien');
        $totalOrders = $orders->count();

        $days = [];
        $products = [];
        $categories = [];

        foreach ($orders as $order) {
            $day = optional($order->NgayDat)->format('Y-m-d') ?: date('Y-m-d');
            $days[$day] = ($days[$day] ?? 0) + (float) $order->TongTien;

            foreach ($order->chiTiet as $ct) {
                $p = $ct->sanPham;
                $pid = (int) $ct->MaSP;
                $dm = (int) ($p?->MaDM ?? 0);
                $dmName = $p?->danhMuc?->TenDM ?? 'Chưa phân loại';
                $qty = (int) $ct->SoLuong;
                $amount = $qty * (float) $ct->DonGia;

                if (!isset($products[$pid])) {
                    $products[$pid] = [
                        'MaSP' => $pid,
                        'TenSP' => $p?->TenSP ?? ('SP #' . $pid),
                        'Quantity' => 0,
                        'Revenue' => 0,
                    ];
                }
                $products[$pid]['Quantity'] += $qty;
                $products[$pid]['Revenue'] += $amount;

                if (!isset($categories[$dm])) {
                    $categories[$dm] = [
                        'MaDM' => $dm,
                        'TenDM' => $dmName,
                        'Quantity' => 0,
                        'Revenue' => 0,
                    ];
                }
                $categories[$dm]['Quantity'] += $qty;
                $categories[$dm]['Revenue'] += $amount;
            }
        }

        usort($products, fn ($a, $b) => $b['Revenue'] <=> $a['Revenue']);
        usort($categories, fn ($a, $b) => $b['Revenue'] <=> $a['Revenue']);
        ksort($days);

        // Export CSV if requested
        if (strtolower((string) $request->get('export', '')) === 'csv') {
            $filename = "doanhthu_sanpham_{$from}_{$to}.csv";
            return response()->streamDownload(function () use ($products) {
                $out = fopen('php://output', 'w');
                fwrite($out, "\xEF\xBB\xBF");
                fputcsv($out, ['MaSP', 'TenSP', 'Quantity', 'Revenue']);
                foreach ($products as $row) {
                    fputcsv($out, [
                        $row['MaSP'],
                        $row['TenSP'],
                        $row['Quantity'],
                        $row['Revenue'],
                    ]);
                }
                fclose($out);
            }, $filename, [
                'Content-Type' => 'text/csv; charset=utf-8',
            ]);
        }

        $labelsByDay = json_encode(array_keys($days));
        $dataByDay = json_encode(array_values($days));

        return view('admin.bao-cao.doanh-thu', [
            'totalRevenue' => $totalRevenue,
            'totalOrders' => $totalOrders,
            'from' => $from,
            'to' => $to,
            'paidOnly' => $paidOnly,
            'labelsByDay' => $labelsByDay,
            'dataByDay' => $dataByDay,
            'productList' => $products,
            'categoryList' => $categories,
        ]);
    }
}
