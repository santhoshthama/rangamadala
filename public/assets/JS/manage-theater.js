// Theater Booking Management - Book theaters for drama performances

const urlParams = new URLSearchParams(window.location.search);
const dramaId = urlParams.get('drama_id') || 1;

console.log('Theater Booking Management initialized for Drama ID:', dramaId);

// Available theaters database
const theaters = {
    elphinstone: {
        name: 'Elphinstone Theatre',
        location: 'Colombo, Sri Lanka',
        capacity: 500,
        costPerHour: 'LKR 50,000',
        facilities: 'Full A/C, Sound System, Professional Lighting, Backstage Facilities'
    },
    colombo_aud: {
        name: 'Colombo Auditorium',
        location: 'Colombo City Center',
        capacity: 1000,
        costPerHour: 'LKR 60,000',
        facilities: 'Premium amenities, State-of-the-art technology, VIP Lounge'
    },
    galle_face: {
        name: 'Galle Face Hotel Theatre',
        location: 'Colombo, Beachfront',
        capacity: 300,
        costPerHour: 'LKR 80,000',
        facilities: 'Heritage venue, Unique ambiance, Professional Setup'
    },
    kandy_art: {
        name: 'Kandy Arts Centre',
        location: 'Kandy',
        capacity: 400,
        costPerHour: 'LKR 40,000',
        facilities: 'Modern facility, Good acoustics, Comfortable seating'
    },
    peradeniya: {
        name: 'Peradeniya Open Air Theatre',
        location: 'Peradeniya, Kandy',
        capacity: 2000,
        costPerHour: 'LKR 30,000',
        facilities: 'Large open air venue, Natural setting, Weather protection'
    }
};

// Open book theater modal
function openBookTheaterModal() {
    const modal = document.getElementById('bookTheaterModal');
    modal.style.display = 'block';
    clearTheaterForm();
    console.log('Book theater modal opened');
}

// Close book theater modal
function closeBookTheaterModal() {
    const modal = document.getElementById('bookTheaterModal');
    modal.style.display = 'none';
    clearTheaterForm();
    console.log('Book theater modal closed');
}

// Clear theater booking form
function clearTheaterForm() {
    document.getElementById('theaterName').value = '';
    document.getElementById('bookingDate').value = '';
    document.getElementById('bookingTime').value = '';
    document.getElementById('endTime').value = '';
    document.getElementById('estimatedAttendance').value = '';
    document.getElementById('specialRequests').value = '';
    document.getElementById('theaterCapacity').textContent = '-';
    document.getElementById('theaterCost').textContent = '-';
    document.getElementById('estimatedTotal').textContent = '-';
    document.getElementById('theaterFacilities').textContent = '-';
}

// Update theater details when selected
function updateTheaterDetails() {
    const theaterKey = document.getElementById('theaterName').value;
    
    if (theaterKey && theaters[theaterKey]) {
        const theater = theaters[theaterKey];
        document.getElementById('theaterCapacity').textContent = `${theater.capacity} seats`;
        document.getElementById('theaterCost').textContent = theater.costPerHour;
        document.getElementById('theaterFacilities').textContent = theater.facilities;
        
        // Calculate estimated total
        calculateEstimatedCost();
        console.log('Theater details updated:', theater.name);
    }
}

// Calculate estimated booking cost
function calculateEstimatedCost() {
    const theaterKey = document.getElementById('theaterName').value;
    const startTime = document.getElementById('bookingTime').value;
    const endTime = document.getElementById('endTime').value;
    
    if (!theaterKey || !startTime || !endTime) {
        document.getElementById('estimatedTotal').textContent = '-';
        return;
    }
    
    // Parse times and calculate duration
    const start = new Date(`2025-01-01 ${startTime}`);
    const end = new Date(`2025-01-01 ${endTime}`);
    
    if (end <= start) {
        alert('End time must be after start time');
        return;
    }
    
    const durationHours = (end - start) / (1000 * 60 * 60);
    const theater = theaters[theaterKey];
    
    // Extract cost per hour amount
    const costPerHour = parseInt(theater.costPerHour.replace(/[^0-9]/g, ''));
    const totalCost = costPerHour * durationHours;
    
    document.getElementById('estimatedTotal').textContent = `LKR ${totalCost.toLocaleString()}`;
    console.log('Estimated cost calculated:', totalCost);
}

// Add event listeners for cost calculation
document.addEventListener('DOMContentLoaded', function() {
    const bookingTime = document.getElementById('bookingTime');
    const endTime = document.getElementById('endTime');
    
    if (bookingTime) {
        bookingTime.addEventListener('change', calculateEstimatedCost);
    }
    if (endTime) {
        endTime.addEventListener('change', calculateEstimatedCost);
    }
});

// Submit theater booking request
function submitTheaterBooking() {
    const theaterKey = document.getElementById('theaterName').value;
    const bookingDate = document.getElementById('bookingDate').value;
    const bookingTime = document.getElementById('bookingTime').value;
    const endTime = document.getElementById('endTime').value;
    const estimatedAttendance = document.getElementById('estimatedAttendance').value;
    const specialRequests = document.getElementById('specialRequests').value;

    // Validate inputs
    if (!theaterKey || !bookingDate || !bookingTime || !endTime || !estimatedAttendance) {
        alert('Please fill in all required fields');
        return;
    }

    const theater = theaters[theaterKey];
    
    // Validate attendance doesn't exceed capacity
    if (parseInt(estimatedAttendance) > theater.capacity) {
        alert(`Estimated attendance cannot exceed theater capacity of ${theater.capacity} seats`);
        return;
    }

    console.log('Submitting theater booking:', {
        theaterKey,
        theaterName: theater.name,
        bookingDate,
        bookingTime,
        endTime,
        estimatedAttendance,
        specialRequests,
        drama_id: dramaId
    });

    // TODO: Send to backend API
    alert(`Theater booking request for ${theater.name} has been submitted successfully!`);
    closeBookTheaterModal();
    loadTheaterBookings();
}

// View booking details
function viewBookingDetails(bookingId) {
    console.log('Viewing booking details:', bookingId);
    // TODO: Fetch and display booking details in modal
    alert('Booking ID: ' + bookingId + '\nFull details view coming soon');
}

// Edit booking
function editBooking(bookingId) {
    console.log('Editing booking:', bookingId);
    // TODO: Load booking data and populate form
    openBookTheaterModal();
}

// Cancel booking
function cancelBooking(bookingId) {
    if (confirm('Are you sure you want to cancel this theater booking?')) {
        console.log('Canceling booking:', bookingId);
        // TODO: Send cancel request to backend API
        alert('Theater booking has been canceled');
        loadTheaterBookings();
    }
}

// Load theater bookings from backend
function loadTheaterBookings() {
    console.log('Loading theater bookings for drama_id:', dramaId);
    // TODO: Fetch theater bookings from backend API
    // Update bookings list with fetched data
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    console.log('Theater Booking Management page loaded');
    loadTheaterBookings();
    
    // Close modal when clicking outside it
    const modal = document.getElementById('bookTheaterModal');
    window.onclick = function(event) {
        if (event.target == modal) {
            closeBookTheaterModal();
        }
    }
});
