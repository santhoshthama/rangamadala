// Search Artists - Search and request functionality

const urlParams = new URLSearchParams(window.location.search);
const dramaId = urlParams.get('drama_id') || 1;

console.log('Search Artists page initialized for Drama ID:', dramaId);

// Search artists
function searchArtists() {
    const searchTerm = document.getElementById('searchInput').value;
    const experience = document.getElementById('experienceFilter').value;
    const specialization = document.getElementById('specializationFilter').value;
    
    console.log('Searching artists with filters:', {
        search: searchTerm,
        experience: experience,
        specialization: specialization,
        drama_id: dramaId
    });
    
    // TODO: Fetch search results from backend
    // For now, show mock results
    displaySearchResults();
}

// Display search results
function displaySearchResults() {
    console.log('Displaying search results');
    // TODO: Render search results
}

// Apply filters
function applyFilters() {
    const experience = document.getElementById('experienceFilter').value;
    const specialization = document.getElementById('specializationFilter').value;
    const availability = document.getElementById('availabilityFilter').value;
    const rating = document.getElementById('ratingFilter').value;
    
    console.log('Applying filters:', {
        experience: experience,
        specialization: specialization,
        availability: availability,
        rating: rating
    });
    
    searchArtists();
}

// Clear all filters
function clearFilters() {
    document.getElementById('searchInput').value = '';
    document.getElementById('experienceFilter').value = '';
    document.getElementById('specializationFilter').value = '';
    document.getElementById('availabilityFilter').value = '';
    document.getElementById('ratingFilter').value = '';
    
    console.log('Filters cleared');
}

// View artist profile
function viewArtistProfile(artistId) {
    console.log('View artist profile:', artistId);
    // TODO: Open modal or navigate to artist profile
    alert('Viewing artist profile: ' + artistId);
}

// Open role request modal
function openRoleRequestModal(artistId, artistName) {
    const modal = document.getElementById('roleRequestModal');
    if (modal) {
        modal.style.display = 'block';
        document.getElementById('selectedArtistName').textContent = artistName;
        document.getElementById('selectedArtistId').value = artistId;
    }
    console.log('Role request modal opened for artist:', artistId);
}

// Close role request modal
function closeRoleRequestModal() {
    const modal = document.getElementById('roleRequestModal');
    if (modal) {
        modal.style.display = 'none';
    }
    // Clear form
    document.getElementById('selectedArtistId').value = '';
    document.getElementById('roleSelect').value = '';
    document.getElementById('requestMessage').value = '';
}

// Submit role request
function submitRoleRequest() {
    const artistId = document.getElementById('selectedArtistId').value;
    const roleId = document.getElementById('roleSelect').value;
    const message = document.getElementById('requestMessage').value;
    
    if (!roleId) {
        alert('Please select a role');
        return;
    }
    
    console.log('Submitting role request...', {
        artist_id: artistId,
        role_id: roleId,
        drama_id: dramaId,
        message: message
    });
    
    // TODO: Send role request to backend
    alert('Role request sent successfully!');
    closeRoleRequestModal();
}

// Quick request action (from artist card)
function quickRequestRole(artistId, artistName) {
    console.log('Quick request role for artist:', artistId);
    openRoleRequestModal(artistId, artistName);
}

// Load available roles
function loadAvailableRoles() {
    console.log('Loading available roles for drama_id:', dramaId);
    // TODO: Fetch available roles from backend and populate dropdown
    
    // Mock roles
    const roles = [
        { id: 1, name: 'King Sinhabahu', salary: 'LKR 80,000' },
        { id: 2, name: 'Princess Suppadevi', salary: 'LKR 70,000' },
        { id: 3, name: 'Dancer Troupe Leader', salary: 'LKR 50,000' }
    ];
    
    const selectElement = document.getElementById('roleSelect');
    if (selectElement) {
        roles.forEach(role => {
            const option = document.createElement('option');
            option.value = role.id;
            option.text = role.name + ' (' + role.salary + ')';
            selectElement.appendChild(option);
        });
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    loadAvailableRoles();
    console.log('Search Artists page loaded');
});

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('roleRequestModal');
    if (modal && event.target === modal) {
        closeRoleRequestModal();
    }
};
