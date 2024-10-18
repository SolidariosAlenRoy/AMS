<?php
require '../includes/config.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Fetch the teacher details
    $query = "SELECT * FROM teachers WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $teacher = $result->fetch_assoc();

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $contact_number = $_POST['contact_number'];

        // Update teacher details
        $update_query = "UPDATE teachers SET name = ?, email = ?, contact_number = ? WHERE id = ?";
        $update_stmt = $conn->prepare($update_query);
        $update_stmt->bind_param('sssi', $name, $email, $contact_number, $id);

        if ($update_stmt->execute()) {
            echo "Teacher updated successfully!";
        } else {
            echo "Error: " . $update_stmt->error;
        }
    }
}
?>

<!-- HTML form for editing teacher -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Teacher</title>
</head>
<body>
<form method="POST" action="edit_teacher.php?id=<?php echo $id; ?>">
    <input type="text" name="name" value="<?php echo $teacher['name']; ?>" required>
    <input type="email" name="email" value="<?php echo $teacher['email']; ?>">
    <input type="text" name="contact_number" value="<?php echo $teacher['contact_number']; ?>">
    <button type="submit">Update Teacher</button>
</form>
</body>
</html>
