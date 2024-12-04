<?php
session_start();
require 'db.php'; 

// Handle Add Student
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $parent_email = $_POST['parent_email'];
    $contact_number = $_POST['contact_number'];
    $year_level = $_POST['year_level'];
    $section_name = $_POST['section'];
    $subjects = $_POST['subjects'] ?? [];

    // Check or insert section
    $section_query = "SELECT id FROM sections WHERE section_name = ?";
    $section_stmt = $conn->prepare($section_query);
    $section_stmt->bind_param('s', $section_name);
    $section_stmt->execute();
    $section_result = $section_stmt->get_result();

    if ($section_result->num_rows > 0) {
        $section_id = $section_result->fetch_assoc()['id'];
    } else {
        $insert_section_query = "INSERT INTO sections (section_name) VALUES (?)";
        $insert_section_stmt = $conn->prepare($insert_section_query);
        $insert_section_stmt->bind_param('s', $section_name);
        $insert_section_stmt->execute();
        $section_id = $conn->insert_id;
    }

    // Insert student
    $query = "INSERT INTO students (name, parent_email, contact_number, year_level, section_id) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('sssii', $name, $parent_email, $contact_number, $year_level, $section_id);
    if ($stmt->execute()) {
        $student_id = $conn->insert_id;

        // Insert student subjects
        foreach ($subjects as $subject_id) {
            $subject_query = "INSERT INTO student_subjects (student_id, subject_id) VALUES (?, ?)";
            $subject_stmt = $conn->prepare($subject_query);
            $subject_stmt->bind_param('ii', $student_id, $subject_id);
            $subject_stmt->execute();
        }
        echo "<p>Student added successfully!</p>";
    } else {
        echo "<p>Error: " . $stmt->error . "</p>";
    }
}


// Handle Filters
$selected_section = isset($_GET['section']) ? $_GET['section'] : '';
$selected_year_level = isset($_GET['year_level']) ? $_GET['year_level'] : '';

// Fetch sections
$sections_query = "SELECT * FROM sections";
$sections_result = $conn->query($sections_query);

// Fetch year levels
$year_levels_query = "SELECT DISTINCT year_level FROM students";
$year_levels_result = $conn->query($year_levels_query);

// Fetch students with filters
$query = "SELECT students.id, students.name, students.parent_email, students.contact_number, students.year_level, sections.section_name 
          FROM students 
          LEFT JOIN sections ON students.section_id = sections.id";
$filters = [];
if ($selected_section) $filters[] = "sections.id = ?";
if ($selected_year_level) $filters[] = "students.year_level = ?";
if (!empty($filters)) $query .= " WHERE " . implode(' AND ', $filters);
$stmt = $conn->prepare($query);

if (!empty($filters)) {
    $param_types = str_repeat('i', count($filters));
    $params = [];
    if ($selected_section) $params[] = $selected_section;
    if ($selected_year_level) $params[] = $selected_year_level;
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
        header("Location: addstudent.php");
        exit;
    } else {
        echo "Error: " . $delete_stmt->error;
    }
}

$userName = isset($_SESSION['username']) ? $_SESSION['username'] : 'Admin';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Students</title>
    <!-- FontAwesome for icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <!-- FullCalendar CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/5.11.3/main.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/5.11.3/main.min.js"></script>
    <link href="css/dashboard.css" rel="stylesheet"> 
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


/* Form */
.form-container-wrapper {
    border: 2px solid #6c757d;
    padding: 20px;
    border-radius: 8px;
    background-color: #f8f9fa;
    margin-bottom: 20px;
}

/* Input Fields */
form
input[type="text"],
input[type="email"],
input[type="number"],
select {
    width: 15%; /* Adjust width to fill container */
    max-width: 300px; /* Add max width for better control */
    padding: 5px;
    margin-bottom: 15px;
    border: 1px solid #ced4da;
    border-radius: 4px;
    font-size: 16px;
    background-color: #fff;
    color: #333;
}

/* Focus Effect for Input Fields */
input[type="text"]:focus,
input[type="email"]:focus,
input[type="number"]:focus,
select:focus {
    outline: none;
    border-color: #6c757d;
}

/* Subject Checkboxes */
label[for="subjects"] {
    font-weight: bold;
    margin-top: 5px;
    display: block;
}

input[type="checkbox"] {
    margin-right: 10px;
    transform: scale(1.2);
    margin-bottom: 10px;
    cursor: pointer;
}

/* Dropdown */
select {
    width: 17%; /* Ensure dropdown takes full width */
    max-width: 300px; /* Add max width for better control */
    padding: 5px;
    margin-bottom: 20px;
    border: 1px solid #ced4da;
    border-radius: 4px;
    font-size: 16px;
    background-color: #fff;
    color: #333;
}

select:focus {
    outline: none;
    border-color: #6c757d;
}

/* Buttons */
button {
    background-color: #708090;
    color: #fff;
    padding: 8px 12px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 14px;
    width: auto;
}

button:hover {
    background-color: #899499;
}

/* Table */
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 5px;
    margin-bottom: 20px;
}

