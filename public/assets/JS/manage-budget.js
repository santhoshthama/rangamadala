// Budget Management - Manage drama budget items

const urlParams = new URLSearchParams(window.location.search);
const dramaId = urlParams.get('drama_id') || 1;
const apiBase = window.PM_BUDGET_API_BASE || `${window.location.origin}/Rangamadala/public/production_manager`;
const serviceRequests = Array.isArray(window.PM_SERVICE_REQUESTS) ? window.PM_SERVICE_REQUESTS : [];
const serviceRequestMap = Object.fromEntries(
    serviceRequests
        .map((request) => [String(request.id), request])
        .filter(([id]) => id && id !== 'undefined' && id !== 'null')
);
let editingBudgetId = null;

console.log('Budget Management initialized for Drama ID:', dramaId);

// Open add budget modal
function openAddBudgetModal() {
    const modal = document.getElementById('budgetModal');
    modal.style.display = 'block';
    document.getElementById('budgetModal').querySelector('h2').innerHTML = '<i class="fas fa-plus"></i> Add Budget Item';
    editingBudgetId = null;
    
    // Clear form
    clearBudgetForm();
    console.log('Budget modal opened');
}

// Close budget modal
function closeBudgetModal() {
    const modal = document.getElementById('budgetModal');
    modal.style.display = 'none';
    clearBudgetForm();
    console.log('Budget modal closed');
}

// Clear budget form
function clearBudgetForm() {
    const idField = document.getElementById('budgetItemId');
    if (idField) idField.value = '';
    const serviceRequestField = document.getElementById('serviceRequestId');
    if (serviceRequestField) serviceRequestField.value = '';
    document.getElementById('itemName').value = '';
    document.getElementById('itemCategory').value = '';
    document.getElementById('itemAmount').value = '';
    const spentInput = document.getElementById('spentAmount');
    if (spentInput) spentInput.value = '0';
    document.getElementById('paymentStatus').value = 'pending';
    document.getElementById('notes').value = '';
    applyLinkedRequestMode(null);
}

function applyLinkedRequestMode(requestId) {
    const itemCategory = document.getElementById('itemCategory');
    const spentAmount = document.getElementById('spentAmount');
    const paymentStatus = document.getElementById('paymentStatus');
    const linked = !!requestId;

    if (itemCategory) itemCategory.disabled = linked;
    if (spentAmount) spentAmount.readOnly = linked;
    if (paymentStatus) paymentStatus.disabled = linked;
}

function handleServiceRequestChange() {
    const serviceRequestField = document.getElementById('serviceRequestId');
    if (!serviceRequestField) return;

    const selectedRequestId = serviceRequestField.value;
    const request = selectedRequestId ? serviceRequestMap[String(selectedRequestId)] : null;
    applyLinkedRequestMode(selectedRequestId || null);

    if (!request) {
        return;
    }

    const itemCategory = document.getElementById('itemCategory');
    const itemAmount = document.getElementById('itemAmount');
    const spentAmount = document.getElementById('spentAmount');
    const paymentStatus = document.getElementById('paymentStatus');

    if (itemCategory && request.service_type) {
        itemCategory.value = String(request.service_type);
    }

    if (itemAmount && Number(request.budget || 0) > 0) {
        itemAmount.value = Number(request.budget || 0);
    }

    if (spentAmount) {
        spentAmount.value = '0';
    }

    if (paymentStatus) {
        paymentStatus.value = 'pending';
    }
}

