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
    <link rel="stylesheet" href="css/addteacher.css">
    <script src="js/addteacher.js"></script>
</head>
<body>
<div class="example-box">
        <div class="background-shapes">
    </div>
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
</body>
</html>