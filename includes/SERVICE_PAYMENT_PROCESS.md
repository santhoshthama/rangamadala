# Service Payment Process (Production Manager <-> Service Provider)

This document explains the real payment flow implemented in the service-request module, based on the current code in controllers, models, and views.

## 1. Roles and ownership

- Production Manager (PM): creates and pays service requests for a drama.
- Service Provider: sends quotation response, accepts/rejects confirmed terms, and verifies manual payments.

Core files:
- [app/controllers/Payment.php](app/controllers/Payment.php)
- [app/controllers/Production_manager.php](app/controllers/Production_manager.php)
- [app/controllers/ServiceProviderRequest.php](app/controllers/ServiceProviderRequest.php)
- [app/controllers/ServiceRequests.php](app/controllers/ServiceRequests.php)
- [app/models/M_payment.php](app/models/M_payment.php)
- [app/models/M_service_request.php](app/models/M_service_request.php)
- [app/views/production_manager/manage_services.php](app/views/production_manager/manage_services.php)
- [app/views/service_requests.view.php](app/views/service_requests.view.php)
- [app/views/payment_checkout.view.php](app/views/payment_checkout.view.php)
- [app/views/payment_cash_form.view.php](app/views/payment_cash_form.view.php)
- [app/views/payment_bank_upload.view.php](app/views/payment_bank_upload.view.php)
- [app/views/payment_receipt.view.php](app/views/payment_receipt.view.php)

## 2. Service request lifecycle (status flow)

Main request statuses used in payment flow:
- `pending`: PM requested service; provider has not responded yet.
- `provider_responded`: provider submitted quote/terms.
- `confirmed`: PM confirmed provider response/terms.
- `accepted`: provider accepted confirmed terms.
- `completed`: provider marked service completed.
- `completed_paid`: service is completed and payment is fully settled.
- `rejected`, `cancelled`: terminated paths.

Where status transitions happen:
- Provider responds: [app/controllers/ServiceProviderRequest.php](app/controllers/ServiceProviderRequest.php)
- PM confirms/rejects provider response: [app/controllers/Production_manager.php](app/controllers/Production_manager.php)
- Provider accepts/rejects confirmed terms: [app/controllers/ServiceProviderRequest.php](app/controllers/ServiceProviderRequest.php)
- Provider marks completed: [app/controllers/ServiceRequests.php](app/controllers/ServiceRequests.php)
- Auto-upgrade to `completed_paid` when fully paid: [app/controllers/Payment.php](app/controllers/Payment.php)

## 3. Payment types supported

The system supports exactly these payment types:
- `advance`
- `remaining`
- `full`

Computed payment states on a request:
- `unpaid`
- `partially_paid`
- `paid`

Calculation rules are in [app/models/M_payment.php](app/models/M_payment.php):
- `full` completed/success => paid
- (`advance` completed/success) + (`remaining` completed/success) => paid
- one of advance/remaining completed/success => partially_paid
- none => unpaid

## 4. Payment methods supported

From checkout UI in [app/views/payment_checkout.view.php](app/views/payment_checkout.view.php):
- Card/PayHere (`payhere`)
- Bank transfer with slip upload (`bank_transfer`)
- Cash record (`cash`)

## 5. End-to-end payment scenarios

### A) Provider requires advance payment

1. Provider submits response with:
- quote amount
- `needs_advance = true`
- advance amount
- advance due date
- optional final payment due date and note

Saved into `service_details_json.provider_response` by [app/models/M_service_request.php](app/models/M_service_request.php).

2. PM reviews response in [app/views/production_manager/manage_services.php](app/views/production_manager/manage_services.php) and confirms.

3. On confirm, PM is redirected to checkout with:
- `type=advance`
- amount = advance amount

4. PM pays with one of 3 methods (card/bank/cash).

5. After advance success, request payment becomes at least `partially_paid`.

6. Later (typically when request is `completed`), PM pays `remaining` amount from Manage Services.

7. Once both advance and remaining are completed/success, payment status becomes `paid`, and if request status is `completed`, system auto-sets request status to `completed_paid`.

### B) No advance required

1. Provider responds with quote but `needs_advance = false`.
2. PM confirms provider response.
3. PM can pay full amount using `type=full` (usually from completed state in Manage Services).
4. On successful full payment, request payment becomes `paid`; if request already `completed`, it auto-upgrades to `completed_paid`.

## 6. Method-specific behavior

### 6.1 Card / PayHere

