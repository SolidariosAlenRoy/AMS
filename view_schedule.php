<?php
$userName = isset($_SESSION['username']) ? $_SESSION['username'] : 'Teacher';
?>

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
/* Global Styles */
body{
    margin: 0;
    padding: 0; 
  
}

h2, h4, p, ul {
    margin: 0;
    padding: 0;  
}

/* Container for Sidebar and Main Content */
.container {
    display: flex;
    height: 100vh; /* Full height */
    margin-left: 290px; /* Account for the sidebar width */
}

/* Sidebar Styles */
.sidebar {
    position: fixed;  /* Make the sidebar fixed */
    top: 0;           /* Align it to the top of the page */
    left: 0;          /* Align it to the left of the page */
    width: 250px;     /* Set the sidebar width */
    height: 100%;     /* Make it take the full height */
    background-color: #343a40;
    color: #fff;
    padding: 20px;
    display: flex;
    flex-direction: column;
    align-items: left;
    z-index: 100;     /* Ensure the sidebar stays on top of the content */
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

/* Profile Bar Styles */
.profile-bar {
    display: flex; 
    align-items: center; 
    gap: 10px;
    border-left: 1px solid #ced4da; 
    padding-left: 20px; 
    cursor: pointer;
}

/* Profile Picture and Info */
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

/* Dropdown Styles */
.profile-dropdown {
    display: none; /* Initially hidden */
    position: absolute; 
    top: 60px; /* Adjust the position to show below the profile */
    right: 20px;
    background-color: #fff;
    border: 1px solid #ccc;
    border-radius: 5px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    width: 150px;
}

.profile-dropdown a {
    list-style: none;
    margin: 0;
    padding: 0;
}

.profile-dropdown a {
    border-bottom: 1px solid #ccc;
}

.profile-dropdown a {
    text-decoration: none;
    color: #333;
    display: block;
    padding: 10px;
}

.profile-dropdown a:hover {
    background-color: #f1f1f1;
}

/* Show dropdown when active */
.profile-dropdown.show {
    display: block;
}


/* Card Container */
.card-container {
    display: flex;
    justify-content: center; 
    flex-wrap: wrap; 
    gap: 20px; 
    margin-top: 20px; 
}

/* Schedule Card Specific Styles */
.schedule-card {
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    text-align: center;
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
            <li><a href="vlassattendance.php"><i class="fas fa-eye"></i> View Class Attendance</a></li>
            <li><a href="email.php"><i class="fas fa-envelope"></i> Generate E-mail</a></li>
        </ul>
    </nav>
</aside>

<main class="main-content">
            <div class="header">
            <h1 class="dashboard-title">Teacher's Schedule</h1>
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

<div class="teacher-info">
        <h1 class="schedule-title">TEACHER'S SCHEDULE</h1>
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


    <script>
        // Toggle dropdown menu on click of profile image or name
    function toggleDropdown(event) {
    const dropdown = document.getElementById('profileDropdown');
    
    // Close the dropdown if the user clicks outside the profile bar
    if (!event.target.closest('.profile-bar') && dropdown.classList.contains('show')) {
        dropdown.classList.remove('show');
    } else {
        dropdown.classList.toggle('show');
    }
}
    </script>
    </body>
</html>