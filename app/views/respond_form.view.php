<!-- Respond Modal -->
<style>
    #respondModal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        align-items: center;
        justify-content: center;
    }

    #respondModal .modal-content {
        background: #fff;
        border-radius: 10px;
        width: 90%;
        max-width: 550px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        overflow: hidden;
    }

    #respondModal .modal-header {
        padding: 20px 24px;
        border-bottom: 1px solid #e5e7eb;
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: linear-gradient(135deg, #d4af37, #aa8c2c);
    }

    #respondModal .modal-header h3 {
        margin: 0;
        font-size: 18px;
        font-weight: 600;
        color: #1a1410;
    }

    #respondModal .modal-header .close-btn {
        background: none;
        border: none;
        font-size: 20px;
        cursor: pointer;
        color: #1a1410;
        width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 4px;
        transition: background 0.2s;
    }

    #respondModal .modal-header .close-btn:hover {
        background: rgba(0, 0, 0, 0.1);
    }

    #respondModal .modal-body {
        padding: 24px;
    }

    #respondModal .form-group {
        margin-bottom: 16px;
    }

    #respondModal .form-group label {
        display: block;
        font-size: 13px;
        font-weight: 600;
        color: #374151;
        margin-bottom: 6px;
    }

    #respondModal .form-group label span {
        color: #dc2626;
    }

    #respondModal .form-group input[type="number"],
    #respondModal .form-group input[type="date"],
    #respondModal .form-group textarea {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        font-size: 14px;
        font-family: inherit;
        transition: all 0.2s;
    }

    #respondModal .form-group input:focus,
    #respondModal .form-group textarea:focus {
        outline: none;
        border-color: #d4af37;
        box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.1);
    }

    #respondModal .form-group textarea {
        resize: vertical;
        min-height: 80px;
    }

    #respondModal .checkbox-group {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 10px;
        margin-bottom: 12px;
        background: #f9fafb;
        border: 1px solid #e5e7eb;
        border-radius: 6px;
        transition: all 0.2s;
    }

    #respondModal .checkbox-group:hover {
        background: #f3f4f6;
        border-color: #d4af37;
    }

    #respondModal .checkbox-group input[type="checkbox"] {
        width: 18px;
        height: 18px;
        cursor: pointer;
        accent-color: #d4af37;
    }

    #respondModal .checkbox-group label {
        font-size: 13px;
        font-weight: 500;
        color: #374151;
        cursor: pointer;
        margin: 0;
    }

    #respondModal .advance-fields {
        display: none;
        padding: 14px;
        background: #f9fafb;
        border: 1px solid #e5e7eb;
        border-radius: 6px;
        margin-bottom: 12px;
    }

    #respondModal .advance-fields.show {
        display: block;
    }

    #respondModal .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
    }

    #respondModal .modal-footer {
        display: flex;
        gap: 10px;
        justify-content: flex-end;
        margin-top: 20px;
        padding-top: 16px;
        border-top: 1px solid #e5e7eb;
    }

    #respondModal .btn {
        padding: 10px 18px;
        font-size: 13px;
        font-weight: 600;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    #respondModal .btn-cancel {
        background: #e5e7eb;
        color: #374151;
    }

    #respondModal .btn-cancel:hover {
        background: #d1d5db;
    }

    #respondModal .btn-primary {
        background: linear-gradient(135deg, #d4af37, #aa8c2c);
        color: #1a1410;
    }

    #respondModal .btn-primary:hover {
        background: linear-gradient(135deg, #aa8c2c, #8b6f47);
    }
</style>

<div id="respondModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-edit"></i> Respond to Request</h3>
            <button type="button" class="close-btn" onclick="closeRespondModal()">&times;</button>
        </div>
        <div class="modal-body">
            <form id="respondForm">
                <input type="hidden" id="respond_request_id" name="request_id">
                
                <div class="form-group">
                    <label>Quotation Amount (Rs) <span>*</span></label>
                    <input type="number" name="quote_amount" placeholder="Enter your quotation amount" required>
                </div>

                <!-- Advance Payment Section -->
                <div class="checkbox-group">
                    <input type="checkbox" id="needs_advance" name="needs_advance" value="1" onchange="toggleAdvanceFields()">
                    <label for="needs_advance"><i class="fas fa-money-bill"></i> Requires Advance Payment</label>
                </div>
                
                <div id="advanceFields" class="advance-fields">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Advance Amount (Rs) <span>*</span></label>
                            <input type="number" name="advance_amount" placeholder="Enter advance amount">
                        </div>
                        <div class="form-group">
                            <label>Advance Due Date <span>*</span></label>
                            <input type="date" name="advance_due_date">
                        </div>
                    </div>
                </div>

                <!-- Final Payment Section -->
                <div class="form-group">
                    <label>Final Payment Due Date</label>
                    <input type="date" name="final_payment_due_date">
                </div>

                <div class="form-group">
                    <label>Notes (optional)</label>
                    <textarea name="note" placeholder="Add any notes or conditions about your quote"></textarea>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-cancel" onclick="closeRespondModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane"></i> Submit Response
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function toggleAdvanceFields() {
        const checkbox = document.getElementById('needs_advance');
        const advanceFields = document.getElementById('advanceFields');
        const advanceAmount = document.querySelector('input[name="advance_amount"]');
        const advanceDueDate = document.querySelector('input[name="advance_due_date"]');
        
        if (checkbox.checked) {
            advanceFields.classList.add('show');
            advanceAmount.required = true;
            advanceDueDate.required = true;
        } else {
            advanceFields.classList.remove('show');
            advanceAmount.required = false;
            advanceDueDate.required = false;
            advanceAmount.value = '';
            advanceDueDate.value = '';
        }
    }
</script>
