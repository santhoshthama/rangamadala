<?php

class M_budget
{
    private $db;
    private $columnCache = [];

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
        return $this->normalizeBudgetItems($this->db->resultSet() ?: []);
    }

    /**
     * Get a specific budget item by ID
     */
    public function getBudgetItemById($id)
    {
        $this->db->query("SELECT * FROM drama_budgets WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->normalizeBudgetItem($this->db->single());
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
        return $this->normalizeBudgetItems($this->db->resultSet() ?: []);
    }

    /**
     * Get budget summary by category
     */
    public function getBudgetSummaryByCategory($drama_id)
    {
        $this->db->query("
            SELECT 
                category,
                    SUM(allocated_amount) as total_allocated,
                    SUM(spent_amount) as total_spent,
                COUNT(*) as item_count
            FROM drama_budgets 
            WHERE drama_id = :drama_id
            GROUP BY category
                ORDER BY total_allocated DESC
        ");
        $this->db->bind(':drama_id', $drama_id);
        return $this->db->resultSet() ?: [];
    }

    /**
     * Create a new budget item
     */
    public function createBudgetItem($data)
    {
        $columns = [
            'drama_id', 'item_name', 'category', 'allocated_amount', 'spent_amount',
            'status', 'created_by'
        ];

        $binds = [
            ':drama_id' => $data['drama_id'],
            ':item_name' => $data['item_name'],
            ':category' => $data['category'],
            ':allocated_amount' => $data['allocated_amount'] ?? 0,
            ':spent_amount' => $data['spent_amount'] ?? 0,
            ':status' => $data['status'] ?? 'pending',
            ':created_by' => $data['created_by'] ?? null,
        ];

        $noteValue = $data['notes'] ?? ($data['note'] ?? null);

        if ($this->hasColumn('drama_budgets', 'notes')) {
            $columns[] = 'notes';
            $binds[':notes'] = $noteValue;
        } elseif ($this->hasColumn('drama_budgets', 'note')) {
            $columns[] = 'note';
            $binds[':note'] = $noteValue;
        }

        if ($this->hasColumn('drama_budgets', 'service_request_id')) {
            $columns[] = 'service_request_id';
            $binds[':service_request_id'] = $data['service_request_id'] ?? null;
        }

        if ($this->hasColumn('drama_budgets', 'source_type')) {
            $columns[] = 'source_type';
            $binds[':source_type'] = $data['source_type'] ?? 'manual';
        }

        if ($this->hasColumn('drama_budgets', 'last_synced_at')) {
            $columns[] = 'last_synced_at';
            $binds[':last_synced_at'] = $data['last_synced_at'] ?? null;
        }

        $placeholders = array_map(function ($column) {
            return ':' . $column;
        }, $columns);

        $this->db->query(" 
            INSERT INTO drama_budgets (" . implode(', ', $columns) . ")
            VALUES (" . implode(', ', $placeholders) . ")
        ");

        foreach ($binds as $param => $value) {
            $this->db->bind($param, $value);
        }

        return $this->db->execute();
    }

    /**
     * Update budget item
     */
    public function updateBudgetItem($id, $data)
    {
        $setParts = [
            'item_name = :item_name',
            'category = :category',
            'allocated_amount = :allocated_amount',
            'spent_amount = :spent_amount',
            'status = :status',
        ];

        $binds = [
            ':id' => $id,
            ':item_name' => $data['item_name'],
            ':category' => $data['category'],
            ':allocated_amount' => $data['allocated_amount'] ?? 0,
            ':spent_amount' => $data['spent_amount'] ?? 0,
            ':status' => $data['status'],
        ];

        $noteValue = $data['notes'] ?? ($data['note'] ?? null);

        if ($this->hasColumn('drama_budgets', 'notes')) {
            $setParts[] = 'notes = :notes';
            $binds[':notes'] = $noteValue;
        } elseif ($this->hasColumn('drama_budgets', 'note')) {
            $setParts[] = 'note = :note';
            $binds[':note'] = $noteValue;
        }

        if ($this->hasColumn('drama_budgets', 'service_request_id') && array_key_exists('service_request_id', $data)) {
            $setParts[] = 'service_request_id = :service_request_id';
            $binds[':service_request_id'] = $data['service_request_id'];
        }

        if ($this->hasColumn('drama_budgets', 'source_type') && array_key_exists('source_type', $data)) {
            $setParts[] = 'source_type = :source_type';
            $binds[':source_type'] = $data['source_type'];
        }

        if ($this->hasColumn('drama_budgets', 'last_synced_at')) {
            $setParts[] = 'last_synced_at = :last_synced_at';
            $binds[':last_synced_at'] = $data['last_synced_at'] ?? null;
        }

        $setParts[] = 'updated_at = CURRENT_TIMESTAMP';

        $this->db->query(" 
            UPDATE drama_budgets SET
                " . implode(",\n                ", $setParts) . "
            WHERE id = :id
        ");

        foreach ($binds as $param => $value) {
            $this->db->bind($param, $value);
        }

        return $this->db->execute();
    }

    public function getBudgetItemByServiceRequest($service_request_id)
    {
        if (!$this->hasColumn('drama_budgets', 'service_request_id')) {
            return null;
        }

        $this->db->query("SELECT * FROM drama_budgets WHERE service_request_id = :service_request_id ORDER BY id DESC LIMIT 1");
        $this->db->bind(':service_request_id', $service_request_id);
        return $this->normalizeBudgetItem($this->db->single());
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
        return $this->normalizeBudgetItems($this->db->resultSet() ?: []);
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

    private function normalizeBudgetItems(array $items): array
    {
        foreach ($items as $index => $item) {
            $items[$index] = $this->normalizeBudgetItem($item);
        }

        return $items;
    }

    private function normalizeBudgetItem($item)
    {
        if (is_object($item)) {
            $hasNote = property_exists($item, 'note');
            $hasNotes = property_exists($item, 'notes');

            if ($hasNote && !$hasNotes) {
                $item->notes = $item->note;
            }

            if ($hasNotes && !$hasNote) {
                $item->note = $item->notes;
            }

            return $item;
        }

        if (is_array($item)) {
            $hasNote = array_key_exists('note', $item);
            $hasNotes = array_key_exists('notes', $item);

            if ($hasNote && !$hasNotes) {
                $item['notes'] = $item['note'];
            }

            if ($hasNotes && !$hasNote) {
                $item['note'] = $item['notes'];
            }

            return $item;
        }

        return $item;
    }

    private function hasColumn(string $table, string $column): bool
    {
        $cacheKey = $table . '.' . $column;
        if (array_key_exists($cacheKey, $this->columnCache)) {
            return $this->columnCache[$cacheKey];
        }

        try {
            $this->db->query("SELECT COLUMN_NAME FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = :table AND COLUMN_NAME = :column LIMIT 1");
            $this->db->bind(':table', $table);
            $this->db->bind(':column', $column);
            $exists = (bool)$this->db->single();
            $this->columnCache[$cacheKey] = $exists;
            return $exists;
        } catch (Exception $e) {
            error_log('M_budget::hasColumn check failed: ' . $e->getMessage());
            $this->columnCache[$cacheKey] = false;
            return false;
        }
    }
}

?>
