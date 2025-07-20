@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">User Management</h3>
                </div>
                <div class="card-body">
                    <!-- Tab Navigation -->
                    <ul class="nav nav-tabs" id="userManagementTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="users-tab" data-bs-toggle="tab" data-bs-target="#users" type="button" role="tab" aria-controls="users" aria-selected="true">
                                Users
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="workforce-tab" data-bs-toggle="tab" data-bs-target="#workforce" type="button" role="tab" aria-controls="workforce" aria-selected="false">
                                Workforce
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="distribution-centers-tab" data-bs-toggle="tab" data-bs-target="#distribution-centers" type="button" role="tab" aria-controls="distribution-centers" aria-selected="false">
                                Distribution Centers
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="deliveries-tab" data-bs-toggle="tab" data-bs-target="#deliveries" type="button" role="tab" aria-controls="deliveries" aria-selected="false">
                                Deliveries
                            </button>
                        </li>
                    </ul>

                    <!-- Tab Content -->
                    <div class="tab-content" id="userManagementTabContent">
                        <!-- Users Tab -->
                        <div class="tab-pane fade show active" id="users" role="tabpanel" aria-labelledby="users-tab">
                            <div id="users-content">
                                @include('admin.users.partials.users-list')
                            </div>
                        </div>

                        <!-- Workforce Tab -->
                        <div class="tab-pane fade" id="workforce" role="tabpanel" aria-labelledby="workforce-tab">
                            <div id="workforce-content">
                                @include('admin.users.partials.workforce-list')
                            </div>
                        </div>

                        <!-- Distribution Centers Tab -->
                        <div class="tab-pane fade" id="distribution-centers" role="tabpanel" aria-labelledby="distribution-centers-tab">
                            <div id="distribution-centers-content">
                                @include('admin.users.partials.distribution-centers-list')
                            </div>
                        </div>

                        <!-- Deliveries Tab -->
                        <div class="tab-pane fade" id="deliveries" role="tabpanel" aria-labelledby="deliveries-tab">
                            <div id="deliveries-content">
                                @include('admin.users.partials.deliveries-list')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Success/Error Toast Container -->
<div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div id="actionToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header">
            <strong class="me-auto" id="toastTitle">Notification</strong>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body" id="toastMessage">
        </div>
    </div>
</div>

<style>
.tab-content {
    padding: 20px 0;
}

.loading-spinner {
    display: none;
    text-align: center;
    padding: 20px;
}

.loading-spinner.show {
    display: block;
}

.action-buttons {
    margin-bottom: 15px;
}

.btn-action {
    margin-right: 5px;
}

