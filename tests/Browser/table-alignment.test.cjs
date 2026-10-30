const {test} = require('node:test');
const assert = require('node:assert/strict');
const fs = require('node:fs');
const vm = require('node:vm');
const path = require('node:path');
const source = fs.readFileSync(path.join(__dirname, '../../public/js/table-alignment.js'), 'utf8');
function cell(text, span = 1) {
    return {textContent: text, colSpan: span, dataset: {}, style: {setProperty(key, value) {this[key] = value;}}, querySelectorAll() {return [];}};
}
test('Vietnamese text, quantity, status and money align in headers and rows', () => {
    const labels = ['Sản phẩm','Ngày đặt','Số lượng','Tồn kho / Trạng thái','Trạng thái','Đơn giá','Giá bán','Thành tiền','Tổng','Đánh giá'];
    const headers = labels.map(value => cell(value));
    const cells = labels.map(value => cell(value));
    const table = {tHead: {rows: [{cells: headers}]}, tBodies: [{rows: [{cells}]}]};
    vm.runInNewContext(source, {document: {querySelectorAll: () => [table], body: {}}, MutationObserver: class {observe() {}}});
    const expected = ['left','center','center','center','center','center','center','center','center','left'];
    assert.deepEqual(headers.map(item => item.style['text-align']), labels.map(() => 'center'));
    assert.deepEqual(cells.map(item => item.style['text-align']), expected);
});
