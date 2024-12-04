<?php
session_start();
require 'db.php'; // Connect to the database

// Query to get the number of students
$studentsQuery = "SELECT COUNT(*) AS total_students FROM students";
$studentsResult = mysqli_query($conn, $studentsQuery);
$studentsData = mysqli_fetch_assoc($studentsResult);
$totalStudents = $studentsData['total_students'];

// Query to get the number of classes
$classesQuery = "SELECT COUNT(*) AS total_classes FROM sections";
$classesResult = mysqli_query($conn, $classesQuery);
$classesData = mysqli_fetch_assoc($classesResult);
$totalClasses = $classesData['total_classes'];

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
    <link href="css/teacherint.css" rel="stylesheet"> 
    <script src="js/teacherint.js"></script>
</head>
<body>
    <div class="container">
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
                <h1>Teacher's Dashboard</h1>
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

            <div class="card-container">
                <!-- Students Card -->
                <div class="dashboard-card">
                    <div class="dashboard-icon text-info"><i class="fas fa-users"></i></div>
                    <div class="dashboard-title">Students</div>
                    <div class="dashboard-value"><?php echo $totalStudents; ?></div>
                </div>

                <!-- Classes Card -->
                <div class="dashboard-card">
                    <div class="dashboard-icon text-primary"><i class="fas fa-chalkboard"></i></div>
                    <div class="dashboard-title">Classes</div>
                    <div class="dashboard-value"><?php echo $totalClasses; ?></div>
                </div>

                <!-- Schedule Card -->
                <div class="dashboard-card schedule-card" onclick="window.location.href='view_schedule.php'" style="cursor: pointer;">
                    <div class="dashboard-icon text-success">
                    <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div class="dashboard-title">Schedule</div>
                    <div class="dashboard-value"></div>
                </div>

            <div class="info-container">
                <!-- Recent Announcements -->
                <div class="announcements">
                    <h3>Recent Announcements</h3>
                    
                </div>
                
                <!-- FullCalendar -->
                <div class="calendar-container">
            <header>
        <div class="calendar-navigation">
            <span id="calendar-prev">&#10094;</span>
            <span id="calendar-next">&#10095;</span>
        </div>
        <div class="calendar-current-date"></div>
    </header>
    <div class="calendar-body">
        <ul class="calendar-weekdays">
            <li>Sun</li>
            <li>Mon</li>
            <li>Tue</li>
            <li>Wed</li>
            <li>Thu</li>
            <li>Fri</li>
            <li>Sat</li>
        </ul>
        <ul class="calendar-dates"></ul>
    </div>
</div>
</div>
</main>
</div>
</body>
</html>