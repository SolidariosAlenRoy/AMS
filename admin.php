<?php
session_start();
require 'db.php'; // Connect to the database

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
/* Global Styles */
body, h2, h4, p, ul {
    margin: 0;
    padding: 0;
}

/* Container for Sidebar and Main Content */
.container {
    display: flex;
    height: 100vh; 
}

/* Sidebar Styles */
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
    font-size: 24px;
    color: #ffc107; 
    margin-bottom: 20px;
    text-align: center;
}

.nav ul {
    list-style: none; 
    width: 100%;
    text-align: left;
    padding: 0;
}

.nav ul li {
    margin-bottom: 20px;
}

.nav ul li a {
    text-decoration: none; 
    color: #adb5bd; 
    font-size: 16px;
    display: block;
    padding: 10px;
    border-radius: 5px;
    transition: background-color 0.3s;
}

.nav ul li a:hover {
    background-color: #495057;
    color: #fff; 
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

/* Main Content Styles */
.main-content {
    flex: 1;
    padding: 20px;
}

.header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 2px solid #dee2e6;
    padding: 10px 0; /* Add padding for spacing */
    margin-bottom: 20px; /* Adjust margin for more space below header */
    gap: 20px;
}

.header h1 {
    color: #007bff;
    font-size: 28px;
    margin: 0; /* Remove default margin */
}

/* Header Content Styles */
.header-content {
    display: flex;
    align-items: center;
    gap: 20px;
}

/* Search Bar Styles */
.search-bar {
    display: flex;
    align-items: center;
    gap: 10px;
}

.search-input {
    padding: 10px;
    border: 1px solid #ced4da;
    border-radius: 5px;
    width: 250px;
    height: 40px;
    font-size: 16px;
}

.search-button {
    background-color: #007bff;
    color: #fff;
    border: none;
    padding: 10px 15px;
    border-radius: 5px;
    cursor: pointer;
    font-size: 16px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Profile Bar Styles */
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

/* Card Container */
.card-container {
    display: flex;
    justify-content: center; 
    flex-wrap: wrap; 
    gap: 20px; 
    margin-top: 20px; 
}

/* Dashboard Card */
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
    color: #6c757d;
    margin-bottom: 5px;
}

.dashboard-value {
    font-size: 32px;
    font-weight: bold;
    color: #333;
}

/* Info Container */
.info-container {
    display: flex; 
    justify-content: space-between; 
    max-width: 1200px; 
    width: 100%; 
    margin: 20px auto; 
    height: auto;   
}

/* Announcements */
.announcements {
    flex: 1; 
    margin-right: 20px; 
    padding: 20px;
    background-color: #f8f9fa;
    border-radius: 10px; 
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); 
}

.announcements h3 {
    text-align: center;
}

.announcements li {
    margin-left: 10px;
}

/* Calendar */
.calendar {
    flex: 1; 
    padding: 20px; 
    background-color: #f8f9fa; 
    border-radius: 10px; 
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); 
}

.calendar h3 {
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
                    <li><a href="login.php"><i class="fas fa-subject-alt"></i>Logout</a></li>
                </ul>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <div class="header">
                <h1>Teacher's Dashboard</h1>
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
                    <div class="dashboard-icon text-success"><i class="fas fa-calendar-alt"></i></div>
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