// Budget Management - Manage drama budget items

const urlParams = new URLSearchParams(window.location.search);
const dramaId = urlParams.get('drama_id') || 1;

console.log('Budget Management initialized for Drama ID:', dramaId);

// Open add budget modal
function openAddBudgetModal() {
    const modal = document.getElementById('budgetModal');
    modal.style.display = 'block';
    document.getElementById('budgetModal').querySelector('h2').innerHTML = '<i class="fas fa-plus"></i> Add Budget Item';
    
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
    document.getElementById('itemName').value = '';
    document.getElementById('itemCategory').value = '';
    document.getElementById('itemAmount').value = '';
    document.getElementById('paymentStatus').value = 'pending';
    document.getElementById('notes').value = '';
}

// Save budget item
function saveBudgetItem() {
    const itemName = document.getElementById('itemName').value;
    const itemCategory = document.getElementById('itemCategory').value;
    const itemAmount = document.getElementById('itemAmount').value;
    const paymentStatus = document.getElementById('paymentStatus').value;
    const notes = document.getElementById('notes').value;

    // Validate inputs
    if (!itemName || !itemCategory || !itemAmount) {
        alert('Please fill in all required fields');
        return;
    }

    console.log('Saving budget item:', {
        itemName,
        itemCategory,
        itemAmount,
        paymentStatus,
        notes,
        drama_id: dramaId
    });

    // TODO: Send to backend API to save budget item
    alert(`Budget item "${itemName}" has been added successfully!`);
    closeBudgetModal();
    
    // Reload the table
    loadBudgetItems();
}

// Edit budget item
function editBudgetItem(itemId) {
    console.log('Editing budget item:', itemId);
    const modal = document.getElementById('budgetModal');
    document.getElementById('budgetModal').querySelector('h2').innerHTML = '<i class="fas fa-pencil-alt"></i> Edit Budget Item';
    
    // TODO: Load item data from backend and populate form
    modal.style.display = 'block';
}

// Delete budget item
function deleteBudgetItem(itemId) {
    if (confirm('Are you sure you want to delete this budget item?')) {
        console.log('Deleting budget item:', itemId);
        // TODO: Send delete request to backend API
        alert('Budget item has been deleted');
        loadBudgetItems();
    }
}

// Load budget items from backend
function loadBudgetItems() {
    console.log('Loading budget items for drama_id:', dramaId);
    // TODO: Fetch budget items from backend API
    // Update table with fetched data
}

// Export budget report
function exportBudgetReport() {
    console.log('Exporting budget report for drama_id:', dramaId);
    // TODO: Generate and download budget report as PDF or CSV
    alert('Budget report generated and ready for download');
    // window.open(`../../controllers/BudgetController.php?action=export&drama_id=${dramaId}`);
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    console.log('Budget Management page loaded');
    loadBudgetItems();
    
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
