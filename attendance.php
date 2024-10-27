<?php
session_start();
require 'db.php';

// Initialize variables
$section_id = null;
$subject_id = null;
$year_level = null;
$subject_name = ''; // Initialize subject_name to avoid undefined variable warning

// Display students based on selected section, subject, and year level
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $section_id = $_POST['section'];
    $subject_id = $_POST['subject'];
    $year_level = $_POST['year_level'];

    // Only fetch subject name if $subject_id is set
    if ($subject_id) {
        $subject_query = "SELECT subject_name FROM subjects WHERE id = ?";
        $stmt_subject = $conn->prepare($subject_query);
        $stmt_subject->bind_param('i', $subject_id);
        $stmt_subject->execute();
        $stmt_subject->bind_result($subject_name);
        $stmt_subject->fetch();
        $stmt_subject->close();
    }
}
?>

    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Attendance</title>
        <!-- FontAwesome for icons -->
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
        <!-- FullCalendar CSS -->
        <link href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/5.11.3/main.min.css" rel="stylesheet">

        <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/5.11.3/main.min.js"></script>
        <link href="css/dashboard.css" rel="stylesheet">
        <style>

/* General Reset */
body, h2, h4, p, ul {
    margin: 0;
    padding: 0;
}

/* Layout */
.container {
    display: flex;
    height: 100vh;
}

.sidebar {
    width: 250px;
    background-color: #343a40;
    color: #fff;
    padding: 20px;
    display: flex;
    flex-direction: column;
    align-items: center;
}

