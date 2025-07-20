<div class="action-buttons">
    <button type="button" class="btn btn-primary btn-action" data-bs-toggle="modal" data-bs-target="#createUserModal">
        <i class="fas fa-plus"></i> Add User
    </button>
    <button type="button" class="btn btn-secondary btn-action" onclick="reloadUsersContent()">
        <i class="fas fa-sync"></i> Refresh
    </button>
</div>

<div class="loading-spinner" id="users-loading">
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
                <th>Created</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
            <tr>
                <td>{{ $user->id }}</td>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>{{ $user->role }}</td>
                <td>
                    <span class="badge {{ $user->status === 'active' ? 'bg-success' : 'bg-danger' }}">
                        {{ ucfirst($user->status) }}
                    </span>
                </td>
                <td>{{ $user->created_at->format('M d, Y') }}</td>
                <td>
                    <button class="btn btn-sm btn-primary btn-edit" data-url="{{ route('admin.users.edit', $user->id) }}">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-danger btn-delete" data-url="{{ route('admin.users.destroy', $user->id) }}" data-id="{{ $user->id }}">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Create User Modal -->
<div class="modal fade" id="createUserModal" tabindex="-1" aria-labelledby="createUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createUserModalLabel">Create New User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form class="ajax-form create-form" action="{{ route('admin.users.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="role" class="form-label">Role</label>
                        <select class="form-control" id="role" name="role" required>
                            <option value="">Select Role</option>
                            <option value="admin">Admin</option>
                            <option value="employee">Employee</option>
                            <option value="vendor">Vendor</option>
                            <option value="customer">Customer</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create User</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function reloadUsersContent() {
    const contentDiv = document.getElementById('users-content');
    const loadingDiv = document.getElementById('users-loading');
    
    if (loadingDiv) {
        loadingDiv.classList.add('show');
    }
    
    fetch('/admin/user-management/users/content')
    .then(response => response.text())
    .then(html => {
        contentDiv.innerHTML = html;
        if (loadingDiv) {
            loadingDiv.classList.remove('show');
        }
    })
    .catch(error => {
        console.error('Error reloading users content:', error);
        if (loadingDiv) {
            loadingDiv.classList.remove('show');
        }
    });
}
</script> 