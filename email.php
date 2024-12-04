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
  height: 100vh; /* Full height */
  margin-left: 290px; /* Account for the sidebar width */
}

/* Sidebar Styles */.sidebar {
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

/* Table */
table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 15px;
}

table th, table td {
  padding: 12px 15px;
  text-align: left;
  border-bottom: 1px solid #ddd;
}

table th {
  background-color: #708090;
  color: white;
  font-size: 16px;
}

table td {
  font-size: 14px;
  color: #555;
}

button {
  background-color: #708090;
  color: white;
  border: none;
  padding: 10px 20px;
  font-size: 14px;
  border-radius: 4px;
  cursor: pointer;
}

button:hover {
  background-color: #899499;
}



.card {
  background-color: #fff;
  border: 2px solid #6c757d;
  border-radius: 8px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  padding: 20px;
  margin: 20px 0;
}

.card h2 {
  font-size: 20px;
  margin-bottom: 15px;
  color: #007bff;
  border-bottom: 1px solid #ddd;
  padding-bottom: 10px;
}



    </style>
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

        function generateEmails() {
            const rows = document.querySelectorAll('#absentStudentsTable tbody tr');
            const emailAddresses = [];
            let emailBody = "Dear Parents,\n\nWe would like to inform you the following students who were absent today:\n\n";
            
            if (rows.length === 0) {
                alert("No absent students to notify.");
                return;
            }

            rows.forEach(row => {
                const studentName = row.cells[0].textContent.trim();
                const guardianEmail = row.cells[1].textContent.trim();

                if (guardianEmail && !emailAddresses.includes(guardianEmail)) {
                    emailAddresses.push(guardianEmail);
                }
                
                emailBody += `- ${studentName}\n`;
            });

            emailBody += "\nBest regards,\nYour School";
            const subject = "Attendance Notification";
            
            if (emailAddresses.length > 0) {
                const mailtoLink = `mailto:${emailAddresses.join(',')}?subject=${encodeURIComponent(subject)}&body=${encodeURIComponent(emailBody)}`;
                
                // Open mailto link in a new tab
                window.open(mailtoLink, '_blank');
            } else {
                alert("No valid parents emails found.");
            }
        }
    </script>

    

    
</body>
</html>