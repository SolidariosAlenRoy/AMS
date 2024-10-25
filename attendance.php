<?php
session_start();
require 'db.php';

// Initialize variables
$section_id = null;
$subject_id = null;
$year_level = null; // Add year level variable

// Display students based on selected section, subject, and year level
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $section_id = $_POST['section'];
    $subject_id = $_POST['subject'];
    $year_level = $_POST['year_level']; // Capture year level
}
?>

    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Attendance</title>
        <!-- FontAwesome for icons -->
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/5.11.3/main.min.js"></script>
        <!-- Include jQuery -->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <!-- Include DataTables CSS -->
        <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
        <!-- Include DataTables JS -->
        <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
        <script>
        $(document).ready(function() {
            // Initialize DataTable
            $('#attendanceTable').DataTable({
                // Optional configurations can go here
                "paging": true,
                "searching": true,
                "ordering": true
            });
        });
</script>

<style> 
body, h2, h4, p, ul {
    margin: 0;
    padding: 0;
}

.container {
    display: flex;
    height: 100vh;
}

/* Main content area */
.main-content {
    flex: 1;
    padding: 20px;
    background-color: #fff;
}

h1 {
    color: #030303;
    font-family: 'Times New Roman', Times, serif;
    font-size: 24px;
    margin-bottom: 20px;
}

.form-container {
    margin-top: 20px; 
}

.row {
    display: flex; 
    justify-content: space-between; 
    align-items: center; 
    margin-bottom: 20px; 
}

.dropdown-item {
    flex: 1; 
    margin-right: 10px; 
}

.dropdown-item:last-child {
    margin-right: 0; 
}

label {
    font-weight: bold;
    margin-bottom: 5px; 
    display: block;
    font-size: 14px; 
}

select {
    width: 90%; 
    padding: 8px; 
    border: 1px solid #ced4da;
    border-radius: 4px;
    font-size: 14px;
    background-color: #fff;
    color: #333;
}

.load-button {
    background-color: #007bff; 
    color: #fff; 
    padding: 8px; 
    border: 1px solid #ced4da; 
    border-radius: 4px; 
    cursor: pointer; 
    flex-shrink: 0; 
    height: 38px; 
    font-size: 14px;
    width: 90%; 
}

.load-button:hover {
    background-color: #0056b3; 
}

/* DataTable Custom Styles */
.dataTables_wrapper {
    margin: 20px auto; 
    max-width: 1300px; 
    border: 1px solid #ced4da; 
    border-radius: 8px;
    overflow: hidden; 
    background-color: #f8f9fa; 
    box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1); 
    padding: 20px; 
}

table.dataTable {
    border-collapse: collapse; 
    width: 100%;
}

table.dataTable thead th {
    background-color: #007bff; 
    color: #ffffff; 
    font-weight: bold; 
    text-align: center; 
    padding: 10px; 
}

table.dataTable tbody tr {
    transition: background-color 0.3s; 
}

table.dataTable tbody tr:hover {
    background-color: #f1f1f1; 
}

table.dataTable tbody td {
    padding: 10px; 
    border-top: 1px solid #ced4da; 
}

button {
    width: 100%;
    margin-top: 10px;
}
/* Sidebar styles */
.sidebar {
    width: 200px; 
    background-color: #e0e0e0;
    padding: 20px;
}

.sidebar h4 {
    color: black; 
    margin-bottom: 20px;
    text-align: center;
}

.header-content {
    display: flex; 
    justify-content: space-between; 
    align-items: center; 
    border-bottom: 1px solid #ced4da; 
    padding-bottom: 10px; 
    margin-bottom: 10px; 
}

/* Search Bar Styles */
.search-bar {
    display: flex;
    align-items: center; 
}

.search-input {
    flex-grow: 1; 
    padding: 10px; 
    border: 1px solid #ced4da; 
    border-radius: 5px; 
    width: 300px;
    height: 20px;
    margin-right: 5px;
}

