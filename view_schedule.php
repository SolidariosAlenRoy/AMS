<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher's Schedule</title>
    <link rel="stylesheet" href="css/sched.css">
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
</head>
    <style>
body {
    margin: 0; 
    font-family: 'Times New Roman', Times, serif;
}

.sidebar {
    width: 200px; 
    background-color: #e0e0e0;
    padding: 20px;
    position: fixed; /* Keep sidebar fixed */
    height: 100vh; /* Full viewport height */
    overflow-y: auto; /* Enable scrolling if needed */
}

.sidebar h4 {
    color: black; 
    margin-bottom: 20px;
    text-align: center;
}

.main-content {
    margin-left: 220px; /* Leave space for the fixed sidebar */
    padding: 20px; /* Add padding for main content */
}

.header-content {
    display: flex; 
    justify-content: space-between; 
    align-items: center; 
    border-bottom: 1px solid #ced4da; 
    padding-bottom: 10px; 
    margin-bottom: 10px; 
}

.search-bar {
    display: flex;
    align-items: center; 
}

.search-input {
    flex-grow: 1; 
    padding: 10px; 
    border: 1px solid #ced4da; 
    border-radius: 5px; 
    width: 1050px;
    height: 20px;
    margin-right: 10px;
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

.header-content {
    display: flex; 
    justify-content: space-between; 
    align-items: center; 
    margin-bottom: 20px;
}

.nav {
    margin-top: 20px;
}

.nav ul {
    list-style: none; 
    padding: 0; 
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

table {
    width: 80%; 
    border-collapse: collapse;
    margin-left: 140px;
    
}

th, td {
    border: 2px solid #000;
    padding: 10px;
    text-align: center;
}

th {
    background-color: #f3f3f3;
    font-weight: bold;
}

h1 {
    text-align: center;
    margin-top: 5px;
}

.header {
    font-weight: bolder;
    font-size: 18px;
    padding: 10px;
    text-align: center; 
}

.subheader {
    font-size: 16px;
    margin-bottom: 10px;
    text-align: center; 
}

.teacher-info {
    text-align: center;
    margin-bottom: 20px;
}

.sched {
    background-color: #59f771; 
}

.lunch-break {
    background-color: #ffdd99; 
    font-weight: bold;
}
    </style>

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

<main class="main-content">
            <div class="header">
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

<div class="teacher-info">
        <h1>TEACHER'S SCHEDULE</h1>
        <p class="header">Mr. Andrew "Waldo" Tate</p>
        <p class="subheader">Teacher I – English Teacher</p>
    </div>

    <table>
        <tr>
            <th>TIME</th>
            <th>MONDAY</th>
            <th>TUESDAY</th>
            <th>WEDNESDAY</th>
            <th>THURSDAY</th>
            <th>FRIDAY</th>
        </tr>
        <tr>
            <td>8:00 - 9:00 AM</td>
            <td class="sched">7-A</td>
            <td></td>
            <td class="sched">7-A</td>
            <td></td>
            <td class="sched">7-A</td>
        </tr>
        <tr>
            <td>9:00 - 10:00 AM</td>
            <td class="sched">7-B</td>
            <td class="sched">7-B</td>
            <td></td>
            <td class="sched">7-B</td>
            <td></td>
        </tr>
        <tr>
            <td>10:00 - 11:00 AM</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td>11:00 - 12:00 PM</td>
            <td colspan="5" class="lunch-break">LUNCH BREAK</td>
        </tr>
        <tr>
            <td>12:00 - 1:00 PM</td>
            <td class="sched">7-C</td>
            <td></td>
            <td></td>
            <td class="sched">7-C</td>
            <td class="sched">7-C</td>
        </tr>
        <tr>
            <td>1:00 - 2:00 PM</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td>2:00 - 3:00 PM</td>
            <td></td>
            <td></td>
            <td class="sched">7-B</td>
            <td class="sched">7-C</td>
            <td class="sched">7-A</td>
        </tr>
        <tr>
            <td>3:00 - 4:00 PM</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
    </table>
