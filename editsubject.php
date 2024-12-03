<?php
require 'db.php'; // Include your database connection

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Fetch the subject details
    $query = "SELECT * FROM subjects WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $subject = $result->fetch_assoc();

    // Check if the subject exists
    if (!$subject) {
        // Subject not found, redirect or show an error message
        header("Location: subject.php?error=Subject not found");
        exit();
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $new_subject_name = $_POST['subject_name'];

        // Update subject details
        $update_query = "UPDATE subjects SET subject_name = ? WHERE id = ?";
        $update_stmt = $conn->prepare($update_query);
        $update_stmt->bind_param('si', $new_subject_name, $id);

        if ($update_stmt->execute()) {
            echo "Subject updated successfully!";
            // Redirect back to the subject management page after updating
            header("Location: subject.php");
            exit();
        } else {
            echo "Error: " . $update_stmt->error;
        }
    }
} else {
    // Redirect back if no ID is provided
    header("Location: subject.php");
    exit();
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


/* Card Container */
.card-container {
    display: flex;
    justify-content: center; 
    flex-wrap: wrap; 
    gap: 20px; 
    margin-top: 20px; 
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

/* Announcements */
.announcements {
    flex: 1; 
    margin-right: 20px; 
    padding: 20px;
    background-color: #f8f9fa;
    border-radius: 10px;
    border: 2px solid #6c757d;
}

.announcements h3 {
    text-align: center;
}

.announcements li {
    margin-left: 10px;
}

/* Calendar Styles */


/* Calendar Styles */
.calendar-container {
    background-color: #f8f9fa;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 15px 40px rgba(0, 0, 0, 0.12);
    max-width: 450px;
    margin: auto;
    border: 2px solid #6c757d;
}

.calendar-container header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 10px 30px;
}

header .calendar-navigation {
    display: flex;
    align-items: center;
}

header .calendar-navigation span {
    height: 38px;
    width: 38px;
    margin: 0 5px;
    cursor: pointer;
    text-align: center;
    line-height: 38px;
    border-radius: 50%;
    color: #aeabab;
    font-size: 1.5rem;
}

header .calendar-navigation span:hover {
    background: #f2f2f2;
}

.calendar-current-date {
    font-weight: 500;
    font-size: 1.45rem;
}

.calendar-body {
    padding: 10px 0;
}

.calendar-body .calendar-weekdays, .calendar-body .calendar-dates {
    list-style: none;
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    margin: 0;
    padding: 0;
}

.calendar-body .calendar-weekdays li {
    width: calc(100% / 7);
    text-align: center;
    font-weight: 600;
    color: #6c757d;
    padding: 5px;
}

.calendar-body .calendar-dates li {
    width: calc(100% / 7);
    font-size: 1.2rem;
    padding: 10px;
    text-align: center;
    cursor: pointer;
    transition: background 0.3s;
}

.calendar-body .calendar-dates li:hover {
    background: #f1f1f1;
}

.calendar-body .calendar-dates li.active {
    background-color: #6332c5;
    color: white;
    border-radius: 50%;
}

.calendar-body .calendar-dates li.inactive {
    color: #ccc;
}

/* Add responsiveness */
@media (max-width: 768px) {
    .calendar-container {
        width: 100%;
        padding: 15px;
    }

    header .calendar-navigation span {
        font-size: 1.2rem;
        height: 30px;
        width: 30px;
    }

    .calendar-body .calendar-weekdays li, .calendar-body .calendar-dates li {
        font-size: 1rem;
    }
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
            <li><a href="admin.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a href="addstudent.php"><i class="fas fa-user-graduate"></i> Manage Students</a></li>
            <li><a href="addteacher.php"><i class="fas fa-teacher-alt"></i> Manage Teacher</a></li>
            <li><a href="subject.php"><i class="fas fa-subject-alt"></i> Manage Subject</a></li>
            <li><a href="login.php"><i class="fas fa-subject-alt"></i>Logout</a></li>
        </ul>
    </nav>
</aside>


        <!-- Main Content -->
        <main class="main-content">
            <div class="header">
                <h1>Admin's Dashboard</h1>
                <div class="header-content">
                    <div class="profile-bar">
                        <img src="image/profile.png" alt="Profile Picture" class="profile-picture"> <!-- Example profile image -->
                        <div class="profile-info">
                            <h5 class="profile-name">Name</h5>
                            <p class="profile-role">Admin</p>
                        </div>
                    </div>
                </div>
            </div>
            <h2>Edit Subject</h2>
<form method="POST" action="editsubject.php?id=<?php echo htmlspecialchars($id); ?>">
    <input type="text" name="subject_name" value="<?php echo htmlspecialchars($subject['subject_name']); ?>" required>
    <button type="submit">Update Subject</button>
</form>

<a href="subject.php">Cancel</a>

            
        </main>
    </div>
 

    

    
</body>
</html>
