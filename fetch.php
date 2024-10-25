<?php
require 'db.php'; // Ensure this file contains the correct database connection

if (isset($_POST['section_id']) && isset($_POST['year_level'])) {
    $section_id = $_POST['section_id'];
    $year_level = $_POST['year_level'];

    // Query to fetch students based on section and year level
    $query = "SELECT student_name, year_level, section_name 
              FROM students 
              INNER JOIN sections ON students.section_id = sections.id 
              WHERE students.section_id = ? AND students.year_level = ?";

    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param('is', $section_id, $year_level); // 'is' indicates int, string
        $stmt->execute();
        $result = $stmt->get_result();

        $students = [];
        while ($row = $result->fetch_assoc()) {
            $students[] = [$row['student_name'], $row['year_level'], $row['section_name'], '<button>View</button>'];
        }

        // Return the result as a JSON array
        echo json_encode($students);
    } else {
        echo json_encode(["error" => "Query failed"]);
    }
}
?>
