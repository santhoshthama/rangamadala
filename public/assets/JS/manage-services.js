// Service Management - Request and manage services for drama

const urlParams = new URLSearchParams(window.location.search);
const dramaId = urlParams.get('drama_id') || 1;

console.log('Service Management initialized for Drama ID:', dramaId);

// Track current payment data
let pendingPaymentData = null;

// Service provider options for different service types
const serviceProviders = {
    sound: [
        { id: 1, name: 'Sri Lanka Sound Services', rating: 4.8, cost: 'LKR 120,000' },
        { id: 2, name: 'Colombo Audio Solutions', rating: 4.6, cost: 'LKR 100,000' },
        { id: 3, name: 'Professional Audio Colombo', rating: 4.9, cost: 'LKR 150,000' }
    ],
    lighting: [
        { id: 4, name: 'Colombo Lighting Studio', rating: 4.7, cost: 'LKR 150,000' },
        { id: 5, name: 'Professional Lighting Services', rating: 4.8, cost: 'LKR 180,000' },
        { id: 6, name: 'Stage Effects Colombo', rating: 4.5, cost: 'LKR 120,000' }
    ],
    makeup: [
        { id: 7, name: 'Elite Makeup Artistry', rating: 4.9, cost: 'LKR 200,000' },
        { id: 8, name: 'Professional Makeup Studio', rating: 4.7, cost: 'LKR 180,000' },
        { id: 9, name: 'Colombo Beauty Services', rating: 4.6, cost: 'LKR 160,000' }
    ],
    transport: [
        { id: 10, name: 'Colombo Transport Services', rating: 4.5, cost: 'LKR 50,000' },
        { id: 11, name: 'Professional Transport Co', rating: 4.6, cost: 'LKR 60,000' }
    ],
    catering: [
        { id: 12, name: 'Colombo Catering Services', rating: 4.8, cost: 'LKR 80,000' },
        { id: 13, name: 'Elite Events Catering', rating: 4.7, cost: 'LKR 100,000' }
    ]
};

// Open request service modal
function openRequestServiceModal() {
    const modal = document.getElementById('requestServiceModal');
    modal.style.display = 'block';
    clearServiceForm();
    console.log('Request service modal opened');
}

// Close request service modal
function closeRequestServiceModal() {
    const modal = document.getElementById('requestServiceModal');
    modal.style.display = 'none';
    clearServiceForm();
    console.log('Request service modal closed');
}

// Close card payment modal
function closeCardPaymentModal() {
    const modal = document.getElementById('cardPaymentModal');
    modal.style.display = 'none';
    clearCardPaymentForm();
    console.log('Card payment modal closed');
}

// Close service details modal
function closeServiceDetailsModal() {
    const modal = document.getElementById('serviceDetailsModal');
    modal.style.display = 'none';
    console.log('Service details modal closed');
}

// Clear service form
function clearServiceForm() {
    document.getElementById('serviceType').value = '';
    document.getElementById('serviceProvider').value = '';
    document.getElementById('serviceDate').value = '';
    document.getElementById('serviceDescription').value = '';
    document.getElementById('estimatedBudget').value = '';
    document.getElementById('specialRequirements').value = '';
}

// Clear card payment form
function clearCardPaymentForm() {
    document.getElementById('cardHolderName').value = '';
    document.getElementById('cardNumber').value = '';
    document.getElementById('expiryDate').value = '';
    document.getElementById('cvv').value = '';
}

// Update service providers based on selected type
function updateServiceProviders() {
    const serviceType = document.getElementById('serviceType').value;
    const providerSelect = document.getElementById('serviceProvider');
    
    // Clear existing options
    providerSelect.innerHTML = '<option value="">Select Service Provider</option>';
    
    if (serviceType && serviceProviders[serviceType]) {
        serviceProviders[serviceType].forEach(provider => {
            const option = document.createElement('option');
            option.value = provider.id;
            option.textContent = `${provider.name} (Rating: ${provider.rating}⭐) - ${provider.cost}`;
            providerSelect.appendChild(option);
        });
        console.log('Updated service providers for type:', serviceType);
    }
}

// Submit service request (Step 1 - Request Only)
function submitServiceRequest() {
    const serviceType = document.getElementById('serviceType').value;
    const serviceProviderId = document.getElementById('serviceProvider').value;
    const serviceDate = document.getElementById('serviceDate').value;
    const serviceDescription = document.getElementById('serviceDescription').value;
    const estimatedBudget = document.getElementById('estimatedBudget').value;
    const specialRequirements = document.getElementById('specialRequirements').value;

    // Validate inputs
    if (!serviceType || !serviceProviderId || !serviceDate || !estimatedBudget) {
        alert('Please fill in all required fields');
        return;
    }

    const provider = serviceProviders[serviceType].find(p => p.id == serviceProviderId);

    console.log('Submitting service request:', {
        serviceType,
        serviceProviderId,
        providerName: provider.name,
        serviceDate,
        serviceDescription,
        estimatedBudget,
        specialRequirements,
        drama_id: dramaId
    });

    // TODO: Send to backend API to create service request
    alert(`Service request submitted to ${provider.name}!\n\nWaiting for provider to accept...`);
    closeRequestServiceModal();
    loadServices();
}

// Open card payment modal (Step 2 - After provider accepts)
function openCardPaymentForService(serviceId, providerName, serviceType, amount) {
    console.log('Opening card payment for service:', serviceId);
    
    // Store payment data
    pendingPaymentData = {
        serviceId: serviceId,
        providerName: providerName,
        serviceType: serviceType,
        amount: parseInt(amount),
        drama_id: dramaId
    };

    // Populate payment modal
    document.getElementById('paymentProviderName').textContent = providerName;
    document.getElementById('paymentServiceType').textContent = serviceType;
    document.getElementById('paymentAmount').textContent = `LKR ${amount.toLocaleString()}`;

    // Show modal
    const modal = document.getElementById('cardPaymentModal');
    modal.style.display = 'block';
    clearCardPaymentForm();
}

