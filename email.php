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
        SELECT s.name, s.parent_email 
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher's Dashboard</title>
    <!-- FontAwesome for icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
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

/* Table Styles */
.table-container {
    margin-top: 30px;
    background-color: #fff;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.table-container h2 {
    font-size: 24px;
    color: #343a40;
    margin-bottom: 15px;
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
            <img src="image/profile.png" alt="Profile Picture" class="profile-picture">
            <div class="profile-info">
                <h5 class="profile-name">Name</h5>
                <p class="profile-role">Teacher</p>
            </div>
        </div>
    </div>
</div>


<!-- Table to Display Absent Students -->
<div class="table-container">
                <h2>Absent Students</h2>
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
                </table>
            </div>
        </main>
    </div>


    
</body>
</html>