<?php
require 'db.php';

// Check if the section ID and year level are set
if (isset($_POST['section_id']) && isset($_POST['year_level'])) {
    $section_id = $_POST['section_id'];
    $year_level = $_POST['year_level'];

    // Fetch students based on section and year level
    $students_query = "SELECT name, year_level, section_id, parent_email, contact_number FROM students WHERE section_id = ? AND year_level = ?";
    $stmt = $conn->prepare($students_query);
    $stmt->bind_param('is', $section_id, $year_level);
    $stmt->execute();
    $result = $stmt->get_result();

     // Create the table
    echo '<table id="studentsTable" class="display">';
    echo '<thead>';
    echo '<tr>';
    echo '<th>Name</th>';
    echo '<th>Year Level</th>';
    echo '<th>Section ID</th>';
    echo '<th>Parent Email</th>';
    echo '<th>Contact Number</th>';
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo '<tr>';
            echo '<td>' . htmlspecialchars($row['name']) . '</td>';
            echo '<td>' . htmlspecialchars($row['year_level']) . '</td>';
            echo '<td>' . htmlspecialchars($row['section_id']) . '</td>';
            echo '<td>' . htmlspecialchars($row['parent_email']) . '</td>';
            echo '<td>' . htmlspecialchars($row['contact_number']) . '</td>';
            echo '</tr>';
        }
    } else {
        echo '<tr><td colspan="5" style="text-align: center;">No students found for the selected year level and section.</td></tr>';
    }

    
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

