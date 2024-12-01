<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['attendance'])) {
    $year_level = $_POST['year_level'];
    $section_id = $_POST['section'];
    $subject_id = $_POST['subject'];
    $attendance_data = $_POST['attendance'];  // Assuming this contains the student IDs and their respective statuses

    foreach ($attendance_data as $student_id => $status) {
        // Ensure that the status is one of the expected values: 'Present', 'Absent', 'Late'
        if (!in_array($status, ['Present', 'Absent', 'Late'])) {
            $status = 'Absent'; // Default to 'Absent' if status is invalid
        }

        // Insert the attendance status into the database
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

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $year_level = $_POST['year_level'];
    $section_id = $_POST['section'];
    $subject_id = $_POST['subject'];
    $attendance_date = $_POST['attendance_date'];
    
    // Fetch the list of students in the selected section and year level
    $students_query = "SELECT id FROM students WHERE section_id = ? AND year_level = ?";
    $stmt_students = $conn->prepare($students_query);
    $stmt_students->bind_param('is', $section_id, $year_level);
    $stmt_students->execute();
    $result_students = $stmt_students->get_result();

    while ($student = $result_students->fetch_assoc()) {
        // Ensure that a status is set for each student; default to 'Absent' if none is provided
        $attendance_status = $_POST["status_{$student['id']}"] ?? 'Absent';

        // Check that the status is valid
        if (!in_array($attendance_status, ['Present', 'Absent', 'Late'])) {
            $attendance_status = 'Absent';  // Default to 'Absent' if invalid
        }

        // Insert attendance data for each student
        $insert_query = "INSERT INTO attendance (student_id, subject_id, date, status) VALUES (?, ?, ?, ?)";
        $stmt_insert = $conn->prepare($insert_query);
        $stmt_insert->bind_param('iiss', $student['id'], $subject_id, $attendance_date, $attendance_status);
        $stmt_insert->execute();
    }

    // Redirect back to attendance page or display a success message
    header('Location: vclassattendance.php');
    exit();
}
?>
