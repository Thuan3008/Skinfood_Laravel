(() => {
    'use strict';
    const embedded = window.self !== window.top;
    const modalElement = document.getElementById('adminRecordModal');
    const frame = document.getElementById('adminRecordFrame');
    const loading = document.getElementById('adminPopupLoading');
    frame.addEventListener('load', () => { if (loading) loading.hidden = true; frame.removeAttribute('aria-busy'); });
    const modal = embedded ? null : new bootstrap.Modal(modalElement);
    if (!embedded) {
        window.closeAdminPopup = () => modal.hide();
        window.adminPopupSaved = messages => {
            sessionStorage.setItem('admin-return-notifications', JSON.stringify(messages));
            window.location.reload();
        };
        modalElement.addEventListener('hidden.bs.modal', () => frame.removeAttribute('src'));
    }

    document.addEventListener('click', event => {
        const link = event.target.closest('a[href]');
        if (!link) return;
        if (embedded && (link.hasAttribute('data-close-admin-popup') || /^(Hủy|Hủy bỏ|Quay lại)$/.test(link.textContent.trim()))) {
            event.preventDefault(); window.parent.closeAdminPopup(); return;
        }
        const url = new URL(link.href, location.href);
        if (url.origin !== location.origin || !url.pathname.startsWith('/admin/')) return;
        const popupLink = link.hasAttribute('data-admin-popup') || /\/(create|edit|tao-moi|sua)$/.test(url.pathname);
        if (!embedded && popupLink) {
            event.preventDefault();
            document.getElementById('adminRecordModalTitle').textContent = link.title || link.textContent.trim() || 'Thông tin';
            if (loading) loading.hidden = false;
            frame.setAttribute('aria-busy', 'true');
            frame.src = url.href;
            modal.show();
        }
    });

    // Registered after action-confirmation.js so Cancel stops this handler too.
    document.addEventListener('submit', async event => {
        const form = event.target;
        if (!(form instanceof HTMLFormElement) || form.method.toLowerCase() === 'get') return;
        if (form.hasAttribute('data-chat-form')) return;
        if (!embedded && !form.hasAttribute('data-admin-hide')) return;
        event.preventDefault();
        event.stopImmediatePropagation();
        if (form.dataset.saving === 'true') return;
        form.dataset.saving = 'true';
        form.querySelectorAll('.admin-form-error').forEach(error => error.remove());
        form.querySelectorAll('[aria-invalid="true"]').forEach(field => { field.removeAttribute('aria-invalid'); field.classList.remove('is-invalid'); });
        const button = event.submitter;
        const originalButtonContent = button?.innerHTML;
        form.setAttribute('aria-busy', 'true');
        try {
            const body = new FormData(form);
            if (button?.name) body.append(button.name, button.value);
            if (button) { button.disabled = true; button.innerHTML = '<span class="spinner-border spinner-border-sm me-2" aria-hidden="true"></span>Đang lưu…'; }
            const response = await fetch(form.action, {method: 'POST', body, headers: {'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest'}, credentials: 'same-origin'});
            const json = response.headers.get('content-type')?.includes('application/json');
            const payload = json ? await response.json() : null;
            if (!response.ok) {
                if (payload?.errors) Object.entries(payload.errors).forEach(([key, errors]) => {
                    const parts = key.split('.');
                    const name = parts.shift() + parts.map(part => `[${part}]`).join('');
                    const field = form.elements.namedItem(name);
                    if (field instanceof HTMLElement) {
                        field.setAttribute('aria-invalid', 'true'); field.classList.add('is-invalid');
                        const hint = document.createElement('div'); hint.className = 'admin-form-error'; hint.textContent = errors.join(' '); field.after(hint);
                    }
                });
                const messages = payload?.errors ? [...new Set(Object.values(payload.errors).flat())] : [payload?.message || 'Không thể lưu. Vui lòng thử lại.'];
                messages.forEach(message => window.showToast(message, 'error'));
                return;
            }
            let messages;
            if (json) {
                if (payload.success === false) { window.showToast(payload.message || 'Không thể lưu.', 'error'); return; }
                messages = [{type: 'success', message: payload.message || 'Đã lưu thay đổi.'}];
            } else {
                if (new URL(response.url).pathname === '/dang-nhap') { window.showToast('Phiên đăng nhập đã hết hạn. Vui lòng đăng nhập lại.', 'error'); return; }
                const page = new DOMParser().parseFromString(await response.text(), 'text/html');
                messages = JSON.parse(page.getElementById('notification-data')?.textContent || '[]');
                if (messages.some(item => item.type === 'error')) {
                    messages.forEach(item => window.showToast(item.message, item.type)); return;
                }
                if (!response.redirected && !messages.some(item => item.type === 'success')) {
                    window.showToast('Chưa lưu được thay đổi. Vui lòng kiểm tra lại thông tin.', 'error'); return;
                }
                if (!messages.length) messages = [{type: 'success', message: 'Đã lưu thay đổi.'}];
            }
            (embedded ? window.parent : window).adminPopupSaved(messages);
        } catch (_) {
            window.showToast('Không thể kết nối. Dữ liệu trong popup vẫn được giữ, vui lòng thử lại.', 'error');
        } finally {
            delete form.dataset.saving;
            form.removeAttribute('aria-busy');
            if (button) { button.disabled = false; button.innerHTML = originalButtonContent; }
        }
    }, true);
})();
