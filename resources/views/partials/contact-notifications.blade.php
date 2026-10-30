@php
    $notifications = auth()->check() ? \App\Services\CustomerNotifications::forUser(auth()->user()->MaND) : ['unread'=>0,'items'=>collect()];
    $replyUnread = $notifications['unread'];
    $replyItems = $notifications['items'];
@endphp
<div class="notif-container contact-notifications" @auth data-replies-url="{{ route('contact.replies') }}" @endauth>
    <a href="{{ auth()->check() ? route('contact.index') : route('login') }}" class="contact-bell" title="Thông báo" aria-label="Thông báo" aria-expanded="false">
        <i class="fa-regular fa-bell" aria-hidden="true"></i><span id="contact-reply-count" class="contact-reply-count {{ $replyUnread ? '' : 'd-none' }}">{{ $replyUnread }}</span>
    </a>
    <ul class="notif-dropdown-menu contact-reply-menu">
        <li class="notif-header p-2 bg-light fw-bold">Thông báo</li>
        <li><ul id="contact-reply-items" class="list-unstyled mb-0">
        @forelse($replyItems as $reply)
            <li><a class="contact-reply-link {{ $reply['unread'] ? 'is-unread' : '' }}" href="{{ $reply['url'] }}"><strong>{{ $reply['title'] }}</strong><span>{{ Str::limit($reply['message'], 120) }}</span><small>{{ $reply['date'] }}</small></a></li>
        @empty <li class="p-3 text-center text-muted">{{ auth()->check() ? 'Chưa có thông báo.' : 'Đăng nhập để xem thông báo.' }}</li> @endforelse
        </ul></li>
    </ul>
</div>

@push('scripts')<script src="{{ asset('js/contact-notifications.js') }}"></script>@endpush
