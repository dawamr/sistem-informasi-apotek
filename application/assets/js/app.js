/**
 * Main Application JavaScript
 * Sistem Informasi Apotek
 */

// ============================================================================
// Utility Functions
// ============================================================================

/**
 * Show toast notification
 */
function showToast(message, type = 'info', duration = 3000) {
    const toastHTML = `
        <div class="toast align-items-center text-white bg-${type} border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    `;
    
    const toastContainer = document.getElementById('toastContainer');
    if (!toastContainer) {
        const container = document.createElement('div');
        container.id = 'toastContainer';
        container.className = 'toast-container position-fixed bottom-0 end-0 p-3';
        document.body.appendChild(container);
    }
    
    const toastElement = document.createElement('div');
    toastElement.innerHTML = toastHTML;
    document.getElementById('toastContainer').appendChild(toastElement.firstElementChild);
    
    const toast = new bootstrap.Toast(toastElement.firstElementChild);
    toast.show();
    
    setTimeout(() => {
        toastElement.firstElementChild.remove();
    }, duration);
}

/**
 * Show success toast
 */
function showSuccess(message, duration = 3000) {
    showToast(message, 'success', duration);
}

/**
 * Show error toast
 */
function showError(message, duration = 3000) {
    showToast(message, 'danger', duration);
}

/**
 * Show warning toast
 */
function showWarning(message, duration = 3000) {
    showToast(message, 'warning', duration);
}

/**
 * Show info toast
 */
function showInfo(message, duration = 3000) {
    showToast(message, 'info', duration);
}

// ============================================================================
// DataTables Initialization
// ============================================================================

/**
 * Initialize DataTable with default settings
 */
function initDataTable(tableId, options = {}) {
    const defaultOptions = {
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.13.0/i18n/id.json'
        },
        pageLength: 10,
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        responsive: true,
        dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>><"row"<"col-sm-12"tr>><"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
        ...options
    };
    
    return $(`#${tableId}`).DataTable(defaultOptions);
}

// ============================================================================
// Form Validation
// ============================================================================

/**
 * Validate form before submission
 */
function validateForm(formId) {
    const form = document.getElementById(formId);
    if (!form.checkValidity()) {
        event.preventDefault();
        event.stopPropagation();
    }
    form.classList.add('was-validated');
    return form.checkValidity();
}

/**
 * Reset form validation
 */
function resetFormValidation(formId) {
    const form = document.getElementById(formId);
    form.classList.remove('was-validated');
    form.reset();
}

// ============================================================================
// Modal Helpers
// ============================================================================

/**
 * Show modal
 */
function showModal(modalId) {
    const modal = new bootstrap.Modal(document.getElementById(modalId));
    modal.show();
}

/**
 * Hide modal
 */
function hideModal(modalId) {
    const modal = bootstrap.Modal.getInstance(document.getElementById(modalId));
    if (modal) {
        modal.hide();
    }
}

/**
 * Show confirmation dialog
 */
function showConfirmDialog(title, message, onConfirm, onCancel = null) {
    const confirmHTML = `
        <div class="modal fade" id="confirmModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">${title}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        ${message}
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="button" class="btn btn-primary" id="confirmBtn">Konfirmasi</button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    let confirmModal = document.getElementById('confirmModal');
    if (confirmModal) {
        confirmModal.remove();
    }
    
    document.body.insertAdjacentHTML('beforeend', confirmHTML);
    confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));
    
    document.getElementById('confirmBtn').addEventListener('click', function() {
        confirmModal.hide();
        if (typeof onConfirm === 'function') {
            onConfirm();
        }
    });
    
    document.getElementById('confirmModal').addEventListener('hidden.bs.modal', function() {
        if (typeof onCancel === 'function') {
            onCancel();
        }
        this.remove();
    });
    
    confirmModal.show();
}

// ============================================================================
// AJAX Helpers
// ============================================================================

/**
 * Make AJAX request
 */
function ajaxRequest(url, method = 'GET', data = null, onSuccess = null, onError = null) {
    $.ajax({
        url: url,
        type: method,
        data: data,
        dataType: 'json',
        success: function(response) {
            if (typeof onSuccess === 'function') {
                onSuccess(response);
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error:', error);
            if (typeof onError === 'function') {
                onError(xhr, status, error);
            } else {
                showError('Terjadi kesalahan. Silakan coba lagi.');
            }
        }
    });
}

/**
 * Delete record with confirmation
 */
function deleteRecord(url, recordName = 'item') {
    showConfirmDialog(
        'Hapus ' + recordName,
        'Apakah Anda yakin ingin menghapus ' + recordName + ' ini?',
        function() {
            ajaxRequest(url, 'DELETE', null, 
                function(response) {
                    if (response.success) {
                        showSuccess(response.message || recordName + ' berhasil dihapus');
                        setTimeout(() => location.reload(), 1000);
                    } else {
                        showError(response.message || 'Gagal menghapus ' + recordName);
                    }
                }
            );
        }
    );
}

// ============================================================================
// Format Helpers
// ============================================================================

/**
 * Format currency (IDR)
 */
function formatCurrency(amount) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(amount);
}

/**
 * Format date
 */
function formatDate(date, format = 'dd/MM/yyyy') {
    const d = new Date(date);
    const day = String(d.getDate()).padStart(2, '0');
    const month = String(d.getMonth() + 1).padStart(2, '0');
    const year = d.getFullYear();
    
    return format
        .replace('dd', day)
        .replace('MM', month)
        .replace('yyyy', year);
}

/**
 * Format time
 */
function formatTime(time) {
    const [hours, minutes] = time.split(':');
    return `${hours}:${minutes}`;
}

// ============================================================================
// Chart Helpers
// ============================================================================

/**
 * Create line chart
 */
function createLineChart(canvasId, labels, data, label = 'Data') {
    const ctx = document.getElementById(canvasId).getContext('2d');
    return new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: label,
                data: data,
                borderColor: '#0d6efd',
                backgroundColor: 'rgba(13, 110, 253, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4,
                pointRadius: 4,
                pointBackgroundColor: '#0d6efd',
                pointBorderColor: '#fff',
                pointBorderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
}

/**
 * Create bar chart
 */
function createBarChart(canvasId, labels, data, label = 'Data') {
    const ctx = document.getElementById(canvasId).getContext('2d');
    return new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: label,
                data: data,
                backgroundColor: '#0d6efd',
                borderColor: '#0b5ed7',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
}

/**
 * Create pie chart
 */
function createPieChart(canvasId, labels, data, label = 'Data') {
    const ctx = document.getElementById(canvasId).getContext('2d');
    const colors = ['#0d6efd', '#198754', '#ffc107', '#dc3545', '#0dcaf0'];
    
    return new Chart(ctx, {
        type: 'pie',
        data: {
            labels: labels,
            datasets: [{
                label: label,
                data: data,
                backgroundColor: colors.slice(0, labels.length),
                borderColor: '#fff',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
}

// ============================================================================
// Document Ready
// ============================================================================

document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Initialize popovers
    const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    popoverTriggerList.map(function(popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });
});
