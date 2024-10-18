<?php
require 'db.php'; // Include your database connection

// Handle adding a new subject
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_subject'])) {
    $subject_name = $_POST['subject_name'];

    // Insert subject into the subjects table
    $query = "INSERT INTO subjects (subject_name) VALUES (?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('s', $subject_name);

    if ($stmt->execute()) {
        echo "Subject added successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }
}

// Handle deleting a subject
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $delete_query = "DELETE FROM subjects WHERE id = ?";
    $delete_stmt = $conn->prepare($delete_query);
    $delete_stmt->bind_param('i', $delete_id);
    $delete_stmt->execute();
}

// Check if we are editing a subject
$edit_subject = false;
$subject_name = '';

if (isset($_GET['edit_id'])) {
    $edit_subject = true;
    $edit_id = $_GET['edit_id'];

    // Fetch the subject details for editing
    $query = "SELECT subject_name FROM subjects WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $edit_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $subject = $result->fetch_assoc();

    if ($subject) {
        $subject_name = $subject['subject_name'];
    } else {
        echo "Subject not found.";
        exit();
    }
}

// Handle updating a subject
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_subject'])) {
    $new_subject_name = $_POST['subject_name'];
    $update_query = "UPDATE subjects SET subject_name = ? WHERE id = ?";
    $update_stmt = $conn->prepare($update_query);
    $update_stmt->bind_param('si', $new_subject_name, $edit_id);

    if ($update_stmt->execute()) {
        echo "Subject updated successfully!";
        header("Location: subject.php");
        exit();
    } else {
        echo "Error: " . $update_stmt->error;
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
                <h1>Manage Subject</h1>
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
            <h2><?php echo $edit_subject ? 'Edit Subject' : 'Add New Subject'; ?></h2>

<form method="POST" action="subject.php<?php if ($edit_subject) echo '?edit_id=' . $edit_id; ?>">
    <input type="text" name="subject_name" placeholder="Subject Name" value="<?php echo htmlspecialchars($subject_name); ?>" required>
    <button type="submit" name="<?php echo $edit_subject ? 'edit_subject' : 'add_subject'; ?>">
        <?php echo $edit_subject ? 'Update Subject' : 'Add Subject'; ?>
    </button>
</form>

<h2>Existing Subjects</h2>
<table border="1">
    <tr>
        <th>Subject Name</th>
        <th>Actions</th>
    </tr>
    <?php
    // Fetch existing subjects from the database
    $subjects_query = "SELECT id, subject_name FROM subjects";
    $subjects_result = $conn->query($subjects_query);

    while ($row = $subjects_result->fetch_assoc()) {
        echo "<tr>
                <td>{$row['subject_name']}</td>
                <td>
                    <a href='subject.php?edit_id={$row['id']}'>Edit</a> |
                    <a href='subject.php?delete_id={$row['id']}' onclick=\"return confirm('Are you sure you want to delete this subject?');\">Delete</a>
                </td>
              </tr>";
    }
    ?>
</table>
        </main> 
    </div>

</body>
</html>

