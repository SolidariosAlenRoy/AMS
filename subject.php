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
    <link href="css/subject.css" rel="stylesheet"> 
    <script src="js/subject.js"></script>
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
</body>
</html>