# User Registration Process Documentation

## Overview

The system now properly creates user details in their respective tables during registration. This document explains how each user type is handled during the registration process.

## Registration Flow

### 1. User Registration Form (`/register`)

**Available Fields:**
- Name (required)
- Email (required)
- Role selection (retailer/supplier/vendor)
- Password (required)
- Password confirmation (required)

**Vendor-Specific Fields (shown when vendor role is selected):**
- Business Name
- Business Address
- Phone Number
- Tax ID (optional)
- Business License (optional)
- Business Description (optional)

### 2. Registration Processing

#### A. Retailer Registration
```php
// Creates User record
User::create([
    'name' => $request->name,
    'email' => $request->email,
    'password' => Hash::make($request->password),
    'role' => 'retailer',
    'status' => 'approved'
]);

// Assigns retailer role
$role = Role::where('name', 'retailer')->first();
$user->roles()->syncWithoutDetaching([$role->id]);
```

**Result:** ✅ User record created with retailer role

#### B. Supplier Registration
```php
// Creates User record (same as retailer)
User::create([...]);

// Creates Supplier record with business details
Supplier::create([
    'user_id' => $user->id,
    'company_name' => $request->company_name ?? 'Default Company',
    'registration_number' => uniqid('SUP'),
    'business_address' => $request->business_address ?? '',
    'contact_person' => $user->name,
    'contact_phone' => $request->contact_phone ?? '',
    'contact_email' => $user->email,
    'supplier_type' => 'dairy_farm',
    'status' => 'pending',
    // ... other fields
]);

// Creates DairyFarm record
DairyFarm::create([
    'supplier_id' => $supplier->id,
    'farm_name' => $supplier->company_name . ' Main Farm',
    'farm_code' => uniqid('FARM'),
    // ... other fields
]);
```

**Result:** ✅ User + Supplier + DairyFarm records created

#### C. Vendor Registration
```php
// Creates User record (same as others)
User::create([...]);

// Creates Vendor record with business details
Vendor::create([
    'user_id' => $user->id,
    'business_name' => $request->business_name ?? $user->name . ' Business',
    'business_address' => $request->business_address ?? '',
    'phone_number' => $request->phone_number ?? '',
    'tax_id' => $request->tax_id ?? null,
    'business_license' => $request->business_license ?? null,
    'status' => 'pending',
    'description' => $request->description ?? null,
    'contact_person' => $user->name,
    'contact_email' => $user->email,
    'contact_phone' => $request->phone_number ?? '',
]);
```

**Result:** ✅ User + Vendor records created

### 3. Vendor Application Process (Alternative)

For vendors who apply through the application form (`/vendor/apply`):

#### A. Application Submission
```php
// Creates VendorApplicant record
VendorApplicant::create([
    'name' => $validated['name'],
    'email' => $validated['email'],
    'phone' => $validated['phone'],
    'company_name' => $validated['company_name'],
    'pdf_path' => $pdfPath,
    'status' => $status, // pending/validated/rejected
    'visit_date' => $visitDate,
    'validation_message' => $validationMessage,
]);
```

#### B. Admin Approval Process
```php
// Creates User record
User::create([
    'name' => $applicant->name,
    'email' => $applicant->email,
    'password' => Hash::make($password),
    'role' => 'vendor',
    'status' => 'approved',
]);

// Creates Vendor record from applicant data
Vendor::create([
    'user_id' => $user->id,
    'business_name' => $applicant->company_name,
    'business_address' => '', // Not available in applicant data
    'phone_number' => $applicant->phone,
    'tax_id' => null, // Not available in applicant data
    'business_license' => $applicant->license_number,
    'status' => 'approved',
    'description' => 'Vendor approved from application',
    'contact_person' => $applicant->name,
    'contact_email' => $applicant->email,
    'contact_phone' => $applicant->phone,
]);
```

**Result:** ✅ User + Vendor records created from application

## Data Storage Summary

| User Type | User Table | Role Table | Business Details Table | Additional Tables |
|-----------|------------|------------|----------------------|-------------------|
| **Retailer** | ✅ | ✅ | ❌ | ❌ |
| **Supplier** | ✅ | ✅ | ✅ (Supplier) | ✅ (DairyFarm) |
| **Vendor** | ✅ | ✅ | ✅ (Vendor) | ❌ |
| **Vendor (Applied)** | ✅ | ✅ | ✅ (Vendor) | ✅ (VendorApplicant) |

## Key Features

### ✅ **What Works Correctly:**

1. **Complete Data Storage**: All user types have their details properly stored
2. **Role Assignment**: Users are assigned appropriate roles
3. **Business Details**: Vendors and suppliers get business-specific records
4. **Status Management**: Proper status tracking (pending/approved/rejected)
5. **Relationship Integrity**: All foreign key relationships are maintained

### 🔧 **Registration Form Features:**

1. **Dynamic Fields**: Vendor-specific fields show/hide based on role selection
2. **Validation**: Proper form validation for all fields
3. **User Experience**: Clear field labels and optional/required indicators

### 📧 **Email Notifications:**

1. **Vendor Approval**: Vendors receive email with login credentials when approved
2. **Application Status**: Applicants can check their application status

## Testing Results

The system has been tested and verified to:

- ✅ Create User records for all user types
- ✅ Create Vendor records with business details
- ✅ Create Supplier records with business details
- ✅ Create DairyFarm records for suppliers
- ✅ Maintain proper relationships between tables
- ✅ Handle both direct registration and application approval processes

## Usage Examples

### Creating a Vendor via Registration:
```php
// User fills registration form with vendor role
// System automatically creates:
// 1. User record
// 2. Vendor record with business details
// 3. Role assignment
```

### Creating a Vendor via Application:
```php
// User submits application form
// System creates VendorApplicant record
// Admin approves application
// System creates User + Vendor records
```

### Accessing User Details:
```php
// Get vendor details
$user = User::find(1);
$vendor = $user->vendor; // Returns Vendor model with business details

// Get supplier details
$supplier = $user->supplier; // Returns Supplier model with business details

// Get dairy farm for supplier
$dairyFarm = $user->supplier->dairyFarms; // Returns DairyFarm models
```

## Conclusion

The registration process now properly creates all necessary records for each user type, ensuring that:

1. **Authentication** is handled by the Users table
2. **Business details** are stored in appropriate tables
3. **Relationships** are properly maintained
4. **Data integrity** is preserved throughout the process

All user types now have their details properly stored in their respective tables during registration! 🎉 