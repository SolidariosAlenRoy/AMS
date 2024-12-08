<?php
// Assume the necessary database connection is established
session_start();
require 'db.php';
require 'fpdf/fpdf.php'; // Include FPDF library

// Function to generate PDF
function generatePDF($filename, $title, $headers, $data, $columnWidths) {
    $pdf = new FPDF();
    $pdf->AddPage('P', 'A4'); // 'P' for portrait, 'A4' for page size
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(0, 10, $title, 0, 1, 'C');
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(0, 10, 'Generated on: ' . date('Y-m-d H:i:s'), 0, 1, 'R');
    $pdf->Ln(5);

    // Table headers
    foreach ($headers as $index => $header) {
        $pdf->Cell($columnWidths[$index], 10, $header, 1, 0, 'C');
    }
    $pdf->Ln();

    // Table data
    if (!empty($data)) {
        foreach ($data as $row) {
            foreach ($row as $index => $cell) {
                $pdf->Cell($columnWidths[$index], 10, $cell, 1);
            }
            $pdf->Ln();
        }
    } else {
        $pdf->Cell(array_sum($columnWidths), 10, 'No records found.', 1, 0, 'C');
    }

    // Output the PDF
    $pdf->Output('D', $filename);
    exit;
}

// Function to generate CSV
function generateCSV($filename, $headers, $data) {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '"');

    $output = fopen('php://output', 'w');
    fputcsv($output, $headers);

    if (!empty($data)) {
        foreach ($data as $row) {
            fputcsv($output, $row);
        }
    } else {
        fputcsv($output, ['No records found.']);
    }
    fclose($output);
    exit;
}

// Fetch year levels, sections, students, and subjects from the database
$year_levels_result = $conn->query("SELECT DISTINCT year_level FROM students");
$sections_result = $conn->query("SELECT * FROM sections");
$students_result = $conn->query("SELECT * FROM students");
$subjects_result = $conn->query("SELECT * FROM subjects");

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['view_class_attendance']) || isset($_POST['generate_class_pdf']) || isset($_POST['generate_class_csv'])) {
        $attendance_date = $_POST['attendance_date'];
        $year_level = $_POST['year_level'];
        $section_id = $_POST['section_id'];
        $subject_id = isset($_POST['subject_id']) ? $_POST['subject_id'] : null;

        $query = "
            SELECT a.attendance_date, s.name AS student_name, a.year_level, sec.section_name, sub.subject_name, a.status
            FROM attendance a
            JOIN students s ON a.student_id = s.id
            JOIN sections sec ON a.section_id = sec.id
            JOIN subjects sub ON a.subject_id = sub.id
            WHERE a.attendance_date = ? AND a.year_level = ? AND a.section_id = ?
        ";
        if ($subject_id) {
            $query .= " AND a.subject_id = ?";
        }

        $query .= " ORDER BY a.attendance_date DESC";
        $stmt = $conn->prepare($query);

        if ($subject_id) {
            $stmt->bind_param("ssii", $attendance_date, $year_level, $section_id, $subject_id);
        } else {
            $stmt->bind_param("ssi", $attendance_date, $year_level, $section_id);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        $attendance_records = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];

        $headers = ['Date', 'Student Name', 'Year Level', 'Section', 'Subject', 'Status'];
        $data = array_map(function ($record) {
            return [
                $record['attendance_date'],
                $record['student_name'],
                $record['year_level'],
                $record['section_name'],
                $record['subject_name'],
                $record['status']
            ];
        }, $attendance_records);

        // Generate PDF
        if (isset($_POST['generate_class_pdf'])) {
            $columnWidths = [30, 50, 20, 25, 35, 30];
            generatePDF('class_attendance.pdf', 'Class Attendance Report', $headers, $data, $columnWidths);
        }

        // Generate CSV
        if (isset($_POST['generate_class_csv'])) {
            generateCSV('class_attendance.csv', $headers, $data);
        }
    } elseif (isset($_POST['view_student_attendance']) || isset($_POST['generate_student_pdf']) || isset($_POST['generate_student_csv'])) {
        $attendance_date = $_POST['attendance_date'];
        $student_id = $_POST['student'];

        $query = "
            SELECT a.attendance_date, s.name AS student_name, a.year_level, sec.section_name, sub.subject_name, a.status
            FROM attendance a
            JOIN students s ON a.student_id = s.id
            JOIN sections sec ON a.section_id = sec.id
            JOIN subjects sub ON a.subject_id = sub.id
            WHERE a.attendance_date = ? AND a.student_id = ?
            ORDER BY a.attendance_date DESC
        ";

        $stmt = $conn->prepare($query);
        $stmt->bind_param("si", $attendance_date, $student_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $student_attendance_records = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];

        $headers = ['Date', 'Student Name', 'Year Level', 'Section', 'Subject', 'Status'];
        $data = array_map(function ($record) {
            return [
                $record['attendance_date'],
                $record['student_name'],
                $record['year_level'],
                $record['section_name'],
                $record['subject_name'],
                $record['status']
            ];
        }, $student_attendance_records);

        // Generate PDF
        if (isset($_POST['generate_student_pdf'])) {
            $columnWidths = [30, 50, 20, 25, 35, 30];
            generatePDF('student_attendance.pdf', 'Student Attendance Report', $headers, $data, $columnWidths);
        }

        // Generate CSV
        if (isset($_POST['generate_student_csv'])) {
            generateCSV('student_attendance.csv', $headers, $data);
        }
    }
}

