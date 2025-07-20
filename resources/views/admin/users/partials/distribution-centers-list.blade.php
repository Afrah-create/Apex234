<div class="action-buttons">
    <button type="button" class="btn btn-primary btn-action" data-bs-toggle="modal" data-bs-target="#addDistributionCenterModal">
        <i class="fas fa-plus"></i> Add Distribution Center
    </button>
    <button type="button" class="btn btn-secondary btn-action" onclick="reloadDistributionCentersContent()">
        <i class="fas fa-sync"></i> Refresh
    </button>
</div>

<div class="loading-spinner" id="distribution-centers-loading">
    <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Loading...</span>
    </div>
</div>

<div class="table-responsive">
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Address</th>
                <th>Capacity</th>
                <th>Status</th>
                <th>Created</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($distributionCenters as $center)
            <tr>
                <td>{{ $center->id }}</td>
                <td>{{ $center->name }}</td>
                <td>{{ $center->address }}</td>
                <td>{{ number_format($center->capacity) }} units</td>
                <td>
                    <span class="badge {{ $center->status === 'active' ? 'bg-success' : 'bg-danger' }}">
                        {{ ucfirst($center->status) }}
                    </span>
                </td>
                <td>{{ $center->created_at->format('M d, Y') }}</td>
                <td>
                    <button class="btn btn-sm btn-primary btn-edit" data-url="{{ route('admin.distribution-centers.edit', $center->id) }}">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-danger btn-delete" data-url="{{ route('admin.distribution-centers.destroy', $center->id) }}" data-id="{{ $center->id }}">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Add Distribution Center Modal -->
<div class="modal fade" id="addDistributionCenterModal" tabindex="-1" aria-labelledby="addDistributionCenterModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addDistributionCenterModalLabel">Add New Distribution Center</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form class="ajax-form create-form" action="{{ route('admin.distribution-centers.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Center Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="address" class="form-label">Address</label>
                        <textarea class="form-control" id="address" name="address" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="capacity" class="form-label">Capacity (units)</label>
                        <input type="number" class="form-control" id="capacity" name="capacity" min="1" required>
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-control" id="status" name="status" required>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Distribution Center</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function reloadDistributionCentersContent() {
    const contentDiv = document.getElementById('distribution-centers-content');
    const loadingDiv = document.getElementById('distribution-centers-loading');
    
    if (loadingDiv) {
        loadingDiv.classList.add('show');
    }
    
    fetch('/admin/user-management/distribution-centers/content')
    .then(response => response.text())
    .then(html => {
        contentDiv.innerHTML = html;
        if (loadingDiv) {
            loadingDiv.classList.remove('show');
        }
    })
    .catch(error => {
        console.error('Error reloading distribution centers content:', error);
        if (loadingDiv) {
            loadingDiv.classList.remove('show');
        }
    });
}
</script> 