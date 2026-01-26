// Assign Production Manager - Manager assignment functionality

const urlParams = new URLSearchParams(window.location.search);
const dramaId = urlParams.get('drama_id') || 1;

console.log('Assign Managers page initialized for Drama ID:', dramaId);

// Load current manager info
function loadManagerData() {
    console.log('Loading manager data for drama_id:', dramaId);
    // TODO: Fetch current manager data from backend
}

// Open assign manager modal
function openAssignManagerModal() {
    const modal = document.getElementById('assignManagerModal');
    if (modal) {
        modal.style.display = 'block';
    }
    console.log('Assign manager modal opened');
}

// Close assign manager modal
function closeAssignManagerModal() {
    const modal = document.getElementById('assignManagerModal');
    if (modal) {
        modal.style.display = 'none';
    }
    // Clear search results
    document.getElementById('artistSearchResults').style.display = 'none';
    document.getElementById('selectedArtist').value = '';
    document.getElementById('selectedArtistId').value = '';
}

// Search artists
function searchArtists() {
    const searchTerm = document.getElementById('searchArtist').value;
    
    if (searchTerm.length < 2) {
        document.getElementById('artistSearchResults').style.display = 'none';
        return;
    }
    
    console.log('Searching for artists:', searchTerm);
    // TODO: Fetch search results from backend
    
    // Mock search results
    const mockResults = [
        { id: 1, name: 'Tharaka Rathnayake', artistId: 'ART-1234', experience: '12 years' },
        { id: 2, name: 'Malini Fernando', artistId: 'ART-2156', experience: '6 years' },
        { id: 3, name: 'Rohan Perera', artistId: 'ART-3087', experience: '4 years' }
    ];
    
    const resultsDiv = document.getElementById('artistSearchResults');
    resultsDiv.innerHTML = '';
    
    mockResults.forEach(artist => {
        const div = document.createElement('div');
        div.style.padding = '10px';
        div.style.borderBottom = '1px solid var(--border)';
        div.style.cursor = 'pointer';
        div.onclick = () => selectArtist(artist.id, artist.name, artist.artistId);
        div.innerHTML = `<strong>${artist.name}</strong> (${artist.artistId}) - ${artist.experience}`;
        resultsDiv.appendChild(div);
    });
    
    resultsDiv.style.display = 'block';
}

// Select artist from search
function selectArtist(artistId, artistName, artistCode) {
    document.getElementById('selectedArtist').value = artistName + ' (' + artistCode + ')';
    document.getElementById('selectedArtistId').value = artistId;
    document.getElementById('artistSearchResults').style.display = 'none';
    console.log('Selected artist:', {id: artistId, name: artistName});
}

// Submit assign manager form
function submitAssignManager() {
    const artistId = document.getElementById('selectedArtistId').value;
    const notes = document.getElementById('managerNotes').value;
    
    if (!artistId) {
        alert('Please select an artist');
        return;
    }
    
    console.log('Assigning manager...', {artist_id: artistId, drama_id: dramaId, notes: notes});
    // TODO: Send to backend
    alert('Manager assigned successfully!');
    closeAssignManagerModal();
    loadManagerData();
}

// View manager details
function viewManagerDetails() {
    const modal = document.getElementById('managerDetailsModal');
    if (modal) {
        modal.style.display = 'block';
    }
    console.log('Manager details modal opened');
    // TODO: Fetch and display manager details
}

// Close manager details modal
function closeManagerDetailsModal() {
    const modal = document.getElementById('managerDetailsModal');
    if (modal) {
        modal.style.display = 'none';
    }
}

// Remove manager
function removeManager() {
    if (confirm('Are you sure you want to remove the Production Manager? This will restrict access to services and budget management.')) {
        console.log('Removing manager for drama_id:', dramaId);
        // TODO: Send removal request to backend
        alert('Production Manager removed!');
        loadManagerData();
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    loadManagerData();
    console.log('Assign Managers page loaded');
});

// Close modal when clicking outside
window.onclick = function(event) {
    const assignModal = document.getElementById('assignManagerModal');
    const detailsModal = document.getElementById('managerDetailsModal');
    
    if (assignModal && event.target === assignModal) {
        closeAssignManagerModal();
    }
    if (detailsModal && event.target === detailsModal) {
        closeManagerDetailsModal();
    }
};
