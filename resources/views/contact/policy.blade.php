@extends('layouts.app')

@section('title', $page === 'huong-dan-mua-hang' ? 'Hướng dẫn mua hàng' : ($page === 'chinh-sach-doi-tra' ? 'Chính sách đổi trả' : 'Chính sách bảo mật'))

@section('content')
<div class="container py-5">
@if($page === 'huong-dan-mua-hang')
    <h2 class="text-center fw-bold mb-4">
        🛒 Hướng Dẫn Mua Hàng
    </h2>

    <p class="text-center text-muted mb-5">
        Làm theo các bước đơn giản dưới đây để đặt mua sản phẩm nhanh chóng và tiện lợi.
    </p>

    <!-- Bước 1 -->
    <div class="card shadow-sm mb-4 border-0">
        <div class="card-body p-4">
            <h4 class="fw-bold">
                <span class="badge bg-primary me-2">1</span>
                Chọn sản phẩm
            </h4>
            <p class="mb-0 text-muted">
                Truy cập danh mục sản phẩm và chọn sản phẩm bạn muốn mua.
                Bạn có thể xem mô tả, giá, hình ảnh và các đánh giá từ khách hàng khác.
            </p>
        </div>
    </div>

    <!-- Bước 2 -->
    <div class="card shadow-sm mb-4 border-0">
        <div class="card-body p-4">
            <h4 class="fw-bold">
                <span class="badge bg-success me-2">2</span>
                Thêm vào giỏ hàng
            </h4>
            <p class="mb-0 text-muted">
                Nhấn nút <strong>“Thêm vào giỏ hàng”</strong> để lưu sản phẩm.
                Bạn có thể tiếp tục mua sắm hoặc chuyển sang thanh toán bất cứ lúc nào.
            </p>
        </div>
    </div>

    <!-- Bước 3 -->
    <div class="card shadow-sm mb-4 border-0">
        <div class="card-body p-4">
            <h4 class="fw-bold">
                <span class="badge bg-warning me-2 text-dark">3</span>
                Kiểm tra giỏ hàng
            </h4>
            <p class="mb-0 text-muted">
                Mở giỏ hàng của bạn để xem lại danh sách sản phẩm, số lượng, giá tiền.
                Tại đây bạn có thể cập nhật số lượng hoặc xóa sản phẩm nếu muốn.
            </p>
        </div>
    </div>

    <!-- Bước 4 -->
    <div class="card shadow-sm mb-4 border-0">
        <div class="card-body p-4">
            <h4 class="fw-bold">
                <span class="badge bg-danger me-2">4</span>
                Nhập thông tin giao hàng
            </h4>
            <p class="mb-0 text-muted">
                Điền đầy đủ họ tên, số điện thoại và địa chỉ giao hàng.
                Vui lòng kiểm tra thật kỹ để tránh sai sót khi giao hàng.
            </p>
        </div>
    </div>

    <!-- Bước 5 -->
    <div class="card shadow-sm mb-4 border-0">
        <div class="card-body p-4">
            <h4 class="fw-bold">
                <span class="badge bg-info me-2 text-white">5</span>
                Chọn phương thức thanh toán
            </h4>
            <p class="mb-0 text-muted">
                Hệ thống hỗ trợ thanh toán khi nhận hàng (COD), chuyển khoản ngân hàng hoặc quét QR Pay.
                Hãy chọn phương thức phù hợp với bạn.
            </p>
        </div>
    </div>

    <!-- Bước 6 -->
    <div class="card shadow-sm mb-4 border-0">
        <div class="card-body p-4">
            <h4 class="fw-bold">
                <span class="badge bg-dark me-2">6</span>
                Hoàn tất đơn hàng
            </h4>
            <p class="mb-0 text-muted">
                Nhấn nút <strong>“Xác nhận đặt hàng”</strong>.
                Đơn hàng của bạn sẽ được xử lý và nhân viên sẽ liên hệ để xác nhận.
            </p>
        </div>
    </div>

    <div class="text-center mt-5">
        <a href="{{ route('home') }}" class="btn btn-primary btn-lg px-4">
            Bắt đầu mua sắm ngay
        </a>
    </div>

@elseif($page === 'chinh-sach-doi-tra')
    <div class="text-center mb-4">
        <h2 class="fw-bold text-primary">Chính Sách Đổi Trả</h2>
        <p class="text-muted">Cam kết mang đến cho khách hàng trải nghiệm mua sắm tốt nhất!</p>
    </div>

    <div class="card shadow-lg border-0 rounded-4 mb-4">
        <div class="card-body p-4">
            <h4 class="fw-bold text-secondary">1. Điều kiện đổi trả</h4>
            <ul class="mt-3 mb-0">
                <li>Sản phẩm còn nguyên tem, nhãn, bao bì như lúc nhận hàng.</li>
                <li>Không bị hư hỏng do tác động bên ngoài: rơi vỡ, va đập, thấm nước…</li>
                <li>Có đầy đủ hóa đơn / mã đơn hàng.</li>
                <li>Còn trong thời hạn đổi trả quy định (xem mục phía dưới).</li>
            </ul>
        </div>
    </div>

    <div class="card shadow-lg border-0 rounded-4 mb-4">
        <div class="card-body p-4">
            <h4 class="fw-bold text-secondary">2. Thời hạn đổi trả</h4>
            <ul class="mt-3 mb-0">
                <li><strong>Đổi sản phẩm:</strong> trong vòng 7 ngày kể từ ngày nhận hàng.</li>
                <li><strong>Trả sản phẩm hoàn tiền:</strong> trong vòng 3 ngày kể từ ngày nhận hàng.</li>
                <li>Áp dụng cho tất cả sản phẩm có lỗi do nhà sản xuất.</li>
            </ul>
        </div>
    </div>

    <div class="card shadow-lg border-0 rounded-4 mb-4">
        <div class="card-body p-4">
            <h4 class="fw-bold text-secondary">3. Các trường hợp không hỗ trợ đổi trả</h4>
            <ul class="mt-3 mb-0">
                <li>Sản phẩm đã qua sử dụng.</li>
                <li>Hư hỏng do khách hàng bảo quản không đúng cách.</li>
                <li>Sản phẩm thuộc danh mục không được phép đổi trả (hàng khuyến mãi đặc biệt…).</li>
            </ul>
        </div>
    </div>

    <div class="card shadow-lg border-0 rounded-4 mb-4">
        <div class="card-body p-4">
            <h4 class="fw-bold text-secondary">4. Quy trình đổi trả</h4>
            <ol class="mt-3 mb-0">
                <li>Liên hệ với bộ phận CSKH của chúng tôi qua hotline hoặc email.</li>
                <li>Cung cấp hình ảnh sản phẩm và mô tả tình trạng.</li>
                <li>Gửi sản phẩm về địa chỉ đổi trả.</li>
                <li>Chúng tôi xác nhận và xử lý trong 1–3 ngày làm việc.</li>
            </ol>
        </div>
    </div>

    <div class="text-center mt-4">
        <a href="{{ route('home') }}" class="btn btn-primary px-4 py-2 rounded-pill shadow">
            Quay lại Trang Chủ
        </a>
    </div>

