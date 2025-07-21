<div class="action-buttons">
    <button type="button" class="btn btn-secondary btn-action" onclick="reloadWorkforceContent()">
        <i class="fas fa-sync"></i> Refresh
    </button>
</div>

<div class="loading-spinner" id="workforce-loading">
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
                <th>Email</th>
                <th>Role</th>
                <th>Status</th>
                <th>Vendor</th>
                <th>Distribution Center</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($employees as $employee)
            <tr>
                <form method="POST" action="{{ route('admin.employees.assignAll', $employee->id) }}" class="d-inline">
                    @csrf
                    <td>{{ $employee->id }}</td>
                    <td>{{ $employee->user->name ?? 'N/A' }}</td>
                    <td>{{ $employee->user->email ?? 'N/A' }}</td>
                    <td>
                        <select name="role" class="form-select form-select-sm">
                            <option value="Warehouse Staff" {{ $employee->role === 'Warehouse Staff' ? 'selected' : '' }}>Warehouse Staff</option>
                            <option value="Driver" {{ $employee->role === 'Driver' ? 'selected' : '' }}>Driver</option>
                        </select>
                    </td>
                    <td>
                        <select name="status" class="form-select form-select-sm">
                            <option value="active" {{ $employee->status === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ $employee->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="terminated" {{ $employee->status === 'terminated' ? 'selected' : '' }}>Terminated</option>
                        </select>
                    </td>
                    <td>
                        <select name="vendor_id" class="form-select form-select-sm">
                            <option value="">Unassigned</option>
                            @foreach($vendors as $vendor)
                                <option value="{{ $vendor->id }}" {{ $employee->vendor_id == $vendor->id ? 'selected' : '' }}>{{ $vendor->business_name }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <select name="distribution_center_id" class="form-select form-select-sm">
                            <option value="">Unassigned</option>
                            @foreach($distributionCenters as $dc)
                                <option value="{{ $dc->id }}" {{ $employee->distribution_center_id == $dc->id ? 'selected' : '' }}>{{ $dc->center_name }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <button type="submit" class="btn btn-primary btn-sm">Assign</button>
                        <button class="btn btn-sm btn-primary btn-edit" data-url="{{ route('admin.employees.edit', $employee->id) }}" type="button">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-danger btn-delete" data-url="{{ route('admin.employees.destroy', $employee->id) }}" data-id="{{ $employee->id }}" type="button">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </form>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<script>
function reloadWorkforceContent() {
    const contentDiv = document.getElementById('workforce-content');
    const loadingDiv = document.getElementById('workforce-loading');
    
    if (loadingDiv) {
        loadingDiv.classList.add('show');
    }
    
    fetch('/admin/user-management/workforce/content')
    .then(response => response.text())
    .then(html => {
        contentDiv.innerHTML = html;
        if (loadingDiv) {
            loadingDiv.classList.remove('show');
        }
    })
    .catch(error => {
        console.error('Error reloading workforce content:', error);
        if (loadingDiv) {
            loadingDiv.classList.remove('show');
        }
    });
}
</script> 