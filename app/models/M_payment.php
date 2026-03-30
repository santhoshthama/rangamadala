<?php

class M_payment
{
    private $db;
    
    public function __construct()
    {
        $this->db = new Database();
    }
    
    /**
     * Record a new payment transaction
     */
    public function createPayment($data)
    {
        $referenceNumber = $data['reference_number'] ?? ($data['gateway_order_id'] ?? ('REQ-' . $data['service_request_id'] . '-' . $data['payment_type'] . '-' . time()));

        $this->db->query("INSERT INTO payments (
            service_request_id, payment_type, amount, payment_gateway,
            payment_status, paid_by, paid_to, paid_at,
            gateway_order_id, reference_number, transaction_response
        ) VALUES (
            :request_id, :type, :amount, :gateway,
            :status, :paid_by, :paid_to, :paid_at,
            :gateway_order_id, :reference_number, :transaction_response
        )");
        
        $this->db->bind(':request_id', $data['service_request_id']);
        $this->db->bind(':type', $data['payment_type']);
        $this->db->bind(':amount', $data['amount']);
        $this->db->bind(':gateway', $data['payment_gateway'] ?? 'payhere');
        $this->db->bind(':status', $data['payment_status'] ?? 'pending');
        $this->db->bind(':paid_by', $data['paid_by'] ?? null);
        $this->db->bind(':paid_to', $data['paid_to'] ?? null);
        $this->db->bind(':paid_at', $data['paid_at'] ?? null);
        $this->db->bind(':gateway_order_id', $data['gateway_order_id'] ?? null);
        $this->db->bind(':reference_number', $referenceNumber);
        $this->db->bind(':transaction_response', $data['transaction_response'] ?? null);
        
        if ($this->db->execute()) {
            return $this->db->lastInsertId();
        }
        return false;
    }

    /**
     * Record a new bank transfer payment with uploaded evidence
     */
    public function createBankPayment($data)
    {
        $referenceNumber = $data['reference_number'] ?? ('REQ-' . $data['service_request_id'] . '-' . $data['payment_type'] . '-' . time());

        $this->db->query("INSERT INTO payments (
            service_request_id, payment_type, amount, payment_gateway,
            payment_status, paid_by, paid_to, paid_at,
            reference_number, transaction_response
        ) VALUES (
            :request_id, :type, :amount, :gateway,
            :status, :paid_by, :paid_to, :paid_at,
            :reference_number, :transaction_response
        )");

        $this->db->bind(':request_id', $data['service_request_id']);
        $this->db->bind(':type', $data['payment_type']);
        $this->db->bind(':amount', $data['amount']);
        $this->db->bind(':gateway', 'bank_transfer');
        $this->db->bind(':status', $data['payment_status'] ?? 'pending');
        $this->db->bind(':paid_by', $data['paid_by'] ?? null);
        $this->db->bind(':paid_to', $data['paid_to'] ?? null);
        $this->db->bind(':paid_at', $data['paid_at'] ?? null);
        $this->db->bind(':reference_number', $referenceNumber);
        $this->db->bind(':transaction_response', $data['transaction_response'] ?? null);

        if ($this->db->execute()) {
            return $this->db->lastInsertId();
        }
        return false;
    }
    
