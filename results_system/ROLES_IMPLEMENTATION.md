# Role-Based Access Control System Implementation

## Overview
The Results Management System now implements a comprehensive role-based access control (RBAC) system with 8 distinct user roles, each with specific responsibilities and permissions.

## User Roles & Responsibilities

### 1. **Administrator**
**Responsibilities:**
- Creating and managing user accounts
- Assigning system roles and permissions
- Managing system configurations
- Monitoring system activities
- Maintaining the system database

**Dashboard Route:** `/admin/dashboard`
**Accessible Features:**
- User management (`/admin/users`)
- Audit logs (`/admin/audit-logs`)

---

### 2. **Lecturers**
**Responsibilities:**
- Uploading student marks for their courses
- Editing results before submission
- Submitting results for departmental review
- Viewing class performance reports

**Dashboard Route:** `/lecturer/dashboard`
**Accessible Features:**
- Upload results (`/lecturer/results/create`)
- View & manage results (`/lecturer/results`)
- Submit results for HOD review
- Edit draft results

---

### 3. **Head of Department (HOD)**
**Responsibilities:**
- Reviewing submitted results
- Approving or rejecting results for corrections
- Forwarding approved results to the faculty or school level
- Monitoring departmental academic performance

**Dashboard Route:** `/hod/dashboard`
**Accessible Features:**
- Review submissions (`/hod/submissions`)
- Approve results (`/hod/results/{result}/approve`)
- Reject results with feedback (`/hod/results/{result}/reject`)

---

### 4. **Finance Department**
**Responsibilities:**
- Verifying student tuition payment status
- Clearing students with no outstanding balances
- Flagging students with pending fees
- Updating financial clearance records

**Dashboard Route:** `/finance/dashboard`
**Accessible Features:**
- View clearances (`/finance/clearances`)
- Manage student financial status (`/finance/students`)
- Clear students for financial obligations

---

### 5. **Library Department**
**Responsibilities:**
- Verifying return of library books
- Clearing students with no outstanding materials
- Recording library clearance status

**Dashboard Route:** `/library/dashboard`
**Accessible Features:**
- View clearances (`/library/clearances`)
- Manage student library status (`/library/students`)
- Mark students as library cleared

---

### 6. **Academic Registrar**
**Responsibilities:**
- Compiling results from different departments
- Verifying accuracy of results
- Preparing results for senate review
- Managing the final stage of results publication

**Dashboard Route:** `/registrar/dashboard`
**Accessible Features:**
- View approved results (`/registrar/results`)
- Compile results (`/registrar/results/compile`)
- View compiled results (`/registrar/compiled-results`)

---

### 7. **University Senate**
**Responsibilities:**
- Reviewing compiled academic results presented by the Academic Registrar
- Approving or recommending corrections to examination results
- Authorizing the official release of results
- Approving graduation lists for eligible students

**Dashboard Route:** `/senate/dashboard`
**Accessible Features:**
- Review results (`/senate/results`)
- Approve results (`/senate/results/{result}/approve`)
- Reject results (`/senate/results/{result}/reject`)
- Publish approved results to students

---

### 8. **Students**
**Responsibilities:**
- Logging into the student portal
- Viewing their cleared and approved results
- Payment of tuition fees
- Tracking their tuition clearance status

**Dashboard Route:** `/student/dashboard`
**Accessible Features:**
- View examination results (`/student/results`)
- Check clearance status (`/student/clearance-status`)
- View finance and library clearance status

---

## Technical Implementation

### Routes & Middleware
All role-specific routes are protected using the `role:role_name` middleware:

```php
Route::middleware('role:lecturer')->prefix('lecturer')->name('lecturer.')->group(function () {
    // Lecturer-specific routes
});
```

### Dynamic Dashboard Routing
The main dashboard route (`/dashboard`) automatically redirects users to their role-specific dashboard:

```php
Route::get('/dashboard', function () {
    $user = auth()->user();
    
    return match($user->role) {
        User::ROLE_ADMIN    => redirect()->route('admin.dashboard'),
        User::ROLE_LECTURER => redirect()->route('lecturer.dashboard'),
        // ... other roles
    };
})->name('dashboard');
```

