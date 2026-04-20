<?php

namespace Database\Seeders;

use App\Models\AcademicSession;
use App\Models\Course;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Faculties & Departments ───────────────────────────────
        // Faculty of Science & Technology
        $faculty = Faculty::create(['name' => 'Faculty of Science & Technology', 'code' => 'FST']);

        $dept = Department::create([
            'name'       => 'Computer Science',
            'code'       => 'CS',
            'faculty_id' => $faculty->id,
        ]);

        // Faculty of Business Administration and Management
        $business_faculty = Faculty::create(['name' => 'Faculty of Business Administration and Management', 'code' => 'FBAM']);

        $business_dept = Department::create([
            'name'       => 'Business Administration',
            'code'       => 'BA',
            'faculty_id' => $business_faculty->id,
        ]);

        // ── Active academic session ───────────────────────────────
        $session = AcademicSession::create([
            'name'       => '2025/2026',
            'semester'   => 2,
            'start_date' => '2026-02-03',
            'end_date'   => '2026-05-16',
            'is_active'  => true,
        ]);

        // ── System Administrator ─────────────────────────────────
        // Overall system admin with access to all faculties
        User::create([
            'name'       => 'System Administrator',
            'email'      => 'admin@umu.ac.ug',
            'password'   => Hash::make('123456789'),
            'role'       => User::ROLE_ADMIN,
            'faculty_id' => null,
            'is_active'  => true,
        ]);

        // ── Faculty Admins ───────────────────────────────────────
        // Science Faculty Administrator
        User::create([
            'name'       => 'Science Faculty Administrator',
            'email'      => 'scienceadmin@umu.ac.ug',
            'password'   => Hash::make('123456789'),
            'role'       => User::ROLE_ADMIN,
            'faculty_id' => $faculty->id,
            'is_active'  => true,
        ]);

        // Business Faculty Administrator
        User::create([
            'name'       => 'Business Faculty Administrator',
            'email'      => 'bamadmin@umu.ac.ug',
            'password'   => Hash::make('123456789'),
            'role'       => User::ROLE_ADMIN,
            'faculty_id' => $business_faculty->id,
            'is_active'  => true,
        ]);

        // ── Lecturer (Dr. Jane Nakato - Computer Science) ────────
        $lecturer = User::create([
            'name'          => 'Dr. Jane Nakato',
            'email'         => 'j.nakato@umu.ac.ug',
            'password'      => Hash::make('123456789'),
            'role'          => User::ROLE_LECTURER,
            'department_id' => $dept->id,
            'is_active'     => true,
        ]);

        // ── Lecturer (Dr. Simon Livingstone - Business Admin) ────
        $lecturer2 = User::create([
            'name'          => 'Dr Simon Livingstone',
            'email'         => 's.livingston@umu.ac.ug',
            'password'      => Hash::make('123456789'),
            'role'          => User::ROLE_LECTURER,
            'department_id' => $business_dept->id,
            'is_active'     => true,
        ]);

        // ── Additional Lecturers (Computer Science) ─────────────────
        $lecturer3 = User::create([
            'name'          => 'Dr. Alice Katusiime',
            'email'         => 'a.katusiime@umu.ac.ug',
            'password'      => Hash::make('123456789'),
            'role'          => User::ROLE_LECTURER,
            'department_id' => $dept->id,
            'is_active'     => true,
        ]);

        $lecturer4 = User::create([
            'name'          => 'Prof. Robert Musoke',
            'email'         => 'r.musoke@umu.ac.ug',
            'password'      => Hash::make('123456789'),
            'role'          => User::ROLE_LECTURER,
            'department_id' => $dept->id,
            'is_active'     => true,
        ]);

        $lecturer5 = User::create([
            'name'          => 'Dr. Patricia Nsubuga',
            'email'         => 'p.nsubuga@umu.ac.ug',
            'password'      => Hash::make('123456789'),
            'role'          => User::ROLE_LECTURER,
            'department_id' => $dept->id,
            'is_active'     => true,
        ]);

        // ── Additional Lecturers (Business Administration) ──────────
        $lecturer6 = User::create([
            'name'          => 'Prof. James Kiyingi',
            'email'         => 'j.kiyingi@umu.ac.ug',
            'password'      => Hash::make('123456789'),
            'role'          => User::ROLE_LECTURER,
            'department_id' => $business_dept->id,
            'is_active'     => true,
        ]);

        $lecturer7 = User::create([
            'name'          => 'Dr. Susan Mwesigwa',
            'email'         => 's.mwesigwa@umu.ac.ug',
            'password'      => Hash::make('123456789'),
            'role'          => User::ROLE_LECTURER,
            'department_id' => $business_dept->id,
            'is_active'     => true,
        ]);

        $lecturer8 = User::create([
            'name'          => 'Dr. Andrew Okwir',
            'email'         => 'a.okwir@umu.ac.ug',
            'password'      => Hash::make('123456789'),
            'role'          => User::ROLE_LECTURER,
            'department_id' => $business_dept->id,
            'is_active'     => true,
        ]);

        // ── HOD (Computer Science) ────────────────────────────────
        $hod = User::create([
            'name'          => 'Prof. David Ssali',
            'email'         => 'd.ssali@umu.ac.ug',
            'password'      => Hash::make('123456789'),
            'role'          => User::ROLE_HOD,
            'department_id' => $dept->id,
            'faculty_id'    => $faculty->id,
            'is_active'     => true,
        ]);

        $dept->update(['hod_id' => $hod->id]);

        // ── HOD (Business Administration) ──────────────────────────
        $hod2 = User::create([
            'name'          => 'Dr. Rebecca Ouma',
            'email'         => 'r.ouma@umu.ac.ug',
            'password'      => Hash::make('123456789'),
            'role'          => User::ROLE_HOD,
            'department_id' => $business_dept->id,
            'faculty_id'    => $business_faculty->id,
            'is_active'     => true,
        ]);

        $business_dept->update(['hod_id' => $hod2->id]);

        // ── Finance ───────────────────────────────────────────────
        User::create([
            'name'      => 'Alice Namukasa',
            'email'     => 'a.namukasa@umu.ac.ug',
            'password'  => Hash::make('123456789'),
            'role'      => User::ROLE_FINANCE,
            'is_active' => true,
        ]);

        // ── Registrar ─────────────────────────────────────────────
        User::create([
            'name'      => 'Mary Apio',
            'email'     => 'm.apio@umu.ac.ug',
            'password'  => Hash::make('123456789'),
            'role'      => User::ROLE_REGISTRAR,
            'is_active' => true,
        ]);

        // ── Senate ────────────────────────────────────────────────
        User::create([
            'name'      => 'Senate Office',
            'email'     => 'senate@umu.ac.ug',
            'password'  => Hash::make('123456789'),
            'role'      => User::ROLE_SENATE,
            'is_active' => true,
        ]);

        // ── Students (Computer Science) ───────────────────────────
        $students = [
            ['name' => 'Brian Kato',    'number' => 'S2400001'],
            ['name' => 'Grace Nambi',   'number' => 'S2400002'],
            ['name' => 'Peter Ochieng', 'number' => 'S2400003'],
        ];

        $cs_students = [];
        foreach ($students as $s) {
            $cs_students[] = User::create([
                'name'           => $s['name'],
                'email'          => strtolower(str_replace(' ', '.', $s['name'])) . '@stud.umu.ac.ug',
                'password'       => Hash::make('123456789'),
                'role'           => User::ROLE_STUDENT,
                'student_number' => $s['number'],
                'department_id'  => $dept->id,
                'is_active'      => true,
            ]);
        }

        // ── Students (Business Administration) ────────────────────
        $ba_students_data = [
            ['name' => 'Sarah Kyambadde',  'number' => 'S2400004'],
            ['name' => 'Michael Kaggwa',   'number' => 'S2400005'],
            ['name' => 'Jennifer Mukama',  'number' => 'S2400006'],
        ];

        $ba_students = [];
        foreach ($ba_students_data as $s) {
            $ba_students[] = User::create([
                'name'           => $s['name'],
                'email'          => strtolower(str_replace(' ', '.', $s['name'])) . '@stud.umu.ac.ug',
                'password'       => Hash::make('123456789'),
                'role'           => User::ROLE_STUDENT,
                'student_number' => $s['number'],
                'department_id'  => $business_dept->id,
                'is_active'      => true,
            ]);
        }

        // ── Courses (Computer Science - Dr. Nakato) ───────────────
        Course::create([
            'name'          => 'Introduction to Programming',
            'code'          => 'CS101',
            'credit_units'  => 3,
            'department_id' => $dept->id,
            'lecturer_id'   => $lecturer->id,
        ]);

        Course::create([
            'name'          => 'Data Structures & Algorithms',
            'code'          => 'CS201',
            'credit_units'  => 4,
            'department_id' => $dept->id,
            'lecturer_id'   => $lecturer->id,
        ]);

        // ── Courses (Business Administration - Dr. Livingstone) ───
        Course::create([
            'name'          => 'Principles of Management',
            'code'          => 'BA101',
            'credit_units'  => 3,
            'department_id' => $business_dept->id,
            'lecturer_id'   => $lecturer2->id,
        ]);

        Course::create([
            'name'          => 'Business Finance',
            'code'          => 'BA201',
            'credit_units'  => 4,
            'department_id' => $business_dept->id,
            'lecturer_id'   => $lecturer2->id,
        ]);

        Course::create([
            'name'          => 'Strategic Management',
            'code'          => 'BA301',
            'credit_units'  => 4,
            'department_id' => $business_dept->id,
            'lecturer_id'   => $lecturer2->id,
        ]);

        $this->command->info('Seeded successfully. Default password for all users: password');
    }
}
