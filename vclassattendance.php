<?php
// Assume the necessary database connection is established
session_start();
require 'db.php';

// Initialize variables for attendance records
$attendance_records = [];
$student_attendance_records = [];

// Fetch year levels, sections, students, and subjects from the database
$year_levels_result = $conn->query("SELECT DISTINCT year_level FROM students");
$sections_result = $conn->query("SELECT * FROM sections");
$students_result = $conn->query("SELECT * FROM students");
$subjects_result = $conn->query("SELECT * FROM subjects");

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['view_class_attendance'])) {
        // Code to view class attendance
        $attendance_date = $_POST['attendance_date'];
        $year_level = $_POST['year_level'];
        $section_id = $_POST['section_id'];
        $subject_id = isset($_POST['subject_id']) ? $_POST['subject_id'] : null; // Check if subject_id is set

        $class_attendance_query = "
            SELECT a.attendance_date, s.name AS student_name, a.year_level, sec.section_name, sub.subject_name, a.status AS status
            FROM attendance a
            JOIN students s ON a.student_id = s.id
            JOIN sections sec ON a.section_id = sec.id
            JOIN subjects sub ON a.subject_id = sub.id
            WHERE a.attendance_date = ? AND a.year_level = ? AND a.section_id = ?";
        
        // Add subject_id condition if it exists
        if ($subject_id) {
            $class_attendance_query .= " AND a.subject_id = ?";
        }
        
        $class_attendance_query .= " ORDER BY a.attendance_date DESC";

        if ($stmt = $conn->prepare($class_attendance_query)) {
            if ($subject_id) {
                $stmt->bind_param("ssii", $attendance_date, $year_level, $section_id, $subject_id);
            } else {
                $stmt->bind_param("ssi", $attendance_date, $year_level, $section_id);
            }
            $stmt->execute();
            $attendance_result = $stmt->get_result();
            if ($attendance_result) {
                $attendance_records = $attendance_result->fetch_all(MYSQLI_ASSOC);
            }
            $stmt->close();
        }
    } elseif (isset($_POST['view_student_attendance'])) {
        // Code to handle specific student attendance viewing
        $attendance_date = $_POST['attendance_date'];
        $student_id = $_POST['student'];

        $student_attendance_query = "
            SELECT a.attendance_date, s.name AS student_name, a.year_level, sec.section_name, sub.subject_name, a.status AS status
            FROM attendance a
            JOIN students s ON a.student_id = s.id
            JOIN sections sec ON a.section_id = sec.id
            JOIN subjects sub ON a.subject_id = sub.id
            WHERE a.attendance_date = ? AND a.student_id = ?
            ORDER BY a.attendance_date DESC";

        if ($stmt = $conn->prepare($student_attendance_query)) {
            $stmt->bind_param("si", $attendance_date, $student_id);
            $stmt->execute();
            $student_attendance_result = $stmt->get_result();
            if ($student_attendance_result) {
                $student_attendance_records = $student_attendance_result->fetch_all(MYSQLI_ASSOC);
            }
            $stmt->close();
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Class Attendance</title>
    <!-- FontAwesome for icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="css/dashboard.css" rel="stylesheet"> 
    <style>
/* Reset */
body, h2, h4, p, ul {
    margin: 0;
    padding: 0;
}

/* Container Layout */
.container {
    display: flex;
    height: 100vh;
}

/* Sidebar */
.sidebar {
    width: 250px;
    background-color: #343a40;
    color: #fff;
    padding: 20px;
    display: flex;
    flex-direction: column;
    align-items: center;
}

.sidebar h4 {
    font-size: 26px;
    color: #ffc107;
    margin-bottom: 20px;
    text-align: center;
}

.logo {
    width: 100%;
    max-width: 200px;
    margin: 0 auto 20px;
    border-radius: 100px;
}

.nav ul {
    list-style: none;
    width: 100%;
    padding: 0;
    text-align: left;
}

.nav ul li {
    margin-bottom: 20px;
}

.nav ul li a {
    display: block;
    padding: 10px;
    font-size: 16px;
    color: #adb5bd;
    text-decoration: none;
    border-radius: 5px;
    transition: background-color 0.3s;
}

.nav ul li a:hover {
    background-color: #495057;
    color: #fff;
}

/* Main Content */
.main-content {
    flex: 1;
    padding: 20px;
}

/* Header */
.header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 2px solid #dee2e6;
    padding: 10px 0;
    margin-bottom: 20px;
    gap: 20px;
}

.header h1 {
    color: #36454F;
    font-size: 28px;
    margin: 0;
}

.header-content {
    display: flex;
    align-items: center;
    gap: 20px;
}

/* Search Bar */
.search-bar {
    display: flex;
    align-items: center;
    gap: 10px;
}

.search-input {
    padding: 10px;
    width: 300px;
    height: 20px;
    font-size: 16px;
    border: 1px solid #ced4da;
    border-radius: 5px;
}

.search-button {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 10px 15px;
    height: 40px;
    font-size: 16px;
    background-color: #899499;
    color: #fff;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}

/* Profile Bar */
.profile-bar {
    display: flex;
    align-items: center;
    gap: 10px;
    border-left: 1px solid #ced4da;
    padding-left: 20px;
}

.profile-picture {
    width: 40px;
    height: 40px;
    border-radius: 50%;
}

.profile-info {
    text-align: left;
}

.profile-name {
    font-size: 16px;
    margin: 0;
    color: #333;
}

.profile-role {
    font-size: 12px;
    color: #6c757d;
}

/* Tabs Styling */
.tab {
    display: inline-block;
    padding: 12px 20px;
    margin-right: 10px;
    background-color: #36454F;
    color: #fff;
    font-size: 16px;
    font-weight: bold;
    cursor: pointer;
    border-radius: 5px 5px 0 0;
    transition: background-color 0.3s ease, color 0.3s ease;
    border: 2px solid #6c757d;
}

.tab:hover {
    background-color:  #899499;
}

.tab.active {
    background-color:  #899499;
    color: #ffff;
    border: 1px solid #ddd;
    border-bottom: none;
}

.tab-content {
    display: none;
    padding: 20px;
    background-color: #ffffff;
    border-radius: 0 5px 5px 5px;
    border: 2px solid #6c757d;
    margin-top: -1px; /* Connect content with active tab */
}

.tab-content.active {
    display: block;
}


/* Table */
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

th, td {
    padding: 8px;
    text-align: left;
    border: 1px solid #ddd;
}

th {
    background-color: #708090;
    color: white;
}

    </style>
</head>
<body>
    <div class="container">
        <!-- Sidebar -->
        <aside class="sidebar">
    <img src="image/logo.png" alt="Logo" class="logo"> 
    <h4 class="text-primary"><i class=""></i>CLASS TRACK</h4>
    <nav class="nav">
        <ul>
            <li><a href="teacherint.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a href="vclasslist.php"><i class="fas fa-list-alt"></i> View Class List</a></li>
            <li><a href="attendance.php"><i class="fas fa-user-check"></i> Take Attendance</a></li>
            <li><a href="vclassattendance.php"><i class="fas fa-eye"></i> View Class Attendance</a></li>
            <li><a href="email.php"><i class="fas fa-envelope"></i> Generate E-mail</a></li>
        </ul>
    </nav>
</aside>
        <!-- Main Content -->
        <main class="main-content">
            <div class="header">
                <h1>View Class Attendance</h1>
                <div class="header-content">
                    <div class="search-bar">
                        <input type="text" placeholder="Search..." class="search-input">
                        <button class="search-button"><i class="fas fa-search"></i></button>
                    </div>
                    <div class="profile-bar">
                        <img src="image/profile.png" alt="Profile Picture" class="profile-picture"> <!-- Example profile image -->
                        <div class="profile-info">
                            <h5 class="profile-name">Name</h5>
                            <p class="profile-role">Teacher</p>
                        </div>
                    </div>
                </div>
            </div>

            <div>
    <!-- Tabs -->
    <div class="tab" data-tab="classAttendance">View Class Attendance</div>
    <div class="tab" data-tab="studentAttendance">View Student Attendance</div>
</div>

<!-- Class Attendance Tab Content -->
<div class="tab-content active" id="classAttendance">
                <h3>View Class Attendance</h3>
                <form id="classAttendanceForm" method="POST">
                    <label for="attendanceDate">Date:</label>
                    <input type="date" id="attendanceDate" name="attendance_date" required>

                    <label for="yearLevelDropdown">Year Level:</label>
                    <select name="year_level" id="yearLevelDropdown" required>
                        <option value="">Select Year Level</option>
                        <?php while ($row = $year_levels_result->fetch_assoc()) : ?>
                            <option value="<?= $row['year_level'] ?>"><?= $row['year_level'] ?></option>
                        <?php endwhile; ?>
                    </select>

                    <label for="sectionDropdown">Section:</label>
                    <select name="section_id" id="sectionDropdown" required>
                        <option value="">Select Section</option>
                        <?php while ($row = $sections_result->fetch_assoc()) : ?>
                            <option value="<?= $row['id'] ?>"><?= $row['section_name'] ?></option>
                        <?php endwhile; ?>
                    </select>

                    <label for="subjectDropdown">Subject:</label>
                    <select name="subject_id" id="subjectDropdown" required>
                        <option value="">Select Subject</option>
                        <?php while ($row = $subjects_result->fetch_assoc()) : ?>
                            <option value="<?= $row['id'] ?>"><?= $row['subject_name'] ?></option>
                        <?php endwhile; ?>
                    </select>

                    <button type="submit" name="view_class_attendance">View Attendance</button>
                </form>

                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Student Name</th>
                            <th>Year Level</th>
                            <th>Section</th>
                            <th>Subject</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($attendance_records)): ?>
                            <?php foreach ($attendance_records as $record): ?>
                                <tr>
                                    <td><?= $record['attendance_date'] ?></td>
                                    <td><?= $record['student_name'] ?></td>
                                    <td><?= $record['year_level'] ?></td>
                                    <td><?= $record['section_name'] ?></td>
                                    <td><?= $record['subject_name'] ?></td>
                                    <td><?= $record['status'] ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6">No records found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Student Attendance Tab Content -->
            <div class="tab-content" id="studentAttendance">
                <h3>View Student Attendance</h3>
                <form id="studentAttendanceForm" method="POST">
                    <label for="attendanceDate">Date:</label>
               
     <input type="date" id="attendanceDate" name="attendance_date" required>

                    <label for="studentDropdown">Select Student:</label>
                    <select name="student" id="studentDropdown" required>
                        <option value="">Select Student</option>
                        <?php while ($row = $students_result->fetch_assoc()) : ?>
                            <option value="<?= $row['id'] ?>"><?= $row['name'] ?></option>
                        <?php endwhile; ?>
                    </select>

                    <button type="submit" name="view_student_attendance">View Attendance</button>
                </form>

                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Student Name</th>
                            <th>Year Level</th>
                            <th>Section</th>
                            <th>Subject</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($student_attendance_records)): ?>
                            <?php foreach ($student_attendance_records as $record): ?>
                                <tr>
                                    <td><?= $record['attendance_date'] ?></td>
                                    <td><?= $record['student_name'] ?></td>
                                    <td><?= $record['year_level'] ?></td>
                                    <td><?= $record['section_name'] ?></td>
                                    <td><?= $record['subject_name'] ?></td>
                                    <td><?= $record['status'] ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6">No records found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <script>
        // JavaScript for tab switching and storing active tab
document.querySelectorAll('.tab').forEach(tab => {
    tab.addEventListener('click', function () {
        document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
        document.querySelectorAll('.tab-content').forEach(tc => tc.classList.remove('active'));

        this.classList.add('active');
        document.getElementById(this.dataset.tab).classList.add('active');

        // Store the active tab in session storage
        sessionStorage.setItem('activeTab', this.dataset.tab);
    });
});

// On page load, set the active tab from session storage
window.onload = function() {
    const activeTab = sessionStorage.getItem('activeTab');
    if (activeTab) {
        document.querySelectorAll('.tab').forEach(tab => {
            tab.classList.remove('active');
        });
        document.querySelectorAll('.tab-content').forEach(tc => {
            tc.classList.remove('active');
        });
        document.querySelector(`.tab[data-tab="${activeTab}"]`).classList.add('active');
        document.getElementById(activeTab).classList.add('active');
    }
};

    </script>


    

    
</body>
</html>