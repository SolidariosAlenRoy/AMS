<?php
session_start();
require 'db.php';

// Initialize variables
$section_id = null;
$subject_id = null;
$year_level = null;
$attendance_date = date('Y-m-d'); // Default to today's date
$subject_name = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $section_id = $_POST['section'] ?? null;
    $subject_id = $_POST['subject'] ?? null;
    $year_level = $_POST['year_level'] ?? null;
    $attendance_date = $_POST['attendance_date'] ?? date('Y-m-d');

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
$userName = isset($_SESSION['username']) ? $_SESSION['username'] : 'Teacher';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/tattendance.css">
</head>
<body>
    <div class="container">
    <div class="example-box">
  <div class="background-shapes">
  </div>
        <aside class="sidebar">
            <img src="image/logo3.jpg" alt="Logo" class="logo">
            <h4 class="text-primary">CLASS TRACK</h4>
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

        <main class="main-content">
                <div class="header">
                    <h1>Student Attendance</h1>
                    <div class="header-content">
                            <!-- Profile Bar -->
                <div class="profile-bar" onclick="toggleDropdown(event)">
                    <img src="image/profile.png" alt="Profile Picture" class="profile-picture"> 
                    <div class="profile-info">
                        <h5 class="profile-name"><?php echo htmlspecialchars($userName); ?></h5>
                        <p class="profile-role"><?php echo htmlspecialchars($_SESSION['role'] ?? 'teacher'); ?></p>
                    </div>
                </div>
                <!-- Dropdown Menu -->
                <div id="profileDropdown" class="profile-dropdown">
                    <a href="login.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </div>
                    </div>
                </div>

                <div class="form-container-wrapper">
            <form method="POST" action="attendance.php" class="form-container">
                <div class="form-grid">
                    <!-- Year Level -->
                    <div class="form-item">
                        <label for="year_level">Select Year Level:</label>
                        <select name="year_level" required>
                            <option value="">--select--</option>
                            <?php
                            $year_query = "SELECT DISTINCT year_level FROM students";
                            $year_result = $conn->query($year_query);
                            while ($year = $year_result->fetch_assoc()) {
                                $selected = ($year_level === $year['year_level']) ? 'selected' : '';
                                echo "<option value='{$year['year_level']}' $selected>{$year['year_level']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <!-- Section -->
                    <div class="form-item">
                        <label for="section">Select Section:</label>
                        <select name="section" required>
                            <option value="">--select--</option>
                            <?php
                            $section_query = "SELECT id, section_name FROM sections";
                            $section_result = $conn->query($section_query);
                            while ($section = $section_result->fetch_assoc()) {
                                $selected = ($section_id == $section['id']) ? 'selected' : '';
                                echo "<option value='{$section['id']}' $selected>{$section['section_name']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <!-- Subject -->
                    <div class="form-item">
                        <label for="subject">Select Subject:</label>
                        <select name="subject" required>
                            <option value="">--select--</option>
                            <?php
                            $subject_query = "SELECT id, subject_name FROM subjects";
                            $subject_result = $conn->query($subject_query);
                            while ($subject = $subject_result->fetch_assoc()) {
                                $selected = ($subject_id == $subject['id']) ? 'selected' : '';
                                echo "<option value='{$subject['id']}' $selected>{$subject['subject_name']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <!-- Attendance Date -->
                    <div class="form-item">
                        <label for="attendance_date">Select Attendance Date:</label>
                        <input type="date" name="attendance_date" value="<?php echo $attendance_date; ?>" required>
                    </div>
                    <div>
                        <button type="submit">Load Students</button>
                    </div>
                </div>
            </form>
            </div>

            <!-- Attendance Table -->
            <div class="form-container-wrapper">
            <form method="POST" action="save_attendance.php">
                <input type="hidden" name="year_level" value="<?php echo htmlspecialchars($year_level); ?>">
                <input type="hidden" name="section" value="<?php echo htmlspecialchars($section_id); ?>">
                <input type="hidden" name="subject" value="<?php echo htmlspecialchars($subject_id); ?>">
                <input type="hidden" name="attendance_date" value="<?php echo htmlspecialchars($attendance_date); ?>">

                <div class="table-container-wrapper">
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
                        if ($section_id && $subject_id && $year_level) {
                            $students_query = "
                                SELECT s.id, s.name, s.year_level, sec.section_name 
                                FROM students s 
                                JOIN sections sec ON s.section_id = sec.id 
                                WHERE s.year_level = ? AND sec.id = ?";
                            $stmt = $conn->prepare($students_query);
                            $stmt->bind_param('si', $year_level, $section_id);
                            $stmt->execute();
                            $students = $stmt->get_result();
                            if ($students->num_rows > 0) {
                                while ($student = $students->fetch_assoc()) {
                                    echo "<tr>
                                        <td>{$student['name']}</td>
                                        <td>{$student['year_level']}</td>
                                        <td>{$student['section_name']}</td>
                                        <td>$subject_name</td>
                                        <td>
                                            <select name='attendance[{$student['id']}]' required>
                                                <option value=''>Select</option>
                                                <option value='Present'>Present</option>
                                                <option value='Absent'>Absent</option>
                                                <option value='Late'>Late</option>
                                            </select>
                                        </td>
                                    </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='5'>No students found for the selected criteria.</td></tr>";
                            }
                        } else {
                            echo "<tr><td colspan='5'>Please select criteria to load students.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
                </div>

                <div class="buttons-container">
                <button type="submit" name="save_attendance">Save Attendance</button>
                <button type="submit" name="send_email" formaction="email.php">Send Email</button>
                </div>
            </form>
            </div>
            </div>
        </main>
    </div>
    <script src="js/attendance.js"></script>
</body>
</html>
