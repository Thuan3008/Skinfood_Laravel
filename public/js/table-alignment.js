(() => {
    'use strict';
    const normalize = value => value.normalize('NFD').replace(/[\u0300-\u036f]/g, '').replace(/đ/gi, 'd').toLowerCase().replace(/\s+/g, ' ').trim();
    function columnAlignment(header) {
        if (header.dataset.align) return header.dataset.align;
        const label = normalize(header.textContent);
        if (/so luong|tong so|ton kho|^sl$|^sl |so sp|so san pham/.test(label)) return 'center';
        if (/^(don gia|gia\b|thanh tien|tong tien|tong$|doanh thu|tong doanh thu|tong gia tri|gia tri|giam gia|giam toi da|don hang toi thieu|tong thanh toan)/.test(label)) return 'center';
        if (/ngay|thoi gian|han su dung|^sdt\b|dien thoai|^ma\b|^mand$|^id$|^stt$|ty le|phan tram/.test(label)) return 'center';
        if (/trang thai|tinh trang|^thanh toan$|^duyet$|^diem$|so sao|vai tro|thao tac|hanh dong|^xoa$|^chi tiet$/.test(label)) return 'center';
        return 'left';
    }
    function alignTable(table) {
        const headerRow = table.tHead?.rows[table.tHead.rows.length - 1];
        if (!headerRow) return;
        const columns = [];
        [...headerRow.cells].forEach(header => {
            const alignment = columnAlignment(header);
            header.style.setProperty('text-align', 'center', 'important');
            for (let i = 0; i < header.colSpan; i++) columns.push(alignment);
        });
        [...table.tBodies, ...(table.tFoot ? [table.tFoot] : [])].forEach(section => {
            [...section.rows].forEach(row => {
                let column = 0;
                [...row.cells].forEach(cell => {
                    const text = cell.textContent.trim();
                    const numeric = /^#?[+−-]?\d[\d\s.,/%₫đ:+−-]*$/u.test(text);
                    const alignment = cell.dataset.align || (numeric ? 'center' : columns[column]) || 'left';
                    // Keep empty-state messages centered across the whole table.
                    if (!(row.cells.length === 1 && cell.colSpan > 1)) cell.style.setProperty('text-align', alignment, 'important');
                    cell.querySelectorAll('input:not([type="hidden"]):not([type="checkbox"]):not([type="radio"])').forEach(input => input.style.setProperty('text-align', alignment, 'important'));
                    column += cell.colSpan;
                });
            });
        });
    }
    document.querySelectorAll('table').forEach(alignTable);
    new MutationObserver(changes => {
        const tables = new Set();
        changes.forEach(change => change.addedNodes.forEach(node => {
            if (node.nodeType !== 1) return;
            const parent = node.closest('table');
            if (parent) tables.add(parent);
            node.querySelectorAll('table').forEach(table => tables.add(table));
        }));
        tables.forEach(alignTable);
    }).observe(document.body, {childList: true, subtree: true});
})();
