<?php
session_start();
require 'db.php'; // Include your database connection

// Handle the add teacher functionality
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_teacher'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $contact_number = $_POST['contact_number'];

    $query = "INSERT INTO teachers (name, email, contact_number) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('sss', $name, $email, $contact_number);

    if ($stmt->execute()) {
        $success_message = "Teacher added successfully!";
    } else {
        $error_message = "Error: " . $stmt->error;
    }
}

// Handle teacher deletion
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];

    $delete_query = "DELETE FROM teachers WHERE id = ?";
    $stmt = $conn->prepare($delete_query);
    $stmt->bind_param('i', $delete_id);

    if ($stmt->execute()) {
        $success_message = "Teacher deleted successfully!";
        header("Location: manage_teachers.php"); // Refresh the page to reflect the deletion
        exit;
    } else {
        $error_message = "Error: " . $stmt->error;
    }
}

// Fetch all teachers
$query = "SELECT id, name, email, contact_number FROM teachers";
$result = $conn->query($query);

$userName = isset($_SESSION['username']) ? $_SESSION['username'] : 'Admin';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Teachers</title>
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
td a[href*="editteacher.php"] {
    background-color: #28a745; /* Green for Edit */
    color: white;
}

td a[href*="editteacher.php"]:hover {
    background-color: #218838; /* Darker green for hover */
}

/* Delete Button Style */
td a[href*="addteacher.php?delete_id"] {
    background-color: #dc3545; /* Red for Delete */
    color: white;
}

td a[href*="addteacher.php?delete_id"]:hover {
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
                <h1>Manage Teachers</h1>
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
        <form method="POST" action="addteacher.php">
        <input type="text" name="name" placeholder="Teacher Name" required>
        <input type="email" name="email" placeholder="Email">
        <input type="text" name="contact_number" placeholder="Contact Number">
        <button type="submit">Add Teacher</button>
    </form>

    <!-- Teachers List -->
    <table>
        <thead>
            <tr>
                <th>Teacher Name</th>
                <th>Email</th>
                <th>Contact Number</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['contact_number']) . "</td>";
                    echo "<td>
                            <a href='editteacher.php?id=" . $row['id'] . "' class='edit-btn'>Edit</a>
                            <a href='addteacher.php?delete_id=" . $row['id'] . "' class='delete-btn' onclick='return confirm(\"Are you sure you want to delete this teacher?\");'>Delete</a>
                         </td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='4'>No teachers found</td></tr>";
            }
            ?>
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