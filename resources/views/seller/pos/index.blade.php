@extends('admin.master')

@section('content')

<style>
/* ════════════════════════════════════════════════════════════
   POS — Point of Sale  |  Seller Interface
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

.pos-wrap {
    display: flex;
    height: calc(100vh - 60px);
    overflow: hidden;
    background: var(--bg);
    font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
}

.pos-left {
    flex: 1 1 0;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    padding: 16px 16px 12px 16px;
    gap: 12px;
    min-width: 0;
}

.pos-page-title {
    font-size: 16px;
    font-weight: 700;
    color: var(--text);
    margin: 0 0 2px 0;
}

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
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='11' height='11' viewBox='0 0 24 24' fill='none' stroke='%236b7a99' stroke-width='2'%3E%3Cpath d='m6 9 6 6 6-6'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 10px center;
}
.search-wrap { flex: 1; position: relative; }
.search-wrap input {
    width: 100%; height: 40px;
    border: 1.5px solid var(--border);
    border-radius: var(--radius-sm);
    padding: 0 14px 0 38px;
    font-size: 13px; background: var(--white);
    color: var(--text); outline: none;
}
.search-wrap .si {
    position: absolute; left: 12px; top: 50%;
    transform: translateY(-50%); color: #bbc; font-size: 14px;
    pointer-events: none;
}

.product-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
    gap: 12px;
    overflow-y: auto;
    flex: 1;
    align-content: start;
}

.product-card {
    background: var(--white);
    border-radius: var(--radius);
    border: 1px solid var(--border);
    padding: 12px;
    cursor: pointer;
    transition: all 0.2s ease;
    display: flex;
    flex-direction: column;
    gap: 8px;
}
.product-card:hover { transform: translateY(-2px); box-shadow: var(--shadow); border-color: var(--accent); }

.pc-img-wrap {
    width: 100%;
    height: 140px;
    background: #f8fafc;
    border-radius: 6px;
    overflow: hidden;
    position: relative;
}
.pc-img { width: 100%; height: 100%; object-fit: contain; }
.pc-info { flex: 1; }
.pc-name { font-size: 13px; font-weight: 600; color: var(--text); display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; height: 36px; line-height: 1.4; }
.pc-price-row { display: flex; align-items: center; gap: 8px; margin-top: 5px; }
.pc-price { font-size: 15px; font-weight: 700; color: var(--blue); }
.pc-old { font-size: 12px; color: var(--muted); text-decoration: line-through; }
.badge-off { font-size: 10px; background: #fee2e2; color: #ef4444; padding: 2px 6px; border-radius: 4px; font-weight: 700; }
.pc-meta { display: flex; align-items: center; justify-content: space-between; margin-top: 8px; font-size: 11px; color: var(--muted); padding-top: 8px; border-top: 1px solid #f1f5f9; }

.pos-pagination { display: flex; align-items: center; gap: 6px; padding: 10px 0; }
.pos-pagination button { min-width: 32px; height: 32px; border: 1px solid var(--border); background: var(--white); border-radius: 6px; font-size: 12px; cursor: pointer; transition: all .2s; }
.pos-pagination button.active { background: var(--accent); color: #fff; border-color: var(--accent); }

.pos-right {
    width: 420px;
    flex-shrink: 0;
    display: flex;
    flex-direction: column;
    background: var(--white);
    border-left: 1px solid var(--border);
}

.cust-section { padding: 16px; border-bottom: 1px solid var(--border); }
.cust-select-row { display: flex; gap: 10px; }
.cust-select-wrap { flex: 1; position: relative; }
.cust-select-wrap select { width: 100%; height: 42px; border: 1.5px solid var(--border); border-radius: 6px; padding: 0 12px 0 36px; font-size: 13px; appearance: none; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='11' height='11' viewBox='0 0 24 24' fill='none' stroke='%236b7a99' stroke-width='2'%3E%3Cpath d='m6 9 6 6 6-6'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 10px center; }
.cust-icon { position: absolute; left: 12px; top: 11px; color: var(--muted); }
.btn-add-cust { height: 42px; padding: 0 12px; border: 1.5px solid var(--accent); background: transparent; color: var(--accent); border-radius: 6px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 6px; }
.btn-add-cust:hover { background: var(--accent); color: #fff; }

.pos-right-body { flex: 1; overflow-y: auto; padding: 16px; }
.cart-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 15px; }
.cart-item { display: flex; gap: 12px; padding: 12px 0; border-bottom: 1px solid #f1f5f9; position: relative; }
.ci-img { width: 50px; height: 50px; object-fit: contain; border-radius: 4px; background: #f8fafc; }
.ci-info { flex: 1; }
.ci-name { font-size: 13px; font-weight: 600; color: var(--text); margin-bottom: 2px; }
.ci-price { font-size: 14px; font-weight: 700; color: var(--blue); }
.ci-qty-row { display: flex; align-items: center; gap: 8px; margin-top: 8px; }
.qty-btn { width: 24px; height: 24px; border: 1px solid var(--border); border-radius: 4px; background: #fff; cursor: pointer; }
.qty-input { width: 40px; height: 24px; text-align: center; border: 1px solid var(--border); border-radius: 4px; font-size: 12px; }
.ci-del { position: absolute; top: 12px; right: 0; color: #cbd5e1; cursor: pointer; }
.ci-del:hover { color: var(--accent); }

.pos-summary { padding: 16px; border-top: 1px solid var(--border); background: #f8fafc; }
.sum-row { display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 13px; }
.grand-total { font-size: 18px; font-weight: 800; color: var(--accent); border-top: 1px dashed var(--border); padding-top: 12px; margin-top: 12px; }

.pos-actions { display: grid; grid-template-columns: 100px 1fr; gap: 12px; margin-top: 16px; }
.btn-draft { height: 48px; border: 1px solid #fde68a; background: #fffbe6; color: #a16207; border-radius: 8px; font-weight: 700; cursor: pointer; }
.btn-checkout { height: 48px; background: var(--accent); color: #fff; border: none; border-radius: 8px; font-weight: 700; font-size: 15px; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; }

.cd-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; display: none; }
.cd-drawer { position: fixed; right: -400px; top: 0; bottom: 0; width: 400px; background: #fff; z-index: 1001; transition: right 0.3s ease; padding: 20px; display: flex; flex-direction: column; }
.cd-overlay.show { display: block; }
.cd-overlay.show + .cd-drawer, .cd-drawer.show { right: 0; }

.empty-cart { text-align: center; padding: 40px 0; color: var(--muted); }
.empty-cart i { font-size: 40px; opacity: 0.3; display: block; margin-bottom: 10px; }

/* Modal */
.pos-modal-bg { position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 2000; display: none; align-items: center; justify-content: center; }
.pos-modal { background: #fff; width: 500px; border-radius: 12px; padding: 24px; }
.pos-modal-bg.show { display: flex; }
</style>

<div class="pos-wrap">
    {{-- Left Panel: Products --}}
    <div class="pos-left">
        <div class="pos-page-title">Point of Sale (POS)</div>
        
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
                <input type="text" id="productSearch" placeholder="Search by product name">
            </div>
        </div>

        <div class="product-grid" id="productGrid">
            {{-- Products load here --}}
        </div>

        <div class="pos-pagination" id="posPagination"></div>
    </div>

    {{-- Right Panel: Cart --}}
    <div class="pos-right">
        <div class="cust-section">
            <div class="cust-select-row">
                <div class="cust-select-wrap">
                    <i class="bi bi-person cust-icon"></i>
                    <select id="customerSelect" onchange="updateCustomerView()">
                        <option value="">Walk-in Customer</option>
                        @foreach($customers as $c)
                            <option value="{{ $c->id }}" data-name="{{ $c->first_name }} {{ $c->last_name }}" data-phone="{{ $c->user->phone ?? '' }}">
                                {{ $c->first_name }} {{ $c->last_name }} ({{ $c->user->phone ?? '' }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <button class="btn-add-cust" onclick="openCustomerModal()"><i class="bi bi-plus-lg"></i> Customer</button>
            </div>
        </div>

        <div class="pos-right-body" id="cartContainer">
            <div class="cart-header">
                <h6><i class="bi bi-cart3"></i> Cart <span id="cartCountBadge">(0)</span></h6>
                <a href="javascript:void(0)" onclick="clearCart()" class="text-muted small">Clear All</a>
            </div>
            <div id="cartList">
                <div class="empty-cart">
                    <i class="bi bi-cart-x"></i>
                    <p>Cart is empty</p>
                </div>
            </div>
        </div>

        <div class="pos-summary">
            <div class="sum-row">
                <span>Sub Total</span>
                <strong id="sumSubTotal">৳0.00</strong>
            </div>
            <div class="sum-row text-danger">
                <span>Discount Amount</span>
                <strong id="sumDiscount">- ৳0.00</strong>
            </div>
            <div class="sum-row">
                <span>Shipping Charge</span>
                <strong id="sumShipping">৳0.00</strong>
            </div>

            <div class="mt-3 p-3 rounded" style="background: #f1f5f9;">
                <div class="small fw-bold mb-2">VAT & Taxes Summary</div>
                <div id="taxSummaryList">
                    @foreach($taxes as $tax)
                    <div class="d-flex justify-content-between small text-muted mb-1 tax-item" data-rate="{{ $tax->percentage }}" data-name="{{ $tax->name }}">
                        <span>{{ $tax->name }}({{ (int)$tax->percentage }}%)</span>
                        <span class="tax-value">৳0.00</span>
                    </div>
                    @endforeach
                </div>
                <div class="d-flex justify-content-between fw-bold border-top pt-2 mt-2">
                    <span>Total Tax Amount:</span>
                    <span id="sumTotalTax">৳0.00</span>
                </div>
            </div>

            <div class="mt-3">
                <div class="small fw-bold mb-2">Shipping Information</div>
                <select id="shippingSelect" class="form-select form-select-sm mb-2" onchange="updateTotals()">
                    <option value="0" data-charge="0">Self Pickup / No Shipping</option>
                    @foreach($shippingCharges as $sc)
                        <option value="{{ $sc->id }}" data-charge="{{ $sc->charge }}">{{ $sc->area_name }} (৳{{ $sc->charge }})</option>
                    @endforeach
                </select>
                
                <div class="input-group">
                    <input type="text" id="couponCode" class="form-control" placeholder="Add Coupon">
                    <button class="btn btn-danger" type="button" onclick="applyCoupon()" style="background: #e7567c; border:none;">Apply</button>
                </div>
                <div id="couponStatus" class="small mt-1" style="display:none;"></div>
            </div>

            <div class="sum-row grand-total">
                <span>Grand Total</span>
                <strong id="sumGrandTotal">৳0.00</strong>
            </div>

            <div class="pos-actions">
                <button class="btn-draft" onclick="placeOrder('draft')">Draft</button>
                <button class="btn-checkout" onclick="openCheckout()">Grand Total <span id="btnTotal">৳0.00</span> <i class="bi bi-arrow-right"></i></button>
            </div>
        </div>
    </div>
</div>

{{-- Checkout Drawer --}}
<div class="cd-overlay" id="checkoutOverlay" onclick="closeCheckout()"></div>
<div class="cd-drawer" id="checkoutDrawer">
    <h5 class="mb-4">Checkout</h5>
    <div class="mb-3">
        <label class="form-label small fw-bold">Payment Method</label>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-primary btn-sm flex-fill active" onclick="setPayment('cash', this)">Cash</button>
            <button class="btn btn-outline-primary btn-sm flex-fill" onclick="setPayment('card', this)">Card</button>
            <button class="btn btn-outline-primary btn-sm flex-fill" onclick="setPayment('mobile', this)">Mobile</button>
        </div>
    </div>
    <div class="mb-4">
        <label class="form-label small fw-bold">Received Amount</label>
        <input type="number" id="receivedAmt" class="form-control form-control-lg fw-bold" oninput="calcChange()">
    </div>
    <div class="p-3 bg-light rounded mb-4">
        <div class="d-flex justify-content-between mb-2">
            <span>Payable:</span>
            <strong id="payTotal">৳0.00</strong>
        </div>
        <div class="d-flex justify-content-between text-success">
            <span>Change:</span>
            <strong id="payChange">৳0.00</strong>
        </div>
    </div>
    <button class="btn btn-primary w-100 py-3 fw-bold" onclick="placeOrder('completed')">Confirm Order</button>
</div>

{{-- Customer Modal --}}
<div class="pos-modal-bg" id="custModal">
    <div class="pos-modal">
        <h5 class="mb-4">Add New Customer</h5>
        <div class="row g-3">
            <div class="col-6">
                <label class="small fw-bold">First Name</label>
                <input type="text" id="cm_first_name" class="form-control">
            </div>
            <div class="col-6">
                <label class="small fw-bold">Last Name</label>
                <input type="text" id="cm_last_name" class="form-control">
            </div>
            <div class="col-12">
                <label class="small fw-bold">Phone</label>
                <input type="text" id="cm_phone" class="form-control">
            </div>
            <div class="col-12">
                <label class="small fw-bold">Email</label>
                <input type="email" id="cm_email" class="form-control">
            </div>
            <div class="col-6">
                <label class="small fw-bold">Password</label>
                <div class="input-group">
                    <input type="password" id="cm_password" class="form-control">
                    <button class="btn btn-outline-secondary" type="button" onclick="togglePass('cm_password')"><i class="bi bi-eye"></i></button>
                </div>
            </div>
            <div class="col-6">
                <label class="small fw-bold">Confirm Password</label>
                <div class="input-group">
                    <input type="password" id="cm_password_confirmation" class="form-control">
                    <button class="btn btn-outline-secondary" type="button" onclick="togglePass('cm_password_confirmation')"><i class="bi bi-eye"></i></button>
                </div>
            </div>
        </div>
        <div class="d-flex justify-content-end gap-2 mt-4">
            <button class="btn btn-light" onclick="closeCustomerModal()">Cancel</button>
            <button class="btn btn-primary" onclick="saveCustomer()">Save Customer</button>
        </div>
    </div>
</div>

<script>
let cart = [];
let currentPage = 1;
let selectedPayment = 'cash';
const ROUTES = {
    products: '{{ route("seller.pos.products") }}',
    storeCustomer: '{{ route("seller.pos.customer.store") }}',
    placeOrder: '{{ route("seller.pos.place-order") }}',
    draft: '{{ route("seller.pos.draft") }}'
};

function loadProducts(page = 1) {
    currentPage = page;
    const search = document.getElementById('productSearch').value;
    const brand = document.getElementById('brandFilter').value;
    const cat = document.getElementById('categoryFilter').value;
    
    fetch(`${ROUTES.products}?page=${page}&search=${search}&brand_id=${brand}&category_id=${cat}`)
        .then(res => res.json())
        .then(data => {
            renderProducts(data.data);
            renderPagination(data);
        });
}

function renderProducts(products) {
    const grid = document.getElementById('productGrid');
    grid.innerHTML = products.map(p => {
        const price = p.discount_price > 0 ? p.discount_price : p.selling_price;
        const old = p.discount_price > 0 ? p.selling_price : '';
        const off = old ? Math.round((1 - price/old)*100) : 0;
        return `
            <div class="product-card" onclick='addToCart(${JSON.stringify(p)})'>
                <div class="pc-img-wrap">
                    <img src="/${p.thumbnail}" class="pc-img" onerror="this.src='/images/no-image.png'">
                    ${p.product_type === 'digital' ? '<span class="badge bg-primary position-absolute top-0 end-0 m-2" style="font-size: 10px;">Digital</span>' : ''}
                </div>
                <div class="pc-info">
                    <div class="pc-name">${p.name}</div>
                    <div class="pc-price-row">
                        <span class="pc-price">৳${price}</span>
                        ${old ? `<span class="pc-old">৳${old}</span>` : ''}
                        ${off ? `<span class="badge-off">${off}% OFF</span>` : ''}
                    </div>
                </div>
                <div class="pc-meta">
                    <span>Stock: ${p.stock_quantity}</span>
                    <span>SKU: ${p.sku}</span>
                </div>
            </div>
        `;
    }).join('');
}

function renderPagination(data) {
    const pg = document.getElementById('posPagination');
    let html = '';
    for(let i=1; i<=data.last_page; i++) {
        html += `<button class="${i===data.current_page?'active':''}" onclick="loadProducts(${i})">${i}</button>`;
    }
    pg.innerHTML = html;
}

function addToCart(product) {
    const existing = cart.find(item => item.id === product.id && item.product_type === product.product_type);
    if(existing) {
        existing.qty++;
    } else {
        const price = product.discount_price > 0 ? product.discount_price : product.selling_price;
        cart.push({ 
            id: product.id, 
            name: product.name, 
            price: price, 
            thumbnail: product.thumbnail, 
            qty: 1,
            product_type: product.product_type 
        });
    }
    renderCart();
}

function renderCart() {
    const list = document.getElementById('cartList');
    if(cart.length === 0) {
        list.innerHTML = '<div class="empty-cart"><i class="bi bi-cart-x"></i><p>Cart is empty</p></div>';
        updateTotals();
        return;
    }
    
    list.innerHTML = cart.map((item, index) => `
        <div class="cart-item">
            <img src="/${item.thumbnail}" class="ci-img" onerror="this.src='/images/no-image.png'">
            <div class="ci-info">
                <div class="ci-name">${item.name}</div>
                <div class="ci-price">৳${item.price} <small class="text-muted">(${item.product_type})</small></div>
                <div class="ci-qty-row">
                    <button class="qty-btn" onclick="updateQty(${index}, -1)">-</button>
                    <input type="text" value="${item.qty}" class="qty-input" readonly>
                    <button class="qty-btn" onclick="updateQty(${index}, 1)">+</button>
                </div>
            </div>
            <i class="bi bi-trash ci-del" onclick="removeFromCart(${index})"></i>
        </div>
    `).join('');
    
    updateTotals();
}

function updateQty(index, delta) {
    cart[index].qty += delta;
    if(cart[index].qty < 1) cart[index].qty = 1;
    renderCart();
}

function removeFromCart(index) {
    cart.splice(index, 1);
    renderCart();
}

function clearCart() {
    cart = [];
    renderCart();
}

let appliedDiscount = 0;
let appliedCouponCode = null;

function updateTotals() {
    const subTotal = cart.reduce((sum, item) => sum + (item.price * item.qty), 0);
    
    // Calculate Taxes
    let totalTax = 0;
    document.querySelectorAll('.tax-item').forEach(el => {
        const rate = parseFloat(el.dataset.rate);
        const taxVal = (subTotal - appliedDiscount) * (rate / 100);
        totalTax += taxVal;
        el.querySelector('.tax-value').innerText = `৳${taxVal.toFixed(2)}`;
    });
    
    const shippingOpt = document.getElementById('shippingSelect').selectedOptions[0];
    const shippingCharge = parseFloat(shippingOpt.dataset.charge || 0);
    
    const grandTotal = (subTotal - appliedDiscount) + totalTax + shippingCharge;
    
    document.getElementById('sumSubTotal').innerText = `৳${subTotal.toFixed(2)}`;
    document.getElementById('sumDiscount').innerText = `- ৳${appliedDiscount.toFixed(2)}`;
    document.getElementById('sumShipping').innerText = `৳${shippingCharge.toFixed(2)}`;
    document.getElementById('sumTotalTax').innerText = `৳${totalTax.toFixed(2)}`;
    document.getElementById('sumGrandTotal').innerText = `৳${grandTotal.toFixed(2)}`;
    document.getElementById('btnTotal').innerText = `৳${grandTotal.toFixed(2)}`;
    document.getElementById('cartCountBadge').innerText = `(${cart.length})`;
}

function applyCoupon() {
    const code = document.getElementById('couponCode').value.trim();
    if(!code) return alert('Please enter a coupon code');
    if(cart.length === 0) return alert('Cart is empty');
    
    const subTotal = cart.reduce((sum, item) => sum + (item.price * item.qty), 0);
    
    fetch('{{ route("seller.pos.coupon.apply") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({ coupon_code: code, sub_total: subTotal })
    })
    .then(res => res.json())
    .then(data => {
        const status = document.getElementById('couponStatus');
        if(data.success) {
            appliedDiscount = data.discount;
            appliedCouponCode = code;
            status.innerText = data.message;
            status.className = 'small mt-1 text-success';
            status.style.display = 'block';
            updateTotals();
        } else {
            appliedDiscount = 0;
            appliedCouponCode = null;
            status.innerText = data.message;
            status.className = 'small mt-1 text-danger';
            status.style.display = 'block';
            updateTotals();
        }
    });
}

function openCheckout() {
    if(cart.length === 0) return alert('Cart is empty');
    const subTotal = cart.reduce((sum, item) => sum + (item.price * item.qty), 0);
    
    // Recalculate tax for checkout display
    let totalTax = 0;
    document.querySelectorAll('.tax-item').forEach(el => {
        const rate = parseFloat(el.dataset.rate);
        totalTax += (subTotal - appliedDiscount) * (rate / 100);
    });
    
    const total = (subTotal - appliedDiscount) + totalTax;
    
    document.getElementById('checkoutOverlay').classList.add('show');
    document.getElementById('checkoutDrawer').classList.add('show');
    document.getElementById('payTotal').innerText = `৳${total.toFixed(2)}`;
    document.getElementById('receivedAmt').value = total.toFixed(2);
    calcChange();
}

function closeCheckout() {
    document.getElementById('checkoutOverlay').classList.remove('show');
    document.getElementById('checkoutDrawer').classList.remove('show');
}

function setPayment(method, btn) {
    selectedPayment = method;
    document.querySelectorAll('#checkoutDrawer .btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
}

function calcChange() {
    const subTotal = cart.reduce((sum, item) => sum + (item.price * item.qty), 0);
    let totalTax = 0;
    document.querySelectorAll('.tax-item').forEach(el => {
        const rate = parseFloat(el.dataset.rate);
        totalTax += (subTotal - appliedDiscount) * (rate / 100);
    });
    const total = (subTotal - appliedDiscount) + totalTax;
    
    const received = document.getElementById('receivedAmt').value;
    const change = received - total;
    document.getElementById('payChange').innerText = `৳${change > 0 ? change.toFixed(2) : '0.00'}`;
}

function togglePass(id) {
    const el = document.getElementById(id);
    const btn = el.nextElementSibling;
    const icon = btn.querySelector('i');
    if(el.type === 'password') {
        el.type = 'text';
        icon.classList.replace('bi-eye', 'bi-eye-slash');
    } else {
        el.type = 'password';
        icon.classList.replace('bi-eye-slash', 'bi-eye');
    }
}

function placeOrder(status) {
    const customerId = document.getElementById('customerSelect').value;
    // Removed strict check for customerId to allow Walk-in Customer
    
    const subTotal = cart.reduce((sum, item) => sum + (item.price * item.qty), 0);
    let totalTax = 0;
    document.querySelectorAll('.tax-item').forEach(el => {
        const rate = parseFloat(el.dataset.rate);
        totalTax += (subTotal - appliedDiscount) * (rate / 100);
    });

    const shippingOpt = document.getElementById('shippingSelect').selectedOptions[0];
    const shippingCharge = parseFloat(shippingOpt.dataset.charge || 0);

    const data = {
        status: status,
        customer_id: customerId,
        payment_method: selectedPayment,
        discount: appliedDiscount,
        tax_amount: totalTax,
        delivery_charge: shippingCharge,
        coupon_code: appliedCouponCode,
        items: cart.map(item => ({ 
            id: item.id, 
            qty: item.qty,
            product_type: item.product_type
        }))
    };
    
    fetch(status === 'draft' ? ROUTES.draft : ROUTES.placeOrder, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify(data)
    })
    .then(res => res.json())
    .then(data => {
        if(data.success) {
            alert(data.message);
            if(data.invoice_id) {
                window.open(`{{ url('seller/pos/invoice') }}/${data.invoice_id}`, '_blank');
            }
            cart = [];
            renderCart();
            closeCheckout();
            loadProducts(1); // Refresh stock
        } else {
            alert(data.message);
        }
    });
}

function openCustomerModal() {
    document.getElementById('custModal').classList.add('show');
}
function closeCustomerModal() {
    document.getElementById('custModal').classList.remove('show');
}

function handleBarcodeScan(e) {
    e.preventDefault();
    const searchVal = e.target.value.trim();
    if(!searchVal) return;

    // We rely on the current search results (which are already filtered as the user/scanner types)
    // If there's exactly one product showing, add it.
    const productCards = document.querySelectorAll('.product-card');
    if(productCards.length === 1) {
        const productId = productCards[0].getAttribute('onclick').match(/\d+/)[0];
        const type = productCards[0].getAttribute('onclick').includes('digital') ? 'digital' : 'normal';
        addToCart(productId, type);
        e.target.value = ''; // Clear search
        loadProducts(1); // Reset view
    } else {
        // If multiple, maybe wait for a more specific scan or just do nothing
    }
}

function saveCustomer() {
    const data = {
        first_name: document.getElementById('cm_first_name').value,
        last_name: document.getElementById('cm_last_name').value,
        phone: document.getElementById('cm_phone').value,
        email: document.getElementById('cm_email').value,
        password: document.getElementById('cm_password').value,
        password_confirmation: document.getElementById('cm_password_confirmation').value,
    };
    
    fetch(ROUTES.storeCustomer, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify(data)
    })
    .then(res => res.json())
    .then(res => {
        if(res.success) {
            const sel = document.getElementById('customerSelect');
            const opt = document.createElement('option');
            opt.value = res.id;
            opt.text = `${res.name}`;
            sel.add(opt);
            sel.value = res.id;
            closeCustomerModal();
            alert('Customer created successfully!');
        } else {
            let msg = res.message || 'Error creating customer';
            if(res.errors) {
                msg = Object.values(res.errors).flat().join('\n');
            }
            alert(msg);
        }
    });
}

// Initial load
loadProducts();
document.getElementById('productSearch').addEventListener('input', () => loadProducts(1));
document.getElementById('brandFilter').addEventListener('change', () => loadProducts(1));
document.getElementById('categoryFilter').addEventListener('change', () => loadProducts(1));
</script>

@endsection
