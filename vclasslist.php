<?php
require 'db.php'; // Ensure this file contains the correct database connection
session_start();

// Fetch sections for the dropdown menu
$sections_query = "SELECT * FROM sections";
$sections_result = $conn->query($sections_query);

// Fetch distinct year levels from the students table
$year_levels_query = "SELECT DISTINCT year_level FROM students";
$year_levels_result = $conn->query($year_levels_query);

// Check if queries succeeded
if (!$sections_result || !$year_levels_result) {
    die("Error fetching data: " . $conn->error);
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

    <style>
        body, h2, h4, p, ul {
            margin: 0;
            padding: 0;
        }

        .container {
            display: flex;
            height: 100vh;
        }

        .sidebar {
            width: 200px;
            background-color: #e0e0e0;
            padding: 20px;
        }

        .sidebar h4 {
            color: black;
            margin-bottom: 20px;
            text-align: center;
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #ced4da;
            padding-bottom: 10px;
            margin-bottom: 10px;
        }

        .search-bar {
            display: flex;
            align-items: center;
        }

        .search-input {
            flex-grow: 1;
            padding: 10px;
            border: 1px solid #ced4da;
            border-radius: 5px;
            width: 300px;
            height: 20px;
            margin-right: 5px;
        }

        .search-button {
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
        }

        .profile-bar {
            display: flex;
            align-items: center;
            margin-left: 20px;
            border-left: 1px solid #ced4da;
            padding-left: 20px;
        }

        .profile-picture {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 10px;
        }

        .profile-info {
            text-align: left;
        }

        .profile-name {
            font-size: 16px;
            margin: 0;
        }

        .profile-role {
            font-size: 12px;
            color: #6c757d;
        }

        .nav {
            margin-top: 20px;
        }

        .nav ul {
            list-style: none;
        }

        .nav ul li {
            margin-bottom: 15px;
        }

        .nav ul li a {
            text-decoration: none;
            color: #343a40;
        }

        .nav ul li a:hover {
            color: #007bff;
        }

        .logo {
            width: 100%;
            max-width: 180px;
            margin-bottom: 20px;
            display: block;
            margin-left: auto;
            margin-right: auto;
            border-radius: 100px;
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
        }

        #sectionForm {
            display: flex;
            align-items: center;
            gap: 20px;
            font-family: Arial, sans-serif;
            margin: 20px 0;
        }

        #sectionForm label {
            margin-right: 10px;
            font-size: 16px;
            color: #333;
        }

        #sectionForm select {
            width: 300px;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ced4da;
            border-radius: 5px;
            background-color: #f8f9fa;
            color: #333;
            appearance: none;
            cursor: pointer;
            transition: border-color 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
        }

        #sectionForm select:hover {
            border-color: #007bff;
        }

        #sectionForm select:focus {
            outline: none;
            border-color: #007bff;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
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
            background-color: #00d4ff;
        }
    </style>
</head>

<body>
<div class="container">
    <!-- Sidebar -->
    <aside class="sidebar">
        <img src="image/classtrack.png" alt="Logo" class="logo">
        <h4 class="text-primary">CLASS TRACK</h4>
        <nav class="nav">
            <ul>
                <li><a href="teacherint.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="vclasslist.php"><i class="fas fa-list-alt"></i> View Class List</a></li>
                <li><a href="attendance.php"><i class="fas fa-user-check"></i> Take Attendance</a></li>
                <li><a href="view_class_attendance.php"><i class="fas fa-eye"></i> View Class Attendance</a></li>
                <li><a href="view_student_attendance.php"><i class="fas fa-user-graduate"></i> View Student Attendance</a></li>
                <li><a href="today_attendance.php"><i class="fas fa-calendar-day"></i> Today's Attendance Report</a></li>
                <li><a href="absent_notification.php"><i class="fas fa-envelope"></i> Generate E-mail</a></li>
            </ul>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <div class="header">
            <h1>View Class List</h1>
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

        <form method="POST" id="sectionForm">
            <label for="yearLevelDropdown">Select Year Level:</label>
            <select name="year_level" id="yearLevelDropdown" required>
                <option value="">Select Year Level</option>
                <?php while ($row = $year_levels_result->fetch_assoc()) : ?>
                    <option value="<?= htmlspecialchars($row['year_level']) ?>"><?= htmlspecialchars($row['year_level']) ?></option>
                <?php endwhile; ?>
            </select>

            <label for="section">Select Section:</label>
            <select name="section" id="sectionDropdown" required>
                <option value="">Select Section</option>
                <?php while ($row = $sections_result->fetch_assoc()) : ?>
                    <option value="<?= htmlspecialchars($row['id']) ?>"><?= htmlspecialchars($row['section_name']) ?></option>
                <?php endwhile; ?>
            </select>
        </form>

        <table id="classListTable">
            <thead>
                <tr>
                    <th>Student Name</th>
                    <th>Year Level</th>
                    <th>Section</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="4" style="text-align: center;">Please select a year level and section.</td>
                </tr>
            </tbody>
        </table>
    </main>
</div>

<script>
    $(document).ready(function() {
        // Load the class list when either dropdown is changed
        $('#sectionDropdown, #yearLevelDropdown').change(function() {
            var sectionId = $('#sectionDropdown').val();
            var yearLevel = $('#yearLevelDropdown').val();
            if (sectionId && yearLevel) {
                loadClassList(sectionId, yearLevel); // Load the class list
            } else {
                // If no selection, reset the table to default message
                $('#classListTable tbody').html('<tr><td colspan="4" style="text-align: center;">Please select a year level and section.</td></tr>');
            }
        });

        // Function to load class list
        function loadClassList(sectionId, yearLevel) {
            $.ajax({
                url: 'fetch.php', // Ensure this file handles fetching the data correctly
                type: 'POST',
                data: { section_id: sectionId, year_level: yearLevel },
                dataType: 'json',
                success: function(response) {
                    // Clear the table before adding new data
                    var tableBody = $('#classListTable tbody');
                    tableBody.empty();
                    // Append the new data
                    response.forEach(row => {
                        var newRow = '<tr><td>' + row[0] + '</td><td>' + row[1] + '</td><td>' + row[2] + '</td><td>' + row[3] + '</td></tr>';
                        tableBody.append(newRow);
                    });
                },
                error: function(xhr, status, error) {
                    console.error("Error fetching class list:", error);
                    alert("Failed to load class list.");
                }
            });
        }
    });
</script>
</body>
</html>
    