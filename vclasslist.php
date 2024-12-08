<?php
require 'db.php';
require 'fpdf/fpdf.php'; // Include FPDF
session_start();

// Fetch sections for the dropdown menu
$sections_query = "SELECT * FROM sections";
$sections_result = $conn->query($sections_query);

// Fetch distinct year levels from the students table
$year_levels_query = "SELECT DISTINCT year_level FROM students";
$year_levels_result = $conn->query($year_levels_query);

// Function to generate CSV
if (isset($_POST['generate_csv'])) {
    $section = $_POST['section'];
    $year_level = $_POST['year_level'];

    $query = "SELECT students.name, students.year_level, sections.section_name, students.parent_email, students.contact_number 
              FROM students 
              INNER JOIN sections ON students.section_id = sections.id 
              WHERE students.year_level = '$year_level' AND students.section_id = '$section'";
    $result = $conn->query($query);

    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="class_list.csv"');

    $output = fopen("php://output", "w");
    fputcsv($output, ['Student Name', 'Year Level', 'Section', 'Parent\'s Email', 'Contact Number']);

    while ($row = $result->fetch_assoc()) {
        fputcsv($output, $row);
    }
    fclose($output);
    exit;
}

// Function to generate PDF
if (isset($_POST['generate_pdf'])) {
    $section = $_POST['section'];
    $year_level = $_POST['year_level'];

    $query = "SELECT students.name, students.year_level, sections.section_name, students.parent_email, students.contact_number 
              FROM students 
              INNER JOIN sections ON students.section_id = sections.id 
              WHERE students.year_level = '$year_level' AND students.section_id = '$section'";
    $result = $conn->query($query);

   // Function to generate PDF
if (isset($_POST['generate_pdf'])) {
    $section = $_POST['section'];
    $year_level = $_POST['year_level'];

    $query = "SELECT students.name, students.year_level, sections.section_name, students.parent_email, students.contact_number 
              FROM students 
              INNER JOIN sections ON students.section_id = sections.id 
              WHERE students.year_level = '$year_level' AND students.section_id = '$section'";
    $result = $conn->query($query);

    // Create a new PDF instance
    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(0, 10, "CLASS LIST", 0, 1, 'C');
    $pdf->Ln(5);

    // Add generated time and date
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(0, 10, 'Generated on: ' . date('Y-m-d H:i:s'), 0, 1, 'R'); // Align to the right
    $pdf->Ln(5);

    // Table header
    $pdf->SetFont('Arial', 'B', 10);
    $cellWidths = [50, 30, 30, 50, 30]; // Adjust column widths proportionally (total 190mm)
    $headers = ['Student Name', 'Year Level', 'Section', "Parent's Email", 'Contact Number'];

    foreach ($headers as $key => $header) {
        $pdf->Cell($cellWidths[$key], 10, $header, 1, 0, 'C');
    }
    $pdf->Ln();

    // Table rows
    $pdf->SetFont('Arial', '', 10);
    while ($row = $result->fetch_assoc()) {
        $pdf->Cell($cellWidths[0], 10, $row['name'], 1);
        $pdf->Cell($cellWidths[1], 10, $row['year_level'], 1);
        $pdf->Cell($cellWidths[2], 10, $row['section_name'], 1);
        $pdf->Cell($cellWidths[3], 10, $row['parent_email'], 1);
        $pdf->Cell($cellWidths[4], 10, $row['contact_number'], 1);
        $pdf->Ln();
    }

    // Output the PDF
    $pdf->Output('D', 'class_list.pdf');
    exit;
}
}

$userName = isset($_SESSION['username']) ? $_SESSION['username'] : 'Teacher';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Class List</title>
    <!-- FontAwesome for icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <!-- FullCalendar CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/5.11.3/main.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
    <link href="css/vclasslist.css" rel="stylesheet">
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
                <h1>View Class List</h1>
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

            <div class="card">
            <form method="POST" id="sectionForm">
            <label for="yearLevelDropdown">Select Year Level:</label>
            <select name="year_level" id="yearLevelDropdown" required>
                <option value="">Select Year Level</option>
                <?php while ($row = $year_levels_result->fetch_assoc()) : ?>
                    <option value="<?= $row['year_level'] ?>"><?= $row['year_level'] ?></option>
                <?php endwhile; ?>
            </select>

            <label for="section">Select Section:</label>
            <select name="section" id="sectionDropdown" required>
                <option value="">Select Section</option>
                <?php while ($row = $sections_result->fetch_assoc()) : ?>
                    <option value="<?= $row['id'] ?>"><?= $row['section_name'] ?></option>
                <?php endwhile; ?>
            </select>
            <button type="submit" name="generate_csv" class="btn btn-primary">Generate CSV</button>
            <button type="submit" name="generate_pdf" class="btn btn-secondary">Generate PDF</button>
        </form>
        </div>

        <div class="card" id="classList">
            <!-- Empty table structure -->
            <table id="classListTable">
                <thead>
                    <tr>
                        <th>Student Name</th>
                        <th>Year Level</th>
                        <th>Section</th>
                        <th>Parent's Email</th>
                        <th>Contact Number</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="5" style="text-align: center;">Please select a year level and section.</td>
                    </tr>
                </tbody>
            </table>
            
        </div>
    </main>
</div>
<script src="js/vclasslist.js"></script>
</body>
</html>