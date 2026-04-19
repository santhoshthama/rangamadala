<?php

class M_class
{
    protected $db;

    public function __construct()
    {
        $this->db = new Database();
        $this->ensureSchema();
    }

    protected function ensureSchema()
    {
        try {
            $this->db->query("CREATE TABLE IF NOT EXISTS drama_classes (
                id INT AUTO_INCREMENT PRIMARY KEY,
                created_by INT NOT NULL,
                title VARCHAR(150) NOT NULL,
                description TEXT NULL,
                class_level ENUM('beginner', 'intermediate', 'advanced', 'all_levels') NOT NULL DEFAULT 'all_levels',
                fee DECIMAL(10,2) NOT NULL DEFAULT 0.00,
                capacity INT NOT NULL DEFAULT 30,
                class_date DATE NULL,
                start_time TIME NULL,
                duration_minutes INT NOT NULL DEFAULT 120,
                venue VARCHAR(255) NULL,
                is_published TINYINT(1) NOT NULL DEFAULT 1,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                KEY idx_drama_classes_created_by (created_by),
                KEY idx_drama_classes_class_date (class_date),
                CONSTRAINT fk_drama_classes_created_by FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
            $this->db->execute();

            $this->db->query("CREATE TABLE IF NOT EXISTS class_enrollments (
                id INT AUTO_INCREMENT PRIMARY KEY,
                class_id INT NOT NULL,
                user_id INT NOT NULL,
                status ENUM('enrolled', 'cancelled', 'completed') NOT NULL DEFAULT 'enrolled',
                enrolled_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                KEY idx_class_enrollments_class_id (class_id),
                KEY idx_class_enrollments_user_id (user_id),
                UNIQUE KEY uniq_class_enrollment (class_id, user_id),
                CONSTRAINT fk_class_enrollments_class_id FOREIGN KEY (class_id) REFERENCES drama_classes(id) ON DELETE CASCADE,
                CONSTRAINT fk_class_enrollments_user_id FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
            $this->db->execute();

            $this->db->query("CREATE TABLE IF NOT EXISTS class_enrollment_payments (
                id INT AUTO_INCREMENT PRIMARY KEY,
                order_id VARCHAR(120) NOT NULL,
                class_id INT NOT NULL,
                user_id INT NOT NULL,
                user_role VARCHAR(20) NOT NULL,
                amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
                status ENUM('initiated', 'completed', 'failed') NOT NULL DEFAULT 'initiated',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                paid_at DATETIME NULL,
                UNIQUE KEY uniq_class_payment_order_id (order_id),
                KEY idx_class_payment_class_user (class_id, user_id),
                KEY idx_class_payment_status (status),
                CONSTRAINT fk_class_payment_class_id FOREIGN KEY (class_id) REFERENCES drama_classes(id) ON DELETE CASCADE,
                CONSTRAINT fk_class_payment_user_id FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
            $this->db->execute();
        } catch (Exception $e) {
            error_log('M_class::ensureSchema failed: ' . $e->getMessage());
        }
    }

    public function validateEnrollmentEligibility($classId, $userId, $allowOwnClass = true)
    {
        try {
            $this->db->query("SELECT dc.*,
                    (SELECT COUNT(*)
                     FROM class_enrollments ce
                     WHERE ce.class_id = dc.id AND ce.status = 'enrolled') AS enrolled_count
                FROM drama_classes dc
                WHERE dc.id = :class_id AND dc.is_published = 1
                LIMIT 1");
            $this->db->bind(':class_id', (int)$classId);
            $class = $this->db->single();

            if (!$class) {
                return ['success' => false, 'message' => 'Class not found or not published.'];
            }

            if (!$allowOwnClass && (int)$class->created_by === (int)$userId) {
                return ['success' => false, 'message' => 'You cannot enroll in your own class.'];
            }

            $this->db->query("SELECT id FROM class_enrollments WHERE class_id = :class_id AND user_id = :user_id LIMIT 1");
            $this->db->bind(':class_id', (int)$classId);
            $this->db->bind(':user_id', (int)$userId);
            if ($this->db->single()) {
                return ['success' => false, 'message' => 'You are already enrolled in this class.'];
            }

            $capacity = (int)($class->capacity ?? 0);
            $enrolled = (int)($class->enrolled_count ?? 0);
            if ($capacity > 0 && $enrolled >= $capacity) {
                return ['success' => false, 'message' => 'This class is already full.'];
            }

            return ['success' => true, 'class' => $class];
        } catch (Exception $e) {
            error_log('M_class::validateEnrollmentEligibility error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Unable to validate class enrollment right now.'];
        }
    }

    public function createEnrollmentPaymentOrder($classId, $userId, $userRole, $allowOwnClass = true)
    {
        $eligibility = $this->validateEnrollmentEligibility($classId, $userId, $allowOwnClass);
        if (!$eligibility['success']) {
            return $eligibility;
        }

        $class = $eligibility['class'];
        $amount = number_format((float)($class->fee ?? 0), 2, '.', '');
        if ((float)$amount <= 0) {
            return ['success' => false, 'message' => 'Class fee is not configured for payment yet.'];
        }

        try {
            $orderId = 'CLASS-' . (int)$classId . '-' . (int)$userId . '-' . time();

            $this->db->query("INSERT INTO class_enrollment_payments
                (order_id, class_id, user_id, user_role, amount, status)
                VALUES (:order_id, :class_id, :user_id, :user_role, :amount, 'initiated')");
            $this->db->bind(':order_id', $orderId);
            $this->db->bind(':class_id', (int)$classId);
            $this->db->bind(':user_id', (int)$userId);
            $this->db->bind(':user_role', strtolower(trim((string)$userRole)));
            $this->db->bind(':amount', $amount);

            if (!$this->db->execute()) {
                return ['success' => false, 'message' => 'Unable to initialize class payment.'];
            }

            return [
                'success' => true,
                'order_id' => $orderId,
                'amount' => $amount,
                'class' => $class,
            ];
        } catch (Exception $e) {
            error_log('M_class::createEnrollmentPaymentOrder error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Unable to initialize class payment.'];
        }
    }

    public function completeEnrollmentPayment($orderId, $userId, $userRole, $allowOwnClass = true)
    {
        try {
            $this->db->query("SELECT *
                FROM class_enrollment_payments
                WHERE order_id = :order_id
                  AND user_id = :user_id
                  AND user_role = :user_role
                LIMIT 1");
            $this->db->bind(':order_id', $orderId);
            $this->db->bind(':user_id', (int)$userId);
            $this->db->bind(':user_role', strtolower(trim((string)$userRole)));
            $payment = $this->db->single();

            if (!$payment) {
                return ['success' => false, 'message' => 'Class payment order not found.'];
            }

            if (strtolower((string)$payment->status) === 'completed') {
                return ['success' => true, 'message' => 'Payment already confirmed. You are enrolled in this class.'];
            }

            $enrollment = $this->enrollUser((int)$payment->class_id, (int)$userId, $allowOwnClass);
            if (!$enrollment['success'] && stripos((string)$enrollment['message'], 'already enrolled') === false) {
                return $enrollment;
            }

            $this->db->query("UPDATE class_enrollment_payments
                SET status = 'completed',
                    paid_at = NOW()
                WHERE id = :id");
            $this->db->bind(':id', (int)$payment->id);
            $this->db->execute();

            return ['success' => true, 'message' => 'Payment successful. You are now enrolled in this class.'];
        } catch (Exception $e) {
            error_log('M_class::completeEnrollmentPayment error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Unable to complete enrollment after payment.'];
        }
    }

    public function getEnrollmentPaymentsByUser($userId, $userRole = 'audience')
    {
        try {
            $this->db->query("SELECT cep.id,
                    cep.order_id,
                    cep.amount,
                    cep.status,
                    cep.created_at,
                    cep.paid_at,
                    dc.id AS class_id,
                    dc.title AS class_title,
                    dc.class_date,
                    dc.venue,
                    u.full_name AS creator_name
                FROM class_enrollment_payments cep
                INNER JOIN drama_classes dc ON dc.id = cep.class_id
                LEFT JOIN users u ON u.id = dc.created_by
                WHERE cep.user_id = :user_id
                  AND cep.user_role = :user_role
                  AND LOWER(cep.status) = 'completed'
                ORDER BY COALESCE(cep.paid_at, cep.created_at) DESC");
            $this->db->bind(':user_id', (int)$userId);
            $this->db->bind(':user_role', strtolower(trim((string)$userRole)));
            return $this->db->resultSet();
        } catch (Exception $e) {
            error_log('M_class::getEnrollmentPaymentsByUser error: ' . $e->getMessage());
            return [];
        }
    }

    public function getEnrollmentPaymentByIdForUser($paymentId, $userId, $userRole = 'audience')
    {
        try {
            $this->db->query("SELECT cep.id,
                    cep.order_id,
                    cep.amount,
                    cep.status,
                    cep.created_at,
                    cep.paid_at,
                    dc.id AS class_id,
                    dc.title AS class_title,
                    dc.class_date,
                    dc.venue,
                    u.full_name AS creator_name
                FROM class_enrollment_payments cep
                INNER JOIN drama_classes dc ON dc.id = cep.class_id
                LEFT JOIN users u ON u.id = dc.created_by
                WHERE cep.id = :payment_id
                  AND cep.user_id = :user_id
                  AND cep.user_role = :user_role
                LIMIT 1");
            $this->db->bind(':payment_id', (int)$paymentId);
            $this->db->bind(':user_id', (int)$userId);
            $this->db->bind(':user_role', strtolower(trim((string)$userRole)));
            return $this->db->single();
        } catch (Exception $e) {
            error_log('M_class::getEnrollmentPaymentByIdForUser error: ' . $e->getMessage());
            return null;
        }
    }

    public function createClass($artistId, $data)
    {
        try {
            $title = trim((string)($data['title'] ?? ''));
            if ($title === '') {
                return ['success' => false, 'message' => 'Class title is required.'];
            }

            $this->db->query("INSERT INTO drama_classes
                (created_by, title, description, class_level, fee, capacity, class_date, start_time, duration_minutes, venue, is_published)
                VALUES
                (:created_by, :title, :description, :class_level, :fee, :capacity, :class_date, :start_time, :duration_minutes, :venue, :is_published)");

            $this->db->bind(':created_by', (int)$artistId);
            $this->db->bind(':title', $title);
            $this->db->bind(':description', trim((string)($data['description'] ?? '')) ?: null);
            $this->db->bind(':class_level', $data['class_level'] ?? 'all_levels');
            $this->db->bind(':fee', number_format((float)($data['fee'] ?? 0), 2, '.', ''));
            $this->db->bind(':capacity', max(1, (int)($data['capacity'] ?? 30)));
            $this->db->bind(':class_date', !empty($data['class_date']) ? $data['class_date'] : null);
            $this->db->bind(':start_time', !empty($data['start_time']) ? $data['start_time'] : null);
            $this->db->bind(':duration_minutes', max(30, (int)($data['duration_minutes'] ?? 120)));
            $this->db->bind(':venue', trim((string)($data['venue'] ?? '')) ?: null);
            $this->db->bind(':is_published', isset($data['is_published']) ? (int)$data['is_published'] : 1);

            if ($this->db->execute()) {
                return ['success' => true, 'message' => 'Class created successfully.'];
            }

            return ['success' => false, 'message' => 'Failed to create class. Please try again.'];
        } catch (Exception $e) {
            error_log('M_class::createClass error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Failed to create class. Please try again.'];
        }
    }

    public function getClassesByCreator($creatorId)
    {
        try {
            $this->db->query("SELECT dc.*,
                    (SELECT COUNT(*)
                     FROM class_enrollments ce
                     WHERE ce.class_id = dc.id AND ce.status = 'enrolled') AS enrolled_count
                FROM drama_classes dc
                WHERE dc.created_by = :creator_id
                ORDER BY dc.created_at DESC");
            $this->db->bind(':creator_id', (int)$creatorId);
            return $this->db->resultSet();
        } catch (Exception $e) {
            error_log('M_class::getClassesByCreator error: ' . $e->getMessage());
            return [];
        }
    }

    public function getPublishedClasses($excludeCreatorId = null)
    {
        try {
            $sql = "SELECT dc.*,
                    u.full_name AS creator_name,
                    (SELECT COUNT(*)
                     FROM class_enrollments ce
                     WHERE ce.class_id = dc.id AND ce.status = 'enrolled') AS enrolled_count
                FROM drama_classes dc
                INNER JOIN users u ON u.id = dc.created_by
                WHERE dc.is_published = 1";

            if ($excludeCreatorId !== null) {
                $sql .= " AND dc.created_by <> :exclude_creator_id";
            }

            $sql .= " ORDER BY COALESCE(dc.class_date, DATE(dc.created_at)) ASC, COALESCE(dc.start_time, '23:59:59') ASC";

            $this->db->query($sql);
            if ($excludeCreatorId !== null) {
                $this->db->bind(':exclude_creator_id', (int)$excludeCreatorId);
            }

            return $this->db->resultSet();
        } catch (Exception $e) {
            error_log('M_class::getPublishedClasses error: ' . $e->getMessage());
            return [];
        }
    }

    public function getEnrolledClassesByUser($userId)
    {
        try {
            $this->db->query("SELECT ce.id AS enrollment_id,
                    ce.status AS enrollment_status,
                    ce.enrolled_at,
                    dc.*,
                    u.full_name AS creator_name,
                    (SELECT COUNT(*)
                     FROM class_enrollments ce2
                     WHERE ce2.class_id = dc.id AND ce2.status = 'enrolled') AS enrolled_count
                FROM class_enrollments ce
                INNER JOIN drama_classes dc ON dc.id = ce.class_id
                INNER JOIN users u ON u.id = dc.created_by
                WHERE ce.user_id = :user_id
                ORDER BY ce.enrolled_at DESC");
            $this->db->bind(':user_id', (int)$userId);
            return $this->db->resultSet();
        } catch (Exception $e) {
            error_log('M_class::getEnrolledClassesByUser error: ' . $e->getMessage());
            return [];
        }
    }

    public function enrollUser($classId, $userId, $allowOwnClass = true)
    {
        try {
            $eligibility = $this->validateEnrollmentEligibility($classId, $userId, $allowOwnClass);
            if (!$eligibility['success']) {
                return $eligibility;
            }

            $this->db->query("INSERT INTO class_enrollments (class_id, user_id, status) VALUES (:class_id, :user_id, 'enrolled')");
            $this->db->bind(':class_id', (int)$classId);
            $this->db->bind(':user_id', (int)$userId);

            if ($this->db->execute()) {
                return ['success' => true, 'message' => 'Enrollment completed successfully.'];
            }

            return ['success' => false, 'message' => 'Enrollment failed. Please try again.'];
        } catch (Exception $e) {
            error_log('M_class::enrollUser error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Enrollment failed. Please try again.'];
        }
    }

    public function togglePublishByOwner($classId, $ownerId)
    {
        try {
            $this->db->query("SELECT id, is_published FROM drama_classes WHERE id = :class_id AND created_by = :owner_id LIMIT 1");
            $this->db->bind(':class_id', (int)$classId);
            $this->db->bind(':owner_id', (int)$ownerId);
            $class = $this->db->single();

            if (!$class) {
                return ['success' => false, 'message' => 'Class not found.'];
            }

            $newStatus = ((int)$class->is_published === 1) ? 0 : 1;
            $this->db->query("UPDATE drama_classes SET is_published = :is_published WHERE id = :class_id AND created_by = :owner_id");
            $this->db->bind(':is_published', $newStatus);
            $this->db->bind(':class_id', (int)$classId);
            $this->db->bind(':owner_id', (int)$ownerId);

            if ($this->db->execute()) {
                return [
                    'success' => true,
                    'message' => $newStatus === 1 ? 'Class published successfully.' : 'Class unpublished successfully.'
                ];
            }

            return ['success' => false, 'message' => 'Unable to update class publish status.'];
        } catch (Exception $e) {
            error_log('M_class::togglePublishByOwner error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Unable to update class publish status.'];
        }
    }

    public function deleteByOwner($classId, $ownerId)
    {
        try {
            $this->db->query("DELETE FROM drama_classes WHERE id = :class_id AND created_by = :owner_id");
            $this->db->bind(':class_id', (int)$classId);
            $this->db->bind(':owner_id', (int)$ownerId);

            if ($this->db->execute() && $this->db->rowCount() > 0) {
                return ['success' => true, 'message' => 'Class deleted successfully.'];
            }

            return ['success' => false, 'message' => 'Class not found or cannot be deleted.'];
        } catch (Exception $e) {
            error_log('M_class::deleteByOwner error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Unable to delete class.'];
        }
    }
}
