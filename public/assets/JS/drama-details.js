// Drama Details - Edit functionality

const urlParams = new URLSearchParams(window.location.search);
const dramaId = urlParams.get('drama_id') || 1;

console.log('Drama Details page initialized for Drama ID:', dramaId);

// Load drama details from backend
function loadDramaDetails() {
    console.log('Loading drama details for drama_id:', dramaId);
    // TODO: Fetch drama data from backend and populate form
}

// Enable edit mode
function enableEdit() {
    const formInputs = document.querySelectorAll('input[type="text"], textarea, select');
    formInputs.forEach(input => {
        input.removeAttribute('readonly');
        input.removeAttribute('disabled');
    });
    
    document.getElementById('editBtn').style.display = 'none';
    document.getElementById('saveBtn').style.display = 'inline-block';
    document.getElementById('cancelBtn').style.display = 'inline-block';
    
    console.log('Edit mode enabled');
}

// Cancel editing
function cancelEdit() {
    location.reload(); // Reload to discard changes
    console.log('Edit cancelled');
}

// Save drama details
function saveDramaDetails() {
    console.log('Saving drama details...');
    
    // Get form data
    const formData = {
        drama_id: dramaId,
        title: document.getElementById('dramaTitleInput').value,
        description: document.getElementById('descriptionInput').value,
        genre: document.getElementById('genreInput').value,
        language: document.getElementById('languageInput').value,
        certificate: document.getElementById('certificateInput').value
    };
    
    console.log('Form data to save:', formData);
    // TODO: Send to backend and save
    alert('Drama details updated successfully!');
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    loadDramaDetails();
    console.log('Drama Details page loaded');
});
