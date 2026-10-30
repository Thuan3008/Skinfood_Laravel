<?php

namespace Tests\Feature;

use App\Models\{NguoiDung, TaiKhoan};
use Illuminate\Support\Facades\{DB, Hash};
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');
        $this->artisan('migrate', ['--database' => 'sqlite', '--force' => true])->assertExitCode(0);
    }

    private function validData(): array
    {
        return [
            'HoTen' => 'Nguyễn Quỳnh Như', 'TenDangNhap' => 'new-user@example.com',
            'MatKhau' => 'SkinFood123', 'NhapLaiMatKhau' => 'SkinFood123',
            'SoDienThoai' => '0901234567', 'DiaChi' => '140 Lê Trọng Tấn',
            'GioiTinh' => 'Nữ', 'NgaySinh' => '2000-02-29', 'Avatar' => '4.jpg',
        ];
    }

    public function test_registration_saves_validated_profile_hashes_password_and_cannot_assign_admin_role(): void
    {
        $this->post('/dang-ky', array_replace($this->validData(), [
            'HoTen' => '  Nguyễn Quỳnh Như  ', 'TenDangNhap' => ' NEW-USER@EXAMPLE.COM ',
            'VaiTro' => 'Admin', 'TrangThai' => 'inactive',
        ]))->assertRedirect('/')->assertSessionHas('success');

        $account = TaiKhoan::firstOrFail();
        $this->assertSame('new-user@example.com', $account->TenDangNhap);
        $this->assertSame('User', $account->VaiTro);
        $this->assertSame('active', $account->TrangThai);
        $this->assertTrue(Hash::check('SkinFood123', $account->MatKhauHash));
        $this->assertSame('Nguyễn Quỳnh Như', $account->nguoiDung->HoTen);
        $this->assertSame('4.jpg', $account->nguoiDung->Avatar);
        $this->assertSame('2000-02-29', $account->nguoiDung->NgaySinh->format('Y-m-d'));
        $this->assertAuthenticatedAs($account);
    }

    #[DataProvider('invalidRegistrationData')]
    public function test_invalid_registration_is_rejected_without_creating_any_records(array $changes, string $field): void
    {
        $this->from('/dang-ky')->post('/dang-ky', array_replace($this->validData(), $changes))
            ->assertRedirect('/dang-ky')->assertSessionHasErrors($field);
        $this->assertSame(0, NguoiDung::count());
        $this->assertSame(0, TaiKhoan::count());
        $this->assertGuest();
    }

    public static function invalidRegistrationData(): array
    {
        return [
            'empty name' => [['HoTen' => '   '], 'HoTen'],
            'short name' => [['HoTen' => 'A'], 'HoTen'],
            'long name' => [['HoTen' => str_repeat('a', 101)], 'HoTen'],
            'email format' => [['TenDangNhap' => 'not-an-email'], 'TenDangNhap'],
            'email array' => [['TenDangNhap' => ['user@example.com']], 'TenDangNhap'],
            'email length' => [['TenDangNhap' => str_repeat('a', 60).'@'.str_repeat('b', 40).'.com'], 'TenDangNhap'],
            'short password' => [['MatKhau' => 'Abc1234', 'NhapLaiMatKhau' => 'Abc1234'], 'MatKhau'],
            'no uppercase' => [['MatKhau' => 'skinfood123'], 'MatKhau'],
            'no lowercase' => [['MatKhau' => 'SKINFOOD123'], 'MatKhau'],
            'no number' => [['MatKhau' => 'SkinFoodOnly'], 'MatKhau'],
            'long password' => [['MatKhau' => 'Ab1'.str_repeat('x', 70)], 'MatKhau'],
            'multibyte password limit' => [['MatKhau' => 'Ab1'.str_repeat('ắ', 24)], 'MatKhau'],
            'password confirmation' => [['NhapLaiMatKhau' => 'Different123'], 'NhapLaiMatKhau'],
            'missing confirmation' => [['NhapLaiMatKhau' => null], 'NhapLaiMatKhau'],
            'phone format' => [['SoDienThoai' => 'abc0901234567'], 'SoDienThoai'],
            'phone too short' => [['SoDienThoai' => '090123'], 'SoDienThoai'],
            'address length' => [['DiaChi' => str_repeat('a', 256)], 'DiaChi'],
            'invalid gender' => [['GioiTinh' => 'invalid'], 'GioiTinh'],
            'invalid calendar date' => [['NgaySinh' => '2001-02-29'], 'NgaySinh'],
            'future birthday' => [['NgaySinh' => '2999-01-01'], 'NgaySinh'],
            'avatar traversal' => [['Avatar' => '../image.jpg'], 'Avatar'],
        ];
    }

    public function test_duplicate_email_is_rejected_after_normalization(): void
    {
        $person = NguoiDung::create(['HoTen' => 'Existing user']);
        TaiKhoan::create(['TenDangNhap' => 'new-user@example.com', 'MaND' => $person->MaND, 'MatKhauHash' => Hash::make('SkinFood123'), 'VaiTro' => 'User']);
        $this->post('/dang-ky', array_replace($this->validData(), ['TenDangNhap' => ' NEW-USER@EXAMPLE.COM ']))
            ->assertSessionHasErrors('TenDangNhap');
        $this->assertSame(1, TaiKhoan::count());
        $this->assertSame(1, NguoiDung::count());
    }

    public function test_optional_fields_can_be_empty_and_existing_input_aliases_still_work(): void
    {
        $this->post('/dang-ky', ['name' => 'Khách mới', 'username' => 'aliases@example.com', 'password' => 'SkinFood123', 'password_confirmation' => 'SkinFood123'])
            ->assertRedirect('/');
        $person = NguoiDung::firstOrFail();
        $this->assertNull($person->NgaySinh);
        $this->assertNull($person->SoDienThoai);
        $this->assertSame('1.jpg', $person->Avatar);
        $this->assertAuthenticated();
    }

    public function test_errors_preserve_safe_input_and_avatar_but_never_flash_passwords(): void
    {
        $this->from('/dang-ky')->post('/dang-ky', array_replace($this->validData(), ['TenDangNhap' => 'invalid-email', 'password' => 'SecretAlias123', 'password_confirmation' => 'SecretAlias123']))
            ->assertSessionHasErrors('TenDangNhap');
        $oldInput = session('_old_input', []);
        foreach (['MatKhau', 'NhapLaiMatKhau', 'password', 'password_confirmation'] as $field) {
            $this->assertArrayNotHasKey($field, $oldInput);
        }
        $this->assertSame('4.jpg', $oldInput['Avatar']);
        $this->assertTrue(session('errors')->has('TenDangNhap'));
        $this->withCookie(session()->getName(), session()->getId())
            ->get('/dang-ky')->assertOk()->assertViewHas('errors', fn ($errors) => $errors->has('TenDangNhap'))->assertSee('Email đăng nhập không đúng định dạng.')
            ->assertSee('id="TenDangNhap-error"', false)->assertSee('id="notification-data"', false)
            ->assertDontSee('SkinFood123')->assertDontSee('SecretAlias123');
    }

    public function test_json_validation_and_international_phone_number(): void
    {
        $this->postJson('/dang-ky', [])->assertUnprocessable()->assertJsonValidationErrors(['HoTen', 'TenDangNhap', 'MatKhau', 'NhapLaiMatKhau']);
        $this->post('/dang-ky', array_replace($this->validData(), ['SoDienThoai' => '+84901234567']))->assertRedirect('/');
        $this->assertSame('+84901234567', NguoiDung::firstOrFail()->SoDienThoai);
    }

    public function test_password_whitespace_is_preserved_during_registration_and_login(): void
    {
        $password = ' SkinFood123 ';
        $this->post('/dang-ky', array_replace($this->validData(), ['MatKhau' => $password, 'NhapLaiMatKhau' => $password]))->assertRedirect('/');
        $account = TaiKhoan::firstOrFail();
        $this->assertTrue(Hash::check($password, $account->MatKhauHash));
        $this->assertFalse(Hash::check(trim($password), $account->MatKhauHash));
        $this->post('/dang-xuat')->assertRedirect('/');
        $this->post('/dang-nhap', ['TenDangNhap' => $account->TenDangNhap, 'MatKhau' => $password])->assertRedirect('/');
        $this->assertAuthenticatedAs($account);
    }
}
