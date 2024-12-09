<?php
session_start();
require 'db.php';

// Initialize variables
$absent_students = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['send_email'])) {
    // Retrieve data from the form
    $year_level = $_POST['year_level'];
    $section_id = $_POST['section'];
    $subject_id = $_POST['subject'];

    // Query to get absent students
    $absent_query = "
        SELECT s.name, s.parent_email, s.contact_number
        FROM students s
        JOIN sections sec ON s.section_id = sec.id
        WHERE sec.id = ? AND s.year_level = ? AND s.id IN (
        SELECT student_id FROM attendance
        WHERE status = 'Absent'
        )";

    $stmt_absent = $conn->prepare($absent_query);
    $stmt_absent->bind_param('is', $section_id, $year_level);
    $stmt_absent->execute();
    $result_absent = $stmt_absent->get_result();

    while ($row = $result_absent->fetch_assoc()) {
        $absent_students[] = $row;
    }
    
}

$userName = isset($_SESSION['username']) ? $_SESSION['username'] : 'Teacher';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher's Dashboard</title>
    <!-- FontAwesome for icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <!-- FullCalendar CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/5.11.3/main.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/5.11.3/main.min.js"></script>
    <link href="css/email1.css" rel="stylesheet"> 
    <script src="js/email.js"></script>

</head>
<body>
    <div class="container">
    <div class="example-box">
  <div class="background-shapes">
  </div>
        <!-- Sidebar -->
        <aside class="sidebar">
    <img src="image/logo3.jpg" alt="Logo" class="logo"> 
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
                <h1>Absent Students</h1>
                <div class="header-content">
                        <!-- Profile Bar -->
                <div class="profile-bar" onclick="toggleDropdown(event)">
                    <img src="image/profile.png" alt="Profile Picture" class="profile-picture"> 
                    <div class="profile-info">
                        <h5 class="profile-name"><?php echo htmlspecialchars($userName); ?></h5>
                        <p class="profile-role"><?php echo htmlspecialchars($_SESSION['role'] ?? 'teacher'); ?></p>
                    </div>
                </div>
                <!-- Dropdown Menu -->
                <div id="profileDropdown" class="profile-dropdown">
                    <a href="login.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </div>
                </div>
            </div>
<!-- Table to Display Absent Students -->
<div class="card">
                <table id="absentStudentsTable" class="display">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Parent Email</th>
                            <th>Contact Number</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($absent_students as $student): ?>
                            <tr>
                                <td><?= htmlspecialchars($student['name']) ?></td>
                                <td><?= htmlspecialchars($student['parent_email']) ?></td>
                                <td><?= htmlspecialchars($student['contact_number']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <button onclick="generateEmails()">Send Email</button>
                </table>
            </div>
        </main>
    </div>
</body>
</html>