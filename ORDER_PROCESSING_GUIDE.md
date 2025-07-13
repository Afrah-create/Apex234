# Order Processing System Guide

## Overview

The order processing system has been significantly enhanced to provide automated, efficient, and reliable order management between customers and retailers. This system includes inventory validation, automated status transitions, notifications, and comprehensive tracking.

## 🔄 **Order Processing Flow**

### **Customer Order Flow**

1. **Order Placement**
   - Customer adds items to cart
   - Proceeds to checkout with delivery details
   - System validates inventory availability
   - Order is created with status: `pending`

2. **Automated Processing**
   - OrderProcessingService automatically processes the order
   - Inventory is validated and reserved
   - Order status changes to: `confirmed`
   - Distribution center is assigned
   - Confirmation emails are sent

3. **Manual Processing (Admin)**
   - Admin reviews order in dashboard
   - Updates status to: `processing`
   - Creates delivery record
   - Assigns driver/vehicle

4. **Shipping & Delivery**
   - Status updated to: `shipped`
   - Delivery tracking activated
   - Status updated to: `delivered`
   - Payment processed (if cash on delivery)

### **Retailer Order Flow**

1. **Order Placement**
   - Retailer adds items via sidebar cart
   - Places order with store details
   - System validates inventory
   - Order created with status: `pending`

2. **Automated Processing**
   - OrderProcessingService processes retailer order
   - Inventory validated and reserved
   - Order status changes to: `confirmed`
   - Retailer receives confirmation

3. **Fulfillment**
   - Admin processes order
   - Status transitions: `processing` → `shipped` → `delivered`
   - Retailer receives status updates

## 📊 **Order Statuses**

| Status | Description | Next Possible Statuses |
|--------|-------------|------------------------|
| `pending` | Order placed, awaiting processing | `confirmed`, `cancelled` |
| `confirmed` | Order validated and confirmed | `processing`, `cancelled` |
| `processing` | Order being prepared for shipping | `shipped`, `cancelled` |
| `shipped` | Order in transit | `delivered`, `cancelled` |
| `delivered` | Order successfully delivered | None (final status) |
| `cancelled` | Order cancelled | None (final status) |

## 🛠 **Key Components**

### **1. OrderProcessingService**
- **Location**: `app/Services/OrderProcessingService.php`
- **Purpose**: Handles automated order processing logic
- **Key Methods**:
  - `processCustomerOrder()` - Processes customer orders
  - `processRetailerOrder()` - Processes retailer orders
  - `updateOrderStatus()` - Updates order status with validation
  - `validateInventory()` - Checks inventory availability
  - `reserveInventory()` - Reserves inventory for orders

### **2. Enhanced CheckoutController**
- **Location**: `app/Http/Controllers/CheckoutController.php`
- **Features**:
  - Inventory validation before checkout
  - Automatic cost calculations (shipping, tax, discounts)
  - Integration with OrderProcessingService
  - Email notifications
  - Comprehensive error handling

### **3. Order Status Notifications**
- **Location**: `app/Notifications/OrderStatusUpdate.php`
- **Features**:
  - Email notifications for status changes
  - Database notifications
  - Customizable messages per status

### **4. Email Templates**
- **Order Confirmation**: `resources/views/emails/order-confirmation.blade.php`
- **Admin Notification**: `resources/views/emails/admin-new-order.blade.php`
- **Features**: Professional HTML emails with order details

### **5. Automated Processing Command**
- **Location**: `app/Console/Commands/ProcessPendingOrders.php`
- **Usage**: `php artisan orders:process-pending --limit=10`
- **Purpose**: Process pending orders in background

## 💰 **Cost Calculations**

### **Shipping Cost**
- Base cost: 5,000 UGX
- Distance multipliers:
  - Kampala: 1.2x
  - Jinja: 1.5x
  - Mbarara: 2.0x
  - Other locations: 1.0x

### **Tax Calculation**
- VAT rate: 18%
- Applied to subtotal

### **Discounts**
- First order: 10% discount
- Loyalty discount (5+ orders): 5% discount

## 📧 **Notification System**