Flow in [app/controllers/Payment.php](app/controllers/Payment.php):
- Checkout opens.
- Frontend calls `createPayHerePayment`.
- Backend creates/reuses pending PayHere payment row (`payment_gateway='payhere'`, `payment_status='pending'`).
- After PayHere returns to app (`Payment/return`), system marks payment `completed` and updates request payment status.
- User is redirected to receipt.

### 6.2 Bank transfer

Flow in [app/controllers/Payment.php](app/controllers/Payment.php):
- PM opens bank form and uploads slip.
- Payment created with:
  - `payment_gateway='bank_transfer'`
  - `payment_status='pending'`
  - transaction metadata includes `bank_slip_path`.
- Provider sees pending verification in Service Requests details view.
- Provider actions:
  - Confirm received -> status set to `completed`.
  - Cannot verify -> verification marked rejected in transaction metadata (status remains pending).

### 6.3 Cash

Flow in [app/controllers/Payment.php](app/controllers/Payment.php):
- PM submits cash record form (date, optional note).
- Payment created with:
  - `payment_gateway='cash'`
  - `payment_status='pending'`
- Provider actions in Service Requests details:
  - Confirm received -> status `completed`.
  - Cannot verify -> verification marked rejected in transaction metadata.

## 7. Manual payment verification and re-submission

For cash/bank payments:
- Provider can reject verification with a reason (`rejectManualPayment`).
- This stores:
  - `provider_verification_status = rejected`
  - `provider_verification_reason`
- PM sees verification failure in Manage Services and can re-submit payment.

Before creating a new manual payment after rejection, system calls `cancelRejectedPayments` to mark older rejected pending entries as `cancelled` (prevents confusion/duplicates).

Implementation:
- [app/controllers/Payment.php](app/controllers/Payment.php)

## 8. Provider acceptance guard for manual payments

In provider UI [app/views/service_requests.view.php](app/views/service_requests.view.php):
- If request is `confirmed` and latest payment method is cash/bank with pending verification, Accept button is disabled.
- Provider must verify payment first (inside details modal) before accepting confirmed terms.

This is currently enforced in the UI layer.

## 9. Receipt and access control

Receipt endpoint: `Payment/receipt/{payment_id}`
- Only payer (`paid_by`) or payee provider (`paid_to`) can open receipt.
- Bank slip viewing (`Payment/viewBankSlip/{payment_id}`) also enforces same authorization.

See [app/controllers/Payment.php](app/controllers/Payment.php) and [app/views/payment_receipt.view.php](app/views/payment_receipt.view.php).

## 10. Data model summary

`payments` table is used for all method/type combinations with:
- `service_request_id`
- `payment_type` (`advance` / `remaining` / `full`)
- `payment_gateway` (`payhere` / `bank_transfer` / `cash`)
- `payment_status` (`pending`, `completed`, `success`, `cancelled`, etc.)
- `paid_by`, `paid_to`, `paid_at`
- `gateway_order_id`, `reference_number`
- `transaction_response` (JSON metadata, including verification and slip info)

Request-level payment status is synchronized via:
- [app/controllers/Payment.php](app/controllers/Payment.php)
- [app/models/M_service_request.php](app/models/M_service_request.php)

## 11. Quick endpoint map

Production Manager side:
- `POST Production_manager/confirmProviderResponse`
- `POST Production_manager/rejectProviderResponse`
- `GET Payment/checkout?request_id=&amount=&type=`
- `GET Payment/bankForm?...`
- `GET Payment/cashForm?...`
- `POST Payment/submitBankSlip`
- `POST Payment/submitCashPayment`

Service Provider side:
- `POST ServiceProviderRequest/respond`
- `POST ServiceProviderRequest/acceptConfirmed`
- `POST ServiceProviderRequest/rejectConfirmed`
- `POST Payment/confirmBankPayment`
- `POST Payment/confirmCashPayment`
- `POST Payment/rejectManualPayment`

PayHere callback:
- `GET Payment/return?order_id=...`

## 12. Business rules (current implementation)

- Advance path: `advance` then `remaining`.
- Non-advance path: `full`.
- Payment is fully settled when:
  - full is successful/completed, or
  - both advance and remaining are successful/completed.
- `completed_paid` is set only when request is already `completed` and payment becomes fully settled.

---
If you change payment rules (for example, strict backend enforcement for provider acceptance after manual payment), update this document and the corresponding controller logic together.
