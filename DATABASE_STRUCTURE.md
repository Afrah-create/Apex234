# Database Structure Documentation

## User Management System

### Core Relationships

#### 1. Users Table (Authentication & Basic Info)
- **Primary table** for user authentication and basic information
- Contains: `id`, `name`, `email`, `password`, `role`, `status`, `profile_photo`
- **Role field**: Determines user type (admin, supplier, vendor, retailer, employee)

#### 2. Vendors Table (Business Details)
- **Linked to Users**: One-to-one relationship via `user_id`
- **Contains business-specific information**:
  - `business_name`, `business_address`, `phone_number`
  - `tax_id`, `business_license`, `status` (pending/approved/rejected/suspended)
  - `contact_person`, `contact_email`, `contact_phone`
  - `description`

#### 3. Employees Table (Workforce Management)
- **Linked to Vendors**: Many-to-one relationship via `vendor_id`
- **Contains employee information**:
  - `name`, `role` (Production Worker, Warehouse Staff, Driver, Sales Manager)
  - `status` (active/inactive/terminated)
  - `vendor_id` (foreign key to vendors table)

### Relationship Flow

```
User (Authentication)
    ↓ (one-to-one)
Vendor (Business Profile)
    ↓ (one-to-many)
Employees (Workforce)
```

### Key Points

1. **A vendor is a user**: Every vendor has a corresponding user account for authentication
2. **Employees belong to vendors**: Employees are assigned to specific vendors, not directly to users
3. **Clear separation of concerns**:
   - Users handle authentication and basic profile
   - Vendors handle business-specific details
   - Employees handle workforce management

### Example Usage

```php
// Get vendor's user account
$vendor = Vendor::find(1);
$user = $vendor->user; // Returns the associated User model

// Get vendor's employees
$employees = $vendor->employees; // Returns collection of Employee models

// Get user's vendor profile
$user = User::find(1);
$vendor = $user->vendor; // Returns the associated Vendor model (if user is a vendor)

// Check if vendor is approved
if ($vendor->isApproved()) {
    // Vendor can access vendor-specific features
}

// Get active employees for a vendor
$activeEmployees = $vendor->employees()->where('status', 'active')->get();
```

### Migration Order

1. `create_users_table` - Base user authentication
2. `create_vendors_table` - Vendor business profiles (links to users)
3. `create_employees_table` - Employee workforce (links to vendors)
4. `add_vendor_id_to_employees_table` - Links employees to vendors
5. `add_status_to_employees_table` - Adds employee status and removes user_id

### Benefits of This Structure

1. **Clear ownership**: Vendors own their employees
2. **Scalable**: Easy to add more vendor-specific features
3. **Secure**: Authentication separate from business logic
4. **Flexible**: Can easily extend vendor or employee attributes
5. **Consistent**: Follows Laravel conventions and best practices 