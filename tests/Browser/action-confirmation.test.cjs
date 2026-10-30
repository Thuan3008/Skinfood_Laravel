const {test} = require('node:test');
const assert = require('node:assert/strict');
const vm = require('node:vm');
const fs = require('node:fs');
const path = require('node:path');
const script = fs.readFileSync(path.join(__dirname, '../../public/js/action-confirmation.js'), 'utf8');

function submit({approved = false, method = 'post', status = null, quantity = null, message = null, asyncApproved} = {}) {
    let handler;
    const prompts = [];
    class Form {
        constructor() {
            this.method = method;
            this.action = 'https://shop.local/admin/san-pham/1';
            this.dataset = message ? {confirm: message} : {};
            this.classList = {contains: () => Boolean(quantity)};
        }
        requestSubmit(submitter) {
            this.resubmissions = (this.resubmissions || 0) + 1;
            const event = {target: this, submitter, preventDefault() { throw new Error('Confirmed form must pass'); }, stopImmediatePropagation() {}};
            handler(event);
        }
        querySelector(selector) {
            if (selector.startsWith('select')) return status;
            if (selector.includes('quantity')) return quantity;
            return null;
        }
    }
    vm.runInNewContext(script, {
        HTMLFormElement: Form, URL, location: {href: 'https://shop.local/'},
        document: {addEventListener: (type, callback, capture) => { assert.equal(type, 'submit'); assert.equal(capture, true); handler = callback; }},
        window: {confirm: text => { prompts.push(text); return approved; }, ...(asyncApproved === undefined ? {} : {requestAdminConfirmation: text => { prompts.push(text); return Promise.resolve(asyncApproved); }})},
    });
    const event = {target: new Form(), submitter: {textContent: 'Cập nhật', dataset: {}}, prevented: false, stopped: false,
        preventDefault() { this.prevented = true; }, stopImmediatePropagation() { this.stopped = true; }};
    handler(event);
    return {event, prompts};
}

test('Cancel blocks submission and downstream AJAX handlers', () => {
    const {event, prompts} = submit();
    assert.equal(prompts.length, 1);
    assert.equal(event.prevented, true);
    assert.equal(event.stopped, true);
});
test('Confirm allows submission without a second popup', () => {
    const {event, prompts} = submit({approved: true});
    assert.equal(prompts.length, 1);
    assert.equal(event.prevented, false);
});
test('Cancel restores auto-submitted order status and explains finality', () => {
    const status = {value: 'Đã thanh toán', dataset: {orderId: '12'}, options: [{value: 'Chưa thanh toán', defaultSelected: true}]};
    const {prompts} = submit({status});
    assert.equal(status.value, 'Chưa thanh toán');
    assert.match(prompts[0], /#12/);
    assert.match(prompts[0], /không thể đổi trạng thái/);
});
test('Cancel restores quantity and preserves custom delete prompt', () => {
    const quantity = {value: '4', defaultValue: '2'};
    const {prompts} = submit({quantity, message: 'Xóa sản phẩm này?'});
    assert.equal(quantity.value, '2');
    assert.equal(prompts[0], 'Xóa sản phẩm này?');
});
test('GET filters do not request confirmation', () => {
    const {prompts} = submit({method: 'get'});
    assert.equal(prompts.length, 0);
});

test('Admin dialog pauses submission and Confirm resubmits exactly once', async () => {
    const {event, prompts} = submit({asyncApproved: true});
    assert.equal(event.prevented, true);
    assert.equal(event.stopped, true);
    assert.equal(event.target.resubmissions, undefined);
    await Promise.resolve();
    assert.equal(event.target.resubmissions, 1);
    assert.equal(prompts.length, 1);
});
test('Admin dialog Cancel restores fields without sending the form', async () => {
    const quantity = {value: '8', defaultValue: '2'};
    const {event, prompts} = submit({asyncApproved: false, quantity});
    await Promise.resolve();
    assert.equal(event.target.resubmissions, undefined);
    assert.equal(quantity.value, '2');
    assert.equal(prompts.length, 1);
});
