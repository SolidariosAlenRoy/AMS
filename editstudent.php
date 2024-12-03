<?php
require 'db.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Fetch the student details
    $query = "SELECT * FROM students WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $student = $result->fetch_assoc();

    // Fetch the student's section
    $section_query = "SELECT section_name FROM sections WHERE id = ?";
    $section_stmt = $conn->prepare($section_query);
    $section_stmt->bind_param('i', $student['section_id']);
    $section_stmt->execute();
    $section_result = $section_stmt->get_result();
    $section = $section_result->fetch_assoc()['section_name'];

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

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $name = $_POST['name'];
        $parent_email = $_POST['parent_email'];
        $contact_number = $_POST['contact_number'];
        $section_name = $_POST['section'];
        $subjects = isset($_POST['subjects']) ? $_POST['subjects'] : [];

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

        // Update student details
        $update_query = "UPDATE students SET name = ?, parent_email = ?, contact_number = ?, section_id = ? WHERE id = ?";
        $update_stmt = $conn->prepare($update_query);
        $update_stmt->bind_param('sssii', $name, $parent_email, $contact_number, $section_id, $id);

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

            echo "Student updated successfully!";
        } else {
            echo "Error: " . $update_stmt->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin's Dashboard</title>
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

/* Subject Checkboxes */
label[for="subjects"] {
    font-weight: bold;
    margin-top: 5px;
    display: block;
}

input[type="checkbox"] {
    margin-right: 10px;
    transform: scale(1.2);
    margin-bottom: 10px;
    cursor: pointer;
}

/* Dropdown */
select {
    width: 17%; /* Ensure dropdown takes full width */
    max-width: 300px; /* Add max width for better control */
    padding: 5px;
    margin-bottom: 20px;
    border: 1px solid #ced4da;
    border-radius: 4px;
    font-size: 16px;
    background-color: #fff;
    color: #333;
}

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




    </style>
</head>
<body>
<div class="container">
    <!-- Sidebar -->
    <aside class="sidebar">
        <img src="image/logo3.jpg" alt="Logo" class="logo"> 
        <h4 class="text-primary">CLASS TRACK</h4>
        <nav class="nav">
            <ul>
                <li><a href="admin.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="addstudent.php"><i class="fas fa-user-graduate"></i> Manage Students</a></li>
                <li><a href="addteacher.php"><i class="fas fa-chalkboard-teacher"></i> Manage Teacher</a></li>
                <li><a href="subject.php"><i class="fas fa-book"></i> Manage Subject</a></li>
                <li><a href="login.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <div class="header">
            <h1>Admin's Dashboard</h1>
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

        <!-- Form Container -->
        <div class="form-container-wrapper">
            <form method="POST" action="editstudent.php?id=<?php echo $id; ?>">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" value="<?php echo $student['name']; ?>" required>

                <label for="parent_email">Parent Email:</label>
                <input type="email" id="parent_email" name="parent_email" value="<?php echo $student['parent_email']; ?>">

                <label for="contact_number">Contact Number:</label>
                <input type="text" id="contact_number" name="contact_number" value="<?php echo $student['contact_number']; ?>">

                <label for="section">Section:</label>
                <input type="text" id="section" name="section" value="<?php echo $section; ?>" required>

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