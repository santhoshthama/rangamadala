<?php

class ServiceRequests
{
	use Controller;

	public function index()
	{
		// Check if user is logged in
		if (!isset($_SESSION['user_id'])) {
			header("Location: " . ROOT . "/Login");
			exit;
		}

		// Check if user has service_provider role
		if ($_SESSION['user_role'] !== 'service_provider') {
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
		if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'service_provider') {
			http_response_code(403);
			echo json_encode(['success' => false, 'error' => 'Unauthorized']);
			return;
		}

		$id = $_POST['id'] ?? null;
		$status = $_POST['status'] ?? null;
		$reason = $_POST['reason'] ?? null;

		if (!$id || !$status) {
			http_response_code(400);
			echo json_encode(['success' => false, 'error' => 'Missing id or status']);
			return;
		}

		$reqModel = new M_service_request();
		$ok = $reqModel->updateStatusDetailed((int)$id, (string)$status, $reason, (int)$_SESSION['user_id']);
		if ($ok) {
			echo json_encode(['success' => true, 'status' => $status]);
		} else {
			http_response_code(500);
			echo json_encode(['success' => false, 'error' => 'Failed to update status']);
		}
	}

	public function updatePayment()
	{
		if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'service_provider') {
			http_response_code(403);
			echo json_encode(['success' => false, 'error' => 'Unauthorized']);
			return;
		}

		$id = $_POST['id'] ?? null;
		$payment_status = $_POST['payment_status'] ?? null;
		if (!$id || !$payment_status) {
			http_response_code(400);
			echo json_encode(['success' => false, 'error' => 'Missing id or payment_status']);
			return;
		}

		$reqModel = new M_service_request();
		$ok = $reqModel->updatePaymentStatus((int)$id, (string)$payment_status, (int)$_SESSION['user_id']);
		if ($ok) {
			echo json_encode(['success' => true, 'payment_status' => $payment_status]);
		} else {
			http_response_code(500);
			echo json_encode(['success' => false, 'error' => 'Failed to update payment status']);
		}
	}
}
