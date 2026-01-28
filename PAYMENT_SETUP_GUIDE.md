# Payment Gateway Setup Guide

## ✅ What's Been Created

### 1. Database
- **File**: `add_payments_table.sql`
- **Also added to**: `database_setup.sql`
- **Run in phpMyAdmin**: Execute the SQL to create the `payments` table

### 2. Models
- **File**: `app/models/M_payment.php`
- Functions for creating, tracking, and managing payments

### 3. Controller
- **File**: `app/controllers/Payment.php`
- Handles payment initiation, processing, success, and cancellation
- Currently uses **simulation mode** (no real PayPal yet)

### 4. UI Components
- **Updated**: `app/views/production_manager/manage_services.php`
- Payment section in confirm modal
- "Pay Advance via PayPal" button
- Shows payment info when advance is required

---

## 🧪 Testing the Payment Flow (Simulation Mode)

### Step 1: Create the Database Table
```sql
-- In phpMyAdmin, run:
SOURCE C:/xampp/htdocs/Rangamadala/add_payments_table.sql;
-- OR paste the contents of add_payments_table.sql into SQL tab
```

### Step 2: Test the Flow
1. Go to Production Manager dashboard
2. Browse services and send a request to a provider
3. Provider responds with a quote that requires advance payment
4. PM sees "Review & Confirm" button
5. Click it → Modal opens showing payment section
6. Click "Pay Advance via PayPal" button
7. Redirected to payment confirmation page
8. Click "Pay with PayPal (Simulation)" 
9. Payment recorded as completed
10. Redirected to success page

### Step 3: Verify Payment
Check the `payments` table in phpMyAdmin to see the recorded transaction.

---

## 🔜 Next Step: PayPal Integration

Once you've tested the simulation and everything works:

1. Get PayPal sandbox credentials from [PayPal Developer](https://developer.paypal.com)
2. Create `.env` file in project root with:
   ```
   PAYPAL_MODE=sandbox
   PAYPAL_CLIENT_ID=your_client_id_here
   PAYPAL_CLIENT_SECRET=your_secret_here
   ```
3. Create `app/core/PayPal.php` (I'll provide the code)
4. Update `app/controllers/Payment.php` to use real PayPal API

---

## 📋 Current Status

✅ Database table created
✅ Payment model created
✅ Payment controller with simulation mode
✅ UI integrated into confirm modal
✅ JavaScript payment functions added

⏳ Pending:
- PayPal API integration (needs credentials first)
- Environment variable loader
- Real payment processing

---

## 🎯 How It Works

### Payment Flow:
```
PM clicks "Review & Confirm"
    ↓
Modal shows provider response + payment section (if advance required)
    ↓
PM clicks "Pay Advance via PayPal"
    ↓
Creates payment record (status: pending)
    ↓
Redirects to payment page
    ↓
PM confirms payment (simulation)
    ↓
Payment status → completed
    ↓
Success page shown
    ↓
PM can now confirm the service request
```

### Database Flow:
```
payments table:
- id
- service_request_id (links to service request)
- payment_type (advance/final)
- amount
- payment_status (pending/completed/failed)
- transaction_id (from PayPal)
- paid_by (PM user_id)
- paid_to (Provider user_id)
```

---

## 🔍 Files Modified/Created

**Created:**
- `app/models/M_payment.php`
- `app/controllers/Payment.php`
- `add_payments_table.sql`
- `PAYMENT_SETUP_GUIDE.md` (this file)

**Modified:**
- `database_setup.sql` (added payments table)
- `app/views/production_manager/manage_services.php` (added payment UI)

---

## 💡 Tips

1. Test in simulation mode first before adding real PayPal
2. Check browser console for JavaScript errors
3. Check `payments` table to verify records are created
4. Payment button disables after click to prevent double payments
5. Each payment has a unique transaction ID (currently SIM-timestamp-random)

---

Ready to test! Let me know if you encounter any issues.
