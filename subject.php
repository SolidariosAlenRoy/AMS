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
        header("Location: subject.php");
        exit();
    } else {
        echo "Error: " . $update_stmt->error;
    }
}

$userName = isset($_SESSION['username']) ? $_SESSION['username'] : 'Admin';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Subject</title>
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
input[type="text"] {
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
input[type="text"]:focus {
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
td a[href*="subject.php"] {
    background-color: #28a745; /* Green for Edit */
    color: white;
}

td a[href*="subject.php"]:hover {
    background-color: #218838; /* Darker green for hover */
}

/* Delete Button Style */
td a[href*="subject.php?delete_id"] {
    background-color: #dc3545; /* Red for Delete */
    color: white;
}

td a[href*="subject.php?delete_id"]:hover {
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
                <h1>Manage Subject</h1>
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

            <div class="form-container-wrapper">
            <form method="POST" action="subject.php<?php if ($edit_subject) echo '?edit_id=' . $edit_id; ?>">
                <input type="text" name="subject_name" placeholder="Subject Name" value="<?php echo htmlspecialchars($subject_name); ?>" required>
                <button type="submit" name="<?php echo $edit_subject ? 'edit_subject' : 'add_subject'; ?>"
                    onclick="return confirm('Are you sure you want to <?php echo $edit_subject ? 'update' : 'add'; ?> this subject?')">
                    <?php echo $edit_subject ? 'Update Subject' : 'Add Subject'; ?>
                </button>
            </form>

        
        <h2>Existing Subjects</h2>
        <table>
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
                            <a href='subject.php?edit_id={$row['id']}'>Edit</a>
                            <a href='subject.php?delete_id={$row['id']}' onclick=\"return confirm('Are you sure you want to delete this subject?');\">Delete</a>
                        </td>
                      </tr>";
            }
            ?>
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