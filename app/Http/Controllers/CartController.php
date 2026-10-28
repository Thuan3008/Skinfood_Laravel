<?php

namespace App\Http\Controllers;

use App\Models\ChiTietDonHang;
use App\Models\DonHang;
use App\Models\KhuyenMai;
use App\Models\SanPham;
use App\Services\VietQrService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CartController extends Controller
{
    private function cart(): array
    {
        $cart = session('cart');
        if ($cart === null && auth()->check()) {
            $json = DB::table('SavedCarts')->where('MaND', auth()->user()->MaND)->value('CartJson');
            $cart = $json ? json_decode($json, true) : [];
            session(['cart' => is_array($cart) ? $cart : []]);
        }
        return is_array($cart) ? $cart : [];
    }

    private function saveCart(array $cart): void
    {
        session(['cart' => $cart]);
        if (auth()->check()) {
            DB::table('SavedCarts')->updateOrInsert(['MaND' => auth()->user()->MaND], ['CartJson' => json_encode($cart, JSON_UNESCAPED_UNICODE), 'Updated' => now()]);
        }
    }

    public function index()
    {
        $cart = $this->cart();
        return view('cart.index', ['cart' => $cart] + $this->cartTotals($cart));
    }

    public function add(Request $request, int $id)
    {
        $product = SanPham::findOrFail($id);
        if (($product->SoLuongTon ?? 0) < 1) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Sản phẩm đã hết hàng.'], 400);
            }
            return back()->with('error', 'Sản phẩm đã hết hàng.');
        }

        $cart = $this->cart();
        $key = (string) $id;
        $quantity = ($cart[$key]['quantity'] ?? 0) + 1;
        if ($quantity > $product->SoLuongTon) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Số lượng vượt quá tồn kho.'], 400);
            }
            return back()->with('error', 'Số lượng vượt quá tồn kho.');
        }
        $price = round((float) $product->GiaBan * (1 - ((float) ($product->GiamGia ?? 0) / 100)), 2);
        $cart[$key] = ['id' => $product->MaSP, 'name' => $product->TenSP, 'price' => $price, 'image' => $product->HinhAnh ? asset('legacy/Content/images/' . $product->HinhAnh) : null, 'quantity' => $quantity];
        $this->saveCart($cart);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Đã thêm "' . $product->TenSP . '" vào giỏ hàng!',
                'cartCount' => count($cart),
                'total' => $this->cartTotals($cart)['total'] ?? 0
            ]);
        }

        return back()->with('success', 'Đã thêm sản phẩm vào giỏ hàng.');
    }

    public function update(Request $request, int $id)
    {
        $data = $request->validate(['quantity' => ['required', 'integer', 'min:0']]);
        $product = SanPham::findOrFail($id);
        $cart = $this->cart();
        if ($data['quantity'] === 0) {
            unset($cart[(string) $id]);
        } elseif ($data['quantity'] <= ($product->SoLuongTon ?? 0)) {
            if (isset($cart[(string) $id])) $cart[(string) $id]['quantity'] = $data['quantity'];
        } else {
            return back()->with('error', 'Số lượng vượt quá tồn kho.');
        }
        $this->saveCart($cart);
        if ($request->expectsJson()) return response()->json($this->cartTotals($cart) + ['success' => true]);
        return back()->with('success', 'Đã cập nhật giỏ hàng.');
    }

    public function remove(int $id)
    {
        $cart = $this->cart();
        unset($cart[(string) $id]);
        $this->saveCart($cart);
        if (request()->expectsJson()) return response()->json($this->cartTotals($cart) + ['success' => true]);
        return back()->with('success', 'Đã xóa sản phẩm khỏi giỏ hàng.');
    }

    public function review()
    {
        $cart = $this->cart();
        if (!$cart) return redirect()->route('cart.index')->with('error', 'Giỏ hàng đang trống.');
        return view('cart.payment-review', ['cart' => $cart] + $this->cartTotals($cart));
    }

    public function checkout()
    {
        return $this->review();
    }

    public function placeOrder(Request $request)
    {
        $request->merge([
            'recipient' => $request->input('tenNguoiNhan', $request->input('recipient')),
            'phone' => $request->input('soDienThoai', $request->input('phone')),
            'address' => $request->input('diaChi', $request->input('diaChiGiaoHang', $request->input('address'))),
            'note' => $request->input('ghiChu', $request->input('note')),
            'payment' => $request->input('phuongThuc', $request->input('payment', 'COD')),
        ]);

        $data = $request->validate([
            'recipient' => ['required', 'string', 'max:100'],
            'phone' => ['required', 'string', 'max:20'],
            'address' => ['required', 'string', 'max:255'],
            'note' => ['nullable', 'string', 'max:255'],
            'payment' => ['required', 'in:COD,BankTransfer,QR']
        ]);
        $cart = $this->cart();
        if (!$cart) return redirect()->route('cart.index')->with('error', 'Giỏ hàng đang trống.');
        $user = $request->user();

        $discountId = session('discount_id');
        $order = DB::transaction(function () use ($cart, $data, $user, $discountId) {
            $total = 0;
            foreach ($cart as $item) {
                $product = SanPham::where('MaSP', $item['id'])->lockForUpdate()->firstOrFail();
                if (($product->SoLuongTon ?? 0) < $item['quantity']) throw new \RuntimeException("Sản phẩm {$product->TenSP} không đủ tồn kho.");
                $unitPrice = round((float) $product->GiaBan * (1 - ((float) ($product->GiamGia ?? 0) / 100)), 2);
                $total += $unitPrice * $item['quantity'];
            }
            $promotion = $discountId ? KhuyenMai::where('MaKM', $discountId)->lockForUpdate()->first() : null;
            $discount = $promotion ? $this->discountAmount($promotion, $total) : 0;
            $order = DonHang::create(['MaND' => $user->MaND, 'NgayDat' => now(), 'TongTien' => max(0, $total - $discount), 'TenNguoiNhan' => $data['recipient'], 'SoDienThoai' => $data['phone'], 'DiaChiGiaoHang' => $data['address'], 'GhiChu' => $data['note'] ?? null, 'TrangThaiThanhToan' => 'Chưa thanh toán', 'PhuongThucThanhToan' => $data['payment'], 'MaKM' => $promotion?->MaKM, 'SoTienGiam' => $discount]);
            foreach ($cart as $item) {
                $product = SanPham::where('MaSP', $item['id'])->lockForUpdate()->firstOrFail();
                $unitPrice = round((float) $product->GiaBan * (1 - ((float) ($product->GiamGia ?? 0) / 100)), 2);
                ChiTietDonHang::create(['MaDH' => $order->MaDH, 'MaSP' => $item['id'], 'SoLuong' => $item['quantity'], 'DonGia' => $unitPrice]);
                $product->decrement('SoLuongTon', $item['quantity']);
            }
            if ($promotion) $promotion->increment('SoLuongDaDung');
            return $order;
        });
        $this->saveCart([]);
        session(['current_order' => $order->MaDH]);
        session()->forget(['discount', 'discount_code', 'discount_id']);
        if (strtolower((string) $data['payment']) === 'banktransfer') return redirect()->route('checkout.bank', $order->MaDH);
        if (strtolower((string) $data['payment']) === 'qr') return redirect()->route('checkout.qr', $order->MaDH);
        return redirect()->route('order.success', $order->MaDH);
    }

    public function success(int $id)
    {
        $order = DonHang::with('chiTiet.sanPham')->findOrFail($id);
        abort_unless((int) auth()->user()->MaND === (int) $order->MaND || strtolower((string) auth()->user()->VaiTro) === 'admin', 403);
        return view('cart.success', compact('order'));
    }

    public function availableStock(int $id)
    {
        $product = SanPham::find($id);
        return response()->json(['success' => (bool) $product, 'available' => (int) ($product->SoLuongTon ?? 0)]);
    }

    public function applyDiscount(Request $request)
    {
        $code = trim((string) $request->input('code', $request->input('maGiamGia', '')));
        $cart = $this->cart();
        if ($code === '' || !$cart) return response()->json(['success' => false, 'message' => 'Mã giảm giá hoặc giỏ hàng không hợp lệ.'], 422);
        $subtotal = collect($cart)->sum(fn ($item) => $item['price'] * $item['quantity']);
        $promotion = KhuyenMai::where('MaCode', $code)->where('TrangThai', true)
            ->where(fn ($q) => $q->whereNull('NgayBatDau')->orWhere('NgayBatDau', '<=', now()))
            ->where(fn ($q) => $q->whereNull('NgayKetThuc')->orWhere('NgayKetThuc', '>=', now()))->first();
        if (!$promotion || ($promotion->SoLuongPhatHanh && $promotion->SoLuongDaDung >= $promotion->SoLuongPhatHanh) || ($promotion->DonHangToiThieu && $subtotal < $promotion->DonHangToiThieu)) return response()->json(['success' => false, 'message' => 'Mã giảm giá không hợp lệ, đã hết lượt hoặc chưa đạt giá trị tối thiểu.'], 422);
        $discount = $this->discountAmount($promotion, $subtotal);
        session(['discount' => $discount, 'discount_code' => $code, 'discount_id' => $promotion->MaKM]);
        return response()->json(['success' => true, 'discount' => $discount, 'total' => $subtotal - $discount]);
    }

    public function bankTransfer(?int $id = null)
    {
        return $this->paymentView($id, 'cart.bank-transfer');
    }

    public function qr(?int $id = null)
    {
        return $this->paymentView($id, 'cart.qr');
    }

    private function paymentView(?int $id, string $view)
    {
        $id ??= session('current_order');
        $order = $id ? DonHang::findOrFail($id) : null;
        abort_unless($order && (int) $order->MaND === (int) auth()->user()->MaND, 403);
        $paid = $order->TrangThaiThanhToan === 'Đã thanh toán';
        $reference = strtoupper(Str::random(6));
        $expiresAt = now()->addMinutes(15);
        if (!$paid) $order->update(['MaThamChieuThanhToan' => $reference, 'QrHetHanLuc' => $expiresAt]);
        $bank = config('services.bank_transfer');
        $description = mb_substr(preg_replace('/[^A-Za-z0-9 ]/u', '', strtoupper($bank['content_prefix']) . ' DH' . $order->MaDH . ' ' . $reference), 0, 25);
        $vietQr = app(VietQrService::class);
        $amount = (int) round((float) $order->TongTien);
        $payload = $paid ? '' : $vietQr->payload($bank['bank_bin'], $bank['account_number'], $amount, $description);
        $qrApiUrl = $paid ? '' : $vietQr->quickLink($bank['bank_bin'], $bank['account_number'], $bank['account_name'], $amount, $description);
        return view($view, compact('order', 'paid', 'bank', 'description', 'payload', 'qrApiUrl', 'expiresAt'));
    }

    public function confirmPayment(Request $request, ?int $id = null)
    {
        $id ??= session('current_order');
        $order = DonHang::where('MaDH', $id)->where('MaND', auth()->user()->MaND)->firstOrFail();
        abort_unless(in_array($order->PhuongThucThanhToan, ['BankTransfer', 'QR'], true), 400);
        if ($order->TrangThaiThanhToan !== 'Đã thanh toán') $order->update(['TrangThaiThanhToan' => 'Đã chuyển khoản - chờ xác nhận', 'NgayThanhToan' => null]);
        if ($request->expectsJson() || $request->input('ajax') === '1') return response()->json(['success' => true, 'message' => 'Đã báo shop kiểm tra giao dịch.']);
        return back()->with('success', 'Đã báo shop kiểm tra giao dịch.');
    }

    public function paymentStatus(int $id)
    {
        $order = DonHang::where('MaDH', $id)->where('MaND', auth()->user()->MaND)->firstOrFail();
        return response()->json(['paid' => $order->TrangThaiThanhToan === 'Đã thanh toán', 'status' => $order->TrangThaiThanhToan]);
    }

    public function paymentWebhook(Request $request)
    {
        $secret = (string) config('services.bank_transfer.webhook_secret');
        $providedSecret = (string) $request->header('X-Webhook-Secret');
        if ($providedSecret === '') {
            $providedSecret = preg_replace('/^(Apikey|Bearer)\s+/i', '', (string) $request->header('Authorization'));
        }
        abort_unless($secret !== '' && hash_equals($secret, $providedSecret), 401);

        $payload = $request->all();
        $amount = $payload['amount'] ?? $payload['transferAmount'] ?? null;
        abort_unless(is_numeric($amount) && (float) $amount > 0, 422, 'Thiếu số tiền giao dịch.');
        abort_if(isset($payload['transferType']) && strtolower((string) $payload['transferType']) !== 'in', 422, 'Webhook không phải giao dịch tiền vào.');
        if (!empty($payload['accountNumber'])) {
            $receivedAccount = preg_replace('/\D/', '', (string) $payload['accountNumber']);
            $configuredAccount = preg_replace('/\D/', '', (string) config('services.bank_transfer.account_number'));
            abort_if($receivedAccount !== $configuredAccount, 422, 'Tài khoản nhận không khớp.');
        }

        $transactionText = strtoupper(implode(' ', array_filter([
            $payload['reference'] ?? null,
            $payload['code'] ?? null,
            $payload['content'] ?? null,
            $payload['description'] ?? null,
        ])));
        preg_match_all('/(?<![A-Z0-9])[A-Z0-9]{6}(?![A-Z0-9])/', $transactionText, $matches);
        $references = array_values(array_unique($matches[0] ?? []));
        abort_if(empty($references), 422, 'Không tìm thấy mã tham chiếu thanh toán.');

        $order = DonHang::whereIn('MaThamChieuThanhToan', $references)->latest('MaDH')->firstOrFail();
        if ($order->TrangThaiThanhToan === 'Đã thanh toán') {
            return response()->json(['success' => true, 'order_id' => $order->MaDH, 'duplicate' => true]);
        }
        abort_if($order->QrHetHanLuc && $order->QrHetHanLuc->isPast(), 422, 'Mã thanh toán đã hết hạn.');
        abort_if((int) round((float) $order->TongTien) !== (int) round((float) $amount), 422, 'Số tiền không khớp.');
        $order->update(['TrangThaiThanhToan' => 'Đã thanh toán', 'NgayThanhToan' => now()]);
        return response()->json(['success' => true, 'order_id' => $order->MaDH]);
    }

    public function paymentSuccess(?int $id = null)
    {
        $id ??= (int) (request('maDH') ?: request('id') ?: session('current_order'));
        return $id ? redirect()->route('order.success', $id) : redirect()->route('home');
    }

    private function discountAmount(KhuyenMai $promotion, float $subtotal): float
    {
        $type = mb_strtolower((string) $promotion->LoaiGiamGia);
        $isPercent = in_array($type, ['phantram', 'phần trăm', 'percent'], true);
        $discount = $isPercent ? $subtotal * ((float) $promotion->GiaTriGiam / 100) : (float) $promotion->GiaTriGiam;
        if ($promotion->GiamToiDa) $discount = min($discount, (float) $promotion->GiamToiDa);
        return max(0, min($discount, $subtotal));
    }

    private function cartTotals(array $cart): array
    {
        $subtotal = (float) collect($cart)->sum(fn ($item) => $item['price'] * $item['quantity']);
        $discount = (float) session('discount', 0);
        return ['subtotal' => $subtotal, 'discount' => min($discount, $subtotal), 'total' => max(0, $subtotal - $discount), 'count' => collect($cart)->sum('quantity')];
    }
}
