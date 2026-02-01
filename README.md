# Online Course Management System

A web-based course management system built with PHP and MySQL that allows administrators to manage courses, instructors, students, and enrollments.

---

## Login Credentials

### Default Admin Account
- **Username:** `admin`
- **Password:** `password123`

> **Note:** Login credentials are stored in the `users` table in plain text format (not hashed). Ensure to modify credentials in production environments.

---

## Setup Instructions

### Prerequisites
- **XAMPP** (or any PHP/Apache/MySQL stack)
- **PHP 7.4+**
- **MySQL 5.7+**
- **Composer** (for dependency management)

### Installation Steps

1. **Clone/Extract the project:**
   ```bash
   cd c:\xampp\htdocs
   # Extract the project files here
   ```

2. **Create the database:**
   - Open phpMyAdmin (http://localhost/phpmyadmin)
   - Create a new database named `course_management`
   - Import the database tables (see Database Setup section below)

3. **Configure database connection:**
   - Edit [db.php](db.php)
   - Update credentials if needed:
     ```php
     $host = "localhost";
     $dbname = "course_management";
     $user = "root";
     $pass = "";
     ```

4. **Install dependencies:**
   ```bash
   composer install
   ```

5. **Access the application:**
   - Open `https://student.bicnepal.edu.np/~np02cs4a240094/index.php` in your browser
   - You will be redirected to the login page
   - Login with the credentials provided above

### Database Setup

The application requires the following tables:

#### Users Table
```sql
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);

INSERT INTO users (username, password) VALUES ('admin', 'password123');
```

#### Courses Table
```sql
CREATE TABLE courses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    category VARCHAR(100),
    level VARCHAR(50),
    instructor_id INT,
    FOREIGN KEY (instructor_id) REFERENCES instructors(id)
);
```

#### Instructors Table
```sql
CREATE TABLE instructors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255)
);
```

#### Students Table
```sql
CREATE TABLE students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255)
);
```

#### Enrollments Table
```sql
CREATE TABLE enrollments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    course_id INT NOT NULL,
    FOREIGN KEY (student_id) REFERENCES students(id),
    FOREIGN KEY (course_id) REFERENCES courses(id)
);
```

---

## Features Implemented

### 1. **User Authentication**
   - Login system with session management
   - Session timeout and security checks
   - Logout functionality
   - User IP and user agent tracking

### 2. **Course Management**
   - View all courses with instructor information
   - Add new courses
   - Edit existing courses
   - Delete courses
   - Assign instructors to courses
   - Search/filter courses by category, level, or instructor

### 3. **Instructor Management**
   - View all instructors
   - Add new instructors
   - Edit instructor details
   - Delete instructors
   - View courses assigned to each instructor
   - AJAX-based instructor search/autocomplete

### 4. **Student Management**
   - View all students
   - Add new students
   - Edit student information
   - Delete students

### 5. **Enrollment Management**
   - View all enrollments
   - Enroll students in courses
   - Unenroll students from courses
   - View student-course relationships

### 6. **Search & Filter**
   - Real-time search functionality for courses
   - AJAX-based autocomplete for instructors
   - Filter by category, level, and instructor name

### 7. **UI/UX Features**
   - Responsive HTML design with CSS styling
   - Dark mode toggle for courses page
   - Professional table layouts
   - Navigation menus for quick access
   - Confirmation dialogs for delete operations
   - Session information display (logged-in user)

### 8. **Templating Support**
   - Blade templating engine integration via Composer
   - Example Blade templates included in `/views` directory

---

## File Structure

```
OnlineCourseManagementSystem/
├── index.php                      # Main course management dashboard
├── login.php                      # User login page
├── logout.php                     # Logout handler
├── session_check.php              # Session validation
├── db.php                         # Database connection
│
├── Courses Management
├── add.php                        # Add new course
├── edit.php                       # Edit course
├── delete.php                     # Delete course
├── check_courses.php              # Course validation
│
├── Instructors Management
├── instructors.php                # View all instructors
├── add_instructor.php             # Add new instructor
├── edit_instructor.php            # Edit instructor
├── delete_instructor.php          # Delete instructor
├── assign_course_to_instructor.php # Assign courses to instructors
├── view_instructor_courses.php    # View instructor's courses
├── check_instructors.php          # Instructor validation
├── ajax_instructor_search.php     # AJAX instructor search
│
├── Students Management
├── students.php                   # View all students
├── add_student.php                # Add new student
├── edit_student.php               # Edit student
├── delete_student.php             # Delete student
│
├── Enrollments Management
├── enrollments.php                # View all enrollments
├── enroll.php                     # Enroll student in course
├── unenroll.php                   # Remove student from course
│
├── Utilities
├── script.js                      # Client-side JavaScript
├── captcha_functions.php          # CAPTCHA functionality
├── blade_config.php               # Blade template configuration
├── check_tables.php               # Database table verification
│
├── Testing & Examples
├── test_add.php                   # Test course addition
├── test_web.php                   # Web testing utilities
├── courses_blade_example.php      # Blade template example
├── ajax_search.php                # AJAX search functionality
│
├── vendor/                        # Composer dependencies
├── views/                         # Blade template files
├── storage/                       # Cache and temporary files
├── composer.json                  # Project dependencies
└── com.webp                       # Background image
```

---

## Known Issues

### 1. **Plain Text Password Storage**
   - Passwords are stored in plain text in the database
   - **Recommended Fix:** Implement password hashing using `password_hash()` and `password_verify()`

### 2. **Session Security**
   - Session timeout is implemented but may not be sufficient for production
   - **Recommended Fix:** Implement token-based authentication or use a more robust session handler

### 3. **Missing Input Validation**
   - Limited server-side validation on some forms
   - **Recommended Fix:** Add comprehensive input validation and sanitization on all forms

### 4. **SQL Injection Risk**
   - While prepared statements are used in most places, ensure all dynamic queries use parameterized statements
   - **Recommended Fix:** Audit all database queries and ensure 100% use of prepared statements

### 5. **CSRF Protection**
   - No CSRF token implementation detected
   - **Recommended Fix:** Implement CSRF tokens on all POST/DELETE operations

### 6. **XSS Vulnerabilities**
   - Output is generally escaped with `htmlspecialchars()`, but should be verified throughout
   - **Recommended Fix:** Ensure all user-generated content is properly escaped before output

### 7. **Error Handling**
   - Generic error messages in some areas
   - **Recommended Fix:** Implement proper logging and user-friendly error pages

### 8. **Missing Delete Confirmations**
   - While some delete operations have confirmation dialogs, ensure all destructive operations are protected
   - **Recommended Fix:** Verify all delete operations require confirmation

### 9. **Database Connection Error Handling**
   - Database connection errors show minimal information
   - **Recommended Fix:** Implement more detailed logging for troubleshooting

### 10. **Missing Data Validation Rules**
   - No email format validation
   - No duplicate email checking for students/instructors
   - **Recommended Fix:** Add comprehensive data validation rules

---

## Troubleshooting

### Issue: Database connection failed
- **Solution:** Verify MySQL is running and credentials in [db.php](db.php) are correct
- Check that the `course_management` database exists

### Issue: Login page redirects indefinitely
- **Solution:** Clear session cookies and restart your browser
- Check [session_check.php](session_check.php) for any issues

### Issue: Courses not displaying
- **Solution:** Verify the courses table exists in the database
- Run [check_tables.php](check_tables.php) to verify all tables

### Issue: AJAX search not working
- **Solution:** Ensure JavaScript is enabled in your browser
- Check browser console for any JavaScript errors

---

## Future Improvements

- [ ] Implement password hashing and secure authentication
- [ ] Add role-based access control (RBAC) - Admin, Instructor, Student roles
- [ ] Implement course content/materials upload
- [ ] Add grading system
- [ ] Implement email notifications
- [ ] Add course completion tracking
- [ ] Implement course prerequisites
- [ ] Add course ratings and reviews
- [ ] Responsive mobile design
- [ ] API endpoints for external integrations

---

## Support & Contact

For issues or questions, please review the code or contact the development team.

---

## License

This project is provided as-is for educational purposes.

---

## Version

**Version:** 1.0.0  
**Last Updated:** January 31, 2026
