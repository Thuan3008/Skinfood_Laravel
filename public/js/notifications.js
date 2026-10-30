(() => {
    'use strict';
    const container = document.getElementById('notification-toasts');
    if (!container) return;
    window.showToast = (message, type = 'success') => {
        if (typeof message !== 'string' || !message.trim()) return;
        if (!['success', 'error', 'warning', 'info'].includes(type)) type = 'info';
        // Avoid duplicate notifications from the same action.
        if ([...container.children].some(node => node.dataset.message === message && node.dataset.type === type)) return;
        const toast = document.createElement('div');
        toast.className = 'notification-toast';
        toast.dataset.type = type;
        toast.dataset.message = message;
        toast.setAttribute('role', type === 'error' ? 'alert' : 'status');
        toast.setAttribute('aria-live', type === 'error' ? 'assertive' : 'polite');
        const text = document.createElement('span');
        text.className = 'notification-toast-message';
        text.textContent = message;
        const close = document.createElement('button');
        close.type = 'button';
        close.className = 'notification-toast-close';
        close.textContent = '×';
        close.setAttribute('aria-label', 'Đóng thông báo');
        let timer;
        const dismiss = () => { clearTimeout(timer); toast.remove(); };
        const schedule = () => { clearTimeout(timer); timer = setTimeout(dismiss, type === 'error' ? 10000 : 5000); };
        close.addEventListener('click', dismiss);
        toast.addEventListener('mouseenter', () => clearTimeout(timer));
        toast.addEventListener('mouseleave', schedule);
        toast.addEventListener('focusin', () => clearTimeout(timer));
        toast.addEventListener('focusout', schedule);
        toast.append(text, close);
        container.append(toast);
        schedule();
        return toast;
    };
    try {
        const messages = JSON.parse(document.getElementById('notification-data')?.textContent || '[]');
        messages.forEach(({message, type}) => window.showToast(message, type));
        if (window.self === window.top) {
            const pending = JSON.parse(sessionStorage.getItem('admin-return-notifications') || '[]');
            sessionStorage.removeItem('admin-return-notifications');
            pending.forEach(({message, type}) => window.showToast(message, type));
        }
    } catch (_) { /* A malformed initial message must not break page actions. */ }
})();
