(() => {
    'use strict';
    const content = document.querySelector('.admin-content');
    if (!content) return;
    content.querySelectorAll('h1').forEach(heading => {
        if (heading.parentElement.classList.contains('d-flex')) heading.parentElement.classList.add('admin-page-heading');
    });
    content.querySelectorAll('table').forEach(table => {
        if (!table.closest('.table-responsive')) {
            const wrapper = document.createElement('div'); wrapper.className = 'table-responsive admin-data-surface';
            table.before(wrapper); wrapper.append(table);
        }
        table.querySelectorAll('tbody td[colspan]').forEach(cell => {
            if (cell.parentElement.children.length !== 1) return;
            const icon = document.createElement('span'); icon.className = 'admin-empty-mark'; icon.setAttribute('aria-hidden', 'true');
            icon.innerHTML = '<i class="fa-regular fa-folder-open"></i>'; cell.prepend(icon);
        });
    });
    content.querySelectorAll('.badge').forEach(badge => {
        const value = badge.textContent.trim();
        if (value === 'Đã thanh toán') badge.classList.add('ap-status-paid');
        else if (value === 'Đã hủy' || value === 'Hết hàng' || value === 'inactive') badge.classList.add('ap-status-cancelled');
        else if (value.includes('chờ xác nhận') || value === 'Chưa thanh toán') badge.classList.add('ap-status-pending');
    });
    content.querySelectorAll('form[method="get"],form[method="GET"]').forEach(form => {
        if (form.closest('nav') || form.classList.contains('page-jump-form')) return;
        form.classList.add('admin-filter-bar');
        const from = form.querySelector('[name="_from"]');
        const to = form.querySelector('[name="_to"]');
        if (from && to) form.addEventListener('submit', event => {
            if (from.value && to.value && from.value >= to.value) {
                event.preventDefault();
                window.showToast('Từ ngày phải nhỏ hơn Đến ngày.', 'error');
                from.focus();
            }
        });
        const query = form.querySelector('input[name="q"]');
        if (query && !query.closest('.input-group')) {
            const wrapper = document.createElement('div'); wrapper.className = 'admin-search-field';
            const icon = document.createElement('i'); icon.className = 'fa-solid fa-magnifying-glass'; icon.setAttribute('aria-hidden', 'true');
            query.before(wrapper); wrapper.append(icon, query);
        }
        if (query && !query.getAttribute('aria-label') && !query.id) query.setAttribute('aria-label', query.placeholder || 'Tìm kiếm');
    });
    const dialog = document.getElementById('adminConfirmDialog');
    if (dialog && typeof dialog.showModal === 'function') {
        let pending = null;
        window.requestAdminConfirmation = message => new Promise(resolve => {
            if (pending) { resolve(false); return; }
            pending = resolve; dialog.querySelector('[data-confirm-message]').textContent = message;
            dialog.showModal(); dialog.querySelector('[data-confirm-cancel]').focus();
        });
        const finish = approved => { dialog.close(); const resolve = pending; pending = null; resolve?.(approved); };
        dialog.querySelector('[data-confirm-cancel]').addEventListener('click', () => finish(false));
        dialog.querySelector('[data-confirm-accept]').addEventListener('click', () => finish(true));
        dialog.addEventListener('cancel', event => { event.preventDefault(); finish(false); });
    }
})();
