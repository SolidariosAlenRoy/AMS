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

    
    <style>
/* Global Styles */
body, h2, h4, p, ul{
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


/* Card Container */
.card-container {
    display: flex;
    justify-content: center; 
    flex-wrap: wrap; 
    gap: 20px; 
    margin-top: 20px; 
}

/* Schedule Card Specific Styles */
.schedule-card {
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    text-align: center;
}


/* Dashboard Card */
.dashboard-card {
    background-color: #7393B3;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    border-radius: 10px;
    padding: 20px;
    text-align: center;
    min-width: 200px;
    flex: 1 0 200px; 
    max-width: 250px; 
}

.dashboard-icon {
    font-size: 40px;
    margin-bottom: 10px;
}

.dashboard-title {
    font-size: 16px;
    text-transform: uppercase;
    color: #36454F;
    margin-bottom: 5px;
}

.dashboard-value {
    font-size: 32px;
    font-weight: bold;
    color: #333;
}

/* Info Container */
.info-container {
    display: flex; 
    justify-content: space-between; 
    max-width: 1200px; 
    width: 100%; 
    margin: 20px auto; 
    height: auto;   
}


/* Dropdown Common Styles */
select {
  width: 460px; 
  padding: 10px; 
  border: 1px solid #ced4da;
  border-radius: 5px; 
  background-color: #fff; 
  color: #333; 
  font-size: 16px; 
  appearance: none; 
  cursor: pointer;
}

select:hover {
  border-color: #6c757d; 
}

select:focus {
  outline: none; 
  border-color: #007bff; 
  box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
}

select option {
  padding: 10px; 
}

/* Styling for the dropdown container */
.dropdown-container {
  border: 2px solid #6c757d;
  padding: 15px;
  border-radius: 10px;
  background-color: #f8f9fa;
  margin-bottom: 20px;
}

/* Styling for the dropdown row */
.dropdown-row {
  display: flex;
  justify-content: space-between;
  gap: 10px;
}

/* Label styling */
.label {
  font-weight: bold;
}

/* Card Container */
.card-container {
            display: flex;
            justify-content: center; 
            flex-wrap: wrap; 
            gap: 20px; 
            margin-top: 20px; 
        }

        /* Card Styling */
        .card {
            background-color: #fff;
            border: 2px solid #6c757d;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }

        .card h2 {
            font-size: 20px;
            margin-bottom: 15px;
            color: #007bff;
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #ced4da; 
            padding: 10px; 
            text-align: left;
        }

        th {
            background-color: #708090;
            color: #fff;
        }

        /* Button Styling */
.btn {
    display: inline-block;
    padding: 10px 20px;
    font-size: 16px;
    text-transform: uppercase;
    font-weight: bold;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: all 0.3s ease-in-out;
    margin-right: 10px; /* Add spacing between buttons */
}

/* Primary Button (CSV) */
.btn-primary {
    background-color: #007bff; /* Blue */
    color: white;
}

.btn-primary:hover {
    background-color: #0056b3; /* Darker blue on hover */
}

/* Secondary Button (PDF) */
.btn-secondary {
    background-color: #6c757d; /* Gray */
    color: white;
}

.btn-secondary:hover {
    background-color: #495057; /* Darker gray on hover */
}

/* Add some spacing to ensure buttons look good in forms */
form .btn {
    margin-top: 20px;
}


    </style>
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
                    
                    <div class="profile-bar">
                        <img src="image/profile.png" alt="Profile Picture" class="profile-picture"> <!-- Example profile image -->
                        <div class="profile-info">
                            <h5 class="profile-name">Name</h5>
                            <p class="profile-role">Teacher</p>
                        </div>
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

<script>
    $(document).ready(function() {
        // Load the class list when either dropdown is changed
        $('#sectionDropdown, #yearLevelDropdown').change(function() {
            var sectionId = $('#sectionDropdown').val();
            var yearLevel = $('#yearLevelDropdown').val();
            if (sectionId && yearLevel) {
                loadClassList(sectionId, yearLevel, 1); // Load page 1 by default
            } else {
                // If no selection, reset the table to default message
                $('#classListTable tbody').html('<tr><td colspan="5" style="text-align: center;">Please select a year level and section.</td></tr>');
            }
        });

        // Function to load class list with pagination
        function loadClassList(sectionId, yearLevel, page) {
            $.ajax({
                url: 'fetch.php',
                type: 'POST',
                data: {section_id: sectionId, year_level: yearLevel, page: page},
                success: function(response) {
                    $('#classList').html(response); // Replace the table with the new class list data
                    // Add event listener for pagination buttons
                    $('.pagination-btn').on('click', function() {
                        var selectedPage = $(this).data('page');
                        loadClassList(sectionId, yearLevel, selectedPage);
                    });
                }
            });
        }
    });

    
</script>
</body>
</html>