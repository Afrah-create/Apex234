<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Caramel Yoghurt</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Arial, Helvetica, sans-serif;
            background: linear-gradient(to right, #f5f5f5, #fff);
            min-height: 100vh;
        }
        .header-topbar {
            background: #f5f5f5;
            border-bottom: 1px solid #ececec;
            font-size: 0.95rem;
            color: #ff9900;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 4px 32px 4px 16px;
        }
        .header-topbar .caramel-mini {
            font-weight: bold;
            color: #222;
            font-size: 1.1rem;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .header-main {
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 18px 32px 18px 32px;
            border-bottom: 3px solid #ff9900;
        }
        .header-logo {
            display: flex;
            align-items: center;
            font-size: 2.2rem;
            font-weight: bold;
            color: #222;
            letter-spacing: 2px;
        }
        .header-logo .star {
            color: #ff9900;
            margin-left: 2px;
            font-size: 1.5rem;
        }
        .header-orange-bar {
            height: 6px;
            background: #ff9900;
            width: 100%;
        }
        .checkout-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 32px 24px;
            display: grid;
            grid-template-columns: 1fr 400px;
            gap: 32px;
        }
        .checkout-form {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.07);
            padding: 32px;
        }
        .checkout-summary {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.07);
            padding: 32px;
            height: fit-content;
        }
        .form-group {
            margin-bottom: 24px;
        }
        .form-label {
            display: block;
            font-weight: 600;
            color: #222;
            margin-bottom: 8px;
            font-size: 1.08rem;
        }
        .form-input {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.2s;
        }
        .form-input:focus {
            outline: none;
            border-color: #ff9900;
            box-shadow: 0 0 0 3px rgba(255, 153, 0, 0.1);
        }
        .form-textarea {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 1rem;
            min-height: 100px;
            resize: vertical;
            transition: border-color 0.2s;
        }
        .form-textarea:focus {
            outline: none;
            border-color: #ff9900;
            box-shadow: 0 0 0 3px rgba(255, 153, 0, 0.1);
        }
        .checkout-btn {
            background: #28a745;
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 16px 32px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
            width: 100%;
        }
        .checkout-btn:hover {
            background: #218838;
        }
        .checkout-btn:disabled {
            background: #6c757d;
            cursor: not-allowed;
        }
        .order-item {
            display: flex;
            align-items: center;
            padding: 16px 0;
            border-bottom: 1px solid #f0f0f0;
        }
        .order-item:last-child {
            border-bottom: none;
        }
        .order-item-img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 8px;
            margin-right: 16px;
            border: 1px solid #eee;
        }
        .order-item-details {
            flex: 1;
        }
        .order-item-name {
            font-weight: 600;
            color: #222;
            margin-bottom: 4px;
        }
        .order-item-price {
            color: #666;
            font-size: 0.95rem;
        }
        .order-item-quantity {
            color: #666;
            font-size: 0.95rem;
        }
        .order-item-total {
            font-weight: 600;
            color: #222;
            text-align: right;
        }
        .order-summary-total {
            border-top: 2px solid #f0f0f0;
            padding-top: 16px;
            margin-top: 16px;
            font-size: 1.2rem;
            font-weight: 700;
            color: #222;
            text-align: right;
        }
        .section-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #222;
            margin-bottom: 24px;
        }
        .back-link {
            color: #ff9900;
            text-decoration: none;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 24px;
        }
        .back-link:hover {
            text-decoration: underline;
        }
        @media (max-width: 768px) {
            .checkout-container {
                grid-template-columns: 1fr;
                gap: 24px;
                padding: 24px 16px;
            }
        }
    </style>
