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
        font-size: 26px;
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
        max-width: 210px; 
        margin-bottom: 20px; 
        display: block; 
        margin-left: auto; 
        margin-right: auto; 
        border-radius: 110px;
        border: 3px solid transparent;
        box-shadow: 0 0 15px 5px rgba(0, 128, 128, 0.7);
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
        padding: 10px 0; /* Add padding for spacing */
        margin-top: 10px;
        margin-bottom: 20px; /* Adjust margin for more space below header */
        gap: 20px;
    }

    .header h1 {
        color: #36454F;
        font-size: 28px;
        margin: 0; /* Remove default margin */
        border-bottom: 2px solid #dee2e6;
        
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
        justify-content: flex-end
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

    .dashboard-title {
       text-align: center;
       font-size: 32px;
       color: #36454F;
       margin-top: 20px;
       font-weight: bold;
       text-transform: uppercase;
}
/* Styles for Teacher's Schedule Title */
    .schedule-title {
       text-align: center;
       font-size: 28px;
       color: #2f4f4f;
       margin-top: 10px;
       font-weight: normal;
       padding-bottom: 10px;
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
    <img src="image/logo3.jpg" alt="Logo" class="logo"> 
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
            <h1 class="dashboard-title">Teacher's Dashboard</h1>
                <div class="header-content">
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
        <h1 class="schedule-title">TEACHER'S SCHEDULE</h1>
        <p class="subheader">Mr. Andrew "Waldo" Tate</p>
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