.main-content {
    flex: 1;
    padding: 20px;
    background-color: #fff;
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

/* Sidebar */
.logo {
    width: 100%;
    max-width: 180px;
    margin-bottom: 20px;
    display: block;
    border-radius: 100px;
}

.sidebar h4 {
    font-size: 24px;
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

/* Header */
.header h1 {
    color: #007bff;
    font-size: 28px;
    margin: 0;
}

.header-content {
    display: flex;
    align-items: center;
    gap: 20px;
}

/* Search Bar */
.search-bar {
    display: flex;
    align-items: center;
    gap: 10px;
}

.search-input {
    padding: 10px;
    border: 1px solid #ced4da;
    border-radius: 5px;
    width: 250px;
    height: 40px;
    font-size: 16px;
}

.search-button {
    background-color: #007bff;
    color: #fff;
    border: none;
    padding: 10px 15px;
    border-radius: 5px;
    cursor: pointer;
    font-size: 16px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Profile Bar */
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

/* Typography */
h1 {
    color: #030303;
    font-family: 'Times New Roman', Times, serif;
    font-size: 24px;
    margin-bottom: 20px;
}

label {
    font-weight: bold;
    margin-bottom: 10px;
    display: block;
    font-size: 16px;
}

/* Form */
.form-container {
    display: flex;
    justify-content: center;
    align-items: center;
    flex-direction: column;
    gap: 20px;
    margin-top: 20px;
    height: auto;
}

form {
    flex: 1;
    padding: 10px;
    width: 80%;
    max-width: 500px;
    background-color: #f8f9fa;
    border: 1px solid #ced4da;
    border-radius: 8px;
    box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
}

/* Dropdown */
select {
    width: 100%;
    padding: 10px;
    margin-bottom: 20px;
    border: 1px solid #ced4da;
    border-radius: 4px;
    font-size: 16px;
    background-color: #fff;
    color: #333;
}

select:focus {
    outline: none;
    border-color: #007bff;
    box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
}

/* Buttons */
.buttons-container {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    margin-top: 20px;
}

button {
    background-color: #007bff;
    color: #fff;
    padding: 8px 12px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 14px;
    width: auto;
}

button:hover {
    background-color: #0056b3;
}

/* Table */
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

th, td {
    border: 1px solid #ced4da;
    padding: 12px;
    text-align: left;
    font-size: 14px;
}

th {
    background-color: #00d4ff;
    font-weight: bold;
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

/* Responsive */
@media (max-width: 768px) {
    .form-container {
        flex-direction: column;
    }

    button {
        width: 100%;
        margin-top: 10px;
    }
}


        </style>
    </head>
    <body>
    <div class="container">
        <!-- Sidebar -->
        <aside class="sidebar">
    <img src="image/classtrack.png" alt="Logo" class="logo"> 
    <h4 class="text-primary"><i class=""></i>CLASS TRACK</h4>
    <nav class="nav">
        <ul>
            <li><a href="teacherint.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a href="vclasslist.php"><i class="fas fa-list-alt"></i> View Class List</a></li>
            <li><a href="attendance.php"><i class="fas fa-user-check"></i> Take Attendance</a></li>
            <li><a href="vclassattendance.php"><i class="fas fa-eye"></i> View Class Attendance</a></li>
            <li><a href="email.php"><i class="fas fa-envelope"></i> Generate E-mail</a></li>
        </ul>
    </nav>
</aside>

            <!-- Main Content -->
            <main class="main-content">
                <div class="header">
                    <h1>Student Attendance</h1>
                    <div class="header-content">
                        <div class="search-bar">
                            <input type="text" placeholder="Search..." class="search-input">
                            <button class="search-button"><i class="fas fa-search"></i></button>
                        </div>
                        <div class="profile-bar">
                            <img src="image/profile.png" alt="Profile Picture" class="profile-picture">
                            <div class="profile-info">
                                <h5 class="profile-name">Name</h5>
                                <p class="profile-role">Teacher</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- HTML Form for Attendance -->
        <div class="form-container">
            <form method="POST" action="attendance.php">
                <label for="year_level">Select Year Level:</label>
                <select name="year_level" required>
                    <option value="">--select--</option>
                    <?php
                    $year_level_query = "SELECT DISTINCT year_level FROM students";
                    $year_levels = $conn->query($year_level_query);
                    while ($year = $year_levels->fetch_assoc()) {
                        echo "<option value='{$year['year_level']}'>{$year['year_level']}</option>";
                    }
                    ?>
                </select>

                <label for="section">Select Section:</label>
                <select name="section" required>
                    <option value="">--select--</option>
                    <?php
                    $section_query = "SELECT id, section_name FROM sections";
                    $sections = $conn->query($section_query);
                    while ($section = $sections->fetch_assoc()) {
                        echo "<option value='{$section['id']}'>{$section['section_name']}</option>";
                    }
                    ?>
                </select>

                <label for="subject">Select Subject:</label>
                <select name="subject" required>
                    <option value="">--select--</option>
                    <?php
                    $subject_query = "SELECT id, subject_name FROM subjects";
                    $subjects = $conn->query($subject_query);
                    while ($subject = $subjects->fetch_assoc()) {
                        echo "<option value='{$subject['id']}'>{$subject['subject_name']}</option>";
                    }
                    ?>
                </select>

                <button type="submit">Load Students</button>
            </form>

            <form method="POST" action="save_attendance.php">
    <input type="hidden" name="year_level" value="<?php echo $year_level; ?>">
    <input type="hidden" name="section" value="<?php echo $section_id; ?>">
    <input type="hidden" name="subject" value="<?php echo $subject_id; ?>">
    <!-- Student Table and Attendance Dropdown -->
    <table>
        <thead>
            <tr>
                <th>Student Name</th>
                <th>Year Level</th>
                <th>Section</th>
                <th>Subject</th>
                <th>Attendance</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if (isset($section_id, $subject_id, $year_level)) {
                $students_query = "
                    SELECT s.id, s.name, s.year_level, sec.section_name 
                    FROM students s 
                    JOIN sections sec ON s.section_id = sec.id 
                    WHERE sec.id = ? AND s.year_level = ?";
                $stmt_students = $conn->prepare($students_query);
                $stmt_students->bind_param('is', $section_id, $year_level);
                $stmt_students->execute();
                $students = $stmt_students->get_result();

                if ($students->num_rows > 0) {
                    while ($row = $students->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>{$row['name']}</td>";
                        echo "<td>{$row['year_level']}</td>";
                        echo "<td>{$row['section_name']}</td>";
                        echo "<td>$subject_name</td>";
                        echo "<td>
                                <select name='attendance[{$row['id']}]' required>
                                    <option value=''>Select Attendance</option>
                                    <option value='Present'>Present</option>
                                    <option value='Absent'>Absent</option>
                                    <option value='Late'>Late</option>
                                </select>
                              </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>No students found in the selected section and year level.</td></tr>";
                }
            } else {
                echo "<tr><td colspan='5'>Please select a section, year level, and subject to load students.</td></tr>";
            }
            ?>
        </tbody>
    </table>
    <div class="buttons-container">
        <button type="submit" name="save_attendance">Save Attendance</button>
        <button type="submit" name="send_email" formaction="email.php">Send Email</button> <!-- New Send Email button -->
    </div>
</form>



        </div>

            </main>
        </div>
    </body>
    </html>