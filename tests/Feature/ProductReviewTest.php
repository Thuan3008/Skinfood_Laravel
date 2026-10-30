<?php

namespace Tests\Feature;

use App\Models\TaiKhoan;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ProductReviewTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');
        Schema::create('SanPham', function (Blueprint $table) {
            $table->increments('MaSP');
        });
        Schema::create('DonHang', function (Blueprint $table) {
            $table->increments('MaDH');
            $table->integer('MaND');
            $table->string('TrangThaiThanhToan');
        });
        Schema::create('ChiTietDonHangs', function (Blueprint $table) {
            $table->integer('MaDH');
            $table->integer('MaSP');
        });
        Schema::create('DanhGia', function (Blueprint $table) {
            $table->increments('MaDG');
            $table->integer('MaSP');
            $table->integer('MaND');
            $table->integer('Diem');
            $table->text('NoiDung');
            $table->dateTime('NgayDanhGia');
            $table->boolean('DuocApprove');
        });
        DB::table('SanPham')->insert(['MaSP' => 1]);
    }

    private function purchase(int $userId, int $productId, string $status): void
    {
        $id = DB::table('DonHang')->insertGetId(['MaND' => $userId, 'TrangThaiThanhToan' => $status], 'MaDH');
        DB::table('ChiTietDonHangs')->insert(['MaDH' => $id, 'MaSP' => $productId]);
    }

    private function signIn(): void
    {
        $this->actingAs(new TaiKhoan(['MaTK' => 1, 'MaND' => 1]));
    }

    public function test_guest_must_sign_in(): void
    {
        $this->post('/san-pham/1/danh-gia')->assertRedirect(route('login'));
    }

    public function test_non_buyer_review_waits_for_approval_even_with_another_users_purchase(): void
    {
        $this->signIn();
        $this->purchase(2, 1, 'Đã thanh toán');
        $this->purchase(1, 2, 'Đã thanh toán');
        $this->post('/san-pham/1/danh-gia', ['Diem' => 5, 'NoiDung' => 'Tốt'])->assertRedirect()->assertSessionHas('review_pending', true);
        $this->assertDatabaseHas('DanhGia', ['MaSP'=>1, 'MaND'=>1, 'DuocApprove'=>0]);
    }

    public function test_unpaid_order_review_requires_approval(): void
    {
        $this->signIn();
        $this->purchase(1, 1, 'Chưa thanh toán');
        $this->post('/san-pham/1/danh-gia', ['Diem' => 4, 'NoiDung' => 'Tốt'])->assertRedirect()->assertSessionHas('review_pending', true);
        $this->assertDatabaseHas('DanhGia', ['MaSP'=>1, 'DuocApprove'=>0]);
    }

    public function test_buyer_can_publish_selected_rating_and_content_immediately(): void
    {
        $this->signIn();
        $this->purchase(1, 1, 'Đã thanh toán');
        $this->post('/san-pham/1/danh-gia', ['Diem' => 3, 'NoiDung' => 'Sản phẩm ổn'])
            ->assertRedirect(route('home.product', 1).'#danhgia')
            ->assertSessionHas('review_submitted', true);
        $this->assertDatabaseHas('DanhGia', ['MaND' => 1, 'MaSP' => 1, 'Diem' => 3, 'NoiDung' => 'Sản phẩm ổn', 'DuocApprove' => 1]);
    }

    public function test_invalid_rating_and_empty_content_are_rejected(): void
    {
        $this->signIn();
        $this->purchase(1, 1, 'Đã thanh toán');
        $this->post('/san-pham/1/danh-gia', ['Diem' => 6, 'NoiDung' => ''])
            ->assertSessionHasErrors(['rating', 'content']);
        $this->assertDatabaseCount('DanhGia', 0);
    }
}
