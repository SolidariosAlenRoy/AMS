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
    <link rel="stylesheet" href="css/addstudent1.css">
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
<script src="js/addstudent.js"></script>
</body>
</html>