// Save budget item
function saveBudgetItem() {
    const idField = document.getElementById('budgetItemId');
    const itemName = document.getElementById('itemName').value;
    const itemCategory = document.getElementById('itemCategory').value;
    const serviceRequestId = document.getElementById('serviceRequestId')?.value || '';
    const itemAmount = document.getElementById('itemAmount').value;
    const spentAmountField = document.getElementById('spentAmount');
    const spentAmount = spentAmountField ? spentAmountField.value : '0';
    const paymentStatus = document.getElementById('paymentStatus').value;
    const notes = document.getElementById('notes').value;

    const linkedRequest = serviceRequestId ? serviceRequestMap[String(serviceRequestId)] : null;
    const effectiveCategory = itemCategory || (linkedRequest && linkedRequest.service_type ? String(linkedRequest.service_type) : '');

    // Validate inputs
    if (!itemName || !effectiveCategory || !itemAmount) {
        alert('Please fill in all required fields');
        return;
    }

    const payload = new URLSearchParams({
        item_name: itemName,
        category: effectiveCategory,
        allocated_amount: itemAmount,
        spent_amount: spentAmount || '0',
        status: paymentStatus,
        notes: notes || ''
    });

    if (serviceRequestId) {
        payload.append('service_request_id', String(serviceRequestId));
    }

    if (editingBudgetId) {
        payload.append('id', String(editingBudgetId));
    }

    fetch(`${apiBase}/save_budget_item?drama_id=${encodeURIComponent(dramaId)}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: payload.toString(),
    })
    .then((res) => res.json())
    .then((json) => {
        if (json.success) {
            alert(json.message || 'Budget item saved successfully');
            closeBudgetModal();
            window.location.reload();
            return;
        }

        alert(json.error || json.message || 'Failed to save budget item');
    })
    .catch((error) => {
        console.error('saveBudgetItem error:', error);
        alert('Network error while saving budget item.');
    });
}

// Edit budget item
function editBudgetItem(itemId) {
    if (!itemId) {
        alert('Invalid budget item');
        return;
    }

    fetch(`${apiBase}/get_budget_item?drama_id=${encodeURIComponent(dramaId)}&id=${encodeURIComponent(itemId)}`)
        .then((res) => res.json())
        .then((json) => {
            if (!json.success || !json.item) {
                alert(json.error || 'Budget item not found');
                return;
            }

            const item = json.item;
            editingBudgetId = item.id;

            const modal = document.getElementById('budgetModal');
            document.getElementById('budgetModal').querySelector('h2').innerHTML = '<i class="fas fa-pencil-alt"></i> Edit Budget Item';

            const idField = document.getElementById('budgetItemId');
            if (idField) idField.value = item.id || '';
            const serviceRequestField = document.getElementById('serviceRequestId');
            if (serviceRequestField) {
                serviceRequestField.value = item.service_request_id ? String(item.service_request_id) : '';
            }
            document.getElementById('itemName').value = item.item_name || '';
            document.getElementById('itemCategory').value = item.category || '';
            document.getElementById('itemAmount').value = item.allocated_amount || '';

            const spentInput = document.getElementById('spentAmount');
            if (spentInput) spentInput.value = item.spent_amount || '0';

            document.getElementById('paymentStatus').value = item.status || 'pending';
            document.getElementById('notes').value = item.notes || '';
            handleServiceRequestChange();
            modal.style.display = 'block';
        })
        .catch((error) => {
            console.error('editBudgetItem error:', error);
            alert('Failed to load budget item for editing');
        });
}

// Delete budget item
function deleteBudgetItem(itemId) {
    if (!itemId) {
        alert('Invalid budget item');
        return;
    }

    if (confirm('Are you sure you want to delete this budget item?')) {
        const payload = new URLSearchParams({ id: String(itemId) });
        fetch(`${apiBase}/delete_budget_item?drama_id=${encodeURIComponent(dramaId)}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: payload.toString(),
        })
        .then((res) => res.json())
        .then((json) => {
            if (json.success) {
                alert(json.message || 'Budget item deleted');
                window.location.reload();
                return;
            }

            alert(json.error || json.message || 'Failed to delete budget item');
        })
        .catch((error) => {
            console.error('deleteBudgetItem error:', error);
            alert('Network error while deleting budget item.');
        });
    }
}

// Load budget items from backend
function loadBudgetItems() {
    console.log('Loading budget items for drama_id:', dramaId);
}

// Export budget report
function exportBudgetReport() {
    console.log('Exporting budget report for drama_id:', dramaId);
    window.open(`${apiBase}/export_budget_report?drama_id=${encodeURIComponent(dramaId)}`, '_blank');
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    console.log('Budget Management page loaded');
    loadBudgetItems();
    handleServiceRequestChange();
    
    // Close modal when clicking outside it
    const modal = document.getElementById('budgetModal');
    window.onclick = function(event) {
        if (event.target == modal) {
            closeBudgetModal();
        }
    }
});

// Print budget items
function printBudgetItems() {
    console.log('Printing budget items');
    window.print();
}
