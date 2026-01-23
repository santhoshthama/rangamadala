<?php

class M_budget
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    /**
     * Get all budget items for a drama
     */
    public function getBudgetByDrama($drama_id)
    {
        $this->db->query("
            SELECT * FROM drama_budgets 
            WHERE drama_id = :drama_id 
            ORDER BY category ASC, created_at DESC
        ");
        $this->db->bind(':drama_id', $drama_id);
        return $this->db->resultSet() ?: [];
    }

    /**
     * Get a specific budget item by ID
     */
    public function getBudgetItemById($id)
    {
        $this->db->query("SELECT * FROM drama_budgets WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    /**
     * Get total allocated budget for a drama
     */
    public function getTotalBudget($drama_id)
    {
        $this->db->query("
            SELECT SUM(allocated_amount) as total FROM drama_budgets 
            WHERE drama_id = :drama_id
        ");
        $this->db->bind(':drama_id', $drama_id);
        $result = $this->db->single();
        return floatval($result->total ?? 0);
    }

    /**
     * Get total spent amount for a drama
     */
    public function getTotalSpent($drama_id)
    {
        $this->db->query("
            SELECT SUM(spent_amount) as total FROM drama_budgets 
            WHERE drama_id = :drama_id
        ");
        $this->db->bind(':drama_id', $drama_id);
        $result = $this->db->single();
        return floatval($result->total ?? 0);
    }

    /**
     * Get remaining budget for a drama
     */
    public function getRemainingBudget($drama_id)
    {
        $total = $this->getTotalBudget($drama_id);
        $spent = $this->getTotalSpent($drama_id);
        return floatval($total - $spent);
    }

    /**
     * Get budget by category
     */
    public function getBudgetByCategory($drama_id, $category)
    {
        $this->db->query("
            SELECT * FROM drama_budgets 
            WHERE drama_id = :drama_id AND category = :category
            ORDER BY created_at DESC
        ");
        $this->db->bind(':drama_id', $drama_id);
        $this->db->bind(':category', $category);
        return $this->db->resultSet() ?: [];
    }

    /**
     * Get budget summary by category
     */
    public function getBudgetSummaryByCategory($drama_id)
    {
        $this->db->query("
            SELECT 
                category,
                SUM(allocated_amount) as allocated,
                SUM(spent_amount) as spent,
                COUNT(*) as item_count
            FROM drama_budgets 
            WHERE drama_id = :drama_id
            GROUP BY category
            ORDER BY allocated DESC
        ");
        $this->db->bind(':drama_id', $drama_id);
        return $this->db->resultSet() ?: [];
    }

    /**
     * Create a new budget item
     */
    public function createBudgetItem($data)
    {
        $this->db->query("
            INSERT INTO drama_budgets (
                drama_id, item_name, category, allocated_amount, spent_amount, 
                status, notes, created_by
            ) VALUES (
                :drama_id, :item_name, :category, :allocated_amount, :spent_amount,
                :status, :notes, :created_by
            )
        ");

        $this->db->bind(':drama_id', $data['drama_id']);
        $this->db->bind(':item_name', $data['item_name']);
        $this->db->bind(':category', $data['category']);
        $this->db->bind(':allocated_amount', $data['allocated_amount'] ?? 0);
        $this->db->bind(':spent_amount', $data['spent_amount'] ?? 0);
        $this->db->bind(':status', $data['status'] ?? 'pending');
        $this->db->bind(':notes', $data['notes'] ?? null);
        $this->db->bind(':created_by', $data['created_by']);

        return $this->db->execute();
    }

    /**
     * Update budget item
     */
    public function updateBudgetItem($id, $data)
    {
        $this->db->query("
            UPDATE drama_budgets SET
                item_name = :item_name,
                category = :category,
                allocated_amount = :allocated_amount,
                spent_amount = :spent_amount,
                status = :status,
                notes = :notes,
                updated_at = CURRENT_TIMESTAMP
            WHERE id = :id
        ");

        $this->db->bind(':id', $id);
        $this->db->bind(':item_name', $data['item_name']);
        $this->db->bind(':category', $data['category']);
        $this->db->bind(':allocated_amount', $data['allocated_amount'] ?? 0);
        $this->db->bind(':spent_amount', $data['spent_amount'] ?? 0);
        $this->db->bind(':status', $data['status']);
        $this->db->bind(':notes', $data['notes'] ?? null);

        return $this->db->execute();
    }

    /**
     * Update spent amount for a budget item
     */
    public function updateSpentAmount($id, $spent_amount)
    {
        $this->db->query("
            UPDATE drama_budgets 
            SET spent_amount = :spent_amount, updated_at = CURRENT_TIMESTAMP 
            WHERE id = :id
        ");
        $this->db->bind(':id', $id);
        $this->db->bind(':spent_amount', $spent_amount);
        return $this->db->execute();
    }

    /**
     * Update budget status
     */
    public function updateStatus($id, $status)
    {
        $this->db->query("
            UPDATE drama_budgets 
            SET status = :status, updated_at = CURRENT_TIMESTAMP 
            WHERE id = :id
        ");
        $this->db->bind(':id', $id);
        $this->db->bind(':status', $status);
        return $this->db->execute();
    }

    /**
     * Delete a budget item
     */
    public function deleteBudgetItem($id)
    {
        $this->db->query("DELETE FROM drama_budgets WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    /**
     * Get budget items by status
     */
    public function getBudgetByStatus($drama_id, $status)
    {
        $this->db->query("
            SELECT * FROM drama_budgets 
            WHERE drama_id = :drama_id AND status = :status
            ORDER BY created_at DESC
        ");
        $this->db->bind(':drama_id', $drama_id);
        $this->db->bind(':status', $status);
        return $this->db->resultSet() ?: [];
    }

    /**
     * Get budget statistics for a drama
     */
    public function getBudgetStats($drama_id)
    {
        $this->db->query("
            SELECT 
                COUNT(*) as item_count,
                SUM(allocated_amount) as total_allocated,
                SUM(spent_amount) as total_spent,
                COUNT(DISTINCT category) as category_count
            FROM drama_budgets 
            WHERE drama_id = :drama_id
        ");
        $this->db->bind(':drama_id', $drama_id);
        return $this->db->single();
    }
}

?>
