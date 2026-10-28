# SkinFood Laravel

Website bán mỹ phẩm viết bằng Laravel, Blade, Bootstrap và MySQL Laragon. Project chính nằm tại `E:\HK7\PHP\DACK\laravel-app`.

## Chạy project

1. Mở Laragon và bật MySQL.
2. Mở Terminal của Laragon tại thư mục project.
3. Chạy:

```bash
composer install
php artisan key:generate
php artisan migrate --seed
php artisan serve --port=8001
```

4. Mở `http://127.0.0.1:8001`.

Nếu database đã có dữ liệu thì chỉ cần chạy `php artisan migrate` rồi `php artisan serve --port=8001`.

## Tài khoản mẫu

| Vai trò | Tên đăng nhập | Mật khẩu |
|---|---|---|
| Admin | admin@skinfood.com | 123456 |
| User | user@skinfood.com | 123456 |

Mã giảm giá mẫu: `WELCOME10` và `GIAM50K`.

## Thanh toán VietQR

Thông tin ngân hàng được cấu hình trong `.env`:

```env
BANK_BIN=970422
BANK_NAME="MB Bank"
BANK_ACCOUNT_NUMBER=06030082005
BANK_ACCOUNT_NAME="SKINFOOD SHOP"
BANK_CONTENT_PREFIX=SKINFOOD
BANK_WEBHOOK_SECRET=thay-bang-chuoi-bi-mat-khi-trien-khai
VIETQR_IMAGE_BASE_URL=https://img.vietqr.io/image
```

QR được tạo theo đúng tổng tiền đơn hàng, có mã tham chiếu riêng và hết hạn sau 15 phút. Khi tải lại hoặc mở lại trang, hệ thống tạo mã mới. QR chỉ biến mất khi webhook xác nhận đơn đã thanh toán hoặc khi hết hạn.

Endpoint nhận giao dịch:

```text
POST /api/thanh-toan/webhook
```

Webhook hỗ trợ body đơn giản `{reference, amount}` và body giao dịch của SePay gồm `content`, `transferAmount`, `transferType`, `accountNumber`. Có thể xác thực bằng một trong hai cách:

```text
X-Webhook-Secret: <BANK_WEBHOOK_SECRET>
Authorization: Apikey <BANK_WEBHOOK_SECRET>
```

Để tự xác nhận bằng giao dịch thật, cần đưa website lên URL HTTPS công khai, kết nối tài khoản ngân hàng với SePay hoặc nhà cung cấp tương đương, rồi cấu hình webhook “Có tiền vào” trỏ tới endpoint trên. Nội dung chuyển khoản phải giữ nguyên mã do hệ thống sinh.

## Kiểm thử

```bash
php artisan test
```

Báo cáo đồ án, ERD, UML Use Case và ảnh chụp màn hình nằm trong thư mục `docs`.
