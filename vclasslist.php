<?php
require 'db.php';
session_start();

// Fetch sections for the dropdown menu
$sections_query = "SELECT * FROM sections";
$sections_result = $conn->query($sections_query);

// Fetch distinct year levels from the students table
$year_levels_query = "SELECT DISTINCT year_level FROM students";
$year_levels_result = $conn->query($year_levels_query);
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/5.11.3/main.min.js"></script>
    
   
   <style>
/* General Reset */
body, h2, h4, p, ul {
    margin: 0;
    padding: 0;
}

/* Layout Containers */
.container {
    display: flex;
    height: 100vh; 
}

/* Sidebar */
.sidebar {
    width: 250px; 
    background-color: #343a40;
    color: #fff;
    padding: 20px;
    display: flex;
    flex-direction: column;
    align-items: center;
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

.logo {
    width: 100%; 
    max-width: 180px; 
    margin-bottom: 20px; 
    display: block; 
    border-radius: 100px;
}

/* Main Content */
.main-content {
    flex: 1;
    padding: 20px;
}

/* Header */
.header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 2px solid #dee2e6;
    padding: 10px 0;
    margin-bottom: 20px;
    gap: 20px;
}

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
    width: 300px;
    height: 20px;
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
    border-color: #007bff; 
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
    border: 2px solid #007bff;
    box-shadow: 0 0 15px rgba(0, 123, 255, 0.6); 
    transition: box-shadow 0.3s ease-in-out; 
    padding: 15px;
    border-radius: 10px;
    background-color: #f8f9fa;
    margin-bottom: 20px;
}

.dropdown-container:hover {
    box-shadow: 0 0 25px rgba(0, 123, 255, 0.9); 
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


/* Table Styling */
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
                    <div class="search-bar">
                        <input type="text" placeholder="Search..." class="search-input">
                        <button class="search-button"><i class="fas fa-search"></i></button>
                    </div>
                    <div class="profile-bar">
                        <img src="image/profile.png" alt="Profile Picture" class="profile-picture"> <!-- Example profile image -->
                        <div class="profile-info">
                            <h5 class="profile-name">Name</h5>
                            <p class="profile-role">Teacher</p>
                        </div>
                    </div>
                </div>
            </div>

            <form method="POST" id="sectionForm">
    <div class="dropdown-container">
        <div class="dropdown-row">
            <div>
                <label for="yearLevelDropdown" class="label">Select Year Level:</label>
                <select name="year_level" id="yearLevelDropdown" required>
                    <option value="">--select--</option>
                    <?php while ($row = $year_levels_result->fetch_assoc()) : ?>
                        <option value="<?= $row['year_level'] ?>"><?= $row['year_level'] ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div>
                <label for="sectionDropdown" class="label">Select Section:</label>
                <select name="section" id="sectionDropdown" required>
                    <option value="">--select--</option>
                    <?php while ($row = $sections_result->fetch_assoc()) : ?>
                        <option value="<?= $row['id'] ?>"><?= $row['section_name'] ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
        </div>
    </div>
</form>



        <div id="classList">
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