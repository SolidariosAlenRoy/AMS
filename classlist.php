<?php
require 'db.php'; // Include your database connection

// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Initialize filters
$selected_section = isset($_GET['section']) ? $_GET['section'] : '';
$selected_year_level = isset($_GET['year_level']) ? $_GET['year_level'] : '';

// Fetch all sections for the dropdown
$sections_query = "SELECT * FROM sections";
$sections_result = $conn->query($sections_query);

// Fetch all unique year levels for the dropdown
$year_levels_query = "SELECT DISTINCT year_level FROM students";
$year_levels_result = $conn->query($year_levels_query);

// Fetch all students based on selected section and year level
$query = "SELECT students.id, students.name, students.parent_email, students.contact_number, students.year_level, sections.section_name 
          FROM students 
          LEFT JOIN sections ON students.section_id = sections.id";

// Apply filters if selected
$filters = [];
if ($selected_section) {
    $filters[] = "sections.id = ?";
}
if ($selected_year_level) {
    $filters[] = "students.year_level = ?";
}

// Combine filters into the query if they exist
if (!empty($filters)) {
    $query .= " WHERE " . implode(' AND ', $filters);
}

$stmt = $conn->prepare($query);

// Bind parameters based on selected filters
if (!empty($filters)) {
    $param_types = str_repeat('i', count($filters)); // assuming section_id and year_level are integers
    $params = [];
    
    if ($selected_section) {
        $params[] = $selected_section;
    }
    if ($selected_year_level) {
        $params[] = $selected_year_level;
    }

    // Bind parameters dynamically
    $stmt->bind_param($param_types, ...$params);
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
                <label for="year_level">Select Year Level:</label>
                <select name="year_level" id="year_level" onchange="this.form.submit()">
                    <option value="">All Year Levels</option>
                    <?php
                    // Populate year level options
                    while ($year_level = $year_levels_result->fetch_assoc()) {
                        $selected = ($selected_year_level == $year_level['year_level']) ? 'selected' : '';
                        echo "<option value='" . htmlspecialchars($year_level['year_level']) . "' $selected>" . htmlspecialchars($year_level['year_level']) . "</option>";
                    }
                    ?>
                </select>

                <label for="section">Select Section:</label>
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
                        <th>ID</th>
                        <th>Name</th>
                        <th>Parent Email</th>
                        <th>Contact Number</th>
                        <th>Year Level</th> <!-- Add Year Level column -->
                        <th>Section</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['id']); ?></td>
                            <td><?php echo htmlspecialchars($row['name']); ?></td>
                            <td><?php echo htmlspecialchars($row['parent_email']); ?></td>
                            <td><?php echo htmlspecialchars($row['contact_number']); ?></td>
                            <td><?php echo htmlspecialchars($row['year_level']); ?></td> <!-- Display Year Level -->
                            <td><?php echo htmlspecialchars($row['section_name']); ?></td>
                            <td>
                            <a href="editstudent.php?id=<?php echo $row['id']; ?>">Edit</a> | 
                            <a href="classlist.php?delete_id=<?php echo $row['id']; ?>"onclick="return confirm('Are you sure you want to delete this student?');">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>


        </main> 
    </div>

</body>
</html>