.search-button {
    background-color: #007bff; 
    color: #fff; 
    border: none;
    padding: 10px 15px; 
    border-radius: 5px; 
    cursor: pointer;
}

.search-button i {
    margin: 0;
}

/* Profile Bar */
.profile-bar {
    display: flex; 
    align-items: center; 
    margin-left: 20px; 
    border-left: 1px solid #ced4da; 
    padding-left: 20px; 
}

.profile-picture {
    width: 40px; 
    height: 40px;
    border-radius: 50%; 
    margin-right: 10px; 
}

.profile-info {
    text-align: left; 
}

.profile-name {
    font-size: 16px; 
    margin: 0; 
}

.profile-role {
    font-size: 12px;
    color: #6c757d;
}

.nav {
    margin-top: 20px;
}

.nav ul {
    list-style: none; 
}

.nav ul li {
    margin-bottom: 15px;
}

.nav ul li a {
    text-decoration: none; 
    color: #343a40; 
}

.nav ul li a:hover {
    color: #007bff; 
}

.logo {
    width: 100%; 
    max-width: 180px; 
    margin-bottom: 20px; 
    display: block; 
    margin-left: auto; 
    margin-right: auto; 
    border-radius: 100px;
}

.main-content {
    flex: 1;
    padding: 20px;
    background-color: #fff; 
}

