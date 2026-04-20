# Quick Start Guide - Role-Based Results System

## System Status ✅

Your Results Management System is now fully configured with an 8-role based access control system.

## Getting Started

### 1. Start the Development Server
```bash
php artisan serve
```
The application will run at: **http://127.0.0.1:8000**

### 2. Login with Test Accounts

The system was seeded with the following test users. All use password: `password`

| Role | Email | Purpose |
|------|-------|---------|
| **Administrator** | admin@university.ac.ug | System management |
| **Lecturer** | j.nakato@university.ac.ug | Upload & manage marks |
| **Head of Department** | d.ssali@university.ac.ug | Review & approve results |
| **Finance Officer** | a.namukasa@university.ac.ug | Student finance clearance |
| **Library Officer** | r.okello@university.ac.ug | Library material clearance |
| **Academic Registrar** | m.apio@university.ac.ug | Compile & prepare results |
| **Senate** | senate@university.ac.ug | Final result approval |

### 3. Test the Role-Based System

#### As Administrator:
1. Login with: `admin@university.ac.ug` / `password`
2. You'll be redirected to `/admin/dashboard`
3. Features available:
   - Manage users (`/admin/users`)
   - Create new users (`/admin/users/create`)
   - View audit logs (`/admin/audit-logs`)

#### As Lecturer:
1. Login with: `j.nakato@university.ac.ug` / `password`
2. You'll be redirected to `/lecturer/dashboard`
3. Features available:
   - **📊 Dashboard**: View stats (draft, submitted, reviewed results)
   - **➕ Add Results**: Enter student marks individually
   - ** Results Listing**: View all results with advanced filtering
     - Search by student name or number
     - Filter by course or status
     - Bulk select and submit multiple results
     - Edit marks before submission
     - Delete draft results
   - **📈 Performance Reports**: Analyze class performance
     - Average marks, pass rates, grade distribution per course
     - Performance insights and recommendations
   - **✅ Submit Results**: Submit individual or bulk results for HOD review
   - **🔒 Workflow**: Draft → Submitted → HOD Review → Published

#### As Head of Department:
1. Login with: `d.ssali@university.ac.ug` / `password`
2. You'll be redirected to `/hod/dashboard`
3. Features available:
   - Review submitted results
   - Approve or reject with feedback
   - Forward to registrar

#### As Finance Officer:
1. Login with: `a.namukasa@university.ac.ug` / `password`
2. You'll be redirected to `/finance/dashboard`
3. Features available:
   - View student clearance requests
   - Mark students as finance-cleared

#### As Library Officer:
1. Login with: `r.okello@university.ac.ug` / `password`
2. You'll be redirected to `/library/dashboard`
3. Features available:
   - View library clearance requests
   - Mark students as library-cleared

#### As Academic Registrar:
1. Login with: `m.apio@university.ac.ug` / `password`
2. You'll be redirected to `/registrar/dashboard`
3. Features available:
   - View approved results
   - Compile results for senate
   - Prepare graduation lists

#### As Senate:
1. Login with: `senate@university.ac.ug` / `password`
2. You'll be redirected to `/senate/dashboard`
3. Features available:
   - Review compiled results
   - Approve or reject results
   - Publish results to students

#### As Student:
1. Create a student account via admin panel
2. Login with student credentials
3. View your published results
4. Check finance & library clearance status

## Key Features Implemented

### ✅ Complete Role-Based Access Control
- 8 distinct user roles with specific permissions
- Route protection with role middleware
- Authorization policies for fine-grained control
- Dynamic role-based dashboard routing

### ✅ Result Workflow Management
- Multi-stage approval process
- Lecturer → HOD → Registrar → Senate workflow
- Status tracking at each stage
- Rejection feedback mechanism

### ✅ Clearance System
- Finance clearance for tuition
- Library clearance for borrowed materials
- Multi-clearance verification before result publication

### ✅ User Management
- Admin can create/edit/delete users
- Role assignment per user
- Department assignment for relevant roles
- Account activation/deactivation

### ✅ Security
- Password hashing
- Role-based middleware protection
- Authorization policies
- Activity logging support

## Database Structure

The system uses these main tables:

| Table | Purpose |
|-------|---------|
| `users` | User accounts with role assignments |
| `results` | Student examination results |
| `courses` | Course information and lecturer assignments |
| `departments` | Department management |
| `finance_clearances` | Student finance clearance records |
| `library_clearances` | Student library clearance records |
| `academic_sessions` | Academic session/semester management |

## Common Tasks

### Creating a New User (Admin Only)
```
1. Login as admin
2. Go to /admin/users/create
3. Fill in: Name, Email, Password, Role, Department (if applicable)
4. Click Create User
```

### Uploading Results (Lecturer)
```
1. Login as lecturer
2. Go to /lecturer/results/create
3. Select course
4. Enter student marks
5. Click Upload
6. Submit for HOD review when ready
```

### Managing Results (Lecturer)
```
1. Login as lecturer
2. Go to /lecturer/results to see all your results
3. Use Search to find by student name or number
4. Use Filters to narrow by:
   - Course
   - Status (Draft, Submitted, HOD Approved, etc.)
5. Edit marks in draft/rejected results by clicking Edit
6. Click Submit to send for HOD review
7. Use checkboxes to bulk-submit multiple results
8. Delete draft results if needed
```

### Viewing Performance Reports (Lecturer)
```
1. Login as lecturer
2. Click "Performance Report" on dashboard or in /lecturer/results
3. View statistics for each course:
   - Total students
   - Average mark
   - Highest and lowest marks
   - Pass rate percentage
   - Grade distribution (A-F breakdown)
4. Read performance insights for each course
```

### Approving Results (HOD)
```
1. Login as HOD
2. Go to /hod/submissions
3. Review each result
4. Click Approve or Reject
5. Add comment if rejecting
```

### Compiling Results (Registrar)
```
1. Login as registrar
2. Go to /registrar/results
3. View all HOD-approved results
4. Click Compile to prepare for Senate
```

### Publishing Results (Senate)
```
1. Login as senate
2. Go to /senate/results
3. Review compiled results
4. Approve all results
5. Click Publish to release to students
```

## Troubleshooting

### 403 Forbidden Error
- This means you don't have permission for that route
- Check your user role and login credentials
- Only visit routes marked for your role

### "Account Deactivated" Message
- Admin must activate your account
- Contact administrator to reactivate

### Results Not Showing
- Lecturer must upload results first
- Results must pass through approval workflow
- Only published results show to students

## Environment Variables

Ensure your `.env` file has:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=results_db
DB_USERNAME=root
DB_PASSWORD=your_password

APP_URL=http://localhost:8000
```

## Support

For issues or questions about the role-based system, refer to:
- [ROLES_IMPLEMENTATION.md](ROLES_IMPLEMENTATION.md) - Detailed documentation
- Database seeders in `database/seeders/DatabaseSeeder.php`
- Controllers in `app/Http/Controllers/`

---

**System Status:** ✅ Ready to Use

**Last Updated:** March 13, 2026

**Version:** 1.0 - Role-Based Access Control
