@php
    $toastMessages = [];
    foreach (['success', 'error', 'warning', 'info', 'status'] as $type) {
        if (session()->has($type) && is_string(session($type))) {
            $toastMessages[] = ['type' => $type === 'status' ? 'info' : $type, 'message' => session($type)];
        }
    }
    if (session('review_submitted')) $toastMessages[] = ['type' => session('review_pending') ? 'info' : 'success', 'message' => session('review_pending') ? 'Đã gửi đánh giá. Đánh giá sẽ hiển thị sau khi quản trị viên duyệt.' : 'Đánh giá của bạn đã được đăng.'];
    foreach ($errors->all() as $message) $toastMessages[] = ['type' => 'error', 'message' => $message];
    $toastMessages = array_values(array_unique($toastMessages, SORT_REGULAR));
@endphp
<link rel="stylesheet" href="{{ asset('css/notifications.css') }}">
<div id="notification-toasts" class="notification-toasts" aria-label="Thông báo"></div>
<script id="notification-data" type="application/json">@json($toastMessages)</script>
<script src="{{ asset('js/notifications.js') }}"></script>
