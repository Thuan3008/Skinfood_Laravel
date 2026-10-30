(() => {
    const widget = document.querySelector('.contact-notifications');
    if (!widget) return;
    const bell = widget.querySelector('.contact-bell');
    let busy = false;
    bell.addEventListener('click', event => {
        if (!widget.dataset.repliesUrl) return;
        event.preventDefault(); event.stopImmediatePropagation();
        widget.classList.toggle('is-open');
        bell.setAttribute('aria-expanded', String(widget.classList.contains('is-open')));
        refresh();
    });
    document.addEventListener('click', event => {
        if (!widget.contains(event.target)) { widget.classList.remove('is-open'); bell.setAttribute('aria-expanded','false'); }
    });
    widget.addEventListener('keydown', event => { if (event.key === 'Escape') { widget.classList.remove('is-open'); bell.setAttribute('aria-expanded','false'); bell.focus(); } });
    async function refresh() {
        if (busy || document.hidden || !widget.dataset.repliesUrl) return;
        busy = true;
        try {
            const response = await fetch(widget.dataset.repliesUrl, {headers:{Accept:'application/json'}, credentials:'same-origin', cache:'no-store'});
            if (!response.ok) return;
            const result = await response.json();
            const badge = document.getElementById('contact-reply-count');
            badge.textContent = result.unread;
            badge.classList.toggle('d-none', !result.unread);
            const items = document.getElementById('contact-reply-items');
            const fragment = document.createDocumentFragment();
            result.items.forEach(item => {
                const li = document.createElement('li');
                const link = document.createElement('a');
                link.href = item.url; link.className = 'contact-reply-link' + (item.unread ? ' is-unread' : '');
                const title = document.createElement('strong'); title.textContent = item.title || `SkinFood đã phản hồi liên hệ #${item.id}`;
                const message = document.createElement('span'); message.textContent = item.message.length > 120 ? item.message.slice(0,120)+'…' : item.message;
                const date = document.createElement('small'); date.textContent = item.date || '';
                link.append(title,message,date); li.append(link); fragment.append(li);
            });
            if (!result.items.length) { const li = document.createElement('li'); li.className='p-3 text-center text-muted'; li.textContent='Chưa có thông báo.'; fragment.append(li); }
            items.replaceChildren(fragment);
        } catch (_) { /* Retain existing notifications on a temporary connection failure. */ }
        finally { busy = false; }
    }
    if (widget.dataset.repliesUrl) { setInterval(refresh,15000); document.addEventListener('visibilitychange',refresh); refresh(); }
})();
