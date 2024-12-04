<?php
require 'db.php'; // Include your database connection

// Fetch all teachers
$query = "SELECT id, name, email, contact_number FROM teachers";
$result = $conn->query($query);

if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];

    // Delete the teacher
    $delete_query = "DELETE FROM teachers WHERE id = ?";
    $stmt = $conn->prepare($delete_query);
    $stmt->bind_param('i', $delete_id);

    if ($stmt->execute()) {
        echo "Teacher deleted successfully!";
        // Refresh the page to reflect the deletion
        header("Location: teacherlist.php");
        exit;
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
    <title>Admin Dashboard</title>
    <!-- FontAwesome for icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <!-- FullCalendar CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/5.11.3/main.min.css" rel="stylesheet">
    <!-- External CSS -->
    <link href="css/teacherlist.css" rel="stylesheet"> 
    <script src="js/teacherlist.js"></script>
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
                <h1>Teachers List</h1>
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
        // Display each teacher in a table row
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                echo "<td>" . htmlspecialchars($row['contact_number']) . "</td>";
                echo "<td>
                        <a href='editteacher.php?id=" . $row['id'] . "' class='edit-btn'>Edit</a>
                        <a href='teacherlist.php?delete_id=" . $row['id'] . "' class='delete-btn' onclick='return confirm(\"Are you sure you want to delete this teacher?\");'>Delete</a>
                     </td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='4'>No teachers found</td></tr>";
        }
        ?>
    </tbody>
</table>
</main> 
</div>
</body>
</html>

