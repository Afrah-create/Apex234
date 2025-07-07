# Vendor Registration and Application Flow

## Overview

The vendor registration process has been updated to require admin approval before vendors can access the dashboard. This ensures quality control and proper validation of vendor applications.

## New Registration Flow

### 1. User Registration (`/register`)

**When a user selects "Vendor" role:**

1. **User Record Created**: Only the basic user record is created (name, email, password, role)
2. **No Vendor Record**: Vendor business details are NOT created at this stage
3. **Session Data Stored**: Registration data is stored in session for pre-filling the application form
4. **Redirect**: User is redirected to the vendor application form

**Registration Data Stored in Session:**
```php
[
    'name' => $request->name,
    'email' => $request->email,
    'business_name' => $request->business_name ?? '',
    'business_address' => $request->business_address ?? '',
    'phone_number' => $request->phone_number ?? '',
    'tax_id' => $request->tax_id ?? '',
    'business_license' => $request->business_license ?? '',
    'description' => $request->description ?? '',
]
```

### 2. Vendor Application Form (`/vendor/apply`)

**Pre-filled Fields:**
- Name (from registration)
- Email (from registration)
- Phone (from registration)
- Company Name (from registration)
- License Number (from registration)

**Additional Required Fields:**
- Annual Revenue
- Reference
- Compliance Certificate

**Form Submission:**
1. **PDF Generation**: Application data is converted to PDF
2. **Server Validation**: PDF is sent to validation server
3. **VendorApplicant Record**: Created with application data and validation status
4. **Session Cleanup**: Registration data is removed from session
5. **Redirect**: User is redirected to confirmation page

### 3. Admin Approval Process

**Admin Dashboard:**
- Shows pending vendor applications
- Admin can review application details and PDF
- Admin clicks "Approve" button

**Approval Process:**
1. **User Status Update**: User status is set to 'approved'
2. **Vendor Record Creation**: Complete vendor record is created with business details
3. **Email Notification**: Vendor receives email with login credentials
4. **Application Status Update**: VendorApplicant status is set to 'approved'

### 4. Vendor Dashboard Access

**Login Flow:**
1. Vendor logs in with provided credentials
2. System checks for approved vendor application
3. If approved → Redirect to vendor dashboard
4. If pending → Redirect to application status page

## Database Changes

### Tables Involved:

1. **users** - Basic user authentication
2. **vendor_applicants** - Application data and validation status
3. **vendors** - Business details (created only after approval)

### Key Relationships:

```php
// User can have one vendor application
User -> VendorApplicant (one-to-one)

// User can have one vendor record (after approval)
User -> Vendor (one-to-one)

// VendorApplicant -> Vendor (one-to-one, after approval)
```

## Code Changes Made

### 1. RegisteredUserController.php
- **Removed**: Vendor record creation during registration
- **Added**: Session data storage for pre-filling application form
- **Updated**: Redirect to vendor application form

### 2. VendorApplicantController.php
- **Updated**: `create()` method to pre-fill form with session data
- **Updated**: `store()` method to clear session after submission

### 3. AdminVendorApplicantController.php
- **Updated**: `approve()` method to create complete vendor record
- **Enhanced**: Business details mapping from application data

### 4. AuthenticatedSessionController.php
- **Updated**: Login logic to handle unapproved vendors
- **Fixed**: Redirect unapproved vendors to status page

### 5. Vendor Application Form (apply.blade.php)
- **Updated**: Pre-fill fields with registration data
- **Enhanced**: User experience with pre-populated fields

## Benefits of New Flow

1. **Quality Control**: Admin approval ensures only qualified vendors
2. **Data Validation**: Server-side validation of applications
3. **Better UX**: Pre-filled forms reduce user effort
4. **Audit Trail**: Complete application history maintained
5. **Flexibility**: Easy to add more validation steps

## Testing the Flow

### Manual Testing Steps:

1. **Register as Vendor:**
   - Go to `/register`
   - Select "Vendor" role
   - Fill in basic details
   - Submit registration

2. **Complete Application:**
   - Should be redirected to `/vendor/apply`
   - Form should be pre-filled with registration data
   - Fill in additional required fields
   - Submit application

3. **Admin Approval:**
   - Login as admin
   - Go to admin dashboard
   - Find pending vendor application
   - Click "Approve"

4. **Vendor Access:**
   - Vendor receives approval email
   - Vendor can now login and access dashboard

## Error Handling

### Common Scenarios:

1. **No Application Found**: Redirect to application form
2. **Pending Application**: Redirect to status page
3. **Rejected Application**: Show rejection message
4. **Validation Server Error**: Store error status and message

## Future Enhancements

1. **Email Notifications**: Send status updates to applicants
2. **Application Tracking**: Allow vendors to track application progress
3. **Document Upload**: Support for additional document attachments
4. **Application History**: Maintain history of all applications per user 