<?php
require 'db.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Delete student
    $query = "DELETE FROM students WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $id);

    if ($stmt->execute()) {
        echo "Student deleted successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }
}
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Delete teacher
    $query = "DELETE FROM teachers WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $id);

    if ($stmt->execute()) {
        echo "Teacher deleted successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>

