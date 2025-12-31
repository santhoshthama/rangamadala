<?php

class ServiceRequests
{
	use Controller;

	public function index()
	{
		$this->view('service_requests');
	}
}
