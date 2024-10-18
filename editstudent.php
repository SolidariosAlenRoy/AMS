<?php
require 'db.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Fetch the student details
    $query = "SELECT * FROM students WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $student = $result->fetch_assoc();

    // Fetch the student's section
    $section_query = "SELECT section_name FROM sections WHERE id = ?";
    $section_stmt = $conn->prepare($section_query);
    $section_stmt->bind_param('i', $student['section_id']);
    $section_stmt->execute();
    $section_result = $section_stmt->get_result();
    $section = $section_result->fetch_assoc()['section_name'];

    // Fetch the student's subjects
    $subject_query = "SELECT subject_id FROM student_subjects WHERE student_id = ?";
    $subject_stmt = $conn->prepare($subject_query);
    $subject_stmt->bind_param('i', $id);
    $subject_stmt->execute();
    $subject_result = $subject_stmt->get_result();
    $student_subjects = [];
    while ($row = $subject_result->fetch_assoc()) {
        $student_subjects[] = $row['subject_id'];
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $name = $_POST['name'];
        $parent_email = $_POST['parent_email'];
        $contact_number = $_POST['contact_number'];
        $section_name = $_POST['section'];
        $subjects = isset($_POST['subjects']) ? $_POST['subjects'] : [];

        // Check if section exists, if not, insert it
        $section_check_query = "SELECT id FROM sections WHERE section_name = ?";
        $section_check_stmt = $conn->prepare($section_check_query);
        $section_check_stmt->bind_param('s', $section_name);
        $section_check_stmt->execute();
        $section_check_stmt->store_result();

        if ($section_check_stmt->num_rows > 0) {
            $section_check_stmt->bind_result($section_id);
            $section_check_stmt->fetch();
        } else {
            $insert_section_query = "INSERT INTO sections (section_name) VALUES (?)";
            $insert_section_stmt = $conn->prepare($insert_section_query);
            $insert_section_stmt->bind_param('s', $section_name);
            $insert_section_stmt->execute();
            $section_id = $conn->insert_id;
        }

        // Update student details
        $update_query = "UPDATE students SET name = ?, parent_email = ?, contact_number = ?, section_id = ? WHERE id = ?";
        $update_stmt = $conn->prepare($update_query);
        $update_stmt->bind_param('sssii', $name, $parent_email, $contact_number, $section_id, $id);

        if ($update_stmt->execute()) {
            // Update student subjects
            $delete_subjects_query = "DELETE FROM student_subjects WHERE student_id = ?";
            $delete_subjects_stmt = $conn->prepare($delete_subjects_query);
            $delete_subjects_stmt->bind_param('i', $id);
            $delete_subjects_stmt->execute();

            foreach ($subjects as $subject_id) {
                $insert_subject_query = "INSERT INTO student_subjects (student_id, subject_id) VALUES (?, ?)";
                $insert_subject_stmt = $conn->prepare($insert_subject_query);
                $insert_subject_stmt->bind_param('ii', $id, $subject_id);
                $insert_subject_stmt->execute();
            }

            echo "Student updated successfully!";
        } else {
            echo "Error: " . $update_stmt->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Student</title>
</head>
<body>
    <!-- HTML form for editing student -->
<form method="POST" action="editstudent.php?id=<?php echo $id; ?>">
    <input type="text" name="name" value="<?php echo $student['name']; ?>" required>
    <input type="email" name="parent_email" value="<?php echo $student['parent_email']; ?>">
    <input type="text" name="contact_number" value="<?php echo $student['contact_number']; ?>">

    <!-- Text field for editing the section -->
    <label for="section">Section:</label>
    <input type="text" name="section" value="<?php echo $section; ?>" required>

    <!-- Checkbox for editing subjects -->
    <label for="subjects">Select Subjects:</label><br>
    <?php
    // Fetch all available subjects from the database
    $subjects_query = "SELECT id, subject_name FROM subjects";
    $subjects_result = $conn->query($subjects_query);

    while ($row = $subjects_result->fetch_assoc()) {
        $checked = in_array($row['id'], $student_subjects) ? 'checked' : '';
        echo "<input type='checkbox' name='subjects[]' value='{$row['id']}' $checked> {$row['subject_name']}<br>";
    }
    ?>

    <button type="submit">Update Student</button>
</form>
</body>
</html>