/* Header styles */
.header {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

/*Button Styles*/
.button-container {
    display: flex;                
    justify-content: flex-start;  
    margin-top: 10px;             
}

.button-container button {
    padding: 10px 20px;           
    font-size: 16px;              
    border: none; 
    border-radius: 5px;  
    cursor: pointer;              
    transition: background-color 0.3s ease; 
    margin-right: 10px;          
}

.button-container button.btn-primary {
    background-color: #007bff;    
    color: white;                  
}

.button-container button.btn-primary:hover {
    background-color: #0056b3;   
}

.button-container button.btn-secondary {
    background-color: #6c757d;    
    color: white;                  
}

.button-container button.btn-secondary:hover {
    background-color: #5a6268;   
}

.button-container button:focus {
    outline: none;                
    box-shadow: 0 0 5px rgba(0, 123, 255, 0.5); 
}

</style>
    </head>
    <body>
    <div class="container">
        <!-- Sidebar -->
        <aside class="sidebar">
    <img src="image/classtrack.png" alt="Logo" class="logo"> 
    <h4 class="text-primary"><i class=""></i>CLASS TRACK</h4>
    <nav class="nav">
        <ul>
            <li><a href="teacherint.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a href="vclasslist.php"><i class="fas fa-list-alt"></i> View Class List</a></li>
            <li><a href="attendance.php"><i class="fas fa-user-check"></i> Take Attendance</a></li>
            <li><a href="view_class_attendance.php"><i class="fas fa-eye"></i> View Class Attendance</a></li>
            <li><a href="view_student_attendance.php"><i class="fas fa-user-graduate"></i> View Student Attendance</a></li>
            <li><a href="today_attendance.php"><i class="fas fa-calendar-day"></i> Today's Attendance Report</a></li>
            <li><a href="absent_notification.php"><i class="fas fa-envelope"></i> Generate E-mail</a></li>
        </ul>
    </nav>
</aside>

            <!-- Main Content -->
            <main class="main-content">
                <div class="header">
                    <h1>Student Attendance</h1>
                    <div class="header-content">
                        <div class="search-bar">
                            <input type="text" placeholder="Search..." class="search-input">
                            <button class="search-button"><i class="fas fa-search"></i></button>
                        </div>
                        <div class="profile-bar">
                            <img src="image/profile.png" alt="Profile Picture" class="profile-picture">
                            <div class="profile-info">
                                <h5 class="profile-name">Name</h5>
                                <p class="profile-role">Teacher</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- HTML Form for Attendance -->
                <div class="form-container">
    <form method="POST" action="attendance.php">
        <!-- First row for Year Level and Section -->
        <div class="row">
            <div class="dropdown-item">
                <label for="year_level">Select Year Level:</label>
                <select name="year_level" required>
                    <option value="">--select--</option>
                    <?php
                    $year_level_query = "SELECT DISTINCT year_level FROM students";
                    $year_levels = $conn->query($year_level_query);
                    while ($year = $year_levels->fetch_assoc()) {
                        echo "<option value='{$year['year_level']}'>{$year['year_level']}</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="dropdown-item">
                <label for="section">Select Section:</label>
                <select name="section" required>
                    <option value="">--select--</option>
                    <?php
                    $section_query = "SELECT id, section_name FROM sections";
                    $sections = $conn->query($section_query);
                    while ($section = $sections->fetch_assoc()) {
                        echo "<option value='{$section['id']}'>{$section['section_name']}</option>";
                    }
                    ?>
                </select>
            </div>
        </div>


<!-- Second row for Subject and Load Students button -->
<div class="row">
    <div class="dropdown-item">
        <label for="subject">Select Subject:</label>
        <select name="subject" required>
            <option value="">--select--</option>
            <?php
            $subject_query = "SELECT id, subject_name FROM subjects";
            $subjects = $conn->query($subject_query);
            while ($subject = $subjects->fetch_assoc()) {
                echo "<option value='{$subject['id']}'>{$subject['subject_name']}</option>";
            }
            ?>
        </select>
    </div>
    <div class="dropdown-item"> <!-- Add div for button for consistency -->
        <button type="submit" class="load-button">Load Students</button>
    </div>
</div>

<div>
    <!-- Empty table structure -->
    <table id="attendanceTable" class="display">
        <thead>
            <tr>
                <th>Student Name</th>
                <th>Year Level</th>
                <th>Section</th>
                <th>Subject</th>
                <th>Attendance</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if (isset($section_id, $subject_id, $year_level)) {
                // Prepare statement to prevent SQL injection
                $subject_query = "SELECT subject_name FROM subjects WHERE id = ?";
                $stmt_subject = $conn->prepare($subject_query);
                $stmt_subject->bind_param('i', $subject_id);
                $stmt_subject->execute();
                $stmt_subject->bind_result($subject_name);
                $stmt_subject->fetch();
                $stmt_subject->close();

                // Query to get the students in the selected section and year level
                $students_query = "
                    SELECT s.id, s.name, s.year_level, sec.section_name 
                    FROM students s 
                    JOIN sections sec ON s.section_id = sec.id 
                    WHERE sec.id = ? AND s.year_level = ?";
                $stmt_students = $conn->prepare($students_query);
                $stmt_students->bind_param('is', $section_id, $year_level);
                $stmt_students->execute();
                $students = $stmt_students->get_result();

                if ($students->num_rows > 0) {
                    while ($row = $students->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>{$row['name']}</td>";
                        echo "<td>{$row['year_level']}</td>";
                        echo "<td>{$row['section_name']}</td>";
                        echo "<td>$subject_name</td>";
                        echo "<td>
                                <select name='attendance[{$row['id']}]' required>
                                    <option value=''>Select Attendance</option>
                                    <option value='Present'>Present</option>
                                    <option value='Absent'>Absent</option>
                                    <option value='Late'>Late</option>
                                </select>
                              </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>No students found in the selected section and year level.</td></tr>";
                }
            } else {
                // Show empty rows until the section and year level are selected
                echo "<tr><td colspan='5'>Please select a section, year level, and subject to load students.</td></tr>";
            }
            ?>
        </tbody>
    </table>

    <!-- Buttons below the search box of the DataTable -->
    <div class="button-container" style="margin-top: 10px;">
        <button type="submit" name="save_attendance" class="btn btn-primary">Save Attendance</button>
        <button type="button" name="send_email" class="btn btn-secondary" onclick="sendEmail()">Send Email</button>
    </div>
</div>

<script>
    function sendEmail() {
        // Add your email sending logic here
        alert('Email sending functionality to be implemented.');
    }
</script>

            </main>
        </div>
    </body>
    </html>