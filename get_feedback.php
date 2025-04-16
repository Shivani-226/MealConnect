<?php
// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$database = "mealconnect";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    // Return error as JSON
    header('Content-Type: application/json');
    echo json_encode(["error" => "Connection failed: " . $conn->connect_error]);
    exit;
}

// Set content type header
header('Content-Type: application/json');

// Fetch testimonials from the database
$feedbacks = [];

// Query to get feedback records
$sql = "SELECT name, email, message FROM feedback ORDER BY id DESC";
$result = $conn->query($sql);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        // Limit the message length if it's too long
        if (strlen($row['message']) > 200) {
            $row['message'] = substr($row['message'], 0, 197) . '...';
        }
        
        // Add feedback to the array
        $feedbacks[] = $row;
    }
} else {
    echo json_encode(["error" => "Error fetching feedback: " . $conn->error]);
    exit;
}

// Close the connection
$conn->close();

// Return the feedback data as JSON
echo json_encode($feedbacks);
?>