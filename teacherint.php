<?php
session_start();
require 'db.php'; // Connect to the database

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
    color: #36454F;
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
    width: 300px;
    height: 20px;
    font-size: 16px;
}

.search-button {
    background-color: #899499;
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
    background-color: #7393B3;
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
    color: #36454F;
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
    border: 2px solid #6c757d;
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
    border: 2px solid #6c757d;
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
    <img src="image/classtrack.png" alt="Logo" class="logo"> 
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
                    <div class="dashboard-icon text-success">
                    <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div class="dashboard-title">Schedule</div>
                    <a href="view_schedule.php" class="btn btn-primary" style="display: block; margin-top: 10px; text-decoration: none; color: white; padding: 5px 10px; border-radius: 5px; background-color: #007bff; text-align: center;">View Schedule</a>
                </div>

            <div class="info-container">
                <!-- Recent Announcements -->
                <div class="announcements">
                    <h3>Recent Announcements</h3>
                    
                </div>
                
                <!-- FullCalendar -->
                <div class="calendar">
                    <h3>Event Calendar</h3>
                    <div id="calendar"></div>
                </div>
            </div>
        </main>
    </div>
 
    <script>
        document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth', // You can customize the view
        events: [
            // Example events, replace these with your own
            {
                title: 'Event 1',
                start: '2024-10-15'
            },
            {
                title: 'Event 2',
                start: '2024-10-18'
            }
        ]
    });
    calendar.render();
});
    </script>
    

    
</body>
</html>