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
        $this->db->query("INSERT INTO payments (
            service_request_id, payment_type, amount, payment_gateway,
            transaction_id, payment_status, gateway_response, paid_by, paid_to, paid_at, payment_stage
        ) VALUES (
            :request_id, :type, :amount, :gateway,
            :transaction_id, :status, :response, :paid_by, :paid_to, :paid_at, :stage
        )");
        
        $this->db->bind(':request_id', $data['service_request_id']);
        $this->db->bind(':type', $data['payment_type']); // 'advance', 'final', or 'full'
        $this->db->bind(':amount', $data['amount']);
        $this->db->bind(':gateway', $data['payment_gateway'] ?? 'paypal');
        $this->db->bind(':transaction_id', $data['transaction_id'] ?? null);
        $this->db->bind(':status', $data['payment_status'] ?? 'pending');
        $this->db->bind(':response', $data['gateway_response'] ?? null);
        $this->db->bind(':paid_by', $data['paid_by'] ?? null);
        $this->db->bind(':paid_to', $data['paid_to'] ?? null);
        $this->db->bind(':paid_at', $data['paid_at'] ?? null);
        $this->db->bind(':stage', $data['payment_stage'] ?? 'after_completion');
        
        if ($this->db->execute()) {
            return $this->db->lastInsertId();
        }
        return false;
    }
    
    /**
     * Update payment status
     */
    public function updatePaymentStatus($payment_id, $status, $transaction_id = null, $gateway_response = null)
    {
        $this->db->query("UPDATE payments 
            SET payment_status = :status,
                transaction_id = COALESCE(:transaction_id, transaction_id),
                gateway_response = COALESCE(:response, gateway_response),
                paid_at = CASE WHEN :status = 'completed' THEN NOW() ELSE paid_at END
            WHERE id = :id");
        
        $this->db->bind(':status', $status);
        $this->db->bind(':transaction_id', $transaction_id);
        $this->db->bind(':response', $gateway_response);
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
     * Check if advance payment is completed
     */
    public function isAdvancePaid($request_id)
    {
        $this->db->query("SELECT COUNT(*) as count FROM payments 
            WHERE service_request_id = :id 
            AND payment_type = 'advance' 
            AND payment_status = 'completed'");
        $this->db->bind(':id', $request_id);
        $result = $this->db->single();
        return $result->count > 0;
    }
    
    /**
     * Check if final payment is completed
     */
    public function isFinalPaid($request_id)
    {
        $this->db->query("SELECT COUNT(*) as count FROM payments 
            WHERE service_request_id = :id 
            AND payment_type = 'final' 
            AND payment_status = 'completed'");
        $this->db->bind(':id', $request_id);
        $result = $this->db->single();
        return $result->count > 0;
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
}
