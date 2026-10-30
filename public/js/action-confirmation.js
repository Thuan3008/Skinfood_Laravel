(() => {
    'use strict';
    const confirmed = new WeakSet();

    document.addEventListener('submit', event => {
        const form = event.target;
        if (!(form instanceof HTMLFormElement) || form.method.toLowerCase() === 'get') return;
        if (form.hasAttribute?.('data-chat-form')) return;
        const path = new URL(form.action, location.href).pathname;
        if (/\/(dang-nhap|dang-ky)$/.test(path)) return;
        if (confirmed.has(form)) { confirmed.delete(form); return; }

        const button = event.submitter;
        const status = form.querySelector('select[name="status"][data-order-id]');
        const method = form.querySelector('[name="_method"]')?.value.toLowerCase();
        const label = (button?.textContent || button?.title || '').trim().replace(/\s+/g, ' ');
        let message = button?.dataset.confirm || form.dataset.confirm;
        if (!message && status) {
            message = `Đổi đơn hàng #${status.dataset.orderId} sang “${status.value}”?`;
            if (status.value === 'Đã thanh toán') message += '\nSau khi xác nhận, không thể đổi trạng thái hoặc hủy đơn hàng.';
            if (status.value === 'Đã hủy') message += '\nĐơn hàng đã hủy không thể mở lại.';
        }
        if (!message) {
            message = method === 'delete' ? 'Bạn có chắc muốn xóa mục này?' : `Bạn có chắc muốn ${label || 'lưu thay đổi'}?`;
        }
        const restore = () => {
            if (status) status.value = [...status.options].find(option => option.defaultSelected)?.value || status.options[0].value;
            if (form.classList.contains('quantity-form')) {
                const input = form.querySelector('[name="quantity"]'); input.value = input.defaultValue;
            }
        };
        if (typeof window.requestAdminConfirmation === 'function') {
            event.preventDefault(); event.stopImmediatePropagation();
            window.requestAdminConfirmation(message).then(approved => {
                if (!approved) { restore(); return; }
                confirmed.add(form); form.requestSubmit(button || undefined); confirmed.delete(form);
            });
            return;
        }
        if (!window.confirm(message)) {
            event.preventDefault();
            event.stopImmediatePropagation();
            // Restore automatically submitted fields when the user cancels.
            restore();
        }
    }, true);
})();
