(() => {
    const chat = document.querySelector('.contact-conversation');
    if (!chat) return;
    const log = chat.querySelector('.contact-chat-messages');
    const form = chat.querySelector('[data-chat-form]');
    const input = form?.elements.message;
    let busy = false, sending = false, signature = '', generation = 0;
    log.scrollTop = log.scrollHeight;
    function render(messages, forceScroll = false) {
        const next = JSON.stringify(messages);
        if (next === signature) return;
        const nearEnd = log.scrollHeight - log.scrollTop - log.clientHeight < 100;
        signature = next;
        const fragment = document.createDocumentFragment();
        messages.forEach(message => {
            const article = document.createElement('article'); article.className='contact-chat-message'+(message.sender === chat.dataset.chatViewer ? ' is-mine' : '');
            const bubble = document.createElement('div'); bubble.className='contact-chat-bubble';
            const sender = document.createElement('strong'); sender.textContent=message.sender === 'admin' ? 'SkinFood' : (chat.dataset.chatViewer === 'user' ? 'Bạn' : 'Khách hàng');
            const body = document.createElement('p'); body.textContent=message.body;
            const date = document.createElement('time'); date.textContent=message.date;
            bubble.append(sender,body,date); article.append(bubble); fragment.append(article);
        });
        log.replaceChildren(fragment);
        if (nearEnd || forceScroll) log.scrollTop = log.scrollHeight;
    }
    async function refresh() {
        if (busy || sending || document.hidden) return;
        busy=true;
        const version=generation;
        try { const response=await fetch(chat.dataset.chatUrl,{headers:{Accept:'application/json'},credentials:'same-origin',cache:'no-store'}); if(response.ok){const result=await response.json(); if(version === generation)render(result.messages);} }
        catch (_) { /* Keep history and draft during connection failures. */ }
        finally { busy=false; }
    }
    form?.addEventListener('submit',async event => {
        event.preventDefault();
        if(sending || !input.value.trim()) return;
        sending=true;
        generation++;
        const button=form.querySelector('button'); button.disabled=true;
        const draft=input.value;
        try {
            const response=await fetch(form.action,{method:'POST',body:new FormData(form),headers:{Accept:'application/json'},credentials:'same-origin'});
            const result=await response.json();
            if(!response.ok) throw new Error(result.errors ? Object.values(result.errors).flat().join('\n') : result.message || 'Không gửi được tin nhắn.');
            if(input.value === draft) input.value='';
            render(result.messages,true); input.focus();
        } catch(error) { window.showToast(error.message || 'Không gửi được tin nhắn. Vui lòng thử lại.','error'); }
        finally { sending=false; button.disabled=false; }
    });
    input?.addEventListener('keydown',event => { if(event.key === 'Enter' && !event.shiftKey && !event.isComposing){event.preventDefault(); if(input.value.trim())form.requestSubmit();} });
    setInterval(refresh,5000); document.addEventListener('visibilitychange',refresh);
})();
