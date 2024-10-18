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

<!-- HTML form for editing a subject -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Subject</title>
</head>
<body>

<h2>Edit Subject</h2>
<form method="POST" action="editsubject.php?id=<?php echo htmlspecialchars($id); ?>">
    <input type="text" name="subject_name" value="<?php echo htmlspecialchars($subject['subject_name']); ?>" required>
    <button type="submit">Update Subject</button>
</form>

<a href="subject.php">Cancel</a>

</body>
</html>
