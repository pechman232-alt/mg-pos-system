@extends('layouts.homePages')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<meta name="csrf-token" content="{{ csrf_token() }}">

<style>
    :root { --brand-red: #CE181E; --text-heading: #0f172a; --bg-main: #f8fafc; --border-color: #cbd5e1; }
    .pos-container { font-family: 'Plus Jakarta Sans', sans-serif !important; padding: 1.5rem; background: var(--bg-main); min-height: 100vh; }
    
    .checkout-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem; }
    .product-panel, .cart-panel { background: #fff; border: 1px solid var(--border-color); border-radius: 10px; padding: 1.5rem; }
    
    .product-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 1rem; margin-top: 1rem; }
    .product-card { border: 1px solid var(--border-color); border-radius: 8px; padding: 1rem; text-align: center; cursor: pointer; transition: 0.2s; }
    .product-card:hover { border-color: var(--brand-red); box-shadow: 0 4px 6px rgba(206,24,30,0.1); transform: translateY(-2px); }
    
    .product-name { font-weight: 600; font-size: 0.9rem; color: var(--text-heading); margin-bottom: 0.5rem; }
    .product-price { color: var(--brand-red); font-weight: 700; }

    .cart-item { display: flex; justify-content: space-between; align-items: center; padding: 10px 0; border-bottom: 1px dashed var(--border-color); }
    .qty-btn { background: #f1f5f9; border: 1px solid var(--border-color); border-radius: 4px; width: 25px; height: 25px; cursor: pointer; font-weight: bold; }
    
    /* ---------------- Popup Modal CSS ---------------- */
    .modal-overlay { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center; backdrop-filter: blur(2px); }
    .modal-box { background: #fff; padding: 2rem; border-radius: 10px; width: 400px; text-align: center; box-shadow: 0 10px 25px rgba(0,0,0,0.1); }
    .modal-total { font-size: 2.5rem; color: var(--brand-red); font-weight: 700; margin: 1rem 0; }
    .btn-modal { padding: 0.8rem 1.5rem; border: none; border-radius: 6px; font-weight: 600; font-size: 1rem; cursor: pointer; width: 100%; margin-bottom: 10px; transition: 0.2s; }
    .btn-print { background: #0f172a; color: #fff; }
    .btn-print:hover { background: #1e293b; }
    .btn-pay { background: var(--brand-red); color: #fff; }
    .btn-pay:hover { background: #a81116; }
    .btn-close { background: transparent; color: #64748b; margin-top: 10px; }

    /* ---------------- សម្រាប់តែពេល Print (លាក់ផ្ទាំង POS បង្ហាញតែវិក្កយបត្រ) ---------------- */
    #print-section { display: none; }
    @media print {
        body * { visibility: hidden; }
        #print-section, #print-section * { visibility: visible; }
        #print-section { display: block; position: absolute; left: 0; top: 0; width: 100%; max-width: 300px; font-family: monospace; }
        .modal-overlay { display: none !important; }
    }
</style>

<div class="pos-container">
    <h2 style="margin-top: 0; color: var(--text-heading); margin-bottom: 1.5rem;">POS - Checkout (បញ្ជរគិតលុយ)</h2>
    
    <div class="checkout-grid">
        <div class="product-panel">
            <input type="text" id="searchInput" onkeyup="searchProduct()" placeholder="ស្វែងរកទំនិញ ឬ ស្កេនកូដ (Search or Scan)..." style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 6px; outline: none; margin-bottom: 1rem;">
            
            <div class="product-grid">
                @forelse($products as $product)
                    <div class="product-card" data-name="{{ strtolower($product->item_name) }}" onclick="addToCart({{ $product->id }}, '{{ $product->item_name }}', {{ $product->price ?? 0 }})">
                        <div class="product-name">{{ $product->item_name }}</div>
                        <div class="product-price">${{ number_format($product->price ?? 0, 2) }}</div>
                        <div style="font-size: 0.75rem; color: #64748b; margin-top: 5px;">Stock: {{ number_format($product->stock_qty, 0) }}</div>
                    </div>
                @empty
                    <div style="grid-column: 1 / -1; text-align: center; padding: 2rem; color: #94a3b8;">មិនមានទំនិញក្នុងស្តុកទេ!</div>
                @endforelse
            </div>
        </div>

        <div class="cart-panel">
            <h3 style="margin-top: 0; border-bottom: 1px solid var(--border-color); padding-bottom: 10px;">Current Order</h3>
            <div id="cart-container" style="min-height: 250px; max-height: 400px; overflow-y: auto;">
                <div id="empty-msg" style="display: flex; align-items: center; justify-content: center; height: 250px; color: #94a3b8;">[កន្ត្រកទំនិញទទេ]</div>
                <div id="cart-items"></div>
            </div>
            
            <div style="border-top: 2px dashed var(--border-color); padding-top: 1rem; margin-top: 1rem;">
                <div style="display: flex; justify-content: space-between; font-weight: 700; font-size: 1.25rem; margin-bottom: 1rem;">
                    <span>Total:</span>
                    <span style="color: var(--brand-red);" id="cart-total">$0.00</span>
                </div>
                <button onclick="openModal()" style="width: 100%; background: var(--brand-red); color: #fff; padding: 1rem; border: none; border-radius: 6px; font-weight: 700; font-size: 1.1rem; cursor: pointer;">
                    Pay Now (គិតលុយ)
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal-overlay" id="checkoutModal">
    <div class="modal-box">
        <h3 style="margin-top: 0; color: var(--text-heading);">Total Payment</h3>
        <div class="modal-total" id="modal-total-amount">$0.00</div>
        <p style="color: #64748b; font-size: 0.9rem; margin-bottom: 1.5rem;">សូមជ្រើសរើសជម្រើសខាងក្រោម៖</p>
        
        <button class="btn-modal btn-print" onclick="submitOrder(true)">🖨️ Pay & Print Invoice</button>
        <button class="btn-modal btn-pay" onclick="submitOrder(false)">✅ Just Pay (No Print)</button>
        <button class="btn-modal btn-close" onclick="closeModal()">Cancel</button>
    </div>
</div>

<div id="print-section">
    <div style="text-align: center; margin-bottom: 10px;">
        <h2 style="margin: 0;">POS SYSTEM</h2>
        <p style="margin: 5px 0; font-size: 12px;">Invoice / Receipt</p>
        <p style="margin: 0; font-size: 12px;">Date: <span id="print-date"></span></p>
    </div>
    <div style="border-bottom: 1px dashed #000; margin-bottom: 10px;"></div>
    <div id="print-items" style="font-size: 12px;"></div>
    <div style="border-bottom: 1px dashed #000; margin: 10px 0;"></div>
    <div style="display: flex; justify-content: space-between; font-weight: bold; font-size: 14px;">
        <span>TOTAL:</span>
        <span id="print-total"></span>
    </div>
    <div style="text-align: center; margin-top: 20px; font-size: 12px;">
        Thank you for your purchase!
    </div>
</div>

<script>
    let cart = [];
    let grandTotal = 0;

    // មុខងារជ្រើសរើស និងគណនាទំនិញ
    function addToCart(id, name, price) {
        let existingItem = cart.find(item => item.id === id);
        if (existingItem) existingItem.qty += 1;
        else cart.push({ id: id, name: name, price: parseFloat(price), qty: 1 });
        renderCart();
    }

    function updateQty(id, change) {
        let index = cart.findIndex(item => item.id === id);
        if (index !== -1) {
            cart[index].qty += change;
            if (cart[index].qty <= 0) cart.splice(index, 1);
        }
        renderCart();
    }

    function renderCart() {
        let cartItemsDiv = document.getElementById('cart-items');
        let emptyMsg = document.getElementById('empty-msg');
        let totalSpan = document.getElementById('cart-total');
        
        cartItemsDiv.innerHTML = '';
        grandTotal = 0;

        if (cart.length === 0) {
            emptyMsg.style.display = 'flex';
        } else {
            emptyMsg.style.display = 'none';
            cart.forEach(item => {
                let itemTotal = item.price * item.qty;
                grandTotal += itemTotal;
                cartItemsDiv.innerHTML += `
                    <div class="cart-item">
                        <div style="flex-grow: 1; text-align: left;">
                            <div style="font-weight:600; font-size:0.85rem;">${item.name}</div>
                            <div style="font-size:0.8rem; color:#64748b;">$${item.price.toFixed(2)}</div>
                        </div>
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <button class="qty-btn" onclick="updateQty(${item.id}, -1)">-</button>
                            <span style="font-weight: 700; width: 20px; text-align: center;">${item.qty}</span>
                            <button class="qty-btn" onclick="updateQty(${item.id}, 1)">+</button>
                        </div>
                        <div style="font-weight: 700; color: var(--brand-red); min-width: 60px; text-align: right;">
                            $${itemTotal.toFixed(2)}
                        </div>
                    </div>`;
            });
        }
        totalSpan.innerText = '$' + grandTotal.toFixed(2);
    }

    function searchProduct() {
        let input = document.getElementById('searchInput').value.toLowerCase();
        document.querySelectorAll('.product-card').forEach(card => {
            card.style.display = card.getAttribute('data-name').includes(input) ? 'block' : 'none';
        });
    }

    // មុខងារបើក/បិទ Modal
    function openModal() {
        if(cart.length === 0) { alert('កន្ត្រកទទេ! សូមជ្រើសរើសទំនិញសិន។'); return; }
        document.getElementById('modal-total-amount').innerText = '$' + grandTotal.toFixed(2);
        document.getElementById('checkoutModal').style.display = 'flex';
    }

    function closeModal() {
        document.getElementById('checkoutModal').style.display = 'none';
    }

    // មុខងារបញ្ជូនទិន្នន័យទៅ Controller និង Print វិក្កយបត្រ
    function submitOrder(shouldPrint) {
        let csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // ប្រើ Fetch API បាញ់ទិន្នន័យទៅ Controller ដើម្បីកាត់ស្តុក
        fetch('{{ route('checkout.process') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({ cart: cart })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // ប្រសិនបើចុច "Pay & Print Invoice"
                if (shouldPrint) {
                    preparePrintReceipt();
                    window.print();
                }
                
                // Refresh ទំព័រ ដើម្បី Update ស្តុកថ្មី
                alert('ប្រតិបត្តិការជោគជ័យ!');
                window.location.reload(); 
            } else {
                alert('មានបញ្ហា: ' + data.message);
            }
        })
        .catch(error => console.error('Error:', error));
    }

    // មុខងាររៀបចំទម្រង់វិក្កយបត្រមុនពេលព្រីន
    function preparePrintReceipt() {
        document.getElementById('print-date').innerText = new Date().toLocaleString();
        let printItemsDiv = document.getElementById('print-items');
        printItemsDiv.innerHTML = '';
        
        cart.forEach(item => {
            printItemsDiv.innerHTML += `
                <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                    <span>${item.qty}x ${item.name}</span>
                    <span>$${(item.qty * item.price).toFixed(2)}</span>
                </div>
            `;
        });
        document.getElementById('print-total').innerText = '$' + grandTotal.toFixed(2);
    }
</script>
@endsection