th, td {
    border: 1px solid #ced4da;
    padding: 12px;
    text-align: left;
    font-size: 14px;
}

th {
    background-color: #708090;
    font-weight: bold;
    color: #fff;
}

td select {
    width: 100%;
    padding: 5px;
    font-size: 14px;
    border: 1px solid #ced4da;
    border-radius: 4px;
    background-color: #fff;
}

tr:hover {
    background-color: #f1f1f1;
}

/* Edit and Delete Links */
td {
    text-align: center; /* Center align the action buttons */
}

td a {
    padding: 5px 10px;
    text-decoration: none;
    border-radius: 4px;
    font-size: 14px;
    margin-right: 5px;
    display: inline-block;
    cursor: pointer;
}

/* Edit Button Style */
td a[href*="editstudent.php"] {
    background-color: #28a745; /* Green for Edit */
    color: white;
}

td a[href*="editstudent.php"]:hover {
    background-color: #218838; /* Darker green for hover */
}

/* Delete Button Style */
td a[href*="addstudent.php?delete_id"] {
    background-color: #dc3545; /* Red for Delete */
    color: white;
}

td a[href*="addstudent.php?delete_id"]:hover {
    background-color: #c82333; /* Darker red for hover */
}

/* Additional Style for Action Links */
td a:hover {
    cursor: pointer;
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
            <li><a href="admin.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a href="addstudent.php"><i class="fas fa-user-graduate"></i> Manage Students</a></li>
            <li><a href="addteacher.php"><i class="fas fa-chalkboard-teacher"></i> Manage Teacher</a></li>
            <li><a href="subject.php"><i class="fas fa-book-open"></i> Manage Subject</a></li>
            <li><a href="register.php"><i class="fas fa-user-plus"></i>Register Acc.</a></li>
        </ul>
    </nav>
</aside>


        <!-- Main Content -->
        <main class="main-content">
            <div class="header">
                <h1>Manage Students</h1>
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

            <!-- Add Student Form -->
            <div class="form-container-wrapper">
            <form method="POST" action="addstudent.php">
                <input type="text" name="name" placeholder="Student Name" required>
                <input type="email" name="parent_email" placeholder="Parent's Email">
                <input type="text" name="contact_number" placeholder="Contact Number">
                <label for="year_level">Year Level:</label>
                <select name="year_level" required>
                    <option value="">-- Select Year Level --</option>
                    <option value="7">Grade 7</option>
                    <option value="8">Grade 8</option>
                    <option value="9">Grade 9</option>
                    <option value="10">Grade 10</option>
                </select>
                <label for="section">Section:</label>
                <input type="text" name="section" required>
                <label for="subjects">Subjects:</label>
                <?php
                $subjects_query = "SELECT id, subject_name FROM subjects";
                $subjects_result = $conn->query($subjects_query);
                while ($row = $subjects_result->fetch_assoc()) {
                    echo "<input type='checkbox' name='subjects[]' value='{$row['id']}'> {$row['subject_name']}<br>";
                }
                ?>
              <button type="submit">Add Student</button>
            </form>
            </div>

            <!-- Class List Filters -->
            <div class="form-container-wrapper">
            <form method="GET" action="addstudent.php">
                <label for="year_level">Year Level:</label>
                <select name="year_level" onchange="this.form.submit()">
                    <option value="">All Year Levels</option>
                    <?php while ($row = $year_levels_result->fetch_assoc()) { ?>
                        <option value="<?= $row['year_level'] ?>" <?= $selected_year_level == $row['year_level'] ? 'selected' : '' ?>>
                            <?= $row['year_level'] ?>
                        </option>
                    <?php } ?>
                </select>
                <label for="section">Section:</label>
                <select name="section" onchange="this.form.submit()">
                    <option value="">All Sections</option>
                    <?php while ($row = $sections_result->fetch_assoc()) { ?>
                        <option value="<?= $row['id'] ?>" <?= $selected_section == $row['id'] ? 'selected' : '' ?>>
                            <?= $row['section_name'] ?>
                        </option>
                    <?php } ?>
                </select>
            </form>

            <!-- Class List Table -->
            <table>
                <thead>
                    <tr>
                        <th>ID NO.</th>
                        <th>Name</th>
                        <th>Parent Email</th>
                        <th>Contact</th>
                        <th>Year Level</th>
                        <th>Section</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()) { ?>
                        <tr>
                            <td><?= $row['id'] ?></td>
                            <td><?= $row['name'] ?></td>
                            <td><?= $row['parent_email'] ?></td>
                            <td><?= $row['contact_number'] ?></td>
                            <td><?= $row['year_level'] ?></td>
                            <td><?= $row['section_name'] ?></td>
                            <td>
                            <a href="editstudent.php?id=<?php echo $row['id']; ?>">Edit</a>  
                            <a href="addstudent.php?delete_id=<?php echo $row['id']; ?>"onclick="return confirm('Are you sure you want to delete this student?');">Delete</a>
                            </td>   
                        </tr>
                    <?php } ?>
                </tbody>
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

    </script>

    
</body>
</html>