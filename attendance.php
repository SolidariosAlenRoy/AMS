<?php
session_start();
require 'db.php';

// Initialize variables
$section_id = null;
$subject_id = null;

// Display students based on selected section and subject
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $section_id = $_POST['section'];
    $subject_id = $_POST['subject'];
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
    <!-- FullCalendar CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/5.11.3/main.min.css" rel="stylesheet">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/5.11.3/main.min.js"></script>
    <link href="css/dashboard.css" rel="stylesheet">
    <style>

body {
    font-family: Arial, sans-serif;
    background-color: #f8f9fa;
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

/* Form container to align both forms side by side */
.form-container {
    display: flex;
    justify-content: space-between;
    gap: 20px; /* space between forms */
    margin-top: 20px;
}

/* Form styles */
form {
    flex: 1;
    padding: 20px;
    background-color: #f8f9fa;
    border: 1px solid #ced4da;
    border-radius: 8px;
    box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
}

/* Form labels */
label {
    font-weight: bold;
    margin-bottom: 10px;
    display: block;
    font-size: 16px;
}

/* Dropdown styles */
select {
    width: 100%;
    padding: 10px;
    margin-bottom: 20px;
    border: 1px solid #ced4da;
    border-radius: 4px;
    font-size: 16px;
    background-color: #fff;
    color: #333;
}

select:focus {
    outline: none;
    border-color: #007bff;
    box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
}

/* Button styles */
button {
    background-color: #007bff;
    color: #fff;
    padding: 10px 15px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 16px;
    margin-right: 10px;
    margin-top: 10px;
    width: 100%;
}

button:hover {
    background-color: #0056b3;
}

/* Table styles */
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

th, td {
    border: 1px solid #ced4da;
    padding: 12px;
    text-align: left;
    font-size: 14px;
}

th {
    background-color: #00d4ff;
    font-weight: bold;
}

td select {
    width: 100%;
    padding: 5px;
    font-size: 14px;
    border: 1px solid #ced4da;
    border-radius: 4px;
    background-color: #fff;
}

/* Table row hover effect */
tr:hover {
    background-color: #f1f1f1;
}

/* Responsive design for mobile */
@media (max-width: 768px) {
    .form-container {
        flex-direction: column;
    }

    button {
        width: 100%;
        margin-top: 10px;
    }
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

    </style>
</head>
<body>
    <div class="container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <!-- Logo -->
            <img src="image/classtrack.png" alt="Logo" class="logo">
            <h4 class="text-primary"><i class=""></i>CLASS TRACK</h4>
            <nav class="nav">
                <ul>
                    <li><a href="teacherint.php"><i class="fas fa-tachometer-alt"></i>Dashboard</a></li>
                    <li><a href="vclasslist.php">View Class List</a></li>
                    <li><a href="attendance.php">Take Attendance</a></li>
                    <li><a href="view_class_attendance.php">View Class Attendance</a></li>
                    <li><a href="view_student_attendance.php">View Student Attendance</a></li>
                    <li><a href="today_attendance.php">Today's Attendance Report</a></li>
                    <li><a href="absent_notification.php">Generate Absent Notification</a></li>
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

                    <button type="submit">Load Students</button>
                </form>

                <?php
                // Validate inputs to prevent SQL injection
                if ($section_id && $subject_id) {
                    // Prepare statement to prevent SQL injection
                    $subject_query = "SELECT subject_name FROM subjects WHERE id = ?";
                    $stmt_subject = $conn->prepare($subject_query);
                    $stmt_subject->bind_param('i', $subject_id);
                    $stmt_subject->execute();
                    $stmt_subject->bind_result($subject_name);
                    $stmt_subject->fetch();
                    $stmt_subject->close();

                    // Query to get the students in the selected section
                    $students_query = "SELECT id, name FROM students WHERE section_id = ?";
                    $stmt_students = $conn->prepare($students_query);
                    $stmt_students->bind_param('i', $section_id);
                    $stmt_students->execute();
                    $students = $stmt_students->get_result();

                    // Display attendance form for students
                    if ($students->num_rows > 0) {
                        echo "<form method='POST' action='save_attendance.php'>";
                        echo "<table>";
                        echo "<tr><th>Student Name</th><th>Subject</th><th>Attendance</th></tr>";

                        while ($row = $students->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>{$row['name']}</td>";
                            echo "<td>$subject_name</td>"; // Display the selected subject in the table
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

                        echo "</table>";
                        echo "</form>";
                    } else {
                        echo "No students found in the selected section.";
                    }
                } elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
                    echo "Invalid input!";
                }
                ?>
            </div>
            <button type='submit'>Save Attendance</button>
            <button type='submit'>Send Email</button>
        </main>
    </div>
</body>
</html>