$userName = isset($_SESSION['username']) ? $_SESSION['username'] : 'Teacher';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Class Attendance</title>
    <!-- FontAwesome for icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="css/vclassattendance1.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <!-- Sidebar -->
        <aside class="sidebar">
    <img src="image/logo3.jpg" alt="Logo" class="logo"> 
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
                <h1>View Class Attendance</h1>
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

            <div>
    <!-- Tabs -->
    <div class="tab" data-tab="classAttendance">View Class Attendance</div>
    <div class="tab" data-tab="studentAttendance">View Student Attendance</div>
</div>

<!-- Class Attendance Tab Content -->
    <div class="tab-content active" id="classAttendance">
                <h3>View Class Attendance</h3>

            <form id="classAttendanceForm" method="POST">
                <label for="attendanceDate">Date:</label>
                <input type="date" id="attendanceDate" name="attendance_date" value="<?= isset($_POST['attendance_date']) ? $_POST['attendance_date'] : '' ?>" required>

                <label for="yearLevelDropdown">Year Level:</label>
                <select name="year_level" id="yearLevelDropdown" required>
                    <option value="">Select Year Level</option>
                    <?php while ($row = $year_levels_result->fetch_assoc()) : ?>
                    <option value="<?= $row['year_level'] ?>" <?= isset($_POST['year_level']) && $_POST['year_level'] == $row['year_level'] ? 'selected' : '' ?>>
                        <?= $row['year_level'] ?>
                    </option>
                    <?php endwhile; ?>
                </select>

                <label for="sectionDropdown">Section:</label>
                <select name="section_id" id="sectionDropdown" required>
                    <option value="">Select Section</option>
                    <?php while ($row = $sections_result->fetch_assoc()) : ?>
                    <option value="<?= $row['id'] ?>" <?= isset($_POST['section_id']) && $_POST['section_id'] == $row['id'] ? 'selected' : '' ?>>
                        <?= $row['section_name'] ?>
                    </option>
                    <?php endwhile; ?>
                </select>


                <label for="subjectDropdown">Subject:</label>
                <select name="subject_id" id="subjectDropdown" required>
                <option value="">Select Subject</option>
                    <?php while ($row = $subjects_result->fetch_assoc()) : ?>
                <option value="<?= $row['id'] ?>" <?= isset($_POST['subject_id']) && $_POST['subject_id'] == $row['id'] ? 'selected' : '' ?>>
                    <?= $row['subject_name'] ?>
                </option>
                <?php endwhile; ?>
                </select>

                <button type="submit" name="view_class_attendance">View Attendance</button>
                <button type="submit" name="generate_class_pdf">Generate PDF</button>
                <button type="submit" name="generate_class_csv">Generate CSV</button>
            </form>

                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Student Name</th>
                            <th>Year Level</th>
                            <th>Section</th>
                            <th>Subject</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($attendance_records)): ?>
                            <?php foreach ($attendance_records as $record): ?>
                                <tr>
                                    <td><?= $record['attendance_date'] ?></td>
                                    <td><?= $record['student_name'] ?></td>
                                    <td><?= $record['year_level'] ?></td>
                                    <td><?= $record['section_name'] ?></td>
                                    <td><?= $record['subject_name'] ?></td>
                                    <td><?= $record['status'] ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6">No records found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Student Attendance Tab Content -->
            <div class="tab-content" id="studentAttendance">
                <h3>View Student Attendance</h3>
                <form id="studentAttendanceForm" method="POST">
                    <label for="attendanceDate">Date:</label>          
                    <input type="date" id="attendanceDate" name="attendance_date" value="<?= htmlspecialchars($_POST['attendance_date'] ?? '') ?>" required>


                    <label for="studentDropdown">Student:</label>
                    <select name="student" id="studentDropdown" required>
                    <option value="">Select Student</option>
                    <?php while ($row = $students_result->fetch_assoc()) : ?>
                    <option value="<?= $row['id'] ?>" <?= isset($_POST['student']) && $_POST['student'] == $row['id'] ? 'selected' : '' ?>>
                        <?= $row['name'] ?>
                    </option>
                    <?php endwhile; ?>
                    </select>

                    <button type="submit" name="view_student_attendance">View Attendance</button>
                    <button type="submit" name="generate_student_pdf">Generate PDF</button>
                    <button type="submit" name="generate_student_csv">Generate CSV</button>
                </form>

                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Student Name</th>
                            <th>Year Level</th>
                            <th>Section</th>
                            <th>Subject</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($student_attendance_records)): ?>
                            <?php foreach ($student_attendance_records as $record): ?>
                                <tr>
                                    <td><?= $record['attendance_date'] ?></td>
                                    <td><?= $record['student_name'] ?></td>
                                    <td><?= $record['year_level'] ?></td>
                                    <td><?= $record['section_name'] ?></td>
                                    <td><?= $record['subject_name'] ?></td>
                                    <td><?= $record['status'] ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6">No records found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
    <script src="js/vclassattendance.js"></script>
</body>
</html>