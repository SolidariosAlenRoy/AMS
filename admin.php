<?php
session_start();
require 'db.php'; // Connect to the database

// Check if user is logged in and is an admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    // If user is not an admin, redirect them to login page or another page
    header('Location: login.php'); 
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <!-- FontAwesome for icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <!-- FullCalendar CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/5.11.3/main.min.css" rel="stylesheet">
    <!-- External CSS -->
    <link href="css/dashboard.css" rel="stylesheet"> 
    <style>
        body, h2, h4, p, ul {
    margin: 0;
    padding: 0;
}

.container {
    display: flex;
    height: 100vh; 
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
/* Card container styles */
.card-container {
    display: flex;
    justify-content: center; 
    flex-wrap: wrap; 
    gap: 20px; 
    margin-top: 20px; 
}

/* Dashboard card styles */
.dashboard-card {
    background-color: #8cf1f5;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    border-radius: 10px;
    padding: 20px;
    text-align: center;
    min-width: 200px;
    flex: 1 0 200px; 
    max-width: 250px; 
}

.dashboard-icon {
    font-size: 40px;
    margin-bottom: 10px;
}

.dashboard-title {
    font-size: 16px;
    text-transform: uppercase;
    margin-bottom: 5px;
    color: #6c757d; 
}

.dashboard-value {
    font-size: 32px;
    font-weight: bold;
    color: #333; 
}
/* Container for announcements and calendar */
.info-container {
    display: flex; 
    justify-content: space-between; 
    margin-top: 20px; 
    max-width: 1200px; 
    width: 100%; 
    margin: 20px auto; 
    height: auto;   
}


/* Announcements Section */
.announcements h3{
    flex: 1; 
    margin-right: 20px; 
    background-color: #f8f9fa; 
    text-align: center;
}
.announcements {
    flex: 1; 
    margin-right: 20px; 
    padding: 20px;
    background-color: #f8f9fa;
    border-radius: 10px; 
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); 
}
.announcements li{
    margin-left: 10px;
}
/* Calendar Section */
.calendar {
    flex: 1; 
    padding: 20px; 
    background-color: #f8f9fa; 
    border-radius: 10px; 
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); 
}
.calendar h3{
    flex: 1;
    text-align: center;
}
    </style>
</head>
<body>
    <div class="container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <!-- Logo -->
            <img src="image/classtrack.png" alt="Logo" class="logo"> 
            <h4 class="text-primary"><i class=""></i> CLASS TRACK</h4>
            <nav class="nav">
                <ul>
                    <li><a href="admin.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                    <li><a href="addstudent.php"><i class="fas fa-user-graduate"></i> Manage Students</a></li>
                    <li><a href="classlist.php"><i class="fas fa-list-alt"></i> Class List</a></li>
                    <li><a href="addteacher.php"><i class="fas fa-teacher-alt"></i> Manage Teacher</a></li>
                    <li><a href="teacherlist.php"><i class="fas fa-list-alt"></i> Teachers List</a></li>
                    <li><a href="subject.php"><i class="fas fa-subject-alt"></i> Manage Subject</a></li>
                </ul>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <div class="header">
                <h1>Admin Dashboard</h1>
                <div class="header-content">
                    <div class="search-bar">
                        <input type="text" placeholder="Search..." class="search-input">
                        <button class="search-button"><i class="fas fa-search"></i></button>
                    </div>
                    <div class="profile-bar">
                        <img src="profile.png" alt="Profile Picture" class="profile-picture"> <!-- Example profile image -->
                        <div class="profile-info">
                            <h5 class="profile-name">Name</h5>
                            <p class="profile-role">Admin</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-container">
                <!-- Students Card -->
                <div class="dashboard-card">
                    <div class="dashboard-icon text-info"><i class="fas fa-users"></i></div>
                    <div class="dashboard-title">Students</div>
                    <div class="dashboard-value">8</div>
                </div>

                <!-- Classes Card -->
                <div class="dashboard-card">
                    <div class="dashboard-icon text-primary"><i class="fas fa-chalkboard"></i></div>
                    <div class="dashboard-title">Classes</div>
                    <div class="dashboard-value">3</div>
                </div>

                <!-- Schedule Card -->
                <div class="dashboard-card">
                    <div class="dashboard-icon text-success"><i class="fa-light fa-calendar-days" style="color: #050505;"></i></div>
                    <div class="dashboard-title">Schedule</div>
                </div>
            </div>

            <div class="info-container">
                <!-- Recent Announcements -->
                <div class="announcements">
                    <h3>Recent Announcements</h3>
                    <ul>
                        <li>New class schedules are out!</li>
                        <li>Parent-teacher meetings next week.</li>
                        <li>School trip planned for next month.</li>
                    </ul>
                </div>
                
                <!-- FullCalendar -->
                <div class="calendar">
                    <h3>Event Calendar</h3>
                    <div id="calendar"></div>
                </div>
            </div>
        </main>
    </div>

    <!-- FullCalendar JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/5.11.3/main.min.js"></script>
    <!-- Calendar JavaScript -->
    <script src="js/dashboard.js"></script> 
</body>
</html>