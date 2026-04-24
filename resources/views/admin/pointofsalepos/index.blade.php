@extends('admin.master')

@section('content')

<style>
/* ════════════════════════════════════════════════════════════
   POS — Point of Sale  |  Clean Professional Design
   ════════════════════════════════════════════════════════════ */
*, *::before, *::after { box-sizing: border-box; }

:root {
    --accent:    #e7567c;
    --accent-dk: #c93f65;
    --bg:        #f0f2f5;
    --white:     #ffffff;
    --border:    #e4e9f2;
    --text:      #1a1f36;
    --muted:     #6b7a99;
    --success:   #22c55e;
    --warning:   #f59e0b;
    --blue:      #4361ee;
    --shadow:    0 1px 4px rgba(0,0,0,.07);
    --shadow-md: 0 4px 20px rgba(0,0,0,.11);
    --radius:    8px;
    --radius-sm: 5px;
}

/* ── Layout ── */
.pos-wrap {
    display: flex;
    height: calc(100vh - 60px);
    overflow: hidden;
    background: var(--bg);
    font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
}

/* ════════════════════════════════════════════════════════════
   LEFT — Product List
════════════════════════════════════════════════════════════ */
.pos-left {
    flex: 1 1 0;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    padding: 16px 16px 12px 16px;
    gap: 12px;
    min-width: 0;
    background: var(--bg);
}

.pos-page-title {
    font-size: 16px;
    font-weight: 700;
    color: var(--text);
    margin: 0 0 2px 0;
}

/* Filter Bar */
.filter-bar {
    display: flex;
    gap: 10px;
    align-items: center;
}
.filter-bar select {
    height: 40px;
    border: 1.5px solid var(--border);
    border-radius: var(--radius-sm);
    padding: 0 30px 0 12px;
    font-size: 13px;
    background: var(--white);
    color: var(--text);
    outline: none;
    cursor: pointer;
    min-width: 140px;
    transition: border-color .15s;
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='11' height='11' viewBox='0 0 24 24' fill='none' stroke='%236b7a99' stroke-width='2'%3E%3Cpath d='m6 9 6 6 6-6'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 10px center;
}
.filter-bar select:focus { border-color: var(--accent); }
.search-wrap { flex: 1; position: relative; }
.search-wrap input {
    width: 100%; height: 40px;
    border: 1.5px solid var(--border);
    border-radius: var(--radius-sm);
    padding: 0 14px 0 38px;
    font-size: 13px; background: var(--white);
    color: var(--text); outline: none;
    transition: border-color .15s;
}
.search-wrap input:focus { border-color: var(--accent); }
.search-wrap .si {
    position: absolute; left: 12px; top: 50%;
    transform: translateY(-50%); color: #bbc; font-size: 14px;
    pointer-events: none;
}

/* Product Grid — 2 column */
.product-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 0;
    overflow-y: auto;
    flex: 1;
    align-content: start;
    background: var(--white);
    border-radius: var(--radius);
    border: 1px solid var(--border);
    overflow: hidden;
}

