<?php
require 'db.php';
session_start();

// Fetch sections for the dropdown menu
$sections_query = "SELECT * FROM sections";
$sections_result = $conn->query($sections_query);
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
body, h2, h4, p, ul {
    margin: 0;
    padding: 0;
}

.container {
    display: flex;
    height: 100vh; 
}

/* Sidebar styles */
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

.search-button i {
    margin: 0;
}

/* Profile Bar */
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

/* Header styles */
.header {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

/* Dashboard card styles */
.dashboard-card {
    background-color: #8cf1f5;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    border-radius: 10px;
    padding: 20px;
    text-align: center;
    min-width: 200px;
    flex: 1 0 200px; 
    max-width: 250px; 
}

/* Styles for the "Select Section" dropdown */
select#sectionDropdown {
    width: 300px; 
    padding: 10px; 
    border: 1px solid #ced4da; 
    border-radius: 5px; 
    background-color: #fff; 
    color: #333; 
    font-size: 16px; 
    appearance: none; 
    -webkit-appearance: none;
    -moz-appearance: none;
    cursor: pointer; 
}

select#sectionDropdown:hover {
    border-color: #007bff;
}

select#sectionDropdown:focus {
    outline: none; 
    border-color: #007bff; 
    box-shadow: 0 0 5px rgba(0, 123, 255, 0.5); 
}

select#sectionDropdown option {
    padding: 10px; 
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

/* Pagination button styles */
.pagination-btn {
    background-color: #007bff;
    color: white;
    border: none;
    padding: 10px;
    margin: 5px;
    cursor: pointer;
    border-radius: 5px;
}

.pagination-btn:hover {
    background-color: #0056b3;
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
                        <img src="image/profile.png" alt="Profile Picture" class="profile-picture"> <!-- Example profile image -->
                        <div class="profile-info">
                            <h5 class="profile-name">Name</h5>
                            <p class="profile-role">Teacher</p>
                        </div>
                    </div>
                </div>
            </div>

            <form method="POST" id="sectionForm">
                <label for="section">Select Section:</label>
                <select name="section" id="sectionDropdown" required>
                    <option value="">Select Section</option>
                    <?php while ($row = $sections_result->fetch_assoc()) : ?>
                        <option value="<?= $row['id'] ?>"><?= $row['section_name'] ?></option>
                    <?php endwhile; ?>
                </select>
            </form>

            <div id="classList">
                <!-- The class list will be dynamically loaded here -->
            </div>
        </main>
    </div>

    <script>
        $(document).ready(function() {
            // Load the class list when the section is selected
            $('#sectionDropdown').change(function() {
                var sectionId = $(this).val();
                if (sectionId) {
                    loadClassList(sectionId, 1); // Load page 1 by default
                }
            });

            // Function to load class list with pagination
            function loadClassList(sectionId, page) {
                $.ajax({
                    url: 'fetch.php',
                    type: 'POST',
                    data: {section_id: sectionId, page: page},
                    success: function(response) {
                        $('#classList').html(response); // Display the response in the class list div
                        // Add event listener for pagination buttons
                        $('.pagination-btn').on('click', function() {
                            var selectedPage = $(this).data('page');
                            loadClassList(sectionId, selectedPage);
                        });
                    }
                });
            }
        });
    </script>
</body>
</html>