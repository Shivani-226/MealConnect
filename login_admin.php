<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$database = "mealconnect";

$conn = new mysqli($servername, $username, $password, $database);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM admin WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();

        if (password_verify($password, $row['password'])) {
            $_SESSION["admin_logged_in"] = true;
            $_SESSION["admin_username"] = $username;
            header("Location: admin_dashboard.php");
            exit;
        } else {
            echo "Invalid password!";
        }
    } else {
        echo "No admin found with this username!";
    }

    $stmt->close();
    $conn->close();
}
?>