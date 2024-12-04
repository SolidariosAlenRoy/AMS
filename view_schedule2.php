<?php
$userName = isset($_SESSION['username']) ? $_SESSION['username'] : 'Admin';
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
    <link href="css/view_schedule2.css" rel="stylesheet">
    <script src="js/view_schedule2.js"></script>
</head>
<body>
<div class="container">
        <!-- Sidebar -->
        <aside class="sidebar">
    <img src="image/logo3.jpg" alt="Logo" class="logo"> 
    <h4 class="text-primary"><i class=""></i>CLASS TRACK</h4>
    <nav class="nav">
        <ul>
            <li><a href="admin.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a href="addstudent.php"><i class="fas fa-user-graduate"></i> Manage Students</a></li>
            <li><a href="addteacher.php"><i class="fas fa-chalkboard-teacher"></i> Manage Teacher</a></li>
            <li><a href="subject.php"><i class="fas fa-book-open"></i> Manage Subject</a></li>
            <li><a href="register.php"><i class="fas fa-user-plus"></i>Register Acc.</a></li>
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
                        <p class="profile-role"><?php echo htmlspecialchars($_SESSION['role'] ?? 'admin'); ?></p>
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
</body>
</html>