.toast {
    z-index: 9999;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Store current active tab
    let currentActiveTab = 'users';
    
    // Initialize tab functionality
    initializeTabs();
    
    // Initialize action handlers
    initializeActionHandlers();
    
    function initializeTabs() {
        const tabButtons = document.querySelectorAll('#userManagementTabs .nav-link');
        
        tabButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                const targetTab = this.getAttribute('data-bs-target').substring(1);
                currentActiveTab = targetTab;
                
                // Store active tab in session storage for persistence
                sessionStorage.setItem('activeUserManagementTab', targetTab);
            });
        });
        
        // Restore active tab from session storage
        const savedTab = sessionStorage.getItem('activeUserManagementTab');
        if (savedTab) {
            const tabButton = document.querySelector(`[data-bs-target="#${savedTab}"]`);
            if (tabButton) {
                tabButton.click();
            }
        }
    }
    
    function initializeActionHandlers() {
        // Handle form submissions with AJAX
        document.addEventListener('submit', function(e) {
            if (e.target.classList.contains('ajax-form')) {
                e.preventDefault();
                handleFormSubmission(e.target);
            }
        });
        
        // Handle delete actions
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('btn-delete')) {
                e.preventDefault();
                handleDeleteAction(e.target);
            }
        });
        
        // Handle edit actions
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('btn-edit')) {
                e.preventDefault();
                handleEditAction(e.target);
            }
        });
    }
    
    function handleFormSubmission(form) {
        const formData = new FormData(form);
        const action = form.action;
        const method = form.method;
        
        // Show loading spinner
        showLoadingSpinner(currentActiveTab);
        
        fetch(action, {
            method: method,
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            hideLoadingSpinner(currentActiveTab);
            
            if (data.success) {
                // Reload the current tab content
                reloadTabContent(currentActiveTab);
                showToast('Success', data.message || 'Action completed successfully', 'success');
                
                // Reset form if it's a create form
                if (form.classList.contains('create-form')) {
                    form.reset();
                }
            } else {
                showToast('Error', data.message || 'An error occurred', 'error');
            }
        })
        .catch(error => {
            hideLoadingSpinner(currentActiveTab);
            showToast('Error', 'An error occurred while processing your request', 'error');
            console.error('Error:', error);
        });
    }
    
    function handleDeleteAction(button) {
        if (!confirm('Are you sure you want to delete this item?')) {
            return;
        }
        
        const url = button.getAttribute('href') || button.dataset.url;
        const id = button.dataset.id;
        
        showLoadingSpinner(currentActiveTab);
        
        fetch(url, {
            method: 'DELETE',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            hideLoadingSpinner(currentActiveTab);
            
            if (data.success) {
                reloadTabContent(currentActiveTab);
                showToast('Success', data.message || 'Item deleted successfully', 'success');
            } else {
                showToast('Error', data.message || 'Failed to delete item', 'error');
            }
        })
        .catch(error => {
            hideLoadingSpinner(currentActiveTab);
            showToast('Error', 'An error occurred while deleting the item', 'error');
            console.error('Error:', error);
        });
    }
    
    function handleEditAction(button) {
        const url = button.getAttribute('href') || button.dataset.url;
        
        // Load edit form in modal or inline
        fetch(url)
        .then(response => response.text())
        .then(html => {
            // You can implement modal or inline editing here
            // For now, we'll just navigate to the edit page
            window.location.href = url;
        })
        .catch(error => {
            showToast('Error', 'Failed to load edit form', 'error');
            console.error('Error:', error);
        });
    }
    
    function reloadTabContent(tabName) {
        const contentDiv = document.getElementById(`${tabName}-content`);
        const loadingDiv = document.getElementById(`${tabName}-loading`);
        
        if (contentDiv) {
            // Show loading spinner
            if (loadingDiv) {
                loadingDiv.classList.add('show');
            }
            
            // Fetch updated content
            fetch(`/admin/user-management/${tabName}/content`)
            .then(response => response.text())
            .then(html => {
                contentDiv.innerHTML = html;
                
                // Hide loading spinner
                if (loadingDiv) {
                    loadingDiv.classList.remove('show');
                }
            })
            .catch(error => {
                console.error('Error reloading tab content:', error);
                if (loadingDiv) {
                    loadingDiv.classList.remove('show');
                }
            });
        }
    }
    
    function showLoadingSpinner(tabName) {
        const loadingDiv = document.getElementById(`${tabName}-loading`);
        if (loadingDiv) {
            loadingDiv.classList.add('show');
        }
    }
    
    function hideLoadingSpinner(tabName) {
        const loadingDiv = document.getElementById(`${tabName}-loading`);
        if (loadingDiv) {
            loadingDiv.classList.remove('show');
        }
    }
    
    function showToast(title, message, type = 'info') {
        const toast = document.getElementById('actionToast');
        const toastTitle = document.getElementById('toastTitle');
        const toastMessage = document.getElementById('toastMessage');
        
        toastTitle.textContent = title;
        toastMessage.textContent = message;
        
        // Set toast color based on type
        toast.className = `toast ${type === 'success' ? 'bg-success text-white' : type === 'error' ? 'bg-danger text-white' : 'bg-info text-white'}`;
        
        // Show toast
        const bsToast = new bootstrap.Toast(toast);
        bsToast.show();
    }
});
</script>
@endsection