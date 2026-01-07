// Service Provider Profile Functions

function goBack() {
    window.history.back();
}

function deleteService(serviceId) {
    if (confirm('Are you sure you want to delete this service? This action cannot be undone.')) {
        // Get ROOT from the page
        const root = document.querySelector('link[href*="assets"]').href.split('/assets')[0];
        window.location.href = root + '/ServiceProviderProfile/deleteService?id=' + serviceId;
    }
}

function deleteProject(projectId) {
    if (confirm('Are you sure you want to delete this project? This action cannot be undone.')) {
        // Get ROOT from the page
        const root = document.querySelector('link[href*="assets"]').href.split('/assets')[0];
        window.location.href = root + '/ServiceProviderProfile/deleteProject?id=' + projectId;
    }
}

function confirmDelete() {
    if (confirm('⚠️ WARNING: This will permanently delete your entire profile, including all services and projects. This action cannot be undone.\n\nAre you absolutely sure?')) {
        if (confirm('Please confirm one more time: Delete entire profile?')) {
            // Get ROOT from the page
            const root = document.querySelector('link[href*="assets"]').href.split('/assets')[0];
            window.location.href = root + '/ServiceProviderProfile/deleteProfile';
        }
    }
}