    /**
     * Update payment status
     */
    public function updatePaymentStatus($payment_id, $status, $transaction_response = null)
    {
        $this->db->query("UPDATE payments 
            SET payment_status = :status,
                transaction_response = COALESCE(:response, transaction_response),
                paid_at = CASE WHEN :status = 'completed' THEN NOW() ELSE paid_at END
            WHERE id = :id");
        
        $this->db->bind(':status', $status);
        $this->db->bind(':response', $transaction_response);
        $this->db->bind(':id', $payment_id);
        
        return $this->db->execute();
    }
    
    /**
     * Get payment by ID
     */
    public function getPaymentById($payment_id)
    {
        $this->db->query("SELECT * FROM payments WHERE id = :id");
        $this->db->bind(':id', $payment_id);
        return $this->db->single();
    }
    
    /**
     * Get all payments for a service request
     */
    public function getPaymentsByRequest($request_id)
    {
        $this->db->query("SELECT * FROM payments 
            WHERE service_request_id = :id 
            ORDER BY created_at DESC");
        $this->db->bind(':id', $request_id);
        return $this->db->resultSet();
    }
    
    /**
     * Get payments by type for a request
     */
    public function getPaymentByType($request_id, $type)
    {
        $this->db->query("SELECT * FROM payments 
            WHERE service_request_id = :id AND payment_type = :type 
            ORDER BY created_at DESC LIMIT 1");
        $this->db->bind(':id', $request_id);
        $this->db->bind(':type', $type);
        return $this->db->single();
    }
    
    /**
     * Get total paid amount for a request
     */
    public function getTotalPaid($request_id)
    {
        $this->db->query("SELECT SUM(amount) as total FROM payments 
            WHERE service_request_id = :id 
            AND payment_status = 'completed'");
        $this->db->bind(':id', $request_id);
        $result = $this->db->single();
        return $result->total ?? 0;
    }
    
    /**
     * Get payment summary for a request
     */
    public function getPaymentSummary($request_id)
    {
        $this->db->query("SELECT 
            payment_type,
            SUM(CASE WHEN payment_status = 'completed' THEN amount ELSE 0 END) as paid,
            SUM(CASE WHEN payment_status = 'pending' THEN amount ELSE 0 END) as pending,
            SUM(CASE WHEN payment_status = 'failed' THEN amount ELSE 0 END) as failed
            FROM payments 
            WHERE service_request_id = :id
            GROUP BY payment_type");
        $this->db->bind(':id', $request_id);
        return $this->db->resultSet();
    }
    
    /**
     * Get all payments made by a user (PM)
     */
    public function getPaymentsByPayer($user_id)
    {
        $this->db->query("SELECT p.*, sr.drama_name, sr.service_type 
            FROM payments p
            LEFT JOIN service_requests sr ON p.service_request_id = sr.id
            WHERE p.paid_by = :user_id 
            ORDER BY p.created_at DESC");
        $this->db->bind(':user_id', $user_id);
        return $this->db->resultSet();
    }
    
    /**
     * Get all payments received by a user (Provider)
     */
    public function getPaymentsReceived($user_id)
    {
        $this->db->query("SELECT p.*, sr.drama_name, sr.service_type 
            FROM payments p
            LEFT JOIN service_requests sr ON p.service_request_id = sr.id
            WHERE p.paid_to = :user_id 
            ORDER BY p.created_at DESC");
        $this->db->bind(':user_id', $user_id);
        return $this->db->resultSet();
    }
    
    /**
     * Get payment by gateway order ID
     * Used to fetch PayHere payments by order_id
     */
    public function getPaymentByOrderId($order_id)
    {
        $this->db->query("SELECT * FROM payments WHERE gateway_order_id = :order_id ORDER BY id DESC LIMIT 1");
        $this->db->bind(':order_id', $order_id);
        return $this->db->single();
    }

    /**
     * Check if request has a pending cash payment awaiting provider confirmation
     */
    public function hasPendingCashPayment($service_request_id)
    {
        $this->db->query("SELECT id FROM payments WHERE service_request_id = :request_id AND payment_gateway = 'cash' AND payment_status = 'pending' LIMIT 1");
        $this->db->bind(':request_id', $service_request_id);
        $result = $this->db->single();
        return !empty($result);
    }

    /**
     * Check if advance payment is completed
     */
    public function isAdvancePaid($service_request_id)
    {
        $this->db->query("
            SELECT COUNT(*) as count FROM payments 
            WHERE service_request_id = :id 
            AND payment_type = 'advance' 
            AND payment_status IN ('completed', 'success')
        ");
        $this->db->bind(':id', $service_request_id);
        $result = $this->db->single();
        return $result->count > 0;
    }

    /**
     * Check if remaining payment is completed
     */
    public function isRemainingPaid($service_request_id)
    {
        $this->db->query("
            SELECT COUNT(*) as count FROM payments 
            WHERE service_request_id = :id 
            AND payment_type = 'remaining' 
            AND payment_status IN ('completed', 'success')
        ");
        $this->db->bind(':id', $service_request_id);
        $result = $this->db->single();
        return $result->count > 0;
    }

    /**
     * Check if full payment is completed
     */
    public function isFullyPaid($service_request_id)
    {
        $this->db->query("
            SELECT COUNT(*) as count FROM payments 
            WHERE service_request_id = :id 
            AND payment_type = 'full' 
            AND payment_status IN ('completed', 'success')
        ");
        $this->db->bind(':id', $service_request_id);
        $result = $this->db->single();
        return $result->count > 0;
    }

    /**
     * Calculate overall payment status
     */
    public function getCalculatedPaymentStatus($service_request_id)
    {
        $fullPaid = $this->isFullyPaid($service_request_id);
        $advancePaid = $this->isAdvancePaid($service_request_id);
        $remainingPaid = $this->isRemainingPaid($service_request_id);

        if ($fullPaid) {
            return 'paid';
        } elseif ($advancePaid && $remainingPaid) {
            return 'paid';
        } elseif ($advancePaid || $remainingPaid) {
            return 'partially_paid';
        } else {
            return 'unpaid';
        }
    }
}
