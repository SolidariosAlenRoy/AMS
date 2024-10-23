<?php
require 'db.php';

// Check if the section ID and year level are set
if (isset($_POST['section_id']) && isset($_POST['year_level'])) {
    $section_id = $_POST['section_id'];
    $year_level = $_POST['year_level'];

    // Fetch the section name for display
    $section_query = "SELECT section_name FROM sections WHERE id = ?";
    $section_stmt = $conn->prepare($section_query);
    $section_stmt->bind_param('i', $section_id); // 'i' for integer
    $section_stmt->execute();
    $section_result = $section_stmt->get_result();
    $section_name = ($section_result->num_rows > 0) ? $section_result->fetch_assoc()['section_name'] : 'N/A';

    // Fetch students for the selected section and year level
    $students_query = "SELECT students.name, students.parent_email, students.contact_number
                       FROM students
                       WHERE students.section_id = ? AND students.year_level = ?";
    $stmt = $conn->prepare($students_query);
    $stmt->bind_param('is', $section_id, $year_level); // 'i' for integer, 's' for string
    $stmt->execute();
    $students_result = $stmt->get_result();
    

    if ($students_result->num_rows > 0) {
        echo '<table>
                <tr>
                    <th>Student Name</th>
                    <th>Year Level</th>
                    <th>Section</th>
                    <th>Parent\'s Email</th>
                    <th>Contact Number</th>
                </tr>';
        while ($row = $students_result->fetch_assoc()) {
            echo '<tr>
                    <td>' . htmlspecialchars($row['name']) . '</td>
                    <td>' . htmlspecialchars($year_level) . '</td> <!-- Display year level -->
                    <td>' . htmlspecialchars($section_name) . '</td> <!-- Display section name -->
                    <td>' . htmlspecialchars($row['parent_email']) . '</td>
                    <td>' . htmlspecialchars($row['contact_number']) . '</td>
                  </tr>';
        }
        echo '</table>';
    } else {
        echo '<p>No students found for the selected section and year level.</p>';
    }
} else {
    echo '<p>Error: Section ID or Year Level not set.</p>';
}
?>
