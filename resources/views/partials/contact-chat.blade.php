@php
    $isAdminChat = $isAdminChat ?? false;
    $canReply = $canReply ?? true;
    $viewer = $isAdminChat ? 'admin' : 'user';
    $messages = \App\Services\ContactConversation::messages($contact);
@endphp
<section class="contact-conversation" data-chat-url="{{ route('contact.messages',$contact->MaLH) }}" data-chat-viewer="{{ $viewer }}">
    <header class="contact-chat-header"><span class="contact-chat-avatar"><i class="fa-solid {{ $isAdminChat ? 'fa-user' : 'fa-headset' }}" aria-hidden="true"></i></span><div><h2>{{ $isAdminChat ? $contact->HoTen : 'Hỗ trợ SkinFood' }}</h2><p>Cuộc trò chuyện #{{ $contact->MaLH }} · {{ $isAdminChat ? ($contact->Email ?? 'Khách hàng') : 'Trao đổi trực tiếp với cửa hàng' }}</p></div></header>
    <div class="contact-chat-messages" role="log" aria-label="Lịch sử trò chuyện" aria-live="polite" aria-relevant="additions">
    @foreach($messages as $message)
        <article class="contact-chat-message {{ $message['sender'] === $viewer ? 'is-mine' : '' }}"><div class="contact-chat-bubble"><strong>{{ $message['sender'] === 'admin' ? 'SkinFood' : ($isAdminChat ? $contact->HoTen : 'Bạn') }}</strong><p>{{ $message['body'] }}</p><time>{{ $message['date'] }}</time></div></article>
    @endforeach
    </div>
    @if($canReply)
    <form method="post" action="{{ route('contact.send-message',$contact->MaLH) }}" data-chat-form class="contact-chat-compose">
        @csrf
        <label for="contact-message-input" class="visually-hidden">Nội dung tin nhắn</label>
        <textarea name="message" id="contact-message-input" rows="2" maxlength="5000" placeholder="Nhập tin nhắn…" required></textarea>
        <button type="submit" class="btn btn-primary" aria-label="Gửi tin nhắn"><i class="fa-solid fa-paper-plane" aria-hidden="true"></i><span>Gửi</span></button>
        <small>Enter để gửi · Shift + Enter để xuống dòng</small>
    </form>
    @endif
</section>
@push('scripts')<script src="{{ asset('js/contact-chat.js') }}"></script>@endpush
