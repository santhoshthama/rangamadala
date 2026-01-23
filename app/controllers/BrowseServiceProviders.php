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

        $data = [
            'providers' => $providers,
            'locations' => $locations,
            'filters' => $filters
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

        $data = [
            'provider' => $provider,
            'services' => $services,
            'projects' => $projects
        ];

        $this->view('service_provider_detail', $data);
    }
}