@else
    <div class="text-center mb-4">
        <h2 class="fw-bold text-primary">Chính Sách Bảo Mật</h2>
        <p class="text-muted">Chúng tôi luôn tôn trọng và bảo vệ thông tin cá nhân của khách hàng.</p>
    </div>

    <div class="card shadow-lg border-0 rounded-4 mb-4">
        <div class="card-body p-4">
            <h4 class="fw-bold text-secondary">1. Mục đích thu thập thông tin</h4>
            <p class="mt-3">Chúng tôi thu thập thông tin cá nhân của khách hàng nhằm:</p>
            <ul class="mb-0">
                <li>Xác nhận và xử lý đơn hàng.</li>
                <li>Liên hệ hỗ trợ, chăm sóc khách hàng.</li>
                <li>Cải thiện chất lượng dịch vụ và trải nghiệm mua sắm.</li>
                <li>Gửi thông báo về chương trình khuyến mãi (nếu khách hàng đồng ý).</li>
            </ul>
        </div>
    </div>

    <div class="card shadow-lg border-0 rounded-4 mb-4">
        <div class="card-body p-4">
            <h4 class="fw-bold text-secondary">2. Phạm vi sử dụng thông tin</h4>
            <p class="mt-3">Thông tin được sử dụng trong nội bộ hệ thống để:</p>
            <ul class="mb-0">
                <li>Xử lý đơn hàng và giao hàng.</li>
                <li>Gửi thông báo liên quan đến tài khoản hoặc giao dịch.</li>
                <li>Phát hiện và ngăn chặn gian lận.</li>
                <li>Cung cấp dịch vụ chăm sóc khách hàng tốt hơn.</li>
            </ul>
        </div>
    </div>

    <div class="card shadow-lg border-0 rounded-4 mb-4">
        <div class="card-body p-4">
            <h4 class="fw-bold text-secondary">3. Thời gian lưu trữ thông tin</h4>
            <p class="mt-3">Thông tin cá nhân của khách hàng sẽ được lưu trữ trong hệ thống cho đến khi:</p>
            <ul class="mb-0">
                <li>Khách hàng yêu cầu hủy, xoá.</li>
                <li>Hoặc hệ thống không còn cần thiết cho mục đích thu thập.</li>
            </ul>
        </div>
    </div>

    <div class="card shadow-lg border-0 rounded-4 mb-4">
        <div class="card-body p-4">
            <h4 class="fw-bold text-secondary">4. Cam kết không chia sẻ thông tin cho bên thứ ba</h4>
            <p class="mt-3">Chúng tôi cam kết:</p>
            <ul class="mb-0">
                <li>Không bán, trao đổi hoặc chia sẻ thông tin khách hàng cho bất kỳ bên thứ ba nào.</li>
                <li>Trừ trường hợp có yêu cầu từ cơ quan nhà nước có thẩm quyền.</li>
            </ul>
        </div>
    </div>

    <div class="card shadow-lg border-0 rounded-4 mb-4">
        <div class="card-body p-4">
            <h4 class="fw-bold text-secondary">5. Bảo mật thông tin khách hàng</h4>
            <p class="mt-3">Chúng tôi áp dụng các biện pháp bảo mật hiện đại để bảo vệ thông tin khách hàng:</p>
            <ul class="mb-0">
                <li>Mã hóa dữ liệu quan trọng.</li>
                <li>Giới hạn quyền truy cập thông tin.</li>
                <li>Sử dụng tường lửa và các kỹ thuật bảo mật khác.</li>
            </ul>
        </div>
    </div>

    <div class="card shadow-lg border-0 rounded-4 mb-4">
        <div class="card-body p-4">
            <h4 class="fw-bold text-secondary">6. Quyền của khách hàng</h4>
            <ul class="mt-3 mb-0">
                <li>Yêu cầu xem lại thông tin cá nhân.</li>
                <li>Yêu cầu chỉnh sửa thông tin không chính xác.</li>
                <li>Yêu cầu xóa thông tin khi không còn sử dụng.</li>
            </ul>
        </div>
    </div>

    <div class="text-center mt-4">
        <a href="{{ route('home') }}" class="btn btn-primary px-4 py-2 rounded-pill shadow">
            Quay lại Trang Chủ
        </a>
    </div>
@endif
</div>
@endsection
