<?php

require_once __DIR__ . '/DirectorFeatureControllerTrait.php';

class DirectorDramaController
{
    use Controller, DirectorFeatureControllerTrait;

    protected $dramaModel;
    protected $roleModel;
    protected $pmModel;
    protected $profileModel;

    public function __construct()
    {
        $this->dramaModel = $this->getModel('M_drama');
        $this->roleModel = $this->getModel('M_role');
        $this->pmModel = $this->getModel('M_production_manager');
        $this->profileModel = $this->getModel('M_universal_profile');
    }

    public function dashboard()
    {
        $this->renderDramaView('dashboard', [], function ($drama) {
            $productionManager = $this->pmModel ? $this->pmModel->getAssignedManager((int)$drama->id) : null;

            $roles = $this->roleModel ? $this->roleModel->getRolesByDrama((int)$drama->id) : [];
            $roleStats = $this->roleModel ? $this->roleModel->getRoleStats((int)$drama->id) : null;
            $pendingApplications = $this->roleModel ? $this->roleModel->getPendingApplications((int)$drama->id) : [];

            $assignedArtists = [];
            if ($this->roleModel && !empty($roles)) {
                foreach ($roles as $role) {
                    $assignments = $this->roleModel->getAssignmentsByRole((int)$role->id);
                    foreach ($assignments as $assignment) {
                        $assignedArtists[] = (object)[
                            'artist_name' => $assignment->artist_name ?? 'Unknown',
                            'role_name' => $role->role_name ?? 'Unknown Role',
                            'role_type' => $role->role_type ?? 'supporting',
                            'assigned_at' => $assignment->assigned_at ?? null,
                        ];
                    }
                }
            }

            $totalRoles = (int)($roleStats->total_roles ?? 0);
            $totalPositions = (int)($roleStats->total_positions ?? 0);
            $filledPositions = (int)($roleStats->filled_positions ?? 0);
            $hasProductionManager = $productionManager ? 1 : 0;
            $pendingApplicationsCount = is_array($pendingApplications) ? count($pendingApplications) : 0;

            return [
                'productionManager' => $productionManager,
                'assignedArtists' => $assignedArtists,
                'dashboardStats' => [
                    'total_roles' => $totalRoles,
                    'total_positions' => $totalPositions,
                    'filled_positions' => $filledPositions,
                    'production_managers' => $hasProductionManager,
                    'pending_applications' => $pendingApplicationsCount,
                ],
            ];
        });
    }

    public function drama_details()
    {
        $this->renderDramaView('drama_details');
    }

    public function view_services_budget()
    {
        $this->renderDramaView('view_services_budget', [], function ($drama) {
            $dramaId = (int)$drama->id;

            $serviceModel = $this->getModel('M_service_request');
            $budgetModel = $this->getModel('M_budget');
            $paymentModel = $this->getModel('M_payment');

            $servicesRaw = $serviceModel ? ($serviceModel->getServicesByDrama($dramaId) ?? []) : [];
            $budgetItemsRaw = $budgetModel ? ($budgetModel->getBudgetByDrama($dramaId) ?? []) : [];
            $budgetCategoriesRaw = $budgetModel ? ($budgetModel->getBudgetSummaryByCategory($dramaId) ?? []) : [];

            $totalBudget = $budgetModel ? (float)$budgetModel->getTotalBudget($dramaId) : 0.0;
            $usedBudget = $budgetModel ? (float)$budgetModel->getTotalSpent($dramaId) : 0.0;
            $remainingBudget = max(0, $totalBudget - $usedBudget);
            $usedPct = $totalBudget > 0 ? round(($usedBudget / $totalBudget) * 100, 2) : 0;
            $remainingPct = max(0, round(100 - $usedPct, 2));

            $pendingPayments = 0.0;
            if ($paymentModel && is_array($servicesRaw) && !empty($servicesRaw)) {
                $requestIds = [];
                foreach ($servicesRaw as $service) {
                    $rid = (int)($service->id ?? 0);
                    if ($rid > 0) {
                        $requestIds[] = $rid;
                    }
                }

                if (!empty($requestIds)) {
                    $paymentStats = $paymentModel->getRequestPaymentStats($requestIds);
                    foreach ($servicesRaw as $service) {
                        $rid = (int)($service->id ?? 0);
                        $serviceBudget = (float)($service->budget ?? 0);
                        $paid = isset($paymentStats[$rid]) ? (float)($paymentStats[$rid]['total_paid'] ?? 0) : 0.0;
                        if ($serviceBudget > $paid) {
                            $pendingPayments += ($serviceBudget - $paid);
                        }
                    }
                }
            }

            $services = [];
            foreach ($servicesRaw as $service) {
                $services[] = [
                    'title' => $service->service_type ?? 'Service',
                    'managed_by' => $service->provider_name ?? null,
                    'details' => $service->description ?? $service->service_required ?? null,
                    'status' => ucfirst((string)($service->status ?? 'pending')),
                    'payment_status' => ucfirst((string)($service->calculated_payment_status ?? $service->payment_status ?? 'unpaid')),
                ];
            }

            $budgetItems = [];
            foreach ($budgetItemsRaw as $item) {
                $budgetItems[] = [
                    'title' => $item->item_name ?? 'Budget Item',
                    'details' => $item->notes ?? null,
                    'amount' => isset($item->allocated_amount) ? (float)$item->allocated_amount : 0,
                    'status' => ucfirst((string)($item->status ?? 'pending')),
                ];
            }

            $budgetCategories = [];
            foreach ($budgetCategoriesRaw as $category) {
                $allocated = isset($category->total_allocated) ? (float)$category->total_allocated : 0.0;
                $pct = $totalBudget > 0 ? round(($allocated / $totalBudget) * 100, 2) : 0.0;
                $budgetCategories[] = [
                    'name' => ucfirst((string)($category->category ?? 'Other')),
                    'amount' => $allocated,
                    'percentage' => $pct,
                ];
            }

            return [
                'services' => $services,
                'budgetItems' => $budgetItems,
                'budgetCategories' => $budgetCategories,
                'theaterBookings' => [],
                'budgetSummary' => [
                    'total_budget' => $totalBudget,
                    'used_budget' => $usedBudget,
                    'remaining_budget' => $remainingBudget,
                    'used_percentage' => $usedPct,
                    'remaining_percentage' => $remainingPct,
                    'pending_payments' => $pendingPayments,
                ],
            ];
        });
    }

