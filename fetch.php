<?php
require 'db.php';

// Check if the section ID and year level are set
if (isset($_POST['section_id']) && isset($_POST['year_level'])) {
    $section_id = $_POST['section_id'];
    $year_level = $_POST['year_level'];

    // Fetch the section name for display
    $section_query = "SELECT section_name FROM sections WHERE id = ?";
    $section_stmt = $conn->prepare($section_query);
    $section_stmt->bind_param('i', $section_id);
    $section_stmt->execute();
    $section_result = $section_stmt->get_result();
    $section_name = ($section_result->num_rows > 0) ? $section_result->fetch_assoc()['section_name'] : 'N/A';

    // Fetch students for the selected section and year level
    $students_query = "SELECT name, parent_email, contact_number
                       FROM students
                       WHERE section_id = ? AND year_level = ?";
    $stmt = $conn->prepare($students_query);
    $stmt->bind_param('is', $section_id, $year_level);
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
                    <td>' . htmlspecialchars($year_level) . '</td>
                    <td>' . htmlspecialchars($section_name) . '</td>
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

if (isset($_POST['mode'])) {
    $mode = $_POST['mode'];

    if ($mode === 'class' && isset($_POST['attendance_date'])) {
        // Fetch attendance records for class
        $attendance_date = $_POST['attendance_date'];

        $attendance_query = "
            SELECT a.attendance_date, s.name AS student_name, a.status, sec.section_name, sub.subject_name
            FROM attendance a
            JOIN students s ON a.student_id = s.id
            JOIN sections sec ON s.section_id = sec.id
            JOIN subjects sub ON a.subject_id = sub.id
            WHERE a.attendance_date = ? AND s.year_level = ? AND s.section_id = ?";
        
        $stmt = $conn->prepare($attendance_query);
        $stmt->bind_param("ssi", $attendance_date, $year_level, $section_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            echo "<table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Student Name</th>
                            <th>Year Level</th>
                            <th>Section</th>
                            <th>Subject</th>
                            <th>Attendance Status</th>
                        </tr>
                    </thead>
                    <tbody>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>{$row['attendance_date']}</td>
                        <td>{$row['student_name']}</td>
                        <td>{$year_level}</td>
                        <td>{$row['section_name']}</td>
                        <td>{$row['subject_name']}</td>
                        <td>{$row['status']}</td>
                      </tr>";
            }
            echo "</tbody></table>";
        } else {
            echo "No attendance records found for the selected criteria.";
        }
    } elseif ($mode === 'student' && isset($_POST['student_id'])) {
        // Fetch attendance records for a specific student
        $student_id = $_POST['student_id'];

        $query = "SELECT date, status FROM attendance WHERE student_id = ? ORDER BY date DESC";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('i', $student_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            echo '<table><tr><th>Date</th><th>Status</th></tr>';
            while ($row = $result->fetch_assoc()) {
                echo '<tr><td>' . htmlspecialchars($row['date']) . '</td><td>' . htmlspecialchars($row['status']) . '</td></tr>';
            }
            echo '</table>';
        } else {
            echo 'No attendance records found for the selected student.';
        }
    }
}



?>