### **Customer Notifications**
- Order confirmation email
- Status update notifications
- Order cancellation notifications

### **Admin Notifications**
- New order alerts
- Order processing updates
- Inventory alerts

### **Retailer Notifications**
- Order confirmation
- Status updates
- Inventory issues

## 🔧 **Configuration**

### **Setting Up Automated Processing**

1. **Register the Command**
   Add to `app/Console/Kernel.php`:
   ```php
   protected function schedule(Schedule $schedule)
   {
       $schedule->command('orders:process-pending')->everyFiveMinutes();
   }
   ```

2. **Configure Email Settings**
   Update `.env` file:
   ```
   MAIL_MAILER=smtp
   MAIL_HOST=your-smtp-host
   MAIL_PORT=587
   MAIL_USERNAME=your-email
   MAIL_PASSWORD=your-password
   MAIL_ENCRYPTION=tls
   MAIL_FROM_ADDRESS=noreply@caramel-yg.com
   MAIL_FROM_NAME="Caramel YG"
   ```

3. **Queue Configuration**
   For background processing:
   ```
   QUEUE_CONNECTION=database
   ```

## 🚀 **Usage Examples**

### **Processing Orders Manually**
```bash
# Process up to 10 pending orders
php artisan orders:process-pending --limit=10

# Process all pending orders
php artisan orders:process-pending --limit=1000
```

### **Updating Order Status**
```php
use App\Services\OrderProcessingService;

$orderProcessingService = new OrderProcessingService();
$orderProcessingService->updateOrderStatus($order, 'processing', 'Order being prepared');
```

### **Checking Order Status**
```php
$order = Order::find($orderId);
echo $order->order_status; // 'confirmed'
echo $order->order_number; // 'CUST-20250115123456ABC123'
```

## 📈 **Monitoring & Analytics**

### **Order Statistics**
- Total orders by status
- Revenue tracking
- Processing times
- Customer satisfaction

### **Inventory Tracking**
- Real-time stock levels
- Reserved inventory
- Low stock alerts
- Stock movement history

### **Performance Metrics**
- Order processing time
- Delivery success rate
- Customer feedback
- Revenue per order

## 🔒 **Security Features**

### **Data Validation**
- Input sanitization
- SQL injection prevention
- XSS protection
- CSRF protection

### **Access Control**
- Role-based permissions
- Order ownership validation
- Admin-only status updates
- Secure payment handling

## 🐛 **Troubleshooting**

### **Common Issues**

1. **Order Not Processing**
   - Check inventory availability
   - Verify distribution center assignment
   - Review error logs

2. **Email Notifications Not Sending**
   - Check mail configuration
   - Verify queue workers
   - Review SMTP settings

3. **Inventory Issues**
   - Check stock levels
   - Verify product availability
   - Review reservation logic

### **Debug Commands**
```bash
# Check pending orders
php artisan tinker
>>> App\Models\Order::where('order_status', 'pending')->count();

# Test email sending
php artisan tinker
>>> Mail::raw('Test email', function($msg) { $msg->to('test@example.com')->subject('Test'); });
```

## 📝 **Best Practices**

1. **Regular Monitoring**
   - Check order processing logs daily
   - Monitor inventory levels
   - Review customer feedback

2. **Performance Optimization**
   - Use queues for background processing
   - Optimize database queries
   - Cache frequently accessed data

3. **Customer Experience**
   - Send timely notifications
   - Provide order tracking
   - Handle cancellations gracefully

4. **Data Integrity**
   - Use database transactions
   - Validate all inputs
   - Maintain audit trails

## 🔄 **Future Enhancements**

1. **Real-time Tracking**
   - GPS tracking for deliveries
   - Live status updates
   - Customer notifications

2. **Advanced Analytics**
   - Predictive ordering
   - Customer behavior analysis
   - Revenue forecasting

3. **Integration Features**
   - Payment gateway integration
   - SMS notifications
   - Mobile app integration

4. **Automation Improvements**
   - AI-powered inventory management
   - Automated pricing
   - Smart delivery routing

---

This order processing system provides a robust, scalable solution for managing orders between customers and retailers with comprehensive automation, monitoring, and notification capabilities. 