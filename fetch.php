<?php
require 'db.php';

// Check if the section ID is set
if (isset($_POST['section_id'])) {
    $section_id = $_POST['section_id'];

    // Fetch students for the selected section
    $students_query = "SELECT students.name, students.parent_email, students.contact_number
                       FROM students
                       WHERE students.section_id = ?";
    $stmt = $conn->prepare($students_query);
    $stmt->bind_param('i', $section_id);
    $stmt->execute();
    $students_result = $stmt->get_result();

    if ($students_result->num_rows > 0) {
        echo '<table>
                <tr>
                    <th>Student Name</th>
                    <th>Parent\'s Email</th>
                    <th>Contact Number</th>
                </tr>';
        while ($row = $students_result->fetch_assoc()) {
            echo '<tr>
                    <td>' . htmlspecialchars($row['name']) . '</td>
                    <td>' . htmlspecialchars($row['parent_email']) . '</td>
                    <td>' . htmlspecialchars($row['contact_number']) . '</td>
                  </tr>';
        }
        echo '</table>';
    } else {
        echo '<p>No students found for the selected section.</p>';
    }
}
?>
