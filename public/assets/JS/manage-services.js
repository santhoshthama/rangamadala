// Service Management - Request and manage services for drama

const urlParams = new URLSearchParams(window.location.search);
const dramaId = urlParams.get('drama_id') || 1;

// Get ROOT from the current URL
const ROOT = window.location.pathname.includes('/Rangamadala') ? '/Rangamadala' : '';

const ENDPOINTS = {
    cancelServiceRequest: ROOT + '/Production_manager/cancelServiceRequest'
};

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

// Close request details modal
function closeDetails() {
    const modal = document.getElementById('detailsModal');
    if (modal) {
        modal.style.display = 'none';
    }
    console.log('Details modal closed');
}

// Wrapper function to open details from button data attribute
function openRequestDetailsFromButton(button) {
    try {
        const jsonStr = button.getAttribute('data-request');
        if (!jsonStr) {
            console.error('No data-request attribute found');
            return;
        }
        const req = JSON.parse(jsonStr);
        openRequestDetails(null, req);
    } catch (e) {
        console.error('Error parsing request data:', e);
        showMessage('Error opening request details', 'error');
    }
}

// Open request details modal (mirroring service_requests.view.php design)
function openRequestDetails(event, req) {
    if (event) {
        event.stopPropagation();
    }
    console.log('Opening request details:', req);
    const modal = document.getElementById('detailsModal');
    
    if (!modal) {
        console.error('Details modal element not found');
        return;
    }
    
    // Build service-specific fields HTML based on service_type
    let serviceSpecificHTML = '';
    
    if (req.service_type === 'Theater Production') {
        serviceSpecificHTML = `
            <div style="margin-bottom: 20px;">
                <strong>Theater Production Details:</strong>
                <div style="background: #f9f9f9; padding: 12px; border-radius: 4px; margin-top: 8px;">
                    <p style="margin: 5px 0;"><strong>Venue Type:</strong> ${req.theater_venue_type || 'N/A'}</p>
                    <p style="margin: 5px 0;"><strong>Stage Type:</strong> ${[req.theater_stage_proscenium && 'Proscenium', req.theater_stage_black_box && 'Black Box', req.theater_stage_open_floor && 'Open Floor'].filter(Boolean).join(', ') || 'N/A'}</p>
                    <p style="margin: 5px 0;"><strong>Stage Size:</strong> ${req.theater_stage_size || 'N/A'}</p>
                    <p style="margin: 5px 0;"><strong>Number of Days:</strong> ${req.theater_num_days || 'N/A'}</p>
                    <p style="margin: 5px 0;"><strong>Time:</strong> ${req.theater_time || 'N/A'}</p>
                    <p style="margin: 5px 0;"><strong>Budget Range:</strong> ${req.theater_budget_range || 'N/A'}</p>
                </div>
            </div>
        `;
    } else if (req.service_type === 'Lighting Design') {
        serviceSpecificHTML = `
            <div style="margin-bottom: 20px;">
                <strong>Lighting Design Details:</strong>
                <div style="background: #f9f9f9; padding: 12px; border-radius: 4px; margin-top: 8px;">
                    <p style="margin: 5px 0;"><strong>Lighting Services:</strong> ${[req.lighting_stage_lighting && 'Stage Lighting', req.lighting_spotlights && 'Spotlights', req.lighting_custom_programming && 'Custom Programming', req.lighting_moving_heads && 'Moving Heads'].filter(Boolean).join(', ') || 'N/A'}</p>
                    <p style="margin: 5px 0;"><strong>Number of Lights:</strong> ${req.lighting_num_lights || 'N/A'}</p>
                    <p style="margin: 5px 0;"><strong>Effects:</strong> ${req.lighting_effects || 'N/A'}</p>
                    <p style="margin: 5px 0;"><strong>Technician Needed:</strong> ${req.lighting_technician_needed || 'N/A'}</p>
                    <p style="margin: 5px 0;"><strong>Budget Range:</strong> ${req.lighting_budget_range || 'N/A'}</p>
                    <p style="margin: 5px 0;"><strong>Additional Requirements:</strong> ${req.lighting_additional_requirements || 'N/A'}</p>
                </div>
            </div>
        `;
    } else if (req.service_type === 'Sound Systems') {
        serviceSpecificHTML = `
            <div style="margin-bottom: 20px;">
                <strong>Sound Systems Details:</strong>
                <div style="background: #f9f9f9; padding: 12px; border-radius: 4px; margin-top: 8px;">
                    <p style="margin: 5px 0;"><strong>Sound Services:</strong> ${[req.sound_pa_system && 'PA System', req.sound_microphones && 'Microphones', req.sound_sound_mixing && 'Sound Mixing', req.sound_background_music && 'Background Music', req.sound_special_effects && 'Special Effects'].filter(Boolean).join(', ') || 'N/A'}</p>
                    <p style="margin: 5px 0;"><strong>Number of Mics:</strong> ${req.sound_num_mics || 'N/A'}</p>
                    <p style="margin: 5px 0;"><strong>Stage Monitor:</strong> ${req.sound_stage_monitor || 'N/A'}</p>
                    <p style="margin: 5px 0;"><strong>Sound Engineer:</strong> ${req.sound_sound_engineer || 'N/A'}</p>
                    <p style="margin: 5px 0;"><strong>Budget Range:</strong> ${req.sound_budget_range || 'N/A'}</p>
                    <p style="margin: 5px 0;"><strong>Additional Services:</strong> ${req.sound_additional_services || 'N/A'}</p>
                </div>
            </div>
        `;
    } else if (req.service_type === 'Video Production') {
        serviceSpecificHTML = `
            <div style="margin-bottom: 20px;">
                <strong>Video Production Details:</strong>
                <div style="background: #f9f9f9; padding: 12px; border-radius: 4px; margin-top: 8px;">
                    <p style="margin: 5px 0;"><strong>Video Type:</strong> ${[req.video_full_event && 'Full Event', req.video_highlight_reel && 'Highlight Reel', req.video_short_promo && 'Short Promo'].filter(Boolean).join(', ') || 'N/A'}</p>
                    <p style="margin: 5px 0;"><strong>Number of Cameras:</strong> ${req.video_num_cameras || 'N/A'}</p>
                    <p style="margin: 5px 0;"><strong>Drone Coverage:</strong> ${req.video_drone_needed || 'N/A'}</p>
                    <p style="margin: 5px 0;"><strong>Gimbals/Steadicams:</strong> ${req.video_gimbals || 'N/A'}</p>
                    <p style="margin: 5px 0;"><strong>Editing:</strong> ${req.video_editing || 'N/A'}</p>
                    <p style="margin: 5px 0;"><strong>Budget Range:</strong> ${req.video_budget_range || 'N/A'}</p>
                    <p style="margin: 5px 0;"><strong>Additional Requirements:</strong> ${req.video_additional_requirements || 'N/A'}</p>
                </div>
            </div>
        `;
    } else if (req.service_type === 'Makeup & Hair') {
        serviceSpecificHTML = `
            <div style="margin-bottom: 20px;">
                <strong>Makeup & Hair Details:</strong>
                <div style="background: #f9f9f9; padding: 12px; border-radius: 4px; margin-top: 8px;">
                    <p style="margin: 5px 0;"><strong>Makeup Type:</strong> ${[req.makeup_stage && 'Stage Makeup', req.makeup_character && 'Character Makeup', req.makeup_traditional && 'Traditional/Cultural', req.makeup_sfx && 'Special Effects', req.makeup_hair_styling && 'Hair Styling'].filter(Boolean).join(', ') || 'N/A'}</p>
                    <p style="margin: 5px 0;"><strong>Number of Artists/Actors:</strong> ${req.makeup_num_artists || 'N/A'}</p>
                    <p style="margin: 5px 0;"><strong>Application Time Per Person:</strong> ${req.makeup_time_per_person || 'N/A'}</p>
                    <p style="margin: 5px 0;"><strong>Touch-up Service:</strong> ${req.makeup_touchup_service || 'N/A'}</p>
                    <p style="margin: 5px 0;"><strong>Number of Days:</strong> ${req.makeup_num_days || 'N/A'}</p>
                    <p style="margin: 5px 0;"><strong>Service Time:</strong> ${req.makeup_service_time || 'N/A'}</p>
                    <p style="margin: 5px 0;"><strong>Budget Range:</strong> ${req.makeup_budget_range || 'N/A'}</p>
                    <p style="margin: 5px 0;"><strong>Additional Services:</strong> ${req.makeup_additional_services || 'N/A'}</p>
                </div>
            </div>
        `;
    } else if (req.service_type === 'Set Design') {
        serviceSpecificHTML = `
            <div style="margin-bottom: 20px;">
                <strong>Set Design Details:</strong>
                <div style="background: #f9f9f9; padding: 12px; border-radius: 4px; margin-top: 8px;">
                    <p style="margin: 5px 0;"><strong>Service Type:</strong> ${[req.set_set_design && 'Design', req.set_set_construction && 'Construction', req.set_set_rental && 'Rental'].filter(Boolean).join(', ') || 'N/A'}</p>
                    <p style="margin: 5px 0;"><strong>Materials:</strong> ${req.set_materials || 'N/A'}</p>
                    <p style="margin: 5px 0;"><strong>Dimensions:</strong> ${req.set_dimensions || 'N/A'}</p>
                    <p style="margin: 5px 0;"><strong>Budget Range:</strong> ${req.set_budget_range || 'N/A'}</p>
                    <p style="margin: 5px 0;"><strong>Deadline:</strong> ${req.set_deadline || 'N/A'}</p>
                </div>
            </div>
        `;
    } else if (req.service_type === 'Costume Design') {
        serviceSpecificHTML = `
            <div style="margin-bottom: 20px;">
                <strong>Costume Design Details:</strong>
                <div style="background: #f9f9f9; padding: 12px; border-radius: 4px; margin-top: 8px;">
                    <p style="margin: 5px 0;"><strong>Service Type:</strong> ${[req.costume_costume_design && 'Design', req.costume_costume_creation && 'Creation', req.costume_costume_rental && 'Rental'].filter(Boolean).join(', ') || 'N/A'}</p>
                    <p style="margin: 5px 0;"><strong>Number of Characters:</strong> ${req.costume_num_characters || 'N/A'}</p>
                    <p style="margin: 5px 0;"><strong>Number of Costumes:</strong> ${req.costume_num_costumes || 'N/A'}</p>
                    <p style="margin: 5px 0;"><strong>Measurements Required:</strong> ${req.costume_measurements_required || 'N/A'}</p>
                    <p style="margin: 5px 0;"><strong>Fitting Dates:</strong> ${req.costume_fitting_dates || 'N/A'}</p>
                    <p style="margin: 5px 0;"><strong>Budget Range:</strong> ${req.costume_budget_range || 'N/A'}</p>
                    <p style="margin: 5px 0;"><strong>Deadline:</strong> ${req.costume_deadline || 'N/A'}</p>
                </div>
            </div>
        `;
    }
    
    document.getElementById('detailsContent').innerHTML = `
        <div style="padding: 20px; background: #fff; border-radius: 8px; max-height: 70vh; overflow-y: auto;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h2 style="margin: 0; color: #333;">${req.service_type || 'Request'} - ${req.drama_name || 'N/A'}</h2>
                <button onclick="closeDetails()" style="background: none; border: none; font-size: 24px; cursor: pointer; color: #666;">&times;</button>
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div>
                    <strong>Status:</strong> <span style="padding: 5px 10px; border-radius: 4px; background: #f0f0f0; text-transform: capitalize;">${req.status}</span>
                </div>
                <div>
                    <strong>Payment Status:</strong> <span style="padding: 5px 10px; border-radius: 4px; background: #f0f0f0; text-transform: capitalize;">${req.payment_status || 'unpaid'}</span>
                </div>
            </div>

            <div style="margin-bottom: 20px;">
                <strong>Service Provider Information:</strong>
                <div style="background: #f9f9f9; padding: 12px; border-radius: 4px; margin-top: 8px;">
                    <p style="margin: 5px 0;"><strong>Name:</strong> ${req.provider_name || 'N/A'}</p>
                    <p style="margin: 5px 0;"><strong>Email:</strong> <a href="mailto:${req.provider_email || ''}" style="color: #007bff; text-decoration: none;">${req.provider_email || 'N/A'}</a></p>
                    <p style="margin: 5px 0;"><strong>Phone:</strong> ${req.provider_phone || 'N/A'}</p>
                </div>
            </div>

            <div style="margin-bottom: 20px;">
                <strong>Schedule:</strong>
                <div style="background: #f9f9f9; padding: 12px; border-radius: 4px; margin-top: 8px;">
                    ${req.service_date ? `<p style="margin: 5px 0;"><strong>Service Date:</strong> ${req.service_date}</p>` : ''}
                    <p style="margin: 5px 0;"><strong>Start Date:</strong> ${req.start_date}</p>
                    <p style="margin: 5px 0;"><strong>End Date:</strong> ${req.end_date}</p>
                </div>
            </div>

            ${serviceSpecificHTML}

            <div style="margin-bottom: 20px;">
                <strong>Description:</strong>
                <div style="background: #f9f9f9; padding: 12px; border-radius: 4px; margin-top: 8px; word-wrap: break-word;">
                    ${req.description || 'No description provided'}
                </div>
            </div>

            <div style="margin-bottom: 20px;">
                <strong>Notes from Requester:</strong>
                <div style="background: #f9f9f9; padding: 12px; border-radius: 4px; margin-top: 8px; word-wrap: break-word;">
                    ${req.notes || 'No notes provided'}
                </div>
            </div>

            ${req.budget ? `
            <div style="margin-bottom: 20px;">
                <strong>Budget:</strong>
                <div style="background: #f9f9f9; padding: 12px; border-radius: 4px; margin-top: 8px;">
                    <p style="margin: 5px 0;">Rs ${parseFloat(req.budget).toLocaleString()}</p>
                </div>
            </div>
            ` : ''}

            <div style="border-top: 1px solid #ddd; padding-top: 15px; margin-top: 20px; font-size: 12px; color: #666;">
                <p style="margin: 5px 0;"><strong>Created:</strong> ${new Date(req.created_at).toLocaleString()}</p>
                ${req.accepted_at ? `<p style="margin: 5px 0;"><strong>Accepted:</strong> ${new Date(req.accepted_at).toLocaleString()}</p>` : ''}
                ${req.completed_at ? `<p style="margin: 5px 0;"><strong>Completed:</strong> ${new Date(req.completed_at).toLocaleString()}</p>` : ''}
            </div>
        </div>
    `;
    modal.style.display = 'flex';
}

