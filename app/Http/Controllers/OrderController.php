<?php

namespace App\Http\Controllers;

use App\Models\DonHang;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = DonHang::where('MaND', $request->user()->MaND)->with('chiTiet.sanPham')->latest('NgayDat')->paginate(10);
        return view('orders.index', compact('orders'));
    }

    public function show(Request $request, int $id)
    {
        $query = DonHang::with('chiTiet.sanPham');
        if (strtolower((string) $request->user()->VaiTro) !== 'admin') $query->where('MaND', $request->user()->MaND);
        $order = $query->findOrFail($id);
        return view('orders.show', compact('order'));
    }
}
