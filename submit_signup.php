<!-- filepath: /c:/xampp/htdocs/MealConnect/submit_signup.php -->
<?php
include 'db_connect.php';

// Check if user already exists
$email = $_POST['email'];
$sql = "SELECT * FROM users WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // User already exists
    echo "<script>alert('User already exists. Please log in.'); window.location.href='signin.html';</script>";
} else {
    // Prepare and bind
    $stmt = $conn->prepare("INSERT INTO users (username, email, password, phone, address, role, plates, food_type) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssss", $username, $email, $password, $phone, $address, $role, $plates, $food_type);

    // Set parameters and execute
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $role = $_POST['role'];
    $plates = $_POST['plates'];
    $food_type = $_POST['food_type'];

    if ($stmt->execute()) {
        if ($role == 'donor') {
            echo "<script> window.location.href='available_recipients.php';</script>";
        } else {
            echo "<script> window.location.href='wait_for_donors.php';</script>";
        }
    } else {
        echo "Error: " . $stmt->error;
    }
}

$stmt->close();
$conn->close();
?>