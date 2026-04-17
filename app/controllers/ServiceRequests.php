<?php

class ServiceRequests
{
	use Controller;

	private function jsonResponse(array $payload, int $statusCode = 200): void
	{
		if (!headers_sent()) {
			header('Content-Type: application/json');
		}
		http_response_code($statusCode);
		echo json_encode($payload);
		exit;
	}

	public function index()
	{
		// Check if user is logged in
		if (!isset($_SESSION['user_id'])) {
			header("Location: " . ROOT . "/Login");
			exit;
		}

		// Check if user has service_provider role
		if (($_SESSION['user_role'] ?? '') !== 'service_provider') {
			header("Location: " . ROOT . "/Home");
			exit;
		}

		// Get provider details for profile image
		$model = new M_service_provider();
		$provider = $model->getProviderById($_SESSION['user_id']);
		
		// Load requests for this provider
		$reqModel = new M_service_request();
		$requests = $reqModel->getRequestsByProvider($_SESSION['user_id']);

		$data = [
			'provider' => $provider,
			'pageTitle' => 'Service Requests',
			'requests' => $requests,
		];

		$this->view('service_requests', $data);
	}

	public function updateStatus()
	{
		// Must be logged in as service provider
		if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'service_provider') {
			$this->jsonResponse(['success' => false, 'error' => 'Unauthorized'], 403);
		}

		$id = $_POST['id'] ?? null;
		$status = $_POST['status'] ?? null;
		$reason = $_POST['reason'] ?? null;

		if (!$id || !$status) {
			$this->jsonResponse(['success' => false, 'error' => 'Missing id or status'], 400);
		}

		try {
			$reqModel = new M_service_request();
			$request = $reqModel->getRequestById((int)$id);
			$ok = $reqModel->updateStatusDetailed((int)$id, (string)$status, $reason, (int)$_SESSION['user_id']);
			if ($ok) {
				if ($request && !empty($request->requested_by)) {
					$notificationType = null;
					$notificationTitle = null;
					$notificationMessage = null;

					if ((string)$status === 'completed') {
						$notificationType = 'pm_provider_marked_completed';
						$notificationTitle = 'Provider Marked Service Completed';
						$notificationMessage = ($request->provider_name ?? 'Provider') . ' marked "' . ($request->service_type ?? 'service') . '" as completed for "' . ($request->drama_name ?? 'your drama') . '".';
					} elseif ((string)$status === 'rejected') {
						$notificationType = 'pm_provider_rejected_request';
						$notificationTitle = 'Provider Rejected Service Request';
						$notificationMessage = ($request->provider_name ?? 'Provider') . ' rejected the service request for "' . ($request->service_type ?? 'service') . '". Reason: ' . ($reason ?: 'No reason provided');
					}

					if ($notificationType) {
						$this->notifyRequesterAction(
							(int)$request->requested_by,
							(int)($request->drama_id ?? 0),
							$notificationType,
							$notificationTitle,
							$notificationMessage,
							ROOT . '/production_manager/manage_services?drama_id=' . (int)($request->drama_id ?? 0)
						);
					}
				}

				$this->jsonResponse(['success' => true, 'status' => $status]);
			} else {
				$this->jsonResponse(['success' => false, 'error' => 'Failed to update status'], 500);
			}
		} catch (Exception $e) {
			error_log("Error in updateStatus: " . $e->getMessage());
			$this->jsonResponse(['success' => false, 'error' => 'Server error: ' . $e->getMessage()], 500);
		}
	}

	public function updatePayment()
	{
		if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'service_provider') {
			$this->jsonResponse(['success' => false, 'error' => 'Unauthorized'], 403);
		}

		$id = $_POST['id'] ?? null;
		$payment_status = $_POST['payment_status'] ?? null;
		if (!$id || !$payment_status) {
			$this->jsonResponse(['success' => false, 'error' => 'Missing id or payment_status'], 400);
		}

		$reqModel = new M_service_request();
		$ok = $reqModel->updatePaymentStatus((int)$id, (string)$payment_status, (int)$_SESSION['user_id']);
		if ($ok) {
			$this->jsonResponse(['success' => true, 'payment_status' => $payment_status]);
		} else {
			$this->jsonResponse(['success' => false, 'error' => 'Failed to update payment status'], 500);
		}
	}

	private function notifyRequesterAction($requesterId, $dramaId, $type, $title, $message, $link = null)
	{
		try {
			$notificationModel = $this->getModel('M_notification');
			if (!$notificationModel || !$requesterId) {
				return;
			}

			$notificationModel->createNotification([
				'user_id' => (int)$requesterId,
				'drama_id' => $dramaId ? (int)$dramaId : null,
				'type' => $type,
				'title' => $title,
				'message' => $message,
				'link' => $link,
			]);
		} catch (Exception $e) {
			error_log('ServiceRequests PM notification error: ' . $e->getMessage());
		}
	}
}
