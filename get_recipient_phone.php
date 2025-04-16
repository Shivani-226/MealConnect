<?php
include 'db_connect.php';

// Get recipient name from query parameter
$name = $_GET['name'] ?? '';

// Prepare response
$response = ['phone' => ''];

if (!empty($name)) {
    // Create a prepared statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT phone FROM users WHERE username = ? AND role = 'recipient'");
    $stmt->bind_param("s", $name);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $response['phone'] = $row['phone'];
    }
    
    $stmt->close();
}

// Return response as JSON
header('Content-Type: application/json');
echo json_encode($response);

$conn->close();
?>