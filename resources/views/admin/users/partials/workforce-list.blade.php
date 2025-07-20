<div class="action-buttons">
    <button type="button" class="btn btn-primary btn-action" data-bs-toggle="modal" data-bs-target="#addEmployeeModal">
        <i class="fas fa-plus"></i> Add Employee
    </button>
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
                <th>Position</th>
                <th>Department</th>
                <th>Hire Date</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($employees as $employee)
            <tr>
                <td>{{ $employee->id }}</td>
                <td>{{ $employee->user->name ?? 'N/A' }}</td>
                <td>{{ $employee->position }}</td>
                <td>{{ $employee->department }}</td>
                <td>{{ $employee->hire_date ? \Carbon\Carbon::parse($employee->hire_date)->format('M d, Y') : 'N/A' }}</td>
                <td>
                    <span class="badge {{ $employee->status === 'active' ? 'bg-success' : 'bg-danger' }}">
                        {{ ucfirst($employee->status) }}
                    </span>
                </td>
                <td>
                    <button class="btn btn-sm btn-primary btn-edit" data-url="{{ route('admin.employees.edit', $employee->id) }}">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-danger btn-delete" data-url="{{ route('admin.employees.destroy', $employee->id) }}" data-id="{{ $employee->id }}">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Add Employee Modal -->
<div class="modal fade" id="addEmployeeModal" tabindex="-1" aria-labelledby="addEmployeeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addEmployeeModalLabel">Add New Employee</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form class="ajax-form create-form" action="{{ route('admin.employees.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="user_id" class="form-label">User</label>
                        <select class="form-control" id="user_id" name="user_id" required>
                            <option value="">Select User</option>
                            @foreach(\App\Models\User::where('role', 'employee')->get() as $user)
                                <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="position" class="form-label">Position</label>
                        <input type="text" class="form-control" id="position" name="position" required>
                    </div>
                    <div class="mb-3">
                        <label for="department" class="form-label">Department</label>
                        <select class="form-control" id="department" name="department" required>
                            <option value="">Select Department</option>
                            <option value="Production">Production</option>
                            <option value="Quality Control">Quality Control</option>
                            <option value="Logistics">Logistics</option>
                            <option value="Sales">Sales</option>
                            <option value="Administration">Administration</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="hire_date" class="form-label">Hire Date</label>
                        <input type="date" class="form-control" id="hire_date" name="hire_date" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Employee</button>
                </div>
            </form>
        </div>
    </div>
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