// Cancel service request with confirmation
async function cancelServiceRequest(button) {
    const id = button.getAttribute('data-id');
    if (!confirm('Are you sure you want to cancel this service request?')) {
        return;
    }
    
    try {
        const response = await fetch(ENDPOINTS.cancelServiceRequest, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({ id, status: 'cancelled' }),
        });
        const json = await response.json();
        if (json.success) {
            showMessage('Service request cancelled successfully', 'success');
            setTimeout(() => location.reload(), 1500);
        } else {
            showMessage(json.error || 'Failed to cancel request', 'error');
        }
    } catch (e) {
        console.error('Error:', e);
        showMessage('Network error while cancelling request', 'error');
    }
}

// Show message notification
function showMessage(text, type) {
    const message = document.createElement('div');
    message.className = `message message-${type}`;
    message.textContent = text;
    
    message.style.position = 'fixed';
    message.style.top = '20px';
    message.style.right = '20px';
    message.style.padding = '12px 20px';
    message.style.borderRadius = '6px';
    message.style.zIndex = '1000';
    message.style.fontWeight = '500';
    message.style.transition = 'all 0.3s ease';
    
    if (type === 'success') {
        message.style.background = '#28a745';
        message.style.color = 'white';
    } else if (type === 'error') {
        message.style.background = '#dc3545';
        message.style.color = 'white';
    } else {
        message.style.background = '#17a2b8';
        message.style.color = 'white';
    }
    
    document.body.appendChild(message);
    
    setTimeout(() => {
        message.style.opacity = '0';
        message.style.transform = 'translateY(-20px)';
        setTimeout(() => document.body.removeChild(message), 300);
    }, 3000);
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('detailsModal');
    if (event.target === modal) {
        closeDetails();
    }
}

// Close service details modal
function closeServiceDetailsModal() {
    closeDetails();
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    console.log('Manage Services page loaded');
    
    // Verify modal exists
    const modal = document.getElementById('detailsModal');
    if (!modal) {
        console.warn('Details modal element not found in DOM');
    } else {
        // Click outside modal to close
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeDetails();
            }
        });
        console.log('Modal close handler attached');
    }
});

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

// View service details (deprecated - use openRequestDetails)
function viewServiceDetails(serviceId) {
    console.log('Viewing service details:', serviceId);
}

// Cancel service (deprecated - use cancelServiceRequest)
function cancelService(serviceId) {
    console.log('Canceling service:', serviceId);
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
