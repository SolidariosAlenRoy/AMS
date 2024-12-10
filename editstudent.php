<?php
session_start();
require 'db.php';

// Check if ID is provided
if (!isset($_GET['id'])) {
    die("No student ID provided.");
}

$id = $_GET['id'];

// Fetch the student details
$query = "SELECT * FROM students WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();

if (!$student) {
    die("Student not found.");
}

// Fetch the student's section
$section_query = "SELECT section_name FROM sections WHERE id = ?";
$section_stmt = $conn->prepare($section_query);
$section_stmt->bind_param('i', $student['section_id']);
$section_stmt->execute();
$section_result = $section_stmt->get_result();
$section = $section_result->fetch_assoc()['section_name'] ?? '';

// Fetch the student's subjects
$subject_query = "SELECT subject_id FROM student_subjects WHERE student_id = ?";
$subject_stmt = $conn->prepare($subject_query);
$subject_stmt->bind_param('i', $id);
$subject_stmt->execute();
$subject_result = $subject_stmt->get_result();
$student_subjects = [];
while ($row = $subject_result->fetch_assoc()) {
    $student_subjects[] = $row['subject_id'];
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $parent_email = $_POST['parent_email'];
    $contact_number = $_POST['contact_number'];
    $section_name = $_POST['section'];
    $subjects = $_POST['subjects'] ?? [];

    // Check if section exists, if not, insert it
    $section_check_query = "SELECT id FROM sections WHERE section_name = ?";
    $section_check_stmt = $conn->prepare($section_check_query);
    $section_check_stmt->bind_param('s', $section_name);
    $section_check_stmt->execute();
    $section_check_stmt->store_result();

    if ($section_check_stmt->num_rows > 0) {
        $section_check_stmt->bind_result($section_id);
        $section_check_stmt->fetch();
    } else {
        $insert_section_query = "INSERT INTO sections (section_name) VALUES (?)";
        $insert_section_stmt = $conn->prepare($insert_section_query);
        $insert_section_stmt->bind_param('s', $section_name);
        $insert_section_stmt->execute();
        $section_id = $conn->insert_id;
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $name = $_POST['name'];
        $parent_email = $_POST['parent_email'];
        $contact_number = $_POST['contact_number'];
        $section_name = $_POST['section'];
        $year_level = $_POST['year_level']; // Capture year level from the form
        $subjects = $_POST['subjects'] ?? [];
    
        // Check if section exists, if not, insert it
        $section_check_query = "SELECT id FROM sections WHERE section_name = ?";
        $section_check_stmt = $conn->prepare($section_check_query);
        $section_check_stmt->bind_param('s', $section_name);
        $section_check_stmt->execute();
        $section_check_stmt->store_result();
    
        if ($section_check_stmt->num_rows > 0) {
            $section_check_stmt->bind_result($section_id);
            $section_check_stmt->fetch();
        } else {
            $insert_section_query = "INSERT INTO sections (section_name) VALUES (?)";
            $insert_section_stmt = $conn->prepare($insert_section_query);
            $insert_section_stmt->bind_param('s', $section_name);
            $insert_section_stmt->execute();
            $section_id = $conn->insert_id;
        }
    
        // Update student details, including year level
        $update_query = "UPDATE students SET name = ?, parent_email = ?, contact_number = ?, section_id = ?, year_level = ? WHERE id = ?";
        $update_stmt = $conn->prepare($update_query);
        $update_stmt->bind_param('sssiii', $name, $parent_email, $contact_number, $section_id, $year_level, $id);
    
        if ($update_stmt->execute()) {
            // Update student subjects
            $delete_subjects_query = "DELETE FROM student_subjects WHERE student_id = ?";
            $delete_subjects_stmt = $conn->prepare($delete_subjects_query);
            $delete_subjects_stmt->bind_param('i', $id);
            $delete_subjects_stmt->execute();
    
            foreach ($subjects as $subject_id) {
                $insert_subject_query = "INSERT INTO student_subjects (student_id, subject_id) VALUES (?, ?)";
                $insert_subject_stmt = $conn->prepare($insert_subject_query);
                $insert_subject_stmt->bind_param('ii', $id, $subject_id);
                $insert_subject_stmt->execute();
            }
    
            // Redirect to the Add Student page after successful update
        header('Location: addstudent.php');
            exit(); // Ensure no further code is executed after the redirect
        } else {
            echo "<p>Error: " . $update_stmt->error . "</p>";
        }
    }
    
}
$userName = isset($_SESSION['username']) ? $_SESSION['username'] : 'Admin';
?>
``
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
    <!-- External CSS -->
    <link href="css/editstudent1.css" rel="stylesheet"> 
    <!-- External JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/5.11.3/main.min.js"></script>
    <script src="js/editstudent.js"></script>
</head>
<body>
    <div class="container">
    <div class="example-box">
        <div class="background-shapes">
    </div>
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
                    <li><a href="register.php"><i class="fas fa-user-plus"></i> Register Acc.</a></li>
                </ul>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Header Section -->
            <div class="header">
                <h1>Manage Students</h1>
                <div class="header-content">
                    <!-- Profile Bar -->
                    <div class="profile-bar" onclick="toggleDropdown(event)">
                        <img src="image/profile.png" alt="Profile Picture" class="profile-picture"> 
                        <div class="profile-info">
                            <h5 class="profile-name">
                                <?php echo htmlspecialchars($userName); ?>
                            </h5>
                            <p class="profile-role">
                                <?php echo htmlspecialchars($_SESSION['role'] ?? 'admin'); ?>
                            </p>
                        </div>
                    </div>
                    <!-- Dropdown Menu -->
                    <div id="profileDropdown" class="profile-dropdown">
                        <a href="login.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                    </div>
                </div>
            </div>

            <!-- Form Container -->
            <div class="form-container-wrapper">
                <form method="POST" action="editstudent.php?id=<?php echo $id; ?>" onsubmit="return confirmUpdate();">
                    <label for="name">Name:</label>
                    <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($student['name']); ?>" required>

                    <label for="parent_email">Parent Email:</label>
                    <input type="email" id="parent_email" name="parent_email" value="<?php echo htmlspecialchars($student['parent_email']); ?>">

                    <label for="contact_number">Contact Number:</label>
                    <input type="text" id="contact_number" name="contact_number" value="<?php echo htmlspecialchars($student['contact_number']); ?>">

                    <label for="year_level">Year Level:</label>
                    <select name="year_level" id="year_level">
                        <option value="7" <?php if ($student['year_level'] == 7) echo 'selected'; ?>>7</option>
                        <option value="8" <?php if ($student['year_level'] == 8) echo 'selected'; ?>>8</option>
                        <option value="9" <?php if ($student['year_level'] == 9) echo 'selected'; ?>>9</option>
                        <option value="10" <?php if ($student['year_level'] == 10) echo 'selected'; ?>>10</option>
                    </select>

                    <label for="section">Section:</label>
                    <input type="text" id="section" name="section" value="<?php echo htmlspecialchars($section); ?>" required>

                    <label for="subjects">Select Subjects:</label><br>
                    <?php
                    $subjects_query = "SELECT id, subject_name FROM subjects";
                    $subjects_result = $conn->query($subjects_query);

                    while ($row = $subjects_result->fetch_assoc()) {
                        $checked = in_array($row['id'], $student_subjects) ? 'checked' : '';
                        echo "<input type='checkbox' name='subjects[]' value='{$row['id']}' $checked> {$row['subject_name']}<br>";
                    }
                    ?>
                    <button type="submit">Update Student</button>
                </form>
            </div>
        </main>
    </div>
</body>
</html>