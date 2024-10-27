<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['attendance'])) {
    $year_level = $_POST['year_level'];
    $section_id = $_POST['section'];
    $subject_id = $_POST['subject'];
    $attendance_data = $_POST['attendance'];

    foreach ($attendance_data as $student_id => $status) {
        $query = "INSERT INTO attendance (student_id, year_level, section_id, subject_id, status, attendance_date) 
                  VALUES (?, ?, ?, ?, ?, NOW())";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('iiiss', $student_id, $year_level, $section_id, $subject_id, $status);
        $stmt->execute();
    }

    // Redirect to view attendance page after saving
    header("Location: attendance.php");
    exit;
} else {
    echo "No attendance data to save.";
}
?>
