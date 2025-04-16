<!-- filepath: /c:/xampp/htdocs/mealconnect/reset_password.php -->
<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $token = $_POST['token'];
    $new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);

    // Database connection
    include 'db_connect.php';

    // Verify token and update password
    $sql = "UPDATE users SET password = ?, reset_token = NULL, reset_token_expiry = NULL WHERE reset_token = ? AND reset_token_expiry > NOW()";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die("Error preparing statement: " . $conn->error);
    }
    $stmt->bind_param("ss", $new_password, $token);
    if ($stmt->execute()) {
        echo "<script>alert('Password reset successful. Please log in.'); window.location.href='signin.html';</script>";
    } else {
        echo "<script>alert('Password reset failed. Please try again.'); window.location.href='reset_password.php?token=" . urlencode($token) . "';</script>";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - MealConnect</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-color: #f0f0f0;
        }

        .reset-password-form {
            background-color: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 300px;
            text-align: center;
        }

        .reset-password-form h2 {
            margin-bottom: 1rem;
        }

        .reset-password-form .form-group {
            margin-bottom: 1rem;
        }

        .reset-password-form .form-group label {
            display: block;
            margin-bottom: 0.5rem;
        }

        .reset-password-form .form-group input {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .reset-password-form .submit-btn {
            background: #FF6B35;
            color: white;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.3s;
            font-size: 1rem;
            font-weight: 600;
            margin-top: 1rem;
        }

        .reset-password-form .submit-btn:hover {
            background: #E55A2B;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <div class="reset-password-form">
        <h2>Reset Password</h2>
        <form action="reset_password.php" method="POST">
            <input type="hidden" name="token" value="<?php echo htmlspecialchars($_GET['token']); ?>">
            <div class="form-group">
                <label for="new_password">New Password</label>
                <input type="password" id="new_password" name="new_password" required>
            </div>
            <button type="submit" class="submit-btn">Reset Password</button>
        </form>
    </div>
</body>
</html>