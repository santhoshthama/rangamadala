<?php

class BrowseServiceProviders
{
    use Controller;

    public function index()
    {
        $model = new M_service_provider();
        
        // Get filter parameters
        $filters = [
            'service_type' => $_GET['service_type'] ?? '',
            'location' => $_GET['location'] ?? '',
            'min_rate' => $_GET['min_rate'] ?? '',
            'max_rate' => $_GET['max_rate'] ?? '',
            'availability' => $_GET['availability'] ?? ''
        ];

        // Get all service providers with their services
        $providers = $model->getAllProvidersWithServices($filters);
        
        // Get unique locations for filter
        $locations = $model->getAllLocations();

        // If drama_id passed, fetch drama name so request form can pre-fill
        $drama_name = null;
        $drama_id = $_GET['drama_id'] ?? null;
        if ($drama_id) {
            try {
                $dramaModel = new M_drama();
                $dramaObj = $dramaModel->getDramaById((int)$drama_id);
                $drama_name = $dramaObj ? $dramaObj->drama_name : null;
            } catch (Exception $e) {
                error_log('Error loading drama for BrowseServiceProviders: ' . $e->getMessage());
            }
        }

        $data = [
            'providers' => $providers,
            'locations' => $locations,
            'filters' => $filters,
            'drama_id' => $drama_id,
            'drama_name' => $drama_name,
        ];

        $this->view('browse_service_providers', $data);
    }

    public function viewProfile($id = null)
    {
        if (!$id) {
            header('Location: ' . ROOT . '/BrowseServiceProviders');
            exit;
        }

        $model = new M_service_provider();
        
        // Get provider details
        $provider = $model->getProviderById($id);
        
        if (!$provider) {
            header('Location: ' . ROOT . '/BrowseServiceProviders');
            exit;
        }

        // Get provider's services
        $services = $model->getProviderServices($id);
        
        // Fetch service details for each service
        if (!empty($services)) {
            foreach ($services as $service) {
                $detail = $model->getServiceDetails($service->id, $service->service_type ?? '');
                if ($detail && empty($service->service_type) && !empty($detail->service_type)) {
                    $service->service_type = $detail->service_type; // backfill for legacy rows
                }
                // Populate rate and description from detail if not already set
                if ($detail) {
                    $service->rate_per_hour = $service->rate_per_hour ?? $detail->rate_per_hour;
                    $service->rate_type = $service->rate_type ?? $detail->rate_type;
                    $service->description = $service->description ?? $detail->description;
                }
                $service->details = $detail;
            }
        }
        
        // Get provider's projects
        $projects = $model->getProviderProjects($id);

        // Get booked dates for this provider
        $bookedDateList = [];
        try {
            $availabilityModel = new M_provider_availability();
            $bookedDates = $availabilityModel->getAvailability($id);
            
            if ($bookedDates) {
                foreach ($bookedDates as $date) {
                    if ($date->status === 'booked') {
                        $bookedDateList[] = $date->available_date;
                    }
                }
            }
        } catch (Exception $e) {
            error_log("Error loading booked dates: " . $e->getMessage());
        }

        // If drama_id passed forward it so the service request form can prefill
        $drama_id = $_GET['drama_id'] ?? null;
        $drama_name = null;
        $dramaServices = [];
        if ($drama_id) {
            try {
                $dramaModel = new M_drama();
                $dramaObj = $dramaModel->getDramaById((int)$drama_id);
                $drama_name = $dramaObj ? $dramaObj->drama_name : null;

                // Preload services already added for this drama so UI can block requests until added
                $dramaServicesModel = new M_drama_services();
                $dramaServices = $dramaServicesModel->getServicesByDrama((int)$drama_id) ?? [];
            } catch (Exception $e) {
                error_log('Error loading drama for provider detail: ' . $e->getMessage());
            }
        }

        $data = [
            'provider' => $provider,
            'services' => $services,
            'projects' => $projects,
            'booked_dates' => $bookedDateList,
            'drama_id' => $drama_id,
            'drama_name' => $drama_name,
            'dramaServices' => $dramaServices,
        ];

        $this->view('service_provider_detail', $data);
    }
}
