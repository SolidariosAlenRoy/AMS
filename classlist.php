<?php
require 'db.php'; // Include your database connection

// Initialize section filter
$selected_section = isset($_GET['section']) ? $_GET['section'] : '';

// Fetch all sections for the dropdown
$sections_query = "SELECT * FROM sections";
$sections_result = $conn->query($sections_query);

// Fetch all students based on selected section
$query = "SELECT students.id, students.name, students.parent_email, students.contact_number, sections.section_name 
          FROM students 
          LEFT JOIN sections ON students.section_id = sections.id";

// Apply section filter if a section is selected
if ($selected_section) {
    $query .= " WHERE sections.id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $selected_section);
} else {
    $stmt = $conn->prepare($query);
}

$stmt->execute();
$result = $stmt->get_result();

// Handle deleting a student
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];

    // Delete the student
    $delete_query = "DELETE FROM students WHERE id = ?";
    $delete_stmt = $conn->prepare($delete_query);
    $delete_stmt->bind_param('i', $delete_id);

    if ($delete_stmt->execute()) {
        echo "Student deleted successfully!";
        // Refresh the page to reflect the deletion
        header("Location: classlist.php");
        exit;
    } else {
        echo "Error: " . $delete_stmt->error;
    }
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
                <h1>Class List</h1>
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
            <form class="filter-form" method="GET" action="classlist.php">
    <label for="section">Filter by Section:</label>
    <select name="section" id="section" onchange="this.form.submit()">
        <option value="">All Sections</option>
        <?php
        // Populate section options
        while ($section = $sections_result->fetch_assoc()) {
            $selected = ($selected_section == $section['id']) ? 'selected' : '';
            echo "<option value='" . htmlspecialchars($section['id']) . "' $selected>" . htmlspecialchars($section['section_name']) . "</option>";
        }
        ?>
    </select>
</form>

<table>
    <thead>
        <tr>
            <th>Student Name</th>
            <th>Parent's Email</th>
            <th>Contact Number</th>
            <th>Section</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php
        // Display each student in a table row
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                echo "<td>" . htmlspecialchars($row['parent_email']) . "</td>";
                echo "<td>" . htmlspecialchars($row['contact_number']) . "</td>";
                echo "<td>" . htmlspecialchars($row['section_name']) . "</td>";
                echo "<td>
                        <a href='editstudent.php?id=" . $row['id'] . "' class='edit-btn'>Edit</a>
                        <a href='classlist.php?delete_id=" . $row['id'] . "' class='delete-btn' onclick='return confirm(\"Are you sure you want to delete this student?\");'>Delete</a>
                     </td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='5'>No students found</td></tr>";
        }
        ?>
    </tbody>
</table>
        </main> 
    </div>

</body>
</html>
