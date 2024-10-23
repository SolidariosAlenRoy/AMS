<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $parent_email = $_POST['parent_email'];
    $contact_number = $_POST['contact_number'];
    $year_level = $_POST['year_level']; // Capture year level from dropdown
    $section_name = $_POST['section']; // Text field for section name
    $subjects = isset($_POST['subjects']) ? $_POST['subjects'] : [];

    // Check if the section already exists
    $section_query = "SELECT id FROM sections WHERE section_name = ?";
    $section_stmt = $conn->prepare($section_query);
    $section_stmt->bind_param('s', $section_name);
    $section_stmt->execute();
    $section_result = $section_stmt->get_result();

    if ($section_result->num_rows > 0) {
        // Section exists, get the section_id
        $section_row = $section_result->fetch_assoc();
        $section_id = $section_row['id'];
    } else {
        // Section does not exist, insert it into the sections table
        $insert_section_query = "INSERT INTO sections (section_name) VALUES (?)";
        $insert_section_stmt = $conn->prepare($insert_section_query);
        $insert_section_stmt->bind_param('s', $section_name);
        $insert_section_stmt->execute();

        // Get the newly created section_id
        $section_id = $conn->insert_id;
    }

    // Insert student data with the valid section_id and year_level
    $query = "INSERT INTO students (name, parent_email, contact_number, year_level, section_id) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('sssii', $name, $parent_email, $contact_number, $year_level, $section_id);

    if ($stmt->execute()) {
        $student_id = $conn->insert_id;

        // Insert into student_subjects table
        foreach ($subjects as $subject_id) {
            $subject_query = "INSERT INTO student_subjects (student_id, subject_id) VALUES (?, ?)";
            $subject_stmt = $conn->prepare($subject_query);
            $subject_stmt->bind_param('ii', $student_id, $subject_id);
            $subject_stmt->execute();
        }

        echo "Student added successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Student</title>
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
                <h1>Manage Student</h1>
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
            
            <form method="POST" action="addstudent.php">
    <input type="text" name="name" placeholder="Student Name" required>
    <input type="email" name="parent_email" placeholder="Parent's Email">
    <input type="text" name="contact_number" placeholder="Contact Number">

    <!-- Dropdown for selecting year level -->
    <label for="year_level">Select Year Level:</label>
    <select name="year_level" required>
        <option value="">-- Select Year Level --</option>
        <option value="7">Grade 7</option>
        <option value="8">Grade 8</option>
        <option value="9">Grade 9</option>
        <option value="10">Grade 10</option>
    </select>

    <!-- Text field for entering section -->
    <label for="section">Enter Section:</label>
    <input type="text" name="section" required placeholder="Section Name">

    <!-- Checkbox for selecting subjects -->
    <label for="subjects">Select Subjects:</label><br>
    <?php
    // Fetch subjects from the database
    $subjects_query = "SELECT id, subject_name FROM subjects";
    $subjects_result = $conn->query($subjects_query);

    while ($row = $subjects_result->fetch_assoc()) {
        echo "<input type='checkbox' name='subjects[]' value='{$row['id']}'> {$row['subject_name']}<br>";
    }
    ?>

    <button type="submit">Add Student</button>
</form>

        </main>
        
    </div>


</body>
</html>