// Process card payment
function processCardPayment() {
    if (!pendingPaymentData) {
        alert('No payment data available');
        return;
    }

    const cardHolderName = document.getElementById('cardHolderName').value;
    const cardNumber = document.getElementById('cardNumber').value;
    const expiryDate = document.getElementById('expiryDate').value;
    const cvv = document.getElementById('cvv').value;

    // Validate card details
    if (!cardHolderName || !cardNumber || !expiryDate || !cvv) {
        alert('Please fill in all card details');
        return;
    }

    if (cardNumber.replace(/\s/g, '').length < 13) {
        alert('Please enter a valid card number');
        return;
    }

    if (cvv.length < 3) {
        alert('Please enter a valid CVV');
        return;
    }

    console.log('Processing card payment:', {
        serviceId: pendingPaymentData.serviceId,
        amount: pendingPaymentData.amount,
        cardHolderName: cardHolderName,
        cardLast4: cardNumber.slice(-4),
        paymentMethod: 'Card'
    });

    // TODO: Send to backend API to:
    // 1. Process card payment
    // 2. Create budget item with service cost
    // 3. Confirm service booking
    
    const confirmMessage = `
Payment Successful! ✓

Provider: ${pendingPaymentData.providerName}
Amount Paid: LKR ${pendingPaymentData.amount.toLocaleString()}
Payment Method: Credit/Debit Card (****${cardNumber.slice(-4)})

This amount has been automatically added to your drama budget.
Service status: CONFIRMED
    `;
    
    alert(confirmMessage);
    
    // Close modal
    closeCardPaymentModal();
    
    // Reset pending payment
    pendingPaymentData = null;
    
    // Reload services list
    loadServices();
}

// Format card number input
document.addEventListener('DOMContentLoaded', function() {
    const cardNumberInput = document.getElementById('cardNumber');
    if (cardNumberInput) {
        cardNumberInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\s/g, '');
            let formattedValue = value.replace(/(\d{4})/g, '$1 ').trim();
            e.target.value = formattedValue;
        });
    }

    const expiryInput = document.getElementById('expiryDate');
    if (expiryInput) {
        expiryInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length >= 2) {
                value = value.slice(0, 2) + '/' + value.slice(2, 4);
            }
            e.target.value = value;
        });
    }
});

// Filter services by status
function filterServices(status) {
    console.log('Filtering services by status:', status);
    // Update UI to show/hide services based on filter
    // TODO: Implement filtering logic
    
    // Update active tab button
    const buttons = document.querySelectorAll('.tab-button');
    buttons.forEach(btn => btn.classList.remove('active'));
    event.target.classList.add('active');
}

// View service details
function viewServiceDetails(serviceId) {
    console.log('Viewing service details:', serviceId);
    
    // TODO: Fetch service details from backend
    const modal = document.getElementById('serviceDetailsModal');
    const content = document.getElementById('serviceDetailsContent');
    
    // Placeholder content - replace with actual data
    content.innerHTML = `
        <div class="service-info-card">
            <div class="service-info-item">
                <span class="service-info-label">Service ID</span>
                <span class="service-info-value">${serviceId}</span>
            </div>
            <div class="service-info-item">
                <span class="service-info-label">Service Type</span>
                <span class="service-info-value">Sound System Setup</span>
            </div>
            <div class="service-info-item">
                <span class="service-info-label">Service Provider</span>
                <span class="service-info-value">Sri Lanka Sound Services</span>
            </div>
            <div class="service-info-item">
                <span class="service-info-label">Status</span>
                <span class="service-info-value">Confirmed</span>
            </div>
            <div class="service-info-item">
                <span class="service-info-label">Service Cost</span>
                <span class="service-info-value">LKR 120,000</span>
            </div>
            <div class="service-info-item">
                <span class="service-info-label">Payment Method</span>
                <span class="service-info-value">Online Card Payment</span>
            </div>
            <div class="service-info-item">
                <span class="service-info-label">Service Date</span>
                <span class="service-info-value">January 15, 2025</span>
            </div>
            <div class="service-info-item">
                <span class="service-info-label">Description</span>
                <span class="service-info-value">Professional sound system setup with technician support</span>
            </div>
        </div>
    `;
    
    modal.style.display = 'block';
}

// Pay for confirmed service
function payForService(serviceId, providerName, serviceType, amount) {
    openCardPaymentForService(serviceId, providerName, serviceType, amount);
}

// Cancel service request
function cancelService(serviceId) {
    if (confirm('Are you sure you want to cancel this service request?')) {
        console.log('Canceling service:', serviceId);
        // TODO: Send cancel request to backend API
        alert('Service request has been canceled');
        loadServices();
    }
}

// Load services from backend
function loadServices() {
    console.log('Loading services for drama_id:', dramaId);
    // TODO: Fetch services from backend API
    // Update service list with fetched data
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    console.log('Service Management page loaded');
    loadServices();
    
    // Close modals when clicking outside them
    const requestModal = document.getElementById('requestServiceModal');
    const paymentModal = document.getElementById('cardPaymentModal');
    const detailsModal = document.getElementById('serviceDetailsModal');
    
    window.onclick = function(event) {
        if (event.target == requestModal) {
            closeRequestServiceModal();
        }
        if (event.target == paymentModal) {
            closeCardPaymentModal();
        }
        if (event.target == detailsModal) {
            closeServiceDetailsModal();
        }
    }
});
