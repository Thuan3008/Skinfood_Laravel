<?php

namespace Tests\Feature;

use App\Models\{DonHang, NguoiDung, SanPham, TaiKhoan};
use App\Services\OrderPaymentService;
use Illuminate\Support\Facades\{DB, Schema};
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class OrderStockTest extends TestCase
{
    private TaiKhoan $account;
    private SanPham $product;

    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');
        $this->artisan('migrate', ['--database' => 'sqlite', '--force' => true])->assertExitCode(0);
        $user = NguoiDung::create(['HoTen' => 'Khách thử']);
        $this->account = TaiKhoan::create(['MaND' => $user->MaND, 'TenDangNhap' => 'stock@test.local', 'MatKhauHash' => 'test', 'VaiTro' => 'Admin']);
        $this->product = SanPham::create(['TenSP' => 'Sản phẩm thử', 'GiaBan' => 100000, 'SoLuongTon' => 5]);
    }

    private function order(int $quantity = 2): DonHang
    {
        $order = DonHang::create(['MaND' => $this->account->MaND, 'TrangThaiThanhToan' => 'Chưa thanh toán', 'TongTien' => 200000, 'MaThamChieuThanhToan' => 'ABC123', 'QrHetHanLuc' => now()->addMinutes(15)]);
        DB::table('ChiTietDonHangs')->insert(['MaDH' => $order->MaDH, 'MaSP' => $this->product->MaSP, 'SoLuong' => $quantity, 'DonGia' => 100000]);
        return $order;
    }

    private function cart(): array
    {
        return [$this->product->MaSP => ['id' => $this->product->MaSP, 'name' => 'Sản phẩm thử', 'price' => 100000, 'quantity' => 2, 'image' => null]];
    }

    public function test_notifications_are_rendered_once_as_toast_data_across_pages(): void
    {
        $this->actingAs($this->account);
        foreach (['/admin/don-hang', '/gio-hang', '/dang-nhap', '/dang-ky'] as $url) {
            if ($url === '/dang-nhap') auth()->logout();
            $response = $this->withSession(['success' => 'Đã cập nhật trạng thái đơn hàng.', 'error' => 'Không thể lưu.'])->get($url)->assertOk();
            $response->assertDontSee('alert alert-success', false)->assertDontSee('alert alert-danger', false);
            preg_match('/<script id="notification-data" type="application\/json">(.*?)<\/script>/s', $response->getContent(), $matches);
            $messages = json_decode($matches[1] ?? 'null', true);
            $this->assertSame([
                ['type' => 'success', 'message' => 'Đã cập nhật trạng thái đơn hàng.'],
                ['type' => 'error', 'message' => 'Không thể lưu.'],
            ], $messages);
            $this->assertSame(1, substr_count($response->getContent(), 'id="notification-toasts"'));
        }
    }

    public function test_add_and_place_order_do_not_deduct_stock(): void
    {
        $this->actingAs($this->account)->postJson('/gio-hang/'.$this->product->MaSP)->assertOk();
        $this->assertEquals(5, $this->product->fresh()->SoLuongTon);
        $this->withSession(['cart' => $this->cart()])->post('/thanh-toan', ['recipient' => 'Khách', 'phone' => '0901234567', 'address' => 'TP HCM', 'payment' => 'COD'])->assertRedirect();
        $this->assertDatabaseCount('DonHang', 1);
        $this->assertEquals(5, $this->product->fresh()->SoLuongTon);
    }

    public function test_revenue_filter_requires_strictly_increasing_valid_dates_including_exports(): void
    {
        $this->actingAs($this->account);
        foreach ([['2026-10-30', '2026-10-29'], ['2026-10-29', '2026-10-29'], ['2026-02-30', '2026-03-01'], ['', '2026-10-30']] as [$from, $to]) {
            foreach (['', 'csv'] as $export) {
                $url = '/admin/bao-cao/doanh-thu?'.http_build_query(compact('from', 'to', 'export'));
                $this->get($url)->assertRedirect(route('admin.bao-cao.doanh-thu'))->assertSessionHasErrors('from');
                $this->getJson($url)->assertUnprocessable()->assertJsonValidationErrors('from');
            }
        }
        $this->get('/admin/bao-cao/doanh-thu?from=2026-10-01&to=2026-10-30')->assertOk()->assertViewHas('from', '2026-10-01')->assertViewHas('to', '2026-10-30');
        $this->get('/admin/bao-cao/doanh-thu?from=2026-10-01&to=2026-10-30&export=csv')->assertDownload();
    }

    public function test_stock_status_tracks_exports_restock_and_payment(): void
    {
        $this->product->decrement('SoLuongTon', 5);
        $this->assertEquals('Hết hàng', $this->product->fresh()->TrangThaiSanPham);
        $this->product->refresh()->increment('SoLuongTon', 2);
        $this->assertEquals('Còn hàng', $this->product->fresh()->TrangThaiSanPham);
        $order = $this->order(2);
        app(OrderPaymentService::class)->updateStatus($order->MaDH, 'Đã thanh toán');
        $this->assertEquals('Hết hàng', $this->product->fresh()->TrangThaiSanPham);
    }

    public function test_manual_stock_status_cannot_conflict_with_quantity_or_resume_suspended_product(): void
    {
        $this->product->update(['SoLuongTon' => 0, 'TrangThaiSanPham' => 'Còn hàng']);
        $this->assertEquals('Hết hàng', $this->product->fresh()->TrangThaiSanPham);
        $this->product->update(['SoLuongTon' => 5, 'TrangThaiSanPham' => 'Hết hàng']);
        $this->assertEquals('Còn hàng', $this->product->fresh()->TrangThaiSanPham);
        foreach (['Tạm ngưng', 'Ngừng kinh doanh'] as $status) {
            $this->product->refresh()->update(['TrangThaiSanPham' => $status]);
            $this->product->decrement('SoLuongTon', 5);
            $this->assertEquals($status, $this->product->fresh()->TrangThaiSanPham);
            $this->product->refresh()->increment('SoLuongTon', 5);
            $this->assertEquals($status, $this->product->fresh()->TrangThaiSanPham);
        }
    }

    public function test_admin_confirmation_deducts_only_once(): void
    {
        $order = $this->order();
        $this->actingAs($this->account);
        foreach (['Đã thanh toán', 'Đã thanh toán'] as $status) {
            $this->put('/admin/don-hang/'.$order->MaDH, ['status' => $status])->assertRedirect();
        }
        $this->assertEquals(3, $this->product->fresh()->SoLuongTon);
        $this->assertTrue($order->fresh()->TonKhoDaTru);
    }

    public function test_paid_orders_cannot_be_reverted_or_cancelled(): void
    {
        $order = $this->order();
        app(OrderPaymentService::class)->updateStatus($order->MaDH, 'Đã thanh toán');
        $paidAt = $order->fresh()->NgayThanhToan;
        $this->actingAs($this->account);
        foreach (['Chưa thanh toán', 'Đã chuyển khoản - chờ xác nhận', 'Đã hủy', 'invalid'] as $status) {
            $this->putJson('/admin/don-hang/'.$order->MaDH, ['status' => $status])->assertUnprocessable()->assertJsonValidationErrors('status');
            $this->assertEquals('Đã thanh toán', $order->fresh()->TrangThaiThanhToan);
        }
        $this->assertEquals($paidAt, $order->fresh()->NgayThanhToan);
        $this->assertEquals(3, $this->product->fresh()->SoLuongTon);
        $this->get('/admin/don-hang')->assertOk()->assertDontSee('data-order-id="'.$order->MaDH.'"', false);
    }

    public function test_only_unpaid_and_pending_orders_can_be_cancelled(): void
    {
        $this->actingAs($this->account);
        foreach (['Chưa thanh toán', 'Đã chuyển khoản - chờ xác nhận'] as $status) {
            $order = $this->order();
            $order->update(['TrangThaiThanhToan' => $status]);
            $this->putJson('/admin/don-hang/'.$order->MaDH, ['status' => 'Đã hủy'])->assertRedirect();
            $this->assertEquals('Đã hủy', $order->fresh()->TrangThaiThanhToan);
            $this->putJson('/admin/don-hang/'.$order->MaDH, ['status' => 'Đã thanh toán'])->assertUnprocessable();
        }
        $this->assertEquals(5, $this->product->fresh()->SoLuongTon);
    }

    public function test_webhook_and_admin_confirmation_share_one_stock_deduction(): void
    {
        config(['services.bank_transfer.webhook_secret' => 'stock-secret']);
        $order = $this->order();
        $this->postJson('/api/thanh-toan/webhook', ['reference' => 'ABC123', 'amount' => 200000], ['X-Webhook-Secret' => 'stock-secret'])->assertOk();
        $this->postJson('/api/thanh-toan/webhook', ['reference' => 'ABC123', 'amount' => 200000], ['X-Webhook-Secret' => 'stock-secret'])->assertOk()->assertJson(['duplicate' => true]);
        app(OrderPaymentService::class)->updateStatus($order->MaDH, 'Đã thanh toán');
        $this->assertEquals(3, $this->product->fresh()->SoLuongTon);
    }

    public function test_customer_reporting_transfer_does_not_deduct_stock(): void
    {
        $order = $this->order();
        $order->update(['PhuongThucThanhToan' => 'QR']);
        $this->actingAs($this->account)->postJson('/thanh-toan/xac-nhan/'.$order->MaDH)->assertOk();
        $this->assertEquals(5, $this->product->fresh()->SoLuongTon);
        $this->assertFalse($order->fresh()->TonKhoDaTru);
    }

    public function test_two_orders_cannot_use_the_same_remaining_stock(): void
    {
        $first = $this->order(4);
        $second = $this->order(4);
        app(OrderPaymentService::class)->updateStatus($first->MaDH, 'Đã thanh toán');
        $this->actingAs($this->account)->put('/admin/don-hang/'.$second->MaDH, ['status' => 'Đã thanh toán'])->assertSessionHasErrors('stock');
        $this->assertEquals(1, $this->product->fresh()->SoLuongTon);
        $this->assertFalse($second->fresh()->TonKhoDaTru);
    }

    public function test_insufficient_stock_rolls_back_all_lines_and_payment(): void
    {
        $order = $this->order();
        $other = SanPham::create(['TenSP' => 'Hết hàng', 'SoLuongTon' => 0]);
        DB::table('ChiTietDonHangs')->insert(['MaDH' => $order->MaDH, 'MaSP' => $other->MaSP, 'SoLuong' => 1]);
        try {
            app(OrderPaymentService::class)->updateStatus($order->MaDH, 'Đã thanh toán');
            $this->fail('Expected insufficient stock');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('stock', $e->errors());
        }
        $this->assertEquals(5, $this->product->fresh()->SoLuongTon);
        $this->assertEquals('Chưa thanh toán', $order->fresh()->TrangThaiThanhToan);
        $this->assertFalse($order->fresh()->TonKhoDaTru);
    }

    public function test_admin_export_is_reflected_in_cart_and_blocks_checkout(): void
    {
        $this->actingAs($this->account)->withSession(['cart' => $this->cart()]);
        $this->post('/admin/phieu-xuat', ['NguoiNhanHang' => 'Kho nhận', 'NgayXuat' => now()->toDateString(), 'details' => [['MaSP' => $this->product->MaSP, 'SoLuong' => 5, 'DonGia' => 100000]]])->assertRedirect();
        $this->assertEquals(0, $this->product->fresh()->SoLuongTon);
        $this->get('/gio-hang')->assertOk()->assertSee('Hết hàng');
        $this->getJson('/gio-hang/ton-kho/'.$this->product->MaSP)->assertOk()->assertJson(['available' => 0]);
        $this->get('/thanh-toan/xem-lai')->assertRedirect(route('cart.index'));
        $this->post('/thanh-toan', ['recipient' => 'Khách', 'phone' => '0901234567', 'address' => 'TP HCM', 'payment' => 'COD'])->assertSessionHasErrors('stock');
        $this->assertDatabaseCount('DonHang', 0);
    }

    public function test_out_of_stock_products_follow_in_stock_products_before_pagination(): void
    {
        SanPham::create(['TenSP' => 'Hết hàng mới nhất', 'SoLuongTon' => 0, 'GiaBan' => 1]);
        foreach (['newest', 'oldest', 'price_asc', 'price_desc', 'name_asc'] as $sort) {
            $this->get('/tat-ca-san-pham?sort='.$sort)->assertOk()->assertViewHas('products', fn ($products) => $products->first()->MaSP === $this->product->MaSP);
        }
    }

    public function test_migration_restores_old_unpaid_stock_and_preserves_paid_orders(): void
    {
        Schema::table('DonHang', fn ($table) => $table->dropColumn('TonKhoDaTru'));
        $unpaid = $this->order();
        $paid = $this->order(1);
        $paid->update(['TrangThaiThanhToan' => 'Đã thanh toán']);
        (require database_path('migrations/2026_10_30_000400_defer_order_stock_until_payment.php'))->up();
        $this->assertEquals(7, $this->product->fresh()->SoLuongTon);
        $this->assertFalse($unpaid->fresh()->TonKhoDaTru);
        $this->assertTrue($paid->fresh()->TonKhoDaTru);
    }
}