### Authorization Policies
The `ResultPolicy` class handles fine-grained authorization:
- Lecturers can only edit their own draft results
- HODs can only review results from their department
- Registrars can compile approved results
- Senate can approve compiled results
- Students can only view their published results

### Middleware Protection
- **RoleMiddleware** (`App\Http\Middleware\RoleMiddleware`) - Protects routes based on user role
- **CheckRole** (`App\Http\Middleware\CheckRole`) - Additional role verification

### User Model Helpers
The User model includes helper methods for easy role checking:

```php
$user->isAdmin();
$user->isLecturer();
$user->isHod();
$user->isFinance();
$user->isLibrary();
$user->isRegistrar();
$user->isSenate();
$user->isStudent();
```

### Database Schema
Users table includes:
- `role` (enum) - One of the 8 role constants
- `department_id` - for lecturers, HODs, and students
- `student_number` - unique identifier for students
- `is_active` - account activation status

### Result Status Workflow
Results flow through a defined status workflow:
1. **draft** - Initial state created by lecturer
2. **submitted** - Lecturer submits for HOD review
3. **hod_approved** / **hod_rejected** - HOD action
4. **compiled** - Registrar compiles approved results
5. **senate_approved** / **senate_rejected** - Senate action
6. **published** - Final state, visible to students

## Test Credentials

Default password for all seeded users: `password`

### Available Test Accounts:
- **Admin**: admin@university.ac.ug
- **Lecturer**: j.nakato@university.ac.ug
- **HOD**: d.ssali@university.ac.ug
- **Finance**: a.namukasa@university.ac.ug
- **Library**: r.okello@university.ac.ug
- **Registrar**: m.apio@university.ac.ug
- **Senate**: senate@university.ac.ug

## File Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── AdminController.php
│   │   ├── LecturerController.php
│   │   ├── HodController.php
│   │   ├── FinanceController.php
│   │   ├── LibraryController.php
│   │   ├── RegistrarController.php
│   │   ├── SenateController.php
│   │   └── StudentController.php
│   └── Middleware/
│       ├── CheckRole.php
│       └── RoleMiddleware.php
├── Models/
│   └── User.php (with role helpers)
└── Policies/
    └── ResultPolicy.php

routes/
└── web.php (role-based route groups)

resources/views/
├── admin/
│   ├── dashboard.blade.php
│   ├── users.blade.php
│   └── create-user.blade.php
├── lecturer/
│   ├── dashboard.blade.php
│   ├── results.blade.php
│   └── create-result.blade.php
├── hod/
│   ├── dashboard.blade.php
│   └── submissions.blade.php
├── finance/
│   ├── dashboard.blade.php
│   └── clearances.blade.php
├── library/
│   ├── dashboard.blade.php
│   └── clearances.blade.php
├── registrar/
│   ├── dashboard.blade.php
│   └── compiled-results.blade.php
├── senate/
│   ├── dashboard.blade.php
│   └── results.blade.php
└── student/
    ├── dashboard.blade.php
    └── results.blade.php
```

## How to Use

### Login & Dashboard Routing
1. Visit `http://localhost:8000/login`
2. Log in with any test credentials (e.g., `admin@university.ac.ug` / `password`)
3. You'll be automatically redirected to your role-specific dashboard

### Create a New User (Admin Only)
1. Login as admin
2. Navigate to `/admin/users/create`
3. Fill in user details and assign a role
4. Click "Create User"

### Upload Results (Lecturer Only)
1. Login as lecturer
2. Navigate to `/lecturer/results/create`
3. Upload marks for your courses
4. Submit for HOD review

### Review Results (HOD Only)
1. Login as HOD
2. Navigate to `/hod/submissions`
3. Review lecturer submissions
4. Approve or reject with feedback

## Security Features

✅ Role-based access control on all routes
✅ Authorization policies for data access
✅ Password hashing and security
✅ User activation/deactivation
✅ Audit logging support
✅ Admin-only user management
✅ Department-scoped HOD permissions
✅ Student result privacy

## Future Enhancements

- [ ] Add email notifications for role actions
- [ ] Implement result publication batch processing
- [ ] Add GPA calculation and transcript generation
- [ ] Create CSV import/export for bulk operations
- [ ] Add workflow approval chains
- [ ] Implement result appeals system
- [ ] Add graduation list automation
