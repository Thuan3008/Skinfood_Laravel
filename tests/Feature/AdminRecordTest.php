<?php

namespace Tests\Feature;

use App\Models\{NguoiDung, TaiKhoan, SanPham, DanhMuc, ThuongHieu, LoaiSP, NhaCungCap, KhuyenMai, DonHang, DanhGia, LienHe, PhieuNhap, PhieuXuat};
use App\Support\AdminResources;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AdminRecordTest extends TestCase
{
    private array $records;
    private TaiKhoan $admin;

    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');
        $this->artisan('migrate', ['--database' => 'sqlite', '--force' => true])->assertExitCode(0);
        $user = NguoiDung::create(['HoTen' => 'Test Admin']);
        $this->admin = TaiKhoan::create(['TenDangNhap' => 'admin-record-test', 'MatKhauHash' => 'secret-hash-must-not-be-shown', 'VaiTro' => 'Admin', 'MaND' => $user->MaND]);
        $category = DanhMuc::create(['TenDM' => 'Danh mục kiểm thử']);
        $brand = ThuongHieu::create(['TenTH' => 'Thương hiệu']);
        $type = LoaiSP::create(['TenLoai' => 'Loại', 'MaDM' => $category->MaDM]);
        $supplier = NhaCungCap::create(['TenNCC' => 'Nhà cung cấp']);
        $product = SanPham::create(['TenSP' => 'Sản phẩm kiểm thử', 'GiaBan' => 100000, 'SoLuongTon' => 10, 'MaDM' => $category->MaDM, 'MaLoai' => $type->MaLoai, 'MaTH' => $brand->MaTH]);
        $promotion = KhuyenMai::create(['TenChuongTrinh' => 'Ưu đãi', 'MaCode' => 'TEST', 'GiaTriGiam' => 10, 'LoaiGiamGia' => 'PhanTram']);
        $order = DonHang::create(['MaND' => $user->MaND, 'TrangThaiThanhToan' => 'Chưa thanh toán', 'NgayDat' => now()]);
        $review = DanhGia::create(['MaSP' => $product->MaSP, 'MaND' => $user->MaND, 'Diem' => 4, 'NoiDung' => 'Đánh giá thử', 'DuocApprove' => true]);
        $contact = LienHe::create(['MaND' => $user->MaND, 'HoTen' => 'Khách', 'NoiDung' => 'Cần hỗ trợ', 'NgayGui' => now()]);
        $import = PhieuNhap::create(['MaND' => $user->MaND, 'MaNCC' => $supplier->MaNCC, 'NgayNhap' => now(), 'TongTien' => 500000]);
        $export = PhieuXuat::create(['MaND' => $user->MaND, 'NgayXuat' => now(), 'TongTien' => 200000]);
        DB::table('ChiTietPhieuNhap')->insert(['MaPN' => $import->MaPN, 'MaSP' => $product->MaSP, 'SoLuong' => 5, 'GiaNhap' => 100000, 'ThanhTien' => 500000]);
        DB::table('ChiTietPhieuXuat')->insert(['MaPX' => $export->MaPX, 'MaSP' => $product->MaSP, 'SoLuong' => 2, 'DonGia' => 100000, 'ThanhTien' => 200000]);
        $this->records = ['san-pham' => $product, 'danh-muc' => $category, 'loai-sp' => $type, 'thuong-hieu' => $brand, 'nha-cung-cap' => $supplier, 'tai-khoan' => $this->admin, 'nguoi-dung' => $user, 'khuyen-mai' => $promotion, 'don-hang' => $order, 'danh-gia' => $review, 'lien-he' => $contact, 'phieu-nhap' => $import, 'phieu-xuat' => $export];
        $this->actingAs($this->admin);
    }

    private function listUrl(string $resource): string
    {
        return '/admin/'.$resource;
    }

    public function test_payment_confirmation_appears_once_in_customer_bell_and_is_private(): void
    {
        $order = $this->records['don-hang'];
        $order->update(['TongTien'=>250000]);
        $contact = $this->records['lien-he'];
        $this->post('/admin/lien-he/'.$contact->MaLH.'/reply',['PhanHoi'=>'Đã nhận liên hệ'])->assertRedirect();
        foreach ([1,2] as $attempt) $this->put('/admin/don-hang/'.$order->MaDH,['status'=>'Đã thanh toán'])->assertRedirect();
        $response = $this->getJson('/lien-he/phan-hoi')->assertOk()->assertJsonPath('unread',2)->assertJsonCount(2,'items');
        $payments = collect($response->json('items'))->where('type','payment');
        $this->assertCount(1,$payments);
        $this->assertStringContainsString('250.000',$payments->first()['message']);
        $this->get('/san-pham/'.$this->records['san-pham']->MaSP)->assertOk()->assertSee('Đơn hàng #'.$order->MaDH.' đã được xác nhận thanh toán');
        $this->get(route('orders.show',$order->MaDH))->assertOk();
        $this->getJson('/lien-he/phan-hoi')->assertJsonPath('unread',1);
        $this->assertNotNull($order->fresh()->DaDocThongBaoThanhToanLuc);
        $user = NguoiDung::create(['HoTen'=>'Người khác']);
        $other = TaiKhoan::create(['TenDangNhap'=>'payment-notification-other','MatKhauHash'=>'test','VaiTro'=>'User','MaND'=>$user->MaND]);
        $this->actingAs($other)->getJson('/lien-he/phan-hoi')->assertOk()->assertJsonPath('unread',0)->assertJsonCount(0,'items');
        $this->get(route('orders.show',$order->MaDH))->assertNotFound();
    }

    public function test_contact_conversation_preserves_history_and_enforces_user_ownership(): void
    {
        $user = NguoiDung::create(['HoTen'=>'Khách trò chuyện']);
        $account = TaiKhoan::create(['TenDangNhap'=>'chat-user','MatKhauHash'=>'test','VaiTro'=>'User','MaND'=>$user->MaND]);
        $contact = LienHe::create(['MaND'=>$user->MaND,'HoTen'=>'Khách trò chuyện','NoiDung'=>'Cần hỗ trợ đơn hàng','NgayGui'=>now()]);
        $this->post('/admin/lien-he/'.$contact->MaLH.'/reply',['PhanHoi'=>'Shop hỗ trợ bạn nhé'])->assertRedirect();
        $this->actingAs($account)->postJson(route('contact.send-message',$contact->MaLH),['message'=>'Tôi muốn đổi địa chỉ','sender'=>'admin'])->assertOk()->assertJsonCount(3,'messages')->assertJsonPath('messages.2.sender','user');
        $this->getJson(route('contact.messages',$this->records['lien-he']->MaLH))->assertNotFound();
        $this->postJson(route('contact.send-message',$this->records['lien-he']->MaLH),['message'=>'Không có quyền'])->assertNotFound();
        $this->postJson(route('contact.send-message',$contact->MaLH),['message'=>''])->assertUnprocessable();
        $this->actingAs($this->admin)->postJson(route('contact.send-message',$contact->MaLH),['message'=>'Bạn gửi địa chỉ mới giúp shop'])->assertOk()->assertJsonCount(4,'messages')->assertJsonPath('messages.1.body','Shop hỗ trợ bạn nhé');
        $this->assertEquals('Bạn gửi địa chỉ mới giúp shop',$contact->fresh()->PhanHoi);
        $this->actingAs($account)->get(route('contact.reply',$contact->MaLH))->assertOk()->assertSee('Cần hỗ trợ đơn hàng')->assertSee('Tôi muốn đổi địa chỉ')->assertSee('Bạn gửi địa chỉ mới giúp shop');
        $this->assertNotNull($contact->fresh()->DaDocPhanHoiLuc);
    }

    public function test_contact_reply_notifies_only_owner_and_is_marked_read_on_open(): void
    {
        $contact = $this->records['lien-he'];
        $message = trim('Phản hồi kiểm thử '.str_repeat('Nội dung dài. ',30));
        $this->post('/admin/lien-he/'.$contact->MaLH.'/reply',['PhanHoi'=>$message])->assertRedirect()->assertSessionHas('success');
        $this->assertEquals($message,$contact->fresh()->PhanHoi);
        $this->assertEquals('Đã phản hồi',$contact->fresh()->TrangThai);
        $this->getJson('/lien-he/phan-hoi')->assertOk()->assertJsonPath('unread',1)->assertJsonPath('items.0.message',$message);
        $this->get('/san-pham/'.$this->records['san-pham']->MaSP)->assertOk()->assertSee('SkinFood đã phản hồi liên hệ #'.$contact->MaLH);
        $otherUser = NguoiDung::create(['HoTen'=>'Người khác']);
        $other = TaiKhoan::create(['TenDangNhap'=>'other-reply-test','MatKhauHash'=>'test','VaiTro'=>'User','MaND'=>$otherUser->MaND]);
        $this->actingAs($other)->getJson('/lien-he/phan-hoi')->assertOk()->assertJsonPath('unread',0)->assertJsonCount(0,'items');
        $this->get('/lien-he/'.$contact->MaLH.'/phan-hoi')->assertNotFound();
        $this->actingAs($this->admin)->get('/lien-he/'.$contact->MaLH.'/phan-hoi')->assertOk()->assertSee($message);
        $this->getJson('/lien-he/phan-hoi')->assertOk()->assertJsonPath('unread',0);
        $this->assertNotNull($contact->fresh()->DaDocPhanHoiLuc);
        $this->post('/admin/lien-he/'.$contact->MaLH.'/reply',['PhanHoi'=>'Phản hồi mới'])->assertRedirect();
        $this->getJson('/lien-he/phan-hoi')->assertOk()->assertJsonPath('unread',1);
        auth()->logout();
        $this->getJson('/lien-he/phan-hoi')->assertUnauthorized();
    }

    public function test_pending_review_is_public_only_after_admin_approval(): void
    {
        $product = $this->records['san-pham'];
        $review = DanhGia::create(['MaSP'=>$product->MaSP,'MaND'=>$this->admin->MaND,'Diem'=>4,'NoiDung'=>'PENDING_REVIEW_VISIBILITY_123','DuocApprove'=>false]);
        $this->get('/san-pham/'.$product->MaSP)->assertOk()->assertDontSee('PENDING_REVIEW_VISIBILITY_123');
        $this->post('/admin/danh-gia/'.$review->MaDG.'/duyet')->assertRedirect();
        $this->get('/san-pham/'.$product->MaSP)->assertOk()->assertSee('PENDING_REVIEW_VISIBILITY_123');
    }

    public function test_order_popup_shows_full_read_only_details_and_only_updates_status(): void
    {
        $order = $this->records['don-hang'];
        $order->update(['TenNguoiNhan'=>'Người nhận thử', 'SoDienThoai'=>'0901234567', 'DiaChiGiaoHang'=>'Địa chỉ giao thử', 'GhiChu'=>'Giao buổi sáng', 'TongTien'=>200000, 'PhuongThucThanhToan'=>'COD']);
        DB::table('ChiTietDonHangs')->insert(['MaDH'=>$order->MaDH, 'MaSP'=>$this->records['san-pham']->MaSP, 'SoLuong'=>2, 'DonGia'=>100000]);
        foreach (['admin.records.edit','admin.records.show'] as $route) {
            $response = $this->get(route($route, ['don-hang',$order->MaDH]))->assertOk();
            foreach (['Người nhận thử','0901234567','Địa chỉ giao thử','Giao buổi sáng','Sản phẩm kiểm thử','200.000'] as $text) $response->assertSee($text);
            foreach (['TenNguoiNhan','SoDienThoai','DiaChiGiaoHang','TongTien','DonGia'] as $field) $response->assertDontSee('name="'.$field.'"', false);
        }
        $this->putJson(route('admin.records.update',['don-hang',$order->MaDH]), ['status'=>'Đã chuyển khoản - chờ xác nhận','TenNguoiNhan'=>'Không được sửa','TongTien'=>1])->assertOk();
        $this->assertEquals('Người nhận thử',$order->fresh()->TenNguoiNhan);
        $this->assertEquals(200000,$order->fresh()->TongTien);
        $this->assertEquals('Đã chuyển khoản - chờ xác nhận',$order->fresh()->TrangThaiThanhToan);
    }

    public function test_all_admin_lists_search_and_expose_sort_filter_and_page_size_controls(): void
    {
        $variables = ['san-pham'=>'products','danh-muc'=>'categories','loai-sp'=>'types','thuong-hieu'=>'brands','nha-cung-cap'=>'suppliers','tai-khoan'=>'accounts','nguoi-dung'=>'users','khuyen-mai'=>'promotions','don-hang'=>'orders','danh-gia'=>'reviews','lien-he'=>'contacts','phieu-nhap'=>'phieus','phieu-xuat'=>'phieus'];
        foreach ($variables as $resource => $variable) {
            $response = $this->get($this->listUrl($resource).'?q=NO_MATCH_98432&_sort=oldest&_per_page=20')->assertOk();
            foreach (['q','_sort'] as $field) $response->assertSee('name="'.$field.'"', false);
            $this->assertEquals(0, $response->viewData($variable)->total());
            $this->assertEquals(20, $response->viewData($variable)->perPage());
        }
    }

    public function test_list_sorting_pagination_and_query_parameters_work_together(): void
    {
        foreach (range(1, 15) as $i) ThuongHieu::create(['TenTH'=>sprintf('Brand %02d',$i), 'QuocGia'=>'Việt Nam']);
        $response = $this->get('/admin/thuong-hieu?q=Brand&f_QuocGia=Vi%E1%BB%87t%20Nam&_sort=name_asc&_per_page=10&page=2')->assertOk();
        $brands = $response->viewData('brands');
        $this->assertEquals(15, $brands->total());
        $this->assertEquals('Brand 11', $brands->first()->TenTH);
        $this->assertStringContainsString('_sort=name_asc', $brands->previousPageUrl());
        $this->assertStringContainsString('f_QuocGia=', $brands->previousPageUrl());
        $this->get('/admin/thuong-hieu?q=Brand&_sort=name_desc')->assertViewHas('brands', fn ($items) => $items->first()->TenTH === 'Brand 15');
    }

    public function test_filters_work_for_relations_statuses_dates_and_zero_values(): void
    {
        $this->get('/admin/loai-sp?f_MaDM='.$this->records['danh-muc']->MaDM)->assertOk()->assertViewHas('types', fn ($items) => $items->total() === 1);
        $this->get('/admin/tai-khoan?f_VaiTro=User')->assertOk()->assertViewHas('accounts', fn ($items) => $items->total() === 0);
        $this->get('/admin/danh-gia?f_DuocApprove=0')->assertOk()->assertViewHas('reviews', fn ($items) => $items->total() === 0);
        $this->get('/admin/danh-gia?f_Diem=4&f_DuocApprove=1')->assertOk()->assertViewHas('reviews', fn ($items) => $items->total() === 1);
        $this->get('/admin/tai-khoan?q=Test%20Admin')->assertOk()->assertViewHas('accounts', fn ($items) => $items->total() === 1);
        $this->get('/admin/phieu-nhap?_from=2000-01-01&_to=2000-01-02')->assertOk()->assertViewHas('phieus', fn ($items) => $items->total() === 0);
        $this->getJson('/admin/phieu-xuat?_from=2026-10-30&_to=2026-10-29')->assertUnprocessable()->assertJsonValidationErrors('_from');
        $this->getJson('/admin/danh-muc?_sort=DROP_TABLE')->assertUnprocessable()->assertJsonValidationErrors('_sort');
        $this->getJson('/admin/danh-muc?_per_page=999999')->assertUnprocessable()->assertJsonValidationErrors('_per_page');
    }

    public function test_restock_section_prioritizes_empty_stock_and_excludes_hidden_or_discontinued_products(): void
    {
        $empty = SanPham::create(['TenSP' => 'Hàng hết cần nhập', 'SoLuongTon' => 0]);
        SanPham::create(['TenSP' => 'Hàng ngừng bán', 'SoLuongTon' => 0, 'TrangThaiSanPham' => 'Ngừng kinh doanh']);
        $hidden = SanPham::create(['TenSP' => 'Hàng đã ẩn', 'SoLuongTon' => 0]);
        $this->deleteJson(route('admin.records.hide', ['san-pham', $hidden->MaSP]))->assertOk();
        foreach (['/admin', '/admin/san-pham'] as $url) {
            $this->get($url)->assertOk()->assertSee('Cần nhập thêm')->assertSee('Hàng hết cần nhập')
                ->assertViewHas('restock', fn ($data) => $data['empty'] === 1 && $data['low'] === 1 && $data['products']->first()->MaSP === $empty->MaSP && $data['products']->count() === 2);
        }
    }

    public function test_every_list_has_view_edit_delete_icons_and_popup_routes(): void
    {
        foreach ($this->records as $resource => $record) {
            $response = $this->get($this->listUrl($resource))->assertOk();
            foreach (['Xem', 'Sửa', 'Xóa'] as $action) $response->assertSee('aria-label="'.$action.'"', false);
            $this->get(route('admin.records.show', [$resource, $record->getKey()]))->assertOk()->assertDontSee('secret-hash-must-not-be-shown');
            $edit = $this->get(route('admin.records.edit', [$resource, $record->getKey()]));
            if (AdminResources::MAP[$resource][2]) {
                $edit->assertRedirect();
                $this->get($edit->headers->get('Location'))->assertOk();
            } else $edit->assertOk();
        }
    }

    public function test_hide_preserves_every_record_and_stock_but_removes_it_from_its_list(): void
    {
        foreach ($this->records as $resource => $record) {
            $this->deleteJson(route('admin.records.hide', [$resource, $record->getKey()]))->assertOk();
            $this->assertDatabaseHas($record->getTable(), [$record->getKeyName() => $record->getKey()]);
            $this->assertFalse($record::visibleInAdmin()->whereKey($record->getKey())->exists());
            $this->get($this->listUrl($resource))->assertOk()->assertDontSee(route('admin.records.show', [$resource, $record->getKey()]), false);
        }
        $this->assertEquals(10, $this->records['san-pham']->fresh()->SoLuongTon);
        $this->assertDatabaseCount('ChiTietPhieuNhap', 1);
        $this->assertDatabaseCount('ChiTietPhieuXuat', 1);
        $this->get('/san-pham/'.$this->records['san-pham']->MaSP)->assertOk();
    }

    public function test_legacy_delete_routes_also_only_hide_instead_of_removing_data(): void
    {
        foreach ($this->records as $resource => $record) {
            if ($resource === 'don-hang') continue;
            $this->delete('/admin/'.$resource.'/'.$record->getKey())->assertRedirect();
            $this->assertDatabaseHas($record->getTable(), [$record->getKeyName() => $record->getKey()]);
        }
        $this->assertEquals(10, $this->records['san-pham']->fresh()->SoLuongTon);
    }

    public function test_edit_receipt_adjusts_stock_by_delta_and_rejects_negative_stock(): void
    {
        $import = $this->records['phieu-nhap'];
        $product = $this->records['san-pham'];
        $url = route('admin.records.update', ['phieu-nhap', $import->MaPN]);
        $data = ['NgayNhap' => '2026-10-30', 'MaNCC' => $import->MaNCC, 'details' => [['MaSP' => $product->MaSP, 'SoLuong' => 8, 'price' => 150000]]];
        $this->putJson($url, $data)->assertOk();
        $this->assertEquals(13, $product->fresh()->SoLuongTon);
        $this->assertEquals(1200000, $import->fresh()->TongTien);
        $product->update(['SoLuongTon' => 1]);
        $data['details'][0]['SoLuong'] = 1;
        $this->putJson($url, $data)->assertUnprocessable();
        $this->assertEquals(1, $product->fresh()->SoLuongTon);
        $this->assertEquals(1200000, $import->fresh()->TongTien);
    }

    public function test_generic_order_editor_preserves_paid_state_lock_and_authentication(): void
    {
        $order = $this->records['don-hang'];
        $order->update(['TrangThaiThanhToan' => 'Đã thanh toán', 'TonKhoDaTru' => true]);
        $this->putJson(route('admin.records.update', ['don-hang', $order->MaDH]), ['status' => 'Đã hủy'])->assertUnprocessable();
        $this->get(route('admin.records.edit', ['don-hang', $order->MaDH]))->assertOk()->assertSee('Trạng thái đã được khóa.');
        $this->admin->VaiTro = 'User';
        $this->get(route('admin.records.show', ['san-pham', $this->records['san-pham']->MaSP]))->assertForbidden();
        $this->deleteJson(route('admin.records.hide', ['san-pham', $this->records['san-pham']->MaSP]))->assertForbidden();
    }

    public function test_popup_create_and_edit_save_with_validation_and_product_selection(): void
    {
        foreach (['san-pham/tao-moi', 'danh-muc/create', 'loai-sp/create', 'thuong-hieu/create', 'nha-cung-cap/create', 'tai-khoan/create', 'khuyen-mai/create', 'phieu-nhap/create', 'phieu-xuat/create'] as $path) {
            $this->get('/admin/'.$path)->assertOk();
        }
        $this->postJson('/admin/danh-muc', ['TenDM' => ''])->assertUnprocessable();
        $this->post('/admin/danh-muc', ['TenDM' => 'Danh mục mới'])->assertRedirect();
        $this->assertDatabaseHas('DanhMuc', ['TenDM' => 'Danh mục mới']);
        $promo = $this->records['khuyen-mai'];
        $this->put('/admin/khuyen-mai/'.$promo->MaKM, [
            'TenChuongTrinh' => 'Đã sửa', 'MaCode' => 'TEST', 'LoaiGiamGia' => 'PhanTram', 'GiaTriGiam' => 5,
            'TrangThai' => 1, 'products' => [$this->records['san-pham']->MaSP],
        ])->assertRedirect(route('admin.khuyen-mai.index'))->assertSessionHas('success');
        $this->assertDatabaseHas('KhuyenMai_SanPham', ['MaKM' => $promo->MaKM, 'MaSP' => $this->records['san-pham']->MaSP]);
    }

    public function test_export_form_saves_recipient_multiple_lines_and_custom_prices(): void
    {
        $first = $this->records['san-pham'];
        $second = SanPham::create(['TenSP' => 'Sản phẩm thứ hai', 'GiaBan' => 90000, 'SoLuongTon' => 3]);
        $this->get('/admin/phieu-xuat/create')->assertOk()->assertSee('Người nhận hàng')->assertSee('Thêm dòng')->assertSee('export-form.js');
        $data = ['SoDienThoaiNguoiNhan' => '0901234567', 'DiaChiNguoiNhan' => '123 Nguyễn Trãi, Hà Nội', 'NguoiNhanHang' => 'Kho Hà Nội', 'GhiChu' => 'Điều chuyển hàng', 'NgayXuat' => '2026-10-30', 'details' => [
            ['MaSP' => $first->MaSP, 'SoLuong' => 2, 'DonGia' => 150000],
            ['MaSP' => $second->MaSP, 'SoLuong' => 3, 'DonGia' => 80000],
        ], 'TongTien' => 1];
        $this->post('/admin/phieu-xuat', $data)->assertRedirect(route('admin.phieu-xuat.index'));
        $export = PhieuXuat::latest('MaPX')->first();
        $this->assertEquals('Kho Hà Nội', $export->NguoiNhanHang);
        $this->assertEquals('0901234567', $export->SoDienThoaiNguoiNhan);
        $this->assertEquals('123 Nguyễn Trãi, Hà Nội', $export->DiaChiNguoiNhan);
        $this->get('/admin/phieu-xuat')->assertOk()->assertSee('0901234567')->assertSee('Kho Hà Nội')->assertSee('Tổng số SP xuất')->assertDontSee('<th>Địa chỉ người nhận</th>', false);
        $this->assertEquals('Điều chuyển hàng', $export->GhiChu);
        $this->assertEquals(540000, $export->TongTien);
        $this->assertEquals(8, $first->fresh()->SoLuongTon);
        $this->assertEquals('Hết hàng', $second->fresh()->TrangThaiSanPham);
        $this->assertDatabaseHas('ChiTietPhieuXuat', ['MaPX' => $export->MaPX, 'MaSP' => $first->MaSP, 'DonGia' => 150000, 'ThanhTien' => 300000]);
        $this->get(route('admin.records.show', ['phieu-xuat', $export->MaPX]))->assertOk()->assertSee('Kho Hà Nội');
    }

    public function test_export_rejects_missing_recipient_duplicate_product_and_stock_shortage_without_partial_changes(): void
    {
        $product = $this->records['san-pham'];
        $base = ['NguoiNhanHang' => 'Kho nhận', 'NgayXuat' => '2026-10-30', 'details' => [['MaSP' => $product->MaSP, 'SoLuong' => 2, 'DonGia' => 150000]]];
        $missing = $base; unset($missing['NguoiNhanHang']);
        $this->postJson('/admin/phieu-xuat', $missing)->assertUnprocessable()->assertJsonValidationErrors('NguoiNhanHang');
        $duplicate = $base; $duplicate['details'][] = $base['details'][0];
        $this->postJson('/admin/phieu-xuat', $duplicate)->assertUnprocessable()->assertJsonValidationErrors('details.0.MaSP');
        $shortage = $base;
        $second = SanPham::create(['TenSP' => 'Không đủ hàng', 'SoLuongTon' => 1]);
        $shortage['details'][] = ['MaSP' => $second->MaSP, 'SoLuong' => 2, 'DonGia' => 1000];
        $this->postJson('/admin/phieu-xuat', $shortage)->assertUnprocessable()->assertJsonValidationErrors('details.1.SoLuong');
        $negative = $base; $negative['details'][0]['DonGia'] = -1;
        $this->postJson('/admin/phieu-xuat', $negative)->assertUnprocessable();
        foreach ([0, -1, 1.5] as $quantity) {
            $invalid = $base;
            $invalid['details'][0]['SoLuong'] = $quantity;
            $this->postJson('/admin/phieu-xuat', $invalid)->assertUnprocessable()->assertJsonValidationErrors('details.0.SoLuong');
            $this->putJson(route('admin.records.update', ['phieu-xuat', $this->records['phieu-xuat']->MaPX]), [
                'NguoiNhanHang' => 'Kho nhận', 'NgayXuat' => '2026-10-30',
                'details' => [['MaSP' => $product->MaSP, 'SoLuong' => $quantity, 'price' => 150000]],
            ])->assertUnprocessable()->assertJsonValidationErrors('details.0.SoLuong');
        }
        $this->assertEquals(10, $product->fresh()->SoLuongTon);
        $this->assertDatabaseCount('PhieuXuat', 1);
        $this->assertDatabaseCount('ChiTietPhieuXuat', 1);
    }
}
