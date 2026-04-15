<div class="header--wrapper">
    <div class="header--title">
        <span>Service Provider</span>
        <h2><?= isset($pageTitle) ? $pageTitle : 'Dashboard' ?></h2>
    </div>
    <div class="user--info">
        <div class="search--box" id="searchContainer" style="position: relative;">
            <i class="fas fa-search"></i>
            <input 
                type="text" 
                id="searchInput" 
                placeholder="Search services, clients..." 
                autocomplete="off"
                style="cursor: pointer; width: 200px;">
            <div id="searchResults" style="display: none; position: absolute; top: 100%; left: 0; right: 0; background: white; border: 1px solid #ddd; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); max-height: 400px; overflow-y: auto; z-index: 1000;"></div>
        </div>
        <a href="<?= ROOT ?>/ServiceProviderProfile" style="cursor: pointer;">
            <img src="<?= !empty($provider->profile_image) ? ROOT . '/uploads/profile_images/' . $provider->profile_image : ROOT . '/uploads/profile_images/default_user.jpg' ?>" alt="Profile" onerror="this.src='<?= ROOT ?>/uploads/profile_images/default_user.jpg'" style="cursor: pointer;">
        </a>
        <a href="<?= ROOT ?>/Logout" class="header-logout" title="Logout">
            <i class="fas fa-right-from-bracket"></i>
            <span>Logout</span>
        </a>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const searchResults = document.getElementById('searchResults');
    const searchContainer = document.getElementById('searchContainer');
    let searchTimeout;

    searchInput.addEventListener('input', function() {
        const query = this.value.trim();
        
        clearTimeout(searchTimeout);
        
        if (query.length < 2) {
            searchResults.style.display = 'none';
            return;
        }

        searchTimeout = setTimeout(function() {
            fetch('<?= ROOT ?>/ServiceProviderDashboard/search?q=' + encodeURIComponent(query))
                .then(response => response.json())
                .then(data => {
                    if (data.results && data.results.length > 0) {
                        let html = '';
                        data.results.forEach(result => {
                            html += `
                                <div class="search-result-item" style="padding: 12px 16px; border-bottom: 1px solid #eee; cursor: pointer; hover:background: #f5f5f5; transition: background 0.2s;">
                                    <div style="font-weight: 600; color: #333;">${result.title}</div>
                                    <div style="font-size: 12px; color: #666;">${result.subtitle}</div>
                                    <div style="font-size: 11px; color: #999; margin-top: 4px;">${result.description}</div>
                                </div>
                            `;
                        });
                        searchResults.innerHTML = html;
                        searchResults.style.display = 'block';

                        // Add click handlers
                        document.querySelectorAll('.search-result-item').forEach((item, index) => {
                            item.addEventListener('click', function() {
                                const result = data.results[index];
                                if (result.type === 'service') {
                                    window.location.href = '<?= ROOT ?>/ServiceRequests#service-' + result.id;
                                } else if (result.type === 'client') {
                                    // Could navigate to payment page or service requests filtered by this client
                                    alert('Client: ' + result.title + '\nBookings: ' + result.subtitle);
                                }
                                searchResults.style.display = 'none';
                                searchInput.value = '';
                            });
                        });
                    } else {
                        searchResults.innerHTML = '<div style="padding: 16px; text-align: center; color: #999;">No results found</div>';
                        searchResults.style.display = 'block';
                    }
                })
                .catch(error => {
                    console.error('Search error:', error);
                    searchResults.innerHTML = '<div style="padding: 16px; text-align: center; color: #d32f2f;">Search error</div>';
                    searchResults.style.display = 'block';
                });
        }, 300);
    });

    // Close results when clicking outside
    document.addEventListener('click', function(event) {
        if (!searchContainer.contains(event.target)) {
            searchResults.style.display = 'none';
        }
    });

    // Allow Enter to trigger search
    searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter' && this.value.trim().length >= 2) {
            // Could implement full-page search results here
            e.preventDefault();
        }
    });
});
</script>
