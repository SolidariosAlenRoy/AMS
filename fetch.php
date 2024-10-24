<?php
require 'db.php'; // Ensure this file contains the correct database connection
session_start();

if (isset($_POST['section_id']) && isset($_POST['year_level'])) {
    $section_id = $_POST['section_id'];
    $year_level = $_POST['year_level'];

    // Prepare a statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT students.name AS student_name, students.year_level, sections.section_name 
                            FROM students 
                            JOIN sections ON students.section_id = sections.id 
                            WHERE students.year_level = ? AND sections.id = ?");
    $stmt->bind_param('si', $year_level, $section_id); // 's' for string, 'i' for integer
    $stmt->execute();
    $result = $stmt->get_result();

    $students = [];
    while ($row = $result->fetch_assoc()) {
        $students[] = [
            'student_name' => htmlspecialchars($row['student_name']),
            'year_level' => htmlspecialchars($row['year_level']),
            'section' => htmlspecialchars($row['section_name']),
            'action' => '<button class="btn btn-edit">Edit</button>' // Example action
        ];
    }

    // Return data as JSON
    echo json_encode($students);
}
