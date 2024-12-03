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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Subject - Admin Dashboard</title>
    <!-- FontAwesome for icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link href="css/dashboard.css" rel="stylesheet">
    <style>
        /* Global Styles */
        body {
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
            position: fixed;
            top: 0;
            left: 0;
            width: 250px;
            height: 100%;
            background-color: #343a40;
            color: #fff;
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: left;
            z-index: 100;
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
            padding: 10px 0;
            margin-bottom: 20px;
            gap: 20px;
        }

        .header h1 {
            color: #36454F;
            font-size: 28px;
        }

        .header-content {
            display: flex;
            align-items: center;
            gap: 20px;
        }

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
                    <li><a href="login.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <div class="header">
                <h1>Manage Subjects</h1>
                <div class="header-content">
                    <div class="profile-bar">
                        <img src="image/profile.png" alt="Profile Picture" class="profile-picture">
                        <div class="profile-info">
                            <h5 class="profile-name">Name</h5>
                            <p class="profile-role">Admin</p>
                        </div>
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