    public function create_drama()
    {
        $this->renderDramaView('create_drama');
    }

    public function manage_dramas()
    {
        $this->renderDramaView('manage_dramas');
    }

    public function role_management()
    {
        $this->renderDramaView('role_management');
    }

    public function update_drama()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $dramaId = $this->getQueryParam('drama_id');
            header('Location: ' . ROOT . '/director/drama_details' . ($dramaId ? "?drama_id={$dramaId}" : ''));
            exit;
        }

        $drama = $this->authorizeDrama();

        $formData = [
            'drama_name' => trim($_POST['drama_name'] ?? ''),
            'certificate_number' => trim($_POST['certificate_number'] ?? ''),
            'owner_name' => trim($_POST['owner_name'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
        ];

        $errors = [];

        if ($formData['drama_name'] === '') {
            $errors[] = 'Drama name is required';
        }

        if ($formData['certificate_number'] === '') {
            $errors[] = 'Certificate number is required';
        }

        if ($formData['owner_name'] === '') {
            $errors[] = 'Owner name is required';
        }

        if ($formData['description'] === '') {
            $errors[] = 'Drama description is required';
        }

        $newImageName = null;
        if (isset($_FILES['certificate_image']) && $_FILES['certificate_image']['error'] !== UPLOAD_ERR_NO_FILE) {
            $newImageName = $this->handleImageUpload($_FILES['certificate_image']);
            if ($newImageName === false) {
                $errors[] = 'Invalid certificate image. Allowed types: JPG, PNG, GIF, WEBP, PDF up to 5MB.';
            }
        }

        if (!empty($errors)) {
            $_SESSION['message'] = implode(' ', $errors);
            $_SESSION['message_type'] = 'error';
            $this->renderDramaView('drama_details', ['form_data' => $formData]);
            return;
        }

        $updateData = $formData;
        if ($newImageName !== null) {
            $updateData['certificate_image'] = $newImageName;
        }

        $updated = $this->dramaModel->updateDrama((int)$drama->id, $updateData);

        if ($updated) {
            if ($newImageName !== null && !empty($drama->certificate_image)) {
                $uploadDir = dirname(__DIR__, 3) . '/public/uploads/certificates/';
                $oldPath = $uploadDir . $drama->certificate_image;
                if (file_exists($oldPath)) {
                    @unlink($oldPath);
                }
            }

            $_SESSION['message'] = 'Drama details updated successfully.';
            $_SESSION['message_type'] = 'success';
        } else {
            if ($newImageName !== null) {
                $uploadDir = dirname(__DIR__, 3) . '/public/uploads/certificates/';
                $newPath = $uploadDir . $newImageName;
                if (file_exists($newPath)) {
                    @unlink($newPath);
                }
            }

            $_SESSION['message'] = 'Failed to update drama. Certificate number might already exist.';
            $_SESSION['message_type'] = 'error';
        }

        header('Location: ' . ROOT . '/director/drama_details?drama_id=' . $drama->id);
        exit;
    }

    public function publish_drama()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $dramaId = $this->getQueryParam('drama_id');
            header('Location: ' . ROOT . '/director/drama_details' . ($dramaId ? '?drama_id=' . (int)$dramaId : ''));
            exit;
        }

        $drama = $this->authorizeDrama();

        $formData = [
            'category_id' => trim($_POST['category_id'] ?? ''),
            'public_description' => trim($_POST['public_description'] ?? ''),
            'language' => trim($_POST['language'] ?? ''),
            'duration_minutes' => trim($_POST['duration_minutes'] ?? ''),
            'showing_prices' => trim($_POST['showing_prices'] ?? ''),
        ];

        $rawShowingPrice = $formData['showing_prices'];
        if ($rawShowingPrice !== '') {
            $normalizedNumeric = str_replace(',', '', $rawShowingPrice);
            if (is_numeric($normalizedNumeric) && (float)$normalizedNumeric >= 0) {
                $formData['showing_prices'] = 'LKR ' . number_format((float)$normalizedNumeric, 2, '.', '');
            }
        }

        $errors = [];

        $categoryId = filter_var($formData['category_id'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        if ($categoryId === false) {
            $errors[] = 'Category is required.';
        }

        if ($formData['public_description'] === '') {
            $errors[] = 'Public drama description is required.';
        }

        if ($formData['language'] === '') {
            $errors[] = 'Language is required.';
        }

        $duration = filter_var($formData['duration_minutes'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        if ($duration === false) {
            $errors[] = 'Duration must be a positive whole number.';
        }

        if ($formData['showing_prices'] === '') {
            $errors[] = 'Showing prices are required.';
        } elseif (!preg_match('/^LKR\s\d+(?:\.\d{2})$/', $formData['showing_prices'])) {
            $errors[] = 'Showing prices must be a valid amount.';
        } elseif (strlen($formData['showing_prices']) > 500) {
            $errors[] = 'Showing prices cannot exceed 500 characters.';
        }

        $posterName = $drama->poster_image ?? null;
        if (isset($_FILES['poster_image']) && $_FILES['poster_image']['error'] !== UPLOAD_ERR_NO_FILE) {
            $newPoster = $this->handlePosterUpload($_FILES['poster_image']);
            if ($newPoster === false) {
                $errors[] = 'Invalid poster image. Allowed types: JPG, PNG, GIF, WEBP up to 8MB.';
            } else {
                $posterName = $newPoster;
            }
        }

        if (empty($posterName)) {
            $errors[] = 'Drama poster image is required to publish.';
        }

        if (!empty($errors)) {
            $_SESSION['message'] = implode(' ', $errors);
            $_SESSION['message_type'] = 'error';
            $this->renderDramaView('drama_details', ['publish_form_data' => $formData]);
            return;
        }

        $publishData = [
            'category_id' => (int)$categoryId,
            'public_description' => $formData['public_description'],
            'genre' => $drama->genre ?? '',
            'language' => $formData['language'],
            'duration_minutes' => (int)$duration,
            'venue' => $drama->venue ?? '',
            'event_date' => $drama->event_date ?? null,
            'event_time' => $drama->event_time ?? null,
            'ticket_price' => isset($drama->ticket_price) ? number_format((float)$drama->ticket_price, 2, '.', '') : '0.00',
            'showing_prices' => $formData['showing_prices'],
            'poster_image' => $posterName,
        ];

        $ok = $this->dramaModel->publishDrama((int)$drama->id, (int)$_SESSION['user_id'], $publishData);

        if ($ok) {
            $queued = $this->dramaModel->queuePosterForAdminHome((int)$drama->id);
            $_SESSION['message'] = $queued
                ? 'Drama published successfully. Poster sent to admin for home page review.'
                : 'Drama published successfully. Poster queue could not be updated for admin.';
            $_SESSION['message_type'] = $queued ? 'success' : 'info';
        } else {
            $_SESSION['message'] = 'Failed to publish drama. Please try again.';
            $_SESSION['message_type'] = 'error';
        }

        header('Location: ' . ROOT . '/director/drama_details?drama_id=' . (int)$drama->id);
        exit;
    }

    protected function handleImageUpload($file)
    {
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp', 'application/pdf'];
        $maxSize = 5 * 1024 * 1024;

        if (!in_array($file['type'], $allowedTypes)) {
            return false;
        }

        if ($file['size'] > $maxSize) {
            return false;
        }

        $uploadDir = dirname(__DIR__, 3) . '/public/uploads/certificates/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $filename = 'certificate_' . time() . '_' . uniqid('', true) . '.' . $extension;
        $filepath = $uploadDir . $filename;

        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            return $filename;
        }

        return false;
    }

    protected function handlePosterUpload($file)
    {
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        $maxSize = 8 * 1024 * 1024;

        if (!in_array($file['type'], $allowedTypes)) {
            return false;
        }

        if ($file['size'] > $maxSize) {
            return false;
        }

        $uploadDir = dirname(__DIR__, 3) . '/public/uploads/dramas/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $filename = 'drama_poster_' . time() . '_' . uniqid('', true) . '.' . $extension;
        $filepath = $uploadDir . $filename;

        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            return $filename;
        }

        return false;
    }
}
