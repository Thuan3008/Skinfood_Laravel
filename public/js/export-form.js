(() => {
    'use strict';
    const form = document.getElementById('export-form');
    if (!form) return;
    const body = document.getElementById('export-lines');
    const template = body.firstElementChild.cloneNode(true);
    const money = new Intl.NumberFormat('vi-VN', {maximumFractionDigits: 2});
    let nextIndex = Math.max(-1, ...[...body.querySelectorAll('.export-product')].map(select => Number(select.name.match(/details\[(\d+)\]/)?.[1] ?? -1))) + 1;
    function recalculate() {
        const selected = [...body.querySelectorAll('.export-product')].map(select => select.value).filter(Boolean);
        let total = 0;
        body.querySelectorAll('tr').forEach(row => {
            const select = row.querySelector('.export-product');
            const option = select.selectedOptions[0];
            const quantity = row.querySelector('.export-quantity');
            const price = row.querySelector('.export-price');
            const stock = Number(option?.dataset.stock || 0);
            const invalidQuantity = quantity.value !== '' && (!Number.isInteger(Number(quantity.value)) || Number(quantity.value) <= 0);
            quantity.setCustomValidity(invalidQuantity ? 'Số lượng xuất phải là số nguyên lớn hơn 0, không được bằng 0 hoặc âm.' : '');
            if (select.value) quantity.max = stock; else quantity.removeAttribute('max');
            const duplicate = select.value && selected.filter(value => value === select.value).length > 1;
            const message = invalidQuantity ? quantity.validationMessage : (duplicate ? 'Sản phẩm đã được chọn ở dòng khác.' : (select.value && Number(quantity.value) > stock ? `Chỉ còn ${stock} sản phẩm trong kho.` : ''));
            select.setCustomValidity(duplicate ? message : '');
            row.querySelector('.export-stock-note').textContent = message;
            [...select.options].forEach(item => {
                if (item.value) item.disabled = Number(item.dataset.stock) <= 0 || (item.value !== select.value && selected.includes(item.value));
            });
            const amount = select.value ? Math.round(Math.max(0, Number(quantity.value) || 0) * Math.max(0, Number(price.value) || 0) * 100) / 100 : 0;
            row.querySelector('.export-line-total').value = money.format(amount);
            total += amount;
        });
        document.getElementById('export-total').textContent = money.format(Math.round(total * 100) / 100) + ' đ';
    }
    body.addEventListener('change', event => {
        if (event.target.matches('.export-product')) event.target.closest('tr').querySelector('.export-price').value = event.target.selectedOptions[0]?.dataset.price || '';
        recalculate();
    });
    body.addEventListener('input', recalculate);
    form.addEventListener('invalid', event => {
        if (event.target.matches('.export-quantity')) {
            event.preventDefault();
            window.showToast(event.target.validationMessage, 'error');
        }
    }, true);
    document.getElementById('export-add-row').addEventListener('click', () => {
        if (body.children.length >= 100) { window.showToast('Mỗi phiếu tối đa 100 dòng.', 'warning'); return; }
        const row = template.cloneNode(true);
        row.querySelectorAll('[name]').forEach(input => input.name = input.name.replace(/details\[\d+\]/, `details[${nextIndex}]`));
        nextIndex++;
        row.querySelector('.export-product').value = '';
        row.querySelector('.export-product').setCustomValidity('');
        row.querySelector('.export-quantity').value = '1';
        row.querySelector('.export-price').value = '';
        body.append(row); recalculate(); row.querySelector('.export-product').focus();
    });
    body.addEventListener('click', async event => {
        const button = event.target.closest('.export-remove-row');
        if (!button) return;
        if (body.children.length === 1) { window.showToast('Phiếu xuất phải có ít nhất một dòng sản phẩm.', 'warning'); return; }
        const message = 'Xóa dòng sản phẩm này khỏi phiếu đang lập?';
        const approved = window.requestAdminConfirmation ? await window.requestAdminConfirmation(message) : window.confirm(message);
        if (!approved) return;
        button.closest('tr').remove(); recalculate();
    });
    recalculate();
})();
