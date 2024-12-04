<?php
require 'db.php'; // Include your database connection

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Fetch the subject details
    $query = "SELECT * FROM subjects WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $subject = $result->fetch_assoc();

    // Check if the subject exists
    if (!$subject) {
        // Subject not found, redirect or show an error message
        header("Location: subject.php?error=Subject not found");
        exit();
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $new_subject_name = $_POST['subject_name'];

        // Update subject details
        $update_query = "UPDATE subjects SET subject_name = ? WHERE id = ?";
        $update_stmt = $conn->prepare($update_query);
        $update_stmt->bind_param('si', $new_subject_name, $id);

        if ($update_stmt->execute()) {
            echo "Subject updated successfully!";
            // Redirect back to the subject management page after updating
            header("Location: subject.php");
            exit();
        } else {
            echo "Error: " . $update_stmt->error;
        }
    }
} else {
    // Redirect back if no ID is provided
    header("Location: subject.php");
    exit();
}

$userName = isset($_SESSION['username']) ? $_SESSION['username'] : 'Admin';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Subject - Admin Dashboard</title>
    <!-- FontAwesome for icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link href="css/editsubject.css" rel="stylesheet">
    <script src="js/editsubject.js"></script>

</head>
<body>
    < class="container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <img src="image/logo3.jpg" alt="Logo" class="logo">
            <h4 class="text-primary">CLASS TRACK</h4>
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
                <h1>Manage Subjects</h1>
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
            <h2>Edit Subject</h2>
            <form method="POST" action="editsubject.php?id=<?php echo htmlspecialchars($id); ?>">
                <input type="text" name="subject_name" value="<?php echo htmlspecialchars($subject['subject_name']); ?>" required>  
                <button type="button" onclick="window.location.href='subject.php'">Cancel</button>
            </form>
        </main>
    </div>
</body>
</html>