</head>
<body>
    <div class="header-orange-bar"></div>
    <div class="header-topbar">
        <div><span style="color:#ff9900; font-size:1.1em;">&#127849;</span> Sell with Caramel Yoghurt</div>
        <div class="caramel-mini">
            <span style="color:#222; font-weight:bold;">CARAMEL</span>
            <span style="color:#ff9900; font-size:1.1em;">&#127849;</span>
            <span style="color:#bfa76a; font-size:1em; font-weight:normal;">FRESH</span>
            <span style="color:#bfa76a; font-size:1em; font-weight:normal;">DAIRY</span>
        </div>
    </div>
    <div class="header-main">
        <div class="header-logo">
            CARAMEL YOGHURT <span class="star">&#127849;</span>
        </div>
    </div>
    <div class="header-orange-bar"></div>

    <div class="checkout-container">
        <div class="checkout-form">
            <a href="{{ route('cart.index') }}" class="back-link">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M15 18l-6-6 6-6"/>
                </svg>
                Back to Cart
            </a>
            
            <h1 class="section-title">Checkout</h1>
            
            @if(session('error'))
                <div style="background:#fee; color:#c33; padding:12px; border-radius:8px; margin-bottom:24px;">
                    {{ session('error') }}
                </div>
            @endif

            @if(session('show_removed_dialog'))
                <div id="removedItemsModal" class="modal-overlay">
                    <div class="modal-content">
                        <h2>Some items were removed from your cart</h2>
                        <p>The following products are currently out of stock and have been removed from your cart:</p>
                        <ul>
                            @foreach(session('removed_items', []) as $removed)
                                <li>{{ $removed }}</li>
                            @endforeach
                        </ul>
                        <p>Would you like to proceed with the remaining available items?</p>
                        <form action="{{ route('checkout.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="confirm_removed" value="1">
                            <input type="hidden" name="delivery_address" value="{{ old('delivery_address') }}">
                            <input type="hidden" name="delivery_contact" value="{{ old('delivery_contact') }}">
                            <input type="hidden" name="delivery_phone" value="{{ old('delivery_phone') }}">
                            <input type="hidden" name="payment_method" value="{{ old('payment_method') }}">
                            <input type="hidden" name="requested_delivery_date" value="{{ old('requested_delivery_date') }}">
                            <input type="hidden" name="special_instructions" value="{{ old('special_instructions') }}">
                            <button type="submit" class="modal-confirm-btn">Yes, Place Order</button>
                            <a href="{{ route('cart.index') }}" class="modal-cancel-btn">No, Return to Cart</a>
                        </form>
                    </div>
                </div>
                <style>
                .modal-overlay {
                    position: fixed;
                    top: 0; left: 0; right: 0; bottom: 0;
                    background: rgba(0,0,0,0.35);
                    z-index: 1000;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                }
                .modal-content {
                    background: #fff;
                    border-radius: 12px;
                    box-shadow: 0 4px 24px rgba(0,0,0,0.15);
                    padding: 32px 28px;
                    max-width: 400px;
                    width: 100%;
                    text-align: center;
                }
                .modal-content h2 {
                    font-size: 1.3rem;
                    font-weight: bold;
                    margin-bottom: 12px;
                    color: #c33;
                }
                .modal-content ul {
                    text-align: left;
                    margin: 12px 0 18px 0;
                    padding-left: 18px;
                }
                .modal-content li {
                    color: #222;
                    margin-bottom: 4px;
                }
                .modal-confirm-btn {
                    background: #28a745;
                    color: #fff;
                    border: none;
                    border-radius: 8px;
                    padding: 12px 24px;
                    font-size: 1rem;
                    font-weight: 600;
                    cursor: pointer;
                    margin-right: 12px;
                    transition: background 0.2s;
                }
                .modal-confirm-btn:hover {
                    background: #218838;
                }
                .modal-cancel-btn {
                    background: #fff;
                    color: #c33;
                    border: 1px solid #c33;
                    border-radius: 8px;
                    padding: 12px 24px;
                    font-size: 1rem;
                    font-weight: 600;
                    cursor: pointer;
                    text-decoration: none;
                    transition: background 0.2s, color 0.2s;
                }
                .modal-cancel-btn:hover {
                    background: #fee;
                    color: #a00;
                }
                </style>
            @endif

            <form action="{{ route('checkout.store') }}" method="POST">
                @csrf
                
                <div class="form-group">
                    <label for="delivery_address" class="form-label">Delivery Address *</label>
                    <input type="text" id="delivery_address" name="delivery_address" class="form-input" 
                           value="{{ old('delivery_address', Auth::user()->address ?? '') }}" required>
                    @error('delivery_address')
                        <div style="color:#c33; font-size:0.9rem; margin-top:4px;">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="delivery_contact" class="form-label">Delivery Contact Name *</label>
                    <input type="text" id="delivery_contact" name="delivery_contact" class="form-input" 
                           value="{{ old('delivery_contact', Auth::user()->name ?? '') }}" required>
                    @error('delivery_contact')
                        <div style="color:#c33; font-size:0.9rem; margin-top:4px;">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="delivery_phone" class="form-label">Delivery Phone *</label>
                    <input type="text" id="delivery_phone" name="delivery_phone" class="form-input" 
                           value="{{ old('delivery_phone', Auth::user()->phone ?? '') }}" required>
                    @error('delivery_phone')
                        <div style="color:#c33; font-size:0.9rem; margin-top:4px;">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="payment_method" class="form-label">Payment Method *</label>
                    <select id="payment_method" name="payment_method" class="form-input" required>
                        <option value="">Select Payment Method</option>
                        <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>Cash on Delivery</option>
                        <option value="mobile_money" {{ old('payment_method') == 'mobile_money' ? 'selected' : '' }}>Mobile Money</option>
                        <option value="bank_transfer" {{ old('payment_method') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                    </select>
                    @error('payment_method')
                        <div style="color:#c33; font-size:0.9rem; margin-top:4px;">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="requested_delivery_date" class="form-label">Requested Delivery Date *</label>
                    <input type="date" id="requested_delivery_date" name="requested_delivery_date" class="form-input" 
                           value="{{ old('requested_delivery_date', date('Y-m-d', strtotime('+2 days'))) }}" 
                           min="{{ date('Y-m-d', strtotime('+1 day')) }}" required>
                    @error('requested_delivery_date')
                        <div style="color:#c33; font-size:0.9rem; margin-top:4px;">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="special_instructions" class="form-label">Special Instructions (Optional)</label>
                    <textarea id="special_instructions" name="special_instructions" class="form-textarea" 
                              placeholder="Any special delivery instructions...">{{ old('special_instructions') }}</textarea>
                </div>

                <button type="submit" class="checkout-btn">
                    Place Order - {{ number_format($total, 2) }} UGX
                </button>
            </form>
        </div>

        <div class="checkout-summary">
            <h2 class="section-title">Order Summary</h2>
            
            @foreach($cartItems as $item)
                <div class="order-item">
                    <div>
                        @if($item->product->image_path)
                            <img src="{{ asset('storage/' . $item->product->image_path) }}" 
                                 alt="{{ $item->product->product_name }}" class="order-item-img">
                        @else
                            <div style="width:60px;height:60px;display:flex;align-items:center;justify-content:center;background:#f5f5f5;border-radius:8px;font-size:1.5rem;color:#bbb;">🥛</div>
                        @endif
                    </div>
                    <div class="order-item-details">
                        <div class="order-item-name">{{ $item->product->product_name }}</div>
                        <div class="order-item-price">{{ number_format($item->product->selling_price, 2) }} UGX each</div>
                        <div class="order-item-quantity">Qty: {{ $item->quantity }}</div>
                    </div>
                    <div class="order-item-total">
                        {{ number_format($item->quantity * $item->product->selling_price, 2) }} UGX
                    </div>
                </div>
            @endforeach

            <div class="order-summary-total">
                Total: {{ number_format($total, 2) }} UGX
            </div>
        </div>
    </div>

    <footer class="customer-footer">
        <div class="footer-container">
            <div class="footer-links">
                <a href="/dashboard/customer">Home</a>
                <a href="/dashboard/customer">Shop</a>
                <a href="{{ route('customer.orders.index') }}">Orders</a>
                <a href="#">Help</a>
                <a href="#">Privacy Policy</a>
                <a href="#">Terms</a>
                <a href="#">Contact</a>
            </div>
            <div class="footer-copy">&copy; {{ date('Y') }} Caramel Yogurt. All rights reserved.</div>
        </div>
    </footer>
    <style>
        .customer-footer {
            background: #222;
            color: #fff;
            padding: 32px 0 18px 0;
            margin-top: 48px;
        }
        .footer-container {
            max-width: 1100px;
            margin: 0 auto;
            padding: 0 24px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .footer-links {
            display: flex;
            flex-wrap: wrap;
            gap: 28px;
            margin-bottom: 16px;
            justify-content: center;
        }
        .footer-links a {
            color: #ff9900;
            text-decoration: none;
            font-size: 1.08rem;
            font-weight: 500;
            transition: color 0.2s;
        }
        .footer-links a:hover {
            color: #fff;
            text-decoration: underline;
        }
        .footer-copy {
            color: #bbb;
            font-size: 0.98rem;
            text-align: center;
        }
    </style>
</body>
</html> 