.product-card {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 14px;
    cursor: pointer;
    transition: background .12s;
    border-bottom: 1px solid #f0f2f5;
    border-right: 1px solid #f0f2f5;
    user-select: none;
    background: var(--white);
}
.product-card:hover { background: #fef6f9; }
.product-card:active { background: #fce8ef; }

.pc-img {
    width: 58px; height: 58px;
    object-fit: cover; border-radius: 6px;
    flex-shrink: 0; background: #f1f5f9;
}
.pc-info { flex: 1; min-width: 0; }
.pc-name {
    font-size: 13px; font-weight: 600; color: var(--text);
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    margin-bottom: 3px;
}
.pc-price-row { display: flex; align-items: center; gap: 7px; flex-wrap: wrap; margin-bottom: 3px; }
.pc-price { font-size: 14px; font-weight: 700; color: var(--blue); }
.pc-old   { font-size: 12px; color: #bbc; text-decoration: line-through; }
.badge-off {
    font-size: 10px; background: var(--accent); color: #fff;
    padding: 2px 7px; border-radius: 20px; font-weight: 700;
}
.pc-meta {
    display: flex; align-items: center; gap: 6px;
    font-size: 11px; color: var(--muted);
}
.pc-meta-sep { color: #dde2e8; }
.pc-stock-low { color: var(--warning); font-weight: 600; }

/* Pagination */
.pos-pagination {
    display: flex; align-items: center; gap: 4px;
    justify-content: flex-start; flex-wrap: wrap;
    padding: 4px 0;
}
.pos-pagination button {
    min-width: 30px; height: 30px; padding: 0 5px;
    border: 1px solid var(--border); background: var(--white);
    border-radius: var(--radius-sm); font-size: 12px; cursor: pointer;
    display: inline-flex; align-items: center; justify-content: center;
    color: var(--text); transition: all .15s; font-family: inherit;
}
.pos-pagination button.active,
.pos-pagination button:hover:not(:disabled) { background: var(--accent); color: #fff; border-color: var(--accent); }
.pos-pagination button:disabled { opacity: .4; cursor: not-allowed; }
.pg-ellipsis { padding: 0 3px; color: var(--muted); font-size: 13px; }

/* ════════════════════════════════════════════════════════════
   RIGHT PANEL
════════════════════════════════════════════════════════════ */
.pos-right {
    width: 430px;
    flex-shrink: 0;
    display: flex;
    flex-direction: column;
    border-left: 1px solid var(--border);
    background: var(--white);
    overflow: hidden;
}

/* Customer Section */
.cust-section {
    padding: 14px 16px 12px;
    border-bottom: 1px solid var(--border);
    flex-shrink: 0;
}
.cust-select-row { display: flex; align-items: center; gap: 8px; }
.cust-select-wrap { flex: 1; position: relative; }
.cust-select-wrap .cust-icon {
    position: absolute; left: 11px; top: 50%;
    transform: translateY(-50%); color: #aab;
    font-size: 15px; pointer-events: none; z-index: 1;
}
.cust-select-wrap select {
    width: 100%; height: 42px;
    border: 1.5px solid var(--border);
    border-radius: var(--radius-sm);
    padding: 0 32px 0 36px;
    font-size: 13px; color: var(--text);
    background: #f9fafb; outline: none;
    cursor: pointer; font-family: inherit;
    transition: border-color .15s;
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='11' height='11' viewBox='0 0 24 24' fill='none' stroke='%236b7a99' stroke-width='2'%3E%3Cpath d='m6 9 6 6 6-6'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 10px center;
}
.cust-select-wrap select:focus {
    border-color: var(--accent);
    background-color: var(--white);
    box-shadow: 0 0 0 3px rgba(231,86,124,.08);
}

.btn-add-cust {
    height: 42px; padding: 0 14px;
    background: #fef6f9; border: 1.5px solid var(--accent);
    color: var(--accent); border-radius: var(--radius-sm);
    font-size: 13px; font-weight: 600; cursor: pointer;
    display: inline-flex; align-items: center; gap: 5px;
    white-space: nowrap; transition: all .15s; font-family: inherit;
}
.btn-add-cust:hover { background: var(--accent); color: #fff; }
.btn-add-cust i { font-size: 15px; }

.sel-cust-info {
    display: none;
    margin-top: 10px;
    background: #fef6f9;
    border: 1px solid #fbc8d9;
    border-radius: var(--radius-sm);
    padding: 10px 12px;
    position: relative;
}
.sel-cust-info.show { display: block; }
.sci-name  { font-size: 13px; font-weight: 700; color: var(--text); margin-bottom: 4px; }
.sci-row   { display: flex; gap: 16px; flex-wrap: wrap; }
.sci-item  { font-size: 12px; color: var(--muted); display: flex; align-items: center; gap: 4px; }
.sci-item i { color: var(--accent); font-size: 12px; }
.sci-clear {
    position: absolute; top: 8px; right: 10px;
    background: none; border: none; color: #ddd;
    font-size: 18px; cursor: pointer; line-height: 1;
    transition: color .12s;
}
.sci-clear:hover { color: var(--accent); }

/* Cart body */
.pos-right-body {
    flex: 1;
    overflow-y: auto;
    padding: 14px 16px;
    display: flex;
    flex-direction: column;
    gap: 0;
}

.cart-header {
    display: flex; align-items: center;
    justify-content: space-between; margin-bottom: 10px;
}
.cart-header h6 {
    font-size: 14px; font-weight: 700;
    color: var(--text); margin: 0;
    display: flex; align-items: center; gap: 6px;
}
.btn-clear-all {
    font-size: 12px; color: var(--muted);
    background: none; border: none; cursor: pointer;
    padding: 0; font-family: inherit; transition: color .12s;
    display: flex; align-items: center; gap: 4px;
}
.btn-clear-all:hover { color: var(--accent); }

/* Cart item */
.cart-item {
    display: flex; align-items: flex-start; gap: 10px;
    padding: 10px 0; border-bottom: 1px solid #f0f2f5;
    position: relative;
}
.cart-item:last-child { border-bottom: none; }
.ci-img {
    width: 54px; height: 54px; object-fit: cover;
    border-radius: 6px; flex-shrink: 0; background: #f1f5f9;
}
.ci-body { flex: 1; min-width: 0; padding-right: 20px; }
.ci-name { font-size: 13px; font-weight: 700; color: var(--text); line-height: 1.3; margin-bottom: 1px; }
.ci-variant { font-size: 11px; color: var(--muted); margin-bottom: 4px; }
.ci-price { font-size: 14px; font-weight: 700; color: var(--blue); }
.ci-old   { font-size: 11px; color: #bbc; text-decoration: line-through; margin-left: 4px; }
.ci-actions { display: flex; align-items: center; gap: 6px; margin-top: 6px; }
.qty-btn {
    width: 26px; height: 26px;
    border: 1px solid var(--border);
    border-radius: var(--radius-sm);
    background: #f8f9fa; cursor: pointer;
    font-size: 14px; display: inline-flex; align-items: center;
    justify-content: center; color: var(--text);
    transition: all .12s; flex-shrink: 0;
}
.qty-btn:hover { background: var(--accent); color: #fff; border-color: var(--accent); }
.qty-input {
    width: 36px; text-align: center; border: 1px solid var(--border);
    border-radius: var(--radius-sm); height: 26px; font-size: 13px;
    font-weight: 600; outline: none; font-family: inherit;
}
.qty-input:focus { border-color: var(--accent); }

/* ── BARCODE display in cart ── */
.ci-barcode {
    font-size: 11px;
    color: #8896b3;
    margin-top: 4px;
    display: flex;
    align-items: center;
    gap: 4px;
}
.ci-barcode i { color: #aab; font-size: 12px; }
.ci-barcode .bc-val {
    font-family: 'Courier New', monospace;
    font-weight: 600;
    color: #4361ee;
    background: #f0f4ff;
    border: 1px solid #d8e0f8;
    border-radius: 3px;
    padding: 1px 5px;
    letter-spacing: .4px;
    font-size: 11px;
}

.ci-del {
    position: absolute; top: 10px; right: 0;
    background: none; border: none; color: #ddd;
    font-size: 16px; cursor: pointer; padding: 2px; line-height: 1;
    transition: color .12s;
}
.ci-del:hover { color: var(--accent); }

/* Empty cart */
.empty-cart { text-align: center; padding: 40px 20px; color: #c8d0db; }
.empty-cart .ec-icon { font-size: 44px; display: block; margin-bottom: 10px; }
.empty-cart p { font-size: 13px; margin: 0; }

/* ════════════════════════════════════════════════════════════
   SUMMARY PANEL
════════════════════════════════════════════════════════════ */
.pos-summary {
    border-top: 1px solid var(--border);
    padding: 14px 16px 10px;
    flex-shrink: 0;
    background: var(--white);
}

.sum-row {
    display: flex; justify-content: space-between; align-items: center;
    font-size: 13px; padding: 3px 0; color: var(--text);
}
.sum-row .sl { color: var(--muted); }
.sum-row .sv { font-weight: 600; }

.disc-row {
    display: flex; justify-content: space-between; align-items: center;
    font-size: 13px; padding: 3px 0;
}
.disc-row .dl { color: var(--muted); }
.disc-row .dv { font-weight: 600; color: var(--accent); }

.tax-summary-box {
    background: #f8fafc;
    border: 1px solid var(--border);
    border-radius: var(--radius-sm);
    padding: 10px 12px;
    margin: 8px 0;
}
.tax-summary-title {
    font-size: 12px; font-weight: 700; color: var(--text);
    margin-bottom: 7px;
}
.tax-row-item {
    display: flex; justify-content: space-between;
    font-size: 12px; color: var(--muted);
    background: #edf0f7; border-radius: 3px; padding: 4px 8px; margin-bottom: 4px;
}
.tax-total-row {
    display: flex; justify-content: space-between; align-items: center;
    font-size: 13px; font-weight: 700; color: var(--text);
    border-top: 1px solid var(--border);
    margin-top: 4px; padding-top: 7px;
}

/* Coupon */
.coupon-applied {
    display: none; align-items: center; gap: 6px;
    background: #f0fdf4; border: 1px solid #bbf7d0;
    color: #16a34a; border-radius: var(--radius-sm);
    padding: 7px 10px; font-size: 12px; font-weight: 600; margin-bottom: 6px;
}
.coupon-applied.show { display: flex; }
.ct-del { cursor: pointer; margin-left: auto; font-size: 15px; background: none; border: none; color: #16a34a; }
.coupon-row { display: flex; gap: 8px; margin: 6px 0; }
.coupon-row input {
    flex: 1; height: 38px; border: 1.5px solid var(--border);
    border-radius: var(--radius-sm); padding: 0 12px;
    font-size: 13px; outline: none; font-family: inherit;
    transition: border-color .15s; color: var(--text);
}
.coupon-row input:focus { border-color: var(--accent); }
.btn-apply {
    height: 38px; padding: 0 18px; background: var(--accent); color: #fff;
    border: none; border-radius: var(--radius-sm); font-size: 13px;
    font-weight: 600; cursor: pointer; font-family: inherit; transition: background .15s;
}
.btn-apply:hover { background: var(--accent-dk); }

/* Grand Total + Actions */
.grand-section { margin-top: 6px; }
.pos-actions { display: flex; gap: 10px; margin-top: 0; }
.btn-draft {
    flex: 0 0 90px; height: 48px; background: #fffbe6;
    color: #a16207; border: 1.5px solid #fde68a;
    border-radius: var(--radius); font-size: 13px; font-weight: 700;
    cursor: pointer; font-family: inherit; transition: all .15s;
    display: flex; align-items: center; justify-content: center; gap: 5px;
}
.btn-draft:hover { background: #fef3c7; border-color: #fcd34d; }
.btn-checkout {
    flex: 1; height: 48px;
    background: linear-gradient(135deg, #e7567c 0%, #c93f65 100%);
    color: #fff; border: none; border-radius: var(--radius);
    font-size: 14px; font-weight: 700; cursor: pointer; font-family: inherit;
    display: inline-flex; align-items: center; justify-content: center;
    gap: 8px; transition: opacity .15s;
}
.btn-checkout:hover { opacity: .9; }

/* ════════════════════════════════════════════════════════════
   CHECKOUT DRAWER
════════════════════════════════════════════════════════════ */
.cd-overlay {
    position: fixed; inset: 0; background: rgba(15,23,42,.45);
    z-index: 20000; opacity: 0; pointer-events: none; transition: opacity .25s;
}
.cd-overlay.show { opacity: 1; pointer-events: all; }
.cd-drawer {
    position: fixed; top: 0; right: 0; bottom: 0; width: 400px; max-width: 100vw;
    background: var(--white); box-shadow: -8px 0 32px rgba(0,0,0,.18);
    transform: translateX(100%); transition: transform .3s cubic-bezier(.4,0,.2,1);
    z-index: 20001; display: flex; flex-direction: column;
}
.cd-overlay.show .cd-drawer { transform: translateX(0); }
.cd-head {
    padding: 18px 20px; border-bottom: 1px solid var(--border);
    display: flex; align-items: center; justify-content: space-between; flex-shrink: 0;
}
.cd-head h5 { font-size: 16px; font-weight: 700; color: var(--text); margin: 0; }
.cd-close { background: none; border: none; font-size: 22px; cursor: pointer; color: #94a3b8; line-height: 1; transition: color .12s; }
.cd-close:hover { color: var(--accent); }
.cd-body { flex: 1; overflow-y: auto; padding: 20px; display: flex; flex-direction: column; gap: 16px; }
.cds-card { background: #f8fafc; border: 1px solid var(--border); border-radius: var(--radius); padding: 16px; }
.cds-row  { display: flex; justify-content: space-between; align-items: center; padding: 5px 0; font-size: 13px; color: var(--muted); }
.cds-row .badge-cnt { background: var(--accent); color: #fff; border-radius: 50%; width: 22px; height: 22px; font-size: 11px; font-weight: 700; display: inline-flex; align-items: center; justify-content: center; margin-left: 6px; }
.cds-total { border-top: 1px solid var(--border); margin-top: 6px; padding-top: 10px; }
.cds-total strong { color: var(--accent); font-size: 18px; font-weight: 800; }
.cd-sec-label { font-size: 11px; font-weight: 700; color: var(--muted); text-transform: uppercase; letter-spacing: .6px; margin-bottom: 10px; }
.pm-grid { display: grid; grid-template-columns: repeat(3,1fr); gap: 10px; }
.pm-btn {
    padding: 14px 8px; border: 2px solid var(--border);
    border-radius: var(--radius); background: #f8fafc; cursor: pointer;
    text-align: center; font-size: 13px; font-weight: 600;
    color: var(--muted); transition: all .15s; font-family: inherit;
}
.pm-btn i { font-size: 22px; display: block; margin-bottom: 5px; }
.pm-btn.active, .pm-btn:hover { border-color: var(--accent); background: #fef6f9; color: var(--accent); }
.amount-wrap { position: relative; }
.amount-prefix { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--muted); font-weight: 700; font-size: 15px; }
.amount-input {
    width: 100%; height: 48px; border: 1.5px solid var(--border);
    border-radius: var(--radius-sm); padding: 0 12px 0 30px;
    font-size: 18px; font-weight: 700; outline: none; font-family: inherit;
    color: var(--text); transition: border-color .15s;
}
.amount-input:focus { border-color: var(--accent); box-shadow: 0 0 0 3px rgba(231,86,124,.10); }
.change-box {
    background: #f0fdf4; border: 1px solid #bbf7d0;
    border-radius: var(--radius-sm); padding: 12px 16px;
    display: flex; justify-content: space-between; align-items: center;
}
.change-box .cl { font-size: 13px; color: #15803d; font-weight: 600; }
.change-box .ca { font-size: 18px; font-weight: 800; color: #15803d; }
.cd-note { width: 100%; border: 1.5px solid var(--border); border-radius: var(--radius-sm); padding: 10px 12px; font-size: 13px; resize: none; outline: none; font-family: inherit; transition: border-color .15s; }
.cd-note:focus { border-color: var(--accent); }
.cd-foot { padding: 16px 20px; border-top: 1px solid var(--border); flex-shrink: 0; display: flex; gap: 10px; }
.cd-btn-cancel { flex: 0 0 100px; height: 46px; background: #f1f5f9; border: 1px solid var(--border); border-radius: var(--radius); font-size: 13px; cursor: pointer; color: var(--muted); font-family: inherit; transition: background .12s; }
.cd-btn-cancel:hover { background: #e2e8f0; }
.cd-btn-confirm {
    flex: 1; height: 46px;
    background: linear-gradient(135deg, #22c55e, #16a34a);
    color: #fff; border: none; border-radius: var(--radius);
    font-size: 14px; font-weight: 700; cursor: pointer; font-family: inherit;
    display: inline-flex; align-items: center; justify-content: center;
    gap: 8px; transition: opacity .15s;
}
.cd-btn-confirm:hover { opacity: .92; }
.cd-btn-confirm:disabled { opacity: .5; cursor: not-allowed; }

/* ════════════════════════════════════════════════════════════
   CUSTOMER MODAL
════════════════════════════════════════════════════════════ */
.pos-modal-bg {
    position: fixed; inset: 0; background: rgba(15,23,42,.5);
    z-index: 30000; display: flex; align-items: center;
    justify-content: center; opacity: 0; pointer-events: none;
    transition: opacity .2s; padding: 16px;
}
.pos-modal-bg.show { opacity: 1; pointer-events: all; }
.pos-modal {
    background: var(--white); border-radius: 14px; width: 540px;
    max-width: 100%; max-height: 90vh; overflow-y: auto;
    box-shadow: 0 24px 64px rgba(0,0,0,.22);
    transform: scale(.96) translateY(8px); transition: transform .2s;
}
.pos-modal-bg.show .pos-modal { transform: scale(1) translateY(0); }
.modal-head { padding: 18px 22px; border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; position: sticky; top: 0; background: var(--white); z-index: 1; border-radius: 14px 14px 0 0; }
.modal-head h5 { font-size: 16px; font-weight: 700; color: var(--text); margin: 0; }
.modal-close { background: none; border: none; font-size: 22px; cursor: pointer; color: #94a3b8; line-height: 1; transition: color .12s; }
.modal-close:hover { color: var(--accent); }
.modal-body { padding: 22px; }
.form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 14px; }
.fg { display: flex; flex-direction: column; gap: 5px; }
.fg.full { grid-column: 1 / -1; }
.fg label { font-size: 12px; font-weight: 600; color: var(--muted); }
.fg input, .fg select {
    height: 40px; border: 1.5px solid var(--border); border-radius: var(--radius-sm);
    padding: 0 12px; font-size: 13px; outline: none; font-family: inherit;
    color: var(--text); transition: border-color .15s, box-shadow .15s;
}
.fg input:focus, .fg select:focus { border-color: var(--accent); box-shadow: 0 0 0 3px rgba(231,86,124,.10); }
.fg .ferr { font-size: 11px; color: var(--accent); margin-top: 2px; display: none; }
.fg.has-error input, .fg.has-error select { border-color: var(--accent); }
.fg.has-error .ferr { display: block; }
.modal-foot { padding: 14px 22px; border-top: 1px solid var(--border); display: flex; justify-content: flex-end; gap: 10px; position: sticky; bottom: 0; background: var(--white); border-radius: 0 0 14px 14px; }
.btn-modal-cancel { height: 40px; padding: 0 18px; background: #f1f5f9; border: 1px solid var(--border); border-radius: var(--radius-sm); font-size: 13px; cursor: pointer; color: var(--muted); font-family: inherit; }
.btn-modal-cancel:hover { background: #e2e8f0; }
.btn-modal-save {
    height: 40px; padding: 0 22px; background: var(--accent);
    color: #fff; border: none; border-radius: var(--radius-sm);
    font-size: 13px; font-weight: 600; cursor: pointer; font-family: inherit;
    display: inline-flex; align-items: center; gap: 6px; transition: background .15s;
}
.btn-modal-save:hover { background: var(--accent-dk); }
.btn-modal-save:disabled { opacity: .6; cursor: not-allowed; }

/* Toast */
.toast-container { position: fixed; bottom: 24px; right: 24px; z-index: 99999; display: flex; flex-direction: column; gap: 8px; pointer-events: none; }
.pos-toast {
    background: #1e293b; color: #fff; border-radius: var(--radius);
    padding: 12px 18px; font-size: 13px; font-weight: 500;
    box-shadow: 0 8px 24px rgba(0,0,0,.2); display: flex;
    align-items: center; gap: 10px; min-width: 260px; max-width: 340px;
    animation: tIn .3s ease; pointer-events: all;
}
.pos-toast.t-success { background: #15803d; }
.pos-toast.t-error   { background: #be123c; }
.pos-toast.t-warning { background: #b45309; }
.pos-toast.t-out     { animation: tOut .3s ease forwards; }
@keyframes tIn  { from { opacity:0; transform:translateX(40px); } to { opacity:1; transform:translateX(0); } }
@keyframes tOut { from { opacity:1; transform:translateX(0); } to { opacity:0; transform:translateX(40px); } }

.pos-loading { grid-column: 1/-1; display: flex; align-items: center; justify-content: center; height: 180px; color: #c8d0db; font-size: 13px; gap: 8px; }
.spin { animation: spin 1s linear infinite; display: inline-block; }
@keyframes spin { to { transform: rotate(360deg); } }

/* Scrollbars */
.product-grid::-webkit-scrollbar,
.pos-right-body::-webkit-scrollbar,
.cd-body::-webkit-scrollbar { width: 4px; }
.product-grid::-webkit-scrollbar-thumb,
.pos-right-body::-webkit-scrollbar-thumb,
.cd-body::-webkit-scrollbar-thumb { background: #dde2e8; border-radius: 4px; }

@media (max-width:1100px) {
    .pos-wrap { flex-direction: column; height: auto; overflow: auto; }
    .pos-left { min-height: 60vh; }
    .pos-right { width: 100%; border-left: none; border-top: 1px solid var(--border); }
    .product-grid { grid-template-columns: 1fr; }
    .cd-drawer { width: 100vw; }
}
@media (max-width:600px) {
    .filter-bar { flex-wrap: wrap; }
    .form-grid { grid-template-columns: 1fr; }
    .pm-grid { grid-template-columns: 1fr 1fr; }
}
</style>

<div class="pos-wrap">

    {{-- ══ LEFT: Products ══ --}}
    <div class="pos-left">

        <div class="pos-page-title">Point of Sale (POS)</div>

        {{-- Filter Bar --}}
        <div class="filter-bar">
            <select id="brandFilter">
                <option value="">Select Brand</option>
                @foreach($brands as $brand)
                    <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                @endforeach
            </select>
            <select id="categoryFilter">
                <option value="">Select Category</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                @endforeach
            </select>
            <div class="search-wrap">
                <i class="bi bi-search si"></i>
                <input type="text" id="productSearch"
                       placeholder="Search by product name"
                       autocomplete="off">
            </div>
        </div>

        {{-- Product Grid --}}
        <div class="product-grid" id="productGrid">
            <div class="pos-loading"><span class="spin bi bi-arrow-repeat"></span>&nbsp;Loading products…</div>
        </div>

        {{-- Pagination --}}
        <div class="pos-pagination" id="posPagination"></div>
    </div>

    {{-- ══ RIGHT: Cart / Order ══ --}}
    <div class="pos-right">

        {{-- Customer Dropdown Section --}}
        <div class="cust-section">
            <div class="cust-select-row">
                <div class="cust-select-wrap">
                    <i class="bi bi-person cust-icon"></i>
                    <select id="customerSelect" onchange="onCustomerChange(this)">
                        <option value="">Enter customer name or phone number</option>
                        @foreach($customers as $c)
                            @php
                                $cUser = $c->user;
                                $cName = trim(($c->first_name ?? '') . ' ' . ($c->last_name ?? ''));
                                if(!$cName) $cName = $cUser?->name ?? '';
                            @endphp
                            <option value="{{ $c->id }}"
                                    data-name="{{ $cName }}"
                                    data-phone="{{ $cUser?->phone ?? '' }}"
                                    data-email="{{ $cUser?->email ?? '' }}">
                                {{ $cName }} — {{ $cUser?->phone ?? '' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <button class="btn-add-cust" onclick="openCustomerModal()">
                    <i class="bi bi-person-plus"></i> Customer
                </button>
            </div>

            {{-- Selected Customer Info --}}
            <div class="sel-cust-info" id="selCustInfo">
                <button class="sci-clear" onclick="clearCustomer()" title="Remove">&times;</button>
                <div class="sci-name" id="sciName">—</div>
                <div class="sci-row">
                    <div class="sci-item" id="sciPhone" style="display:none;">
                        <i class="bi bi-telephone-fill"></i>
                        <span id="sciPhoneVal">—</span>
                    </div>
                    <div class="sci-item" id="sciEmail" style="display:none;">
                        <i class="bi bi-envelope-fill"></i>
                        <span id="sciEmailVal">—</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Cart Body --}}
        <div class="pos-right-body" id="posRightBody">

            {{-- Cart header --}}
            <div class="cart-header" id="cartHeader" style="display:none;">
                <h6><i class="bi bi-cart3"></i> Cart <span id="cartCount" style="font-size:12px;color:var(--muted);font-weight:500;"></span></h6>
                <button class="btn-clear-all" onclick="clearCart()">
                    <i class="bi bi-trash"></i> Clear all
                </button>
            </div>

            {{-- Cart list --}}
            <div id="cartList">
                <div class="empty-cart">
                    <span class="ec-icon bi bi-cart3"></span>
                    <p>No items yet. Click a product to add.</p>
                </div>
            </div>
        </div>

        {{-- Summary Panel --}}
        <div class="pos-summary" id="posSummary" style="display:none;">

            <div class="sum-row">
                <span class="sl">Sub Total</span>
                <span class="sv" id="sumSubTotal">{{ $settings->default_currency ?? '৳' }}0.00</span>
            </div>

            <div class="disc-row" id="discountRow" style="display:none;">
                <span class="dl">Discount Amount</span>
                <span class="dv" id="sumDiscount">– {{ $settings->default_currency ?? '৳' }}0.00</span>
            </div>

            @if($taxes->count())
            <div class="tax-summary-box">
                <div class="tax-summary-title">VAT &amp; Taxes Summary</div>
                <div id="taxRows">
                    @foreach($taxes as $tax)
                    <div class="tax-row-item" data-rate="{{ $tax->percentage }}" data-name="{{ $tax->name }}">
                        <span>{{ $tax->name }} ({{ $tax->percentage }}%)</span>
                        <span class="tax-val">{{ $settings->default_currency ?? '৳' }}0.00</span>
                    </div>
                    @endforeach
                </div>
                <div class="tax-total-row">
                    <span>Total Tax Amount:</span>
                    <strong id="totalTaxAmt">{{ $settings->default_currency ?? '৳' }}0.00</strong>
                </div>
            </div>
            @endif

            {{-- Coupon --}}
            <div class="coupon-applied" id="couponTag">
                <i class="bi bi-tag-fill"></i>
                <span id="couponTagText">Coupon applied</span>
                <button class="ct-del" onclick="removeCoupon()">&times;</button>
            </div>
            <div class="coupon-row" id="couponRow">
                <input type="text" id="couponInput" placeholder="Add Coupon" autocomplete="off">
                <button class="btn-apply" onclick="applyCoupon()">Apply</button>
            </div>

            {{-- Grand Total + Actions --}}
            <div class="grand-section">
                <div class="pos-actions">
                    <button class="btn-draft" onclick="placeOrder('draft')">
                        <i class="bi bi-save"></i> Draft
                    </button>
                    <button class="btn-checkout" onclick="openCheckoutDrawer()">
                        Grand Total <strong id="grandTotalBtn">{{ $settings->default_currency ?? '৳' }}0.00</strong>
                        &nbsp;<i class="bi bi-arrow-right"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Toast container --}}
<div class="toast-container" id="toastContainer"></div>

{{-- ══ CHECKOUT DRAWER ══ --}}
<div class="cd-overlay" id="cdOverlay" onclick="closeDrawer(event)">
    <div class="cd-drawer" id="cdDrawer">
        <div class="cd-head">
            <h5><i class="bi bi-receipt"></i> Checkout</h5>
            <button class="cd-close" onclick="closeDrawer()">&times;</button>
        </div>

        <div class="cd-body">
            <div class="cds-card">
                <div class="cds-row">
                    <span>Total Products <span class="badge-cnt" id="cdTotalProduct">0</span></span>
                </div>
                <div class="cds-row cds-total">
                    <span>Grand Total</span>
                    <strong id="cdTotalAmount">{{ $settings->default_currency ?? '৳' }}0.00</strong>
                </div>
            </div>

            <div>
                <div class="cd-sec-label">Payment Method</div>
                <div class="pm-grid">
                    <div class="pm-btn active" data-method="cash" onclick="selectPM(this)">
                        <i class="bi bi-cash-stack"></i> Cash
                    </div>
                    <div class="pm-btn" data-method="card" onclick="selectPM(this)">
                        <i class="bi bi-credit-card-2-front"></i> Card
                    </div>
                    <div class="pm-btn" data-method="mobile" onclick="selectPM(this)">
                        <i class="bi bi-phone"></i> Mobile
                    </div>
                </div>
            </div>

            <div>
                <div class="cd-sec-label">Received Amount</div>
                <div class="amount-wrap">
                    <span class="amount-prefix">{{ $settings->default_currency ?? '৳' }}</span>
                    <input class="amount-input" type="number" id="cdReceived"
                           placeholder="0.00" min="0" step="0.01" oninput="calcChange()">
                </div>
            </div>

            <div class="change-box" id="cdChangeBox" style="display:none;">
                <span class="cl"><i class="bi bi-arrow-return-left"></i> Change to Return</span>
                <span class="ca" id="cdChange">{{ $settings->default_currency ?? '৳' }}0.00</span>
            </div>

            <div>
                <div class="cd-sec-label">Order Note (Optional)</div>
                <textarea class="cd-note" id="cdNote" rows="2" placeholder="Add any note…"></textarea>
            </div>
        </div>

        <div class="cd-foot">
            <button class="cd-btn-cancel" onclick="closeDrawer()">Cancel</button>
            <button class="cd-btn-confirm" id="cdConfirmBtn" onclick="confirmOrder()">
                <i class="bi bi-check-circle"></i> Confirm &amp; Place Order
            </button>
        </div>
    </div>
</div>

{{-- ══ CUSTOMER MODAL ══ --}}
<div class="pos-modal-bg" id="custModalBg">
    <div class="pos-modal">
        <div class="modal-head">
            <h5><i class="bi bi-person-plus"></i> Add New Customer</h5>
            <button class="modal-close" onclick="closeCustomerModal()">&times;</button>
        </div>
        <div class="modal-body">
            <div class="form-grid">
                <div class="fg" id="fg_first_name">
                    <label>First Name <span style="color:var(--accent)">*</span></label>
                    <input type="text" id="cm_first_name" placeholder="First name">
                    <span class="ferr">First name is required.</span>
                </div>
                <div class="fg" id="fg_last_name">
                    <label>Last Name</label>
                    <input type="text" id="cm_last_name" placeholder="Last name (optional)">
                    <span class="ferr"></span>
                </div>
            </div>
            <div class="form-grid">
                <div class="fg" id="fg_phone">
                    <label>Phone <span style="color:var(--accent)">*</span></label>
                    <input type="text" id="cm_phone" placeholder="Phone number">
                    <span class="ferr">Phone is required.</span>
                </div>
                <div class="fg" id="fg_email">
                    <label>Email <span style="color:var(--accent)">*</span></label>
                    <input type="email" id="cm_email" placeholder="Email address">
                    <span class="ferr">Valid email is required.</span>
                </div>
            </div>
            <div class="form-grid">
                <div class="fg" id="fg_password">
                    <label>Password <span style="color:var(--accent)">*</span></label>
                    <input type="password" id="cm_password" placeholder="Min. 6 characters">
                    <span class="ferr">Minimum 6 characters.</span>
                </div>
                <div class="fg" id="fg_password_confirmation">
                    <label>Confirm Password <span style="color:var(--accent)">*</span></label>
                    <input type="password" id="cm_password_confirmation" placeholder="Repeat password">
                    <span class="ferr">Passwords do not match.</span>
                </div>
            </div>
            <div class="form-grid">
                <div class="fg" id="fg_gender">
                    <label>Gender</label>
                    <select id="cm_gender">
                        <option value="">Select gender</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                        <option value="other">Other</option>
                    </select>
                    <span class="ferr"></span>
                </div>
                <div class="fg" id="fg_dob">
                    <label>Date of Birth</label>
                    <input type="date" id="cm_dob">
                    <span class="ferr"></span>
                </div>
            </div>
        </div>
        <div class="modal-foot">
            <button class="btn-modal-cancel" onclick="closeCustomerModal()">Cancel</button>
            <button class="btn-modal-save" id="btnSaveCust" onclick="saveCustomer()">
                <i class="bi bi-check-circle"></i> Save Customer
            </button>
        </div>
    </div>
</div>

<script>
'use strict';

/* ── Routes ── */
const ROUTES = {
    products:      '{{ route("admin.pointofsalepos.products") }}',
    storeCustomer: '{{ route("admin.pointofsalepos.customers.store") }}',
    applyCoupon:   '{{ route("admin.pointofsalepos.apply.coupon") }}',
    placeOrder:    '{{ route("admin.pointofsalepos.place.order") }}',
    draft:         '{{ route("admin.pointofsalepos.draft") }}',
};
const CSRF     = '{{ csrf_token() }}';
const CURRENCY = '{{ $settings->default_currency ?? "৳" }}';

/* ── State ── */
let cart             = [];
let currentPage      = 1;
let debounceTimer    = null;
let selectedCustomer = null;
let appliedCoupon    = null;
let selectedPayment  = 'cash';

/* ════════════════════════════════════════════════════════════
   HELPERS
════════════════════════════════════════════════════════════ */
function escHtml(str) {
    if (str == null) return '';
    return String(str)
        .replace(/&/g,'&amp;').replace(/</g,'&lt;')
        .replace(/>/g,'&gt;').replace(/"/g,'&quot;')
        .replace(/'/g,'&#39;');
}
function fmt(n) { return CURRENCY + parseFloat(n || 0).toFixed(2); }

/* ════════════════════════════════════════════════════════════
   TOAST
════════════════════════════════════════════════════════════ */
function showToast(msg, type = 'info', ms = 3200) {
    const icons = {
        success: 'check-circle-fill',
        error:   'x-circle-fill',
        warning: 'exclamation-triangle-fill',
        info:    'info-circle-fill'
    };
    const c = document.getElementById('toastContainer');
    const t = document.createElement('div');
    t.className = `pos-toast t-${type}`;
    t.innerHTML = `<i class="bi bi-${icons[type]||icons.info}"></i><span>${msg}</span>`;
    c.appendChild(t);
    setTimeout(() => {
        t.classList.add('t-out');
        t.addEventListener('animationend', () => t.remove(), { once: true });
    }, ms);
}

/* ════════════════════════════════════════════════════════════
   CUSTOMER DROPDOWN
════════════════════════════════════════════════════════════ */
function onCustomerChange(sel) {
    const opt = sel.options[sel.selectedIndex];
    if (!sel.value) { clearCustomer(); return; }

    const id    = sel.value;
    const name  = opt.dataset.name  || '';
    const phone = opt.dataset.phone || '';
    const email = opt.dataset.email || '';

    selectedCustomer = { id, name, phone, email };

    document.getElementById('sciName').textContent = name || '—';

    const phoneEl = document.getElementById('sciPhone');
    const emailEl = document.getElementById('sciEmail');

    if (phone) {
        document.getElementById('sciPhoneVal').textContent = phone;
        phoneEl.style.display = 'flex';
    } else {
        phoneEl.style.display = 'none';
    }
    if (email) {
        document.getElementById('sciEmailVal').textContent = email;
        emailEl.style.display = 'flex';
    } else {
        emailEl.style.display = 'none';
    }

    document.getElementById('selCustInfo').classList.add('show');
    showToast(`Customer selected: ${name}`, 'success', 1800);
}

function clearCustomer() {
    selectedCustomer = null;
    document.getElementById('customerSelect').value = '';
    document.getElementById('selCustInfo').classList.remove('show');
}

/* ════════════════════════════════════════════════════════════
   PRODUCTS
════════════════════════════════════════════════════════════ */
function loadProducts(page = 1) {
    currentPage = page;
    const search   = document.getElementById('productSearch').value.trim();
    const brand    = document.getElementById('brandFilter').value;
    const category = document.getElementById('categoryFilter').value;
    const params   = new URLSearchParams({
        page, search, brand_id: brand, category_id: category
    });

    document.getElementById('productGrid').innerHTML =
        '<div class="pos-loading"><span class="spin bi bi-arrow-repeat"></span>&nbsp;Loading…</div>';

    fetch(ROUTES.products + '?' + params)
        .then(r => { if (!r.ok) throw new Error(); return r.json(); })
        .then(data => {
            renderProducts(data.data);
            renderPagination(data.current_page, data.last_page);
        })
        .catch(() => {
            document.getElementById('productGrid').innerHTML =
                '<div class="pos-loading" style="color:#e7567c;"><i class="bi bi-wifi-off"></i>&nbsp;Failed to load.</div>';
        });
}

function renderProducts(products) {
    const grid = document.getElementById('productGrid');
    if (!products.length) {
        grid.innerHTML = '<div class="pos-loading"><i class="bi bi-inbox"></i>&nbsp;No products found.</div>';
        return;
    }
    grid.innerHTML = products.map(p => {
        const price  = parseFloat(p.discount_price) > 0 ? p.discount_price : p.selling_price;
        const old    = parseFloat(p.discount_price) > 0 ? p.selling_price  : null;
        const offPct = old ? Math.round((1 - price / old) * 100) : null;
        const thumb  = p.thumbnail ? '/' + p.thumbnail : '/images/no-image.png';
        const left   = parseInt(p.stock_quantity) || 0;
        const sold   = parseInt(p.sold)           || 0;
        const encoded = encodeURIComponent(JSON.stringify(p));
        return `
        <div class="product-card" onclick="addToCart(JSON.parse(decodeURIComponent('${encoded}')))">
            <img class="pc-img" src="${thumb}" alt="${escHtml(p.name)}"
                 onerror="this.src='/images/no-image.png'" loading="lazy">
            <div class="pc-info">
                <div class="pc-name">${escHtml(p.name)}</div>
                <div class="pc-price-row">
                    <span class="pc-price">${CURRENCY}${parseFloat(price).toFixed(2)}</span>
                    ${old ? `<span class="pc-old">${CURRENCY}${parseFloat(old).toFixed(2)}</span>` : ''}
                    ${offPct ? `<span class="badge-off">${offPct}% OFF</span>` : ''}
                </div>
                <div class="pc-meta">
                    <span>${sold} Sold</span>
                    <span class="pc-meta-sep">|</span>
                    ${left <= 5
                        ? `<span class="pc-stock-low">${left} Left</span>`
                        : `<span>${left} Left</span>`}
                </div>
            </div>
        </div>`;
    }).join('');
}

function renderPagination(current, last) {
    const pg = document.getElementById('posPagination');
    if (last <= 1) { pg.innerHTML = ''; return; }

    let pages = [];
    if (last <= 7) {
        for (let i = 1; i <= last; i++) pages.push(i);
    } else {
        pages.push(1, 2);
        if (current > 4) pages.push('…');
        for (let i = Math.max(3, current - 1); i <= Math.min(last - 2, current + 1); i++) pages.push(i);
        if (current < last - 3) pages.push('…');
        pages.push(last - 1, last);
    }

    let html = `<button onclick="loadProducts(${current-1})" ${current===1?'disabled':''}>Prev</button>`;
    const seen = new Set();
    pages.forEach(p => {
        if (p === '…') { html += `<span class="pg-ellipsis">…</span>`; }
        else if (!seen.has(p)) {
            seen.add(p);
            html += `<button class="${p===current?'active':''}" onclick="loadProducts(${p})">${p}</button>`;
        }
    });
    html += `<button onclick="loadProducts(${current+1})" ${current===last?'disabled':''}>Next</button>`;
    pg.innerHTML = html;
}

/* ════════════════════════════════════════════════════════════
   CART
════════════════════════════════════════════════════════════ */
function addToCart(product) {
    const existing = cart.find(c => c.product.id === product.id);
    if (existing) {
        existing.qty++;
        showToast(`+1 "${product.name}" → Qty: ${existing.qty}`, 'info', 1600);
    } else {
        cart.push({ product, qty: 1 });
        showToast(`"${product.name}" added`, 'success', 1600);
    }
    renderCart();
}

function removeFromCart(idx) { cart.splice(idx, 1); renderCart(); }

function clearCart() {
    if (!cart.length) return;
    if (!confirm('Remove all items from cart?')) return;
    cart = []; appliedCoupon = null; renderCart();
}

function updateQty(idx, val) {
    const q = parseInt(val, 10);
    cart[idx].qty = (isNaN(q) || q < 1) ? 1 : q;
    renderCart();
}

function renderCart() {
    const list    = document.getElementById('cartList');
    const header  = document.getElementById('cartHeader');
    const summary = document.getElementById('posSummary');

    if (!cart.length) {
        list.innerHTML = `<div class="empty-cart"><span class="ec-icon bi bi-cart3"></span><p>No items yet. Click a product to add.</p></div>`;
        header.style.display  = 'none';
        summary.style.display = 'none';
        return;
    }

    header.style.display  = 'flex';
    summary.style.display = 'block';
    const total = cart.reduce((s, c) => s + c.qty, 0);
    document.getElementById('cartCount').textContent = `(${total} item${total===1?'':'s'})`;

    list.innerHTML = cart.map((c, i) => {
        const p         = c.product;
        const price     = parseFloat(p.discount_price) > 0 ? p.discount_price : p.selling_price;
        const old       = parseFloat(p.discount_price) > 0 ? p.selling_price  : null;
        const thumb     = p.thumbnail ? '/' + p.thumbnail : '/images/no-image.png';
        const variant   = [p.size, p.color].filter(Boolean).join(' | ');
        const lineTotal = (parseFloat(price) * c.qty).toFixed(2);

        const barcodeVal = (p.barcode && String(p.barcode).trim())
            ? String(p.barcode).trim()
            : (p.sku && String(p.sku).trim() ? String(p.sku).trim() : '');

        return `
        <div class="cart-item">
            <img class="ci-img" src="${thumb}" alt="${escHtml(p.name)}"
                 onerror="this.src='/images/no-image.png'">
            <div class="ci-body">
                <div class="ci-name">${escHtml(p.name)}</div>
                ${variant ? `<div class="ci-variant">${escHtml(variant)}</div>` : ''}
                <div>
                    <span class="ci-price">${CURRENCY}${parseFloat(price).toFixed(2)}</span>
                    ${old ? `<span class="ci-old">${CURRENCY}${parseFloat(old).toFixed(2)}</span>` : ''}
                    <span style="font-size:12px;color:var(--blue);margin-left:6px;font-weight:700;">${CURRENCY}${lineTotal}</span>
                </div>
                <div class="ci-actions">
                    <button class="qty-btn" onclick="updateQty(${i}, ${c.qty - 1})">−</button>
                    <input class="qty-input" type="number" value="${c.qty}" min="1"
                           onchange="updateQty(${i}, this.value)"
                           onblur="if(!this.value||this.value<1)this.value=1">
                    <button class="qty-btn" onclick="updateQty(${i}, ${c.qty + 1})">+</button>
                </div>
                ${barcodeVal ? `
                <div class="ci-barcode">
                    <i class="bi bi-upc-scan"></i>
                    <span>SKU NO.</span>
                    <span class="bc-val">${escHtml(barcodeVal)}</span>
                </div>` : ''}
            </div>
            <button class="ci-del" onclick="removeFromCart(${i})" title="Remove">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>`;
    }).join('');

    updateTotals();
}

function calcSubTotal() {
    return cart.reduce((s, c) => {
        const price = parseFloat(c.product.discount_price) > 0
            ? c.product.discount_price
            : c.product.selling_price;
        return s + parseFloat(price) * c.qty;
    }, 0);
}

function updateTotals() {
    const subTotal  = calcSubTotal();
    const discount  = appliedCoupon ? appliedCoupon.discount : 0;
    const afterDisc = Math.max(0, subTotal - discount);

    let totalTax = 0;
    document.querySelectorAll('#taxRows .tax-row-item[data-rate]').forEach(row => {
        const rate = parseFloat(row.dataset.rate) || 0;
        const val  = afterDisc * rate / 100;
        totalTax  += val;
        row.querySelector('.tax-val').textContent = CURRENCY + val.toFixed(2);
    });

    const taxEl = document.getElementById('totalTaxAmt');
    if (taxEl) taxEl.textContent = CURRENCY + totalTax.toFixed(2);

    const grand = afterDisc + totalTax;
    document.getElementById('sumSubTotal').textContent   = CURRENCY + subTotal.toFixed(2);
    document.getElementById('grandTotalBtn').textContent = CURRENCY + grand.toFixed(2);

    const dr = document.getElementById('discountRow');
    if (discount > 0) {
        document.getElementById('sumDiscount').textContent = '– ' + CURRENCY + discount.toFixed(2);
        dr.style.display = 'flex';
    } else {
        dr.style.display = 'none';
    }

    return { subTotal, discount, totalTax, grand };
}

/* ════════════════════════════════════════════════════════════
   CHECKOUT DRAWER
════════════════════════════════════════════════════════════ */
function openCheckoutDrawer() {
    if (!cart.length) { showToast('Cart is empty!', 'warning'); return; }
    const { grand } = updateTotals();
    const totalItems = cart.reduce((s,c) => s + c.qty, 0);
    document.getElementById('cdTotalProduct').textContent = totalItems;
    document.getElementById('cdTotalAmount').textContent  = CURRENCY + grand.toFixed(2);
    document.getElementById('cdReceived').value           = grand.toFixed(2);
    calcChange();
    document.getElementById('cdOverlay').classList.add('show');
}

function closeDrawer(e) {
    if (e && e.target !== document.getElementById('cdOverlay')) return;
    document.getElementById('cdOverlay').classList.remove('show');
}

function selectPM(el) {
    document.querySelectorAll('.pm-btn').forEach(b => b.classList.remove('active'));
    el.classList.add('active');
    selectedPayment = el.dataset.method;
}

function calcChange() {
    const { grand } = updateTotals();
    const received  = parseFloat(document.getElementById('cdReceived').value) || 0;
    const change    = Math.max(0, received - grand);
    const box       = document.getElementById('cdChangeBox');
    if (received > 0) {
        document.getElementById('cdChange').textContent = CURRENCY + change.toFixed(2);
        box.style.display = 'flex';
    } else {
        box.style.display = 'none';
    }
}

function confirmOrder() { placeOrder('completed'); }

/* ════════════════════════════════════════════════════════════
   PLACE ORDER  ★ invoice নতুন ট্যাবে খুলবে ★
════════════════════════════════════════════════════════════ */
function placeOrder(status = 'completed') {
    if (!cart.length) { showToast('Cart is empty!', 'warning'); return; }

    const { subTotal, discount, totalTax } = updateTotals();
    const url  = status === 'draft' ? ROUTES.draft : ROUTES.placeOrder;
    const body = {
        items:           cart.map(c => ({ id: c.product.id, qty: c.qty })),
        customer_id:     selectedCustomer ? selectedCustomer.id : null,
        discount,
        tax_amount:      totalTax,
        coupon_code:     appliedCoupon ? appliedCoupon.code : null,
        payment_method:  selectedPayment,
        note:            (document.getElementById('cdNote')?.value || '').trim() || null,
        received_amount: parseFloat(document.getElementById('cdReceived')?.value || 0),
    };

    const btn = document.getElementById('cdConfirmBtn');
    if (btn) btn.disabled = true;

    fetch(url, {
        method:  'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
        body:    JSON.stringify(body),
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            document.getElementById('cdOverlay').classList.remove('show');

            if (status === 'completed' && data.invoice_url) {
                /* ★ নতুন ট্যাবে ইনভয়েস ওপেন করো, POS পেজ এখানেই থাকুক ★ */
                window.open(data.invoice_url, '_blank');
                showToast('Order placed! Invoice opened in new tab.', 'success', 3000);
                resetCart();
            } else {
                showToast(data.message, 'success', 5000);
                resetCart();
            }
        } else {
            showToast('❌ ' + (data.message || 'Something went wrong.'), 'error', 5000);
        }
    })
    .catch(() => showToast('Network error. Please try again.', 'error'))
    .finally(() => { if (btn) btn.disabled = false; });
}

function resetCart() {
    cart = []; appliedCoupon = null; selectedCustomer = null; selectedPayment = 'cash';
    clearCustomer();
    document.getElementById('couponInput').value       = '';
    document.getElementById('couponTag').classList.remove('show');
    document.getElementById('couponRow').style.display = 'flex';
    const noteEl = document.getElementById('cdNote');
    if (noteEl) noteEl.value = '';
    document.querySelectorAll('.pm-btn').forEach(b => b.classList.remove('active'));
    const cashBtn = document.querySelector('.pm-btn[data-method="cash"]');
    if (cashBtn) cashBtn.classList.add('active');
    renderCart();
}

/* ════════════════════════════════════════════════════════════
   COUPON
════════════════════════════════════════════════════════════ */
function applyCoupon() {
    const code = document.getElementById('couponInput').value.trim();
    if (!code)        { showToast('Please enter a coupon code.', 'warning'); return; }
    if (!cart.length) { showToast('Add items to cart first.', 'warning');   return; }

    const subTotal = calcSubTotal();

    fetch(ROUTES.applyCoupon, {
        method:  'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
        body:    JSON.stringify({ coupon_code: code, sub_total: subTotal }),
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            appliedCoupon = { code, discount: parseFloat(data.discount) };
            document.getElementById('couponTagText').textContent =
                `"${code}" — −${CURRENCY}${parseFloat(data.discount).toFixed(2)} off`;
            document.getElementById('couponTag').classList.add('show');
            document.getElementById('couponRow').style.display = 'none';
            updateTotals();
            showToast(data.message, 'success');
        } else {
            showToast(data.message, 'error');
        }
    })
    .catch(() => showToast('Failed to apply coupon.', 'error'));
}

function removeCoupon() {
    appliedCoupon = null;
    document.getElementById('couponInput').value       = '';
    document.getElementById('couponTag').classList.remove('show');
    document.getElementById('couponRow').style.display = 'flex';
    updateTotals();
    showToast('Coupon removed.', 'info');
}

/* ════════════════════════════════════════════════════════════
   CUSTOMER MODAL
════════════════════════════════════════════════════════════ */
function openCustomerModal() {
    ['cm_first_name','cm_last_name','cm_phone','cm_email',
     'cm_password','cm_password_confirmation'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.value = '';
    });
    document.getElementById('cm_gender').value = '';
    document.getElementById('cm_dob').value    = '';
    document.querySelectorAll('.fg.has-error').forEach(el => el.classList.remove('has-error'));
    document.getElementById('custModalBg').classList.add('show');
}

function closeCustomerModal() {
    document.getElementById('custModalBg').classList.remove('show');
}

function setFgError(id, msg) {
    const g = document.getElementById(id);
    if (!g) return;
    g.classList.add('has-error');
    const e = g.querySelector('.ferr');
    if (e && msg) e.textContent = msg;
}

function saveCustomer() {
    document.querySelectorAll('.fg.has-error').forEach(el => el.classList.remove('has-error'));

    const firstName = document.getElementById('cm_first_name').value.trim();
    const phone     = document.getElementById('cm_phone').value.trim();
    const email     = document.getElementById('cm_email').value.trim();
    const password  = document.getElementById('cm_password').value;
    const confirm   = document.getElementById('cm_password_confirmation').value;

    let hasErr = false;
    if (!firstName)          { setFgError('fg_first_name', 'First name is required.');             hasErr = true; }
    if (!phone)               { setFgError('fg_phone', 'Phone is required.');                      hasErr = true; }
    if (!email)               { setFgError('fg_email', 'Email is required.');                      hasErr = true; }
    if (password.length < 6) { setFgError('fg_password', 'Minimum 6 characters.');                 hasErr = true; }
    if (password !== confirm) { setFgError('fg_password_confirmation', 'Passwords do not match.'); hasErr = true; }
    if (hasErr) return;

    const btn = document.getElementById('btnSaveCust');
    btn.disabled = true;
    btn.innerHTML = '<span class="spin bi bi-arrow-repeat"></span> Saving…';

    fetch(ROUTES.storeCustomer, {
        method:  'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
        body: JSON.stringify({
            first_name:            firstName,
            last_name:             document.getElementById('cm_last_name').value.trim(),
            phone, email, password,
            password_confirmation: confirm,
            gender:                document.getElementById('cm_gender').value    || null,
            date_of_birth:         document.getElementById('cm_dob').value       || null,
        }),
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            closeCustomerModal();

            const sel = document.getElementById('customerSelect');
            const opt = document.createElement('option');
            opt.value         = res.id;
            opt.dataset.name  = res.name;
            opt.dataset.phone = res.phone;
            opt.dataset.email = res.email;
            opt.textContent   = `${res.name} — ${res.phone}`;
            sel.appendChild(opt);
            sel.value = res.id;
            onCustomerChange(sel);

            showToast('Customer created and selected!', 'success');
        } else if (res.errors) {
            const map = {
                first_name:            'fg_first_name',
                last_name:             'fg_last_name',
                phone:                 'fg_phone',
                email:                 'fg_email',
                password:              'fg_password',
                password_confirmation: 'fg_password_confirmation',
            };
            Object.entries(res.errors).forEach(([f, msgs]) => {
                if (map[f]) setFgError(map[f], msgs[0]);
            });
        } else {
            showToast(res.message || 'Failed to save customer.', 'error');
        }
    })
    .catch(() => showToast('Something went wrong.', 'error'))
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-check-circle"></i> Save Customer';
    });
}

/* ── Close modals on background click ── */
document.querySelectorAll('.pos-modal-bg').forEach(bg => {
    bg.addEventListener('click', e => {
        if (e.target === bg) bg.classList.remove('show');
    });
});

/* ── Filter listeners ── */
document.getElementById('brandFilter').addEventListener('change', () => loadProducts(1));
document.getElementById('categoryFilter').addEventListener('change', () => loadProducts(1));
document.getElementById('productSearch').addEventListener('input', function () {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => loadProducts(1), 350);
});
document.getElementById('couponInput').addEventListener('keydown', e => {
    if (e.key === 'Enter') applyCoupon();
});

/* ── Init ── */
loadProducts(1);
</script>

@endsection
