<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Order Confirmation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: #4CAF50;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .content {
            background: #f9f9f9;
            padding: 20px;
            border-radius: 0 0 8px 8px;
        }
        .order-details {
            background: white;
            padding: 15px;
            margin: 15px 0;
            border-radius: 5px;
            border-left: 4px solid #4CAF50;
        }
        .order-item {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #eee;
        }
        .order-item:last-child {
            border-bottom: none;
        }
        .total {
            font-weight: bold;
            font-size: 1.1em;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 2px solid #4CAF50;
        }
        .btn {
            display: inline-block;
            background: #4CAF50;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 5px;
            margin: 15px 0;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            color: #666;
            font-size: 0.9em;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Order Confirmation</h1>
        <p>Thank you for your order!</p>
    </div>
    
    <div class="content">
        <p>Dear {{ $user->name }},</p>
        
        <p>Your order has been confirmed and is being prepared for processing. Here are your order details:</p>
        
        <div class="order-details">
            <h3>Order Information</h3>
            <p><strong>Order Number:</strong> {{ $order->order_number }}</p>
            <p><strong>Order Date:</strong> {{ $order->order_date->format('F j, Y') }}</p>
            <p><strong>Status:</strong> <span style="color: #4CAF50; font-weight: bold;">{{ ucfirst($order->order_status) }}</span></p>
            <p><strong>Requested Delivery Date:</strong> {{ $order->requested_delivery_date->format('F j, Y') }}</p>
        </div>
        
        <div class="order-details">
            <h3>Delivery Information</h3>
            <p><strong>Address:</strong> {{ $order->delivery_address }}</p>
            <p><strong>Contact:</strong> {{ $order->delivery_contact }}</p>
            <p><strong>Phone:</strong> {{ $order->delivery_phone }}</p>
            @if($order->special_instructions)
                <p><strong>Special Instructions:</strong> {{ $order->special_instructions }}</p>
            @endif
        </div>
        
        @if(!empty($partiallyFulfilled))
        <div class="order-details" style="border-left: 4px solid #FFA500;">
            <h3 style="color: #FFA500;">Partial Fulfillment Notice</h3>
            <p>Some items in your order could only be partially fulfilled due to limited inventory. See details below:</p>
            <ul>
                @foreach($partiallyFulfilled as $partial)
                    <li>
                        <strong>{{ $partial['product_name'] }}</strong>: Requested {{ $partial['requested'] }}, Fulfilled {{ $partial['fulfilled'] }}
                    </li>
                @endforeach
            </ul>
        </div>
        @endif
        
        <div class="order-details">
            <h3>Order Items</h3>
            @foreach($orderItems as $item)
                <div class="order-item">
                    <div>
                        <strong>{{ $item->yogurtProduct->product_name }}</strong><br>
                        <small>Quantity: {{ $item->quantity }}</small>
                    </div>
                    <div>
                        {{ number_format($item->unit_price, 2) }} UGX<br>
                        <strong>{{ number_format($item->final_price, 2) }} UGX</strong>
                    </div>
                </div>
            @endforeach
            
            <div class="total">
                <div class="order-item">
                    <span>Subtotal:</span>
                    <span>{{ number_format($order->subtotal, 2) }} UGX</span>
                </div>
                @if($order->tax_amount > 0)
                <div class="order-item">
                    <span>Tax (18% VAT):</span>
                    <span>{{ number_format($order->tax_amount, 2) }} UGX</span>
                </div>
                @endif
                @if($order->shipping_cost > 0)
                <div class="order-item">
                    <span>Shipping:</span>
                    <span>{{ number_format($order->shipping_cost, 2) }} UGX</span>
                </div>
                @endif
                @if($order->discount_amount > 0)
                <div class="order-item">
                    <span>Discount:</span>
                    <span>-{{ number_format($order->discount_amount, 2) }} UGX</span>
                </div>
                @endif
                <div class="order-item">
                    <span><strong>Total:</strong></span>
                    <span><strong>{{ number_format($order->total_amount, 2) }} UGX</strong></span>
                </div>
            </div>
        </div>
        
        <div class="order-details">
            <h3>Payment Information</h3>
            <p><strong>Payment Method:</strong> {{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}</p>
            <p><strong>Payment Status:</strong> {{ ucfirst($order->payment_status) }}</p>
        </div>
        
        <p>We will keep you updated on the status of your order. You can track your order by logging into your account.</p>
        
        <a href="{{ route('customer.orders.show', $order->id) }}" class="btn">View Order Details</a>
        
        <p>If you have any questions about your order, please don't hesitate to contact our customer service team.</p>
        
        <p>Thank you for choosing our services!</p>
    </div>
    
    <div class="footer">
        <p>This email was sent to {{ $user->email }}</p>
        <p>&copy; {{ date('Y') }} Caramel YG. All rights reserved.</p>
    </div>
</body>
</html> 