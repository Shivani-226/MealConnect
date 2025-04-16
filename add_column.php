<?php
include 'db_connect.php'; // Include your database connection

// SQL to add the 'is_available' column
$sql = "ALTER TABLE users ADD COLUMN is_available TINYINT(1) NOT NULL DEFAULT 1";

if ($conn->query($sql) === TRUE) {
    echo "Column added successfully";
} else {
    echo "Error adding column: " . $conn->error;
}

$conn->close();
?>