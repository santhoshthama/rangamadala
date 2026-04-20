<?php
/**
 * PayHere Integration Setup Guide
 * 
 * Follow these steps to complete PayHere integration
 */
?>

## PayHere Integration Setup Guide

### ✅ Completed Implementation

1. **PayHere Configuration**
   - Location: `app/core/payhere.config.php`
   - Contains merchant credentials and URLs
   - **ACTION REQUIRED**: Replace `merchant_id` and `merchant_secret` with your PayHere credentials

2. **PayHere Helper Class**
   - Location: `app/core/PayHereHelper.php`
   - Handles hash generation and webhook verification
   - Methods:
     - `generateHash()` - Creates secure payment hash
     - `verifyPaymentNotification()` - Validates PayHere webhooks
     - `getStatusMessage()` - Maps status codes to messages

3. **Payment Controller**
   - Location: `app/controllers/Payment.php`
   - Methods implemented:
     - `checkout()` - Displays checkout with PayHere configuration
     - `notify()` - **Webhook endpoint** - called by PayHere server
     - `return()` - Return URL handler after payment

4. **Payment Model Updates**
   - Location: `app/models/M_payment.php`
   - New columns supported:
     - `gateway_payment_id` - PayHere payment ID
     - `gateway_order_id` - PayHere order ID
     - `transaction_response` - Webhook response data
   - New method: `getPaymentByOrderId()` - Fetch payment by PayHere order ID

5. **Database Migration**
   - Location: `dev/payhere_migration.sql`
   - Adds PayHere-specific columns to payments table
   - **ACTION REQUIRED**: Run this migration script

6. **Conditional PayHere Script**
   - Location: `app/views/includes/header.php`
   - Loads PayHere JS only on checkout pages

### 🔧 Setup Steps

#### Step 1: Update Config with Your Credentials
```php
// File: app/core/payhere.config.php
return [
    'merchant_id' => '121YOUR_MERCHANT_ID',          // Get from PayHere Dashboard
    'merchant_secret' => 'YOUR_MERCHANT_SECRET',      // Get from PayHere Dashboard
    // ... rest unchanged
];
```

#### Step 2: Run Database Migration
```bash
# Execute via phpMyAdmin or command line
mysql -u username -p database_name < dev/payhere_migration.sql
```

#### Step 3: Configure Return URLs
In your PayHere Dashboard settings:
- **Return URL**: `https://yourdomain.com/Payment/return`
- **Notify URL**: `https://yourdomain.com/Payment/notify`
- **Cancel URL**: `https://yourdomain.com/Payment/cancel`

⚠️ **Important**: 
- Return/Notify URLs must be publicly accessible (not localhost)
- Notify URL will NOT appear in browser (it's server-to-server)
- Use HTTPS for production

#### Step 4: Update Checkout View (Optional)
The checkout view needs payment method selection. Update:
```html
<!-- Near payment method selection -->
<input type="radio" name="paymentMethod" value="payhere" checked> PayHere Payment
```

### 🔐 Security Features Implemented

✅ **Hash Generation** - Server-side only (merchant_secret never exposed)
✅ **Webhook Signature Verification** - Validates MD5 checksum from PayHere
✅ **Order ID Validation** - Prevents order ID manipulation
✅ **Status Code Mapping** - Maps PayHere status codes correctly
✅ **Transaction Logging** - All payments logged for audit trail
✅ **Error Handling** - Graceful error handling with logging

### 📊 Payment Status Codes

From PayHere:
- `2` = Success ✅
- `0` = Pending ⏳
- `-1` = Canceled ❌
- `-2` = Failed ❌
- `-3` = Chargedback ⚠️

### 🧪 Testing

1. **Sandbox Mode**
   - Set `'sandbox' => true` in config
   - Use PayHere test cards

2. **Webhook Testing**
   - PayHere requires public domain for notify_url
   - Cannot test webhooks on localhost
   - Use ngrok or deploy to staging server

3. **Test Flow**
   - Go to checkout page → Select PayHere → Enter test card details
   - Complete payment in popup
   - Return URL redirects you back
   - Webhook updates payment status in DB

### 🚨 Common Issues & Solutions

| Issue | Solution |
|-------|----------|
| Hash mismatch error | Verify merchant_id and merchant_secret are correct |
| Webhook not triggered | Ensure notify_url is publicly accessible (HTTPS) |
| Order not found | Check order_id format (REQ-{id}-{type}-{time}) |
| Payment status not updating | Check error logs, verify signature validation |

### 📝 Database Tables Used

- **payments** - Main payment records
- **service_requests** - Services being paid for
- **users** - Payer/Provider info

### 🔗 Useful Links

- PayHere Dashboard: https://www.payhere.lk/
- PayHere Documentation: https://www.payhere.lk/documentation  
- PayHere Support: https://www.payhere.lk/support

---

**Implementation Status**: ✅ COMPLETE
**Ready for Testing**: After config update and migration
