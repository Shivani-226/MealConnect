<?php
include 'mail_config.php'; // Include the mail configuration

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];

    // Database connection
    $servername = "localhost";
    $username = "root";
    $password = "";
    $database = "mealconnect";

    $conn = new mysqli($servername, $username, $password, $database);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Check if email exists
    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Generate a secure token
        $token = bin2hex(random_bytes(50));

        // Store token in the database with expiry
        $sql = "UPDATE users SET reset_token = ?, reset_token_expiry = DATE_ADD(NOW(), INTERVAL 1 HOUR) WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $token, $email);

        if ($stmt->execute()) {
            // Send reset link to user's email
            $reset_link = "http://localhost/mealconnect/reset_password.php?token=" . $token;
            $subject = "Password Reset Request";
            $email_body = "Click the following link to reset your password: <a href='" . $reset_link . "'>Reset Password</a>";

            if (send_email($email, $subject, $email_body)) {
                $message = "✅ Password reset link has been sent to your email.";
            } else {
                $message = "❌ Failed to send email. Please try again.";
            }
        } else {
            $message = "❌ Error updating token: " . $stmt->error;
        }
    } else {
        $message = "❌ Email not found in our records.";
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
    <title>Forgot Password - MealConnect</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #FFDE59;
            --primary-hover: #FFD700;
            --dark: #1A1A1A;
            --text-light: #f0f0f0;
            --text-dark: #333333;
            --accent: #FFB800;
        }

        body {
            font-family: 'Inter', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-color: #FFDF4D;
            margin: 0;
            padding: 0;
            position: relative;
            overflow: hidden;
        }

        .container {
            display: flex;
            max-width: 900px;
            width: 90%;
            height: 500px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            border-radius: 12px;
            overflow: hidden;
            position: relative;
            z-index: 1;
        }

        .logo-image {
            margin-bottom: 20px;
            width: 100px;
        }

        .welcome-section {
            background-color: var(--dark);
            flex: 1;
            padding: 3rem;
            color: var(--text-light);
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
        }

        .welcome-section h1 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            font-weight: 700;
        }

        .welcome-section p {
            margin-bottom: 2rem;
            opacity: 0.8;
            line-height: 1.6;
        }

        .form-section {
            background-color: #2A2A2A;
            flex: 1;
            padding: 3rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .form-title {
            color: var(--text-light);
            margin-bottom: 1.5rem;
            text-align: center;
            font-weight: 600;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            color: var(--text-light);
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }

        .form-group input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #444;
            border-radius: 8px;
            background-color: #333;
            color: white;
            transition: border-color 0.3s;
        }

        .form-group input:focus {
            outline: none;
            border-color: var(--primary);
        }

        .submit-btn {
            width: 100%;
            background: linear-gradient(to right, var(--primary), var(--accent));
            color: var(--dark);
            padding: 0.75rem;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.3s;
            margin-top: 0.5rem;
        }

        .submit-btn:hover {
            transform: translateY(-2px);
        }

        .back-to-login {
            text-align: center;
            margin-top: 1.5rem;
            color: var(--text-light);
            font-size: 0.9rem;
        }

        .back-to-login a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
        }

        .back-to-login a:hover {
            color: var(--primary-hover);
        }

        .message {
            text-align: center;
            margin-top: 1.5rem;
            padding: 0.75rem;
            border-radius: 8px;
            font-weight: 500;
            color: var(--text-light);
            background-color: rgba(51, 51, 51, 0.7);
        }

        .shape {
            position: absolute;
            opacity: 0.15;
            z-index: -1;
        }

        .circle1 {
            width: 200px;
            height: 200px;
            border-radius: 50%;
            background-color: var(--primary);
            top: -100px;
            left: 200px;
        }

        .circle2 {
            width: 500px;
            height: 500px;
            border-radius: 50%;
            background: radial-gradient(circle, var(--primary) 20%, transparent 20%, transparent 40%, var(--primary) 40%, var(--primary) 60%, transparent 60%, transparent 80%, var(--primary) 80%);
            background-size: 50px 50px;
            bottom: 60px;
            right: 80px;
        }

        .wave {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 50%;
            background: linear-gradient(to top, rgba(255, 222, 89, 0.1), transparent);
            clip-path: polygon(0 80%, 100% 40%, 100% 100%, 0% 100%);
        }

        @media (max-width: 768px) {
            .container {
                flex-direction: column;
                height: auto;
            }
            
            .welcome-section, .form-section {
                padding: 2rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="welcome-section">
            <div class="shape circle1"></div>
            <div class="wave"></div>
            <div class="logo">
                <img src="images/logo.png" alt="MealConnect Logo" class="logo-image">   
            </div>
            <h1>Forgot Password?</h1>
            <p>Don't worry! It happens to the best of us. Enter your email address and we'll send you a link to reset your password.</p>
        </div>
        
        <div class="form-section">
            <h2 class="form-title">Reset Password</h2>
            
            <form action="forgot_password.php" method="POST">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>
                
                <button type="submit" class="submit-btn">Send Reset Link</button>
            </form>
            
            <?php if (!empty($message)): ?>
                <div class="message"><?php echo $message; ?></div>
            <?php endif; ?>
            
            <div class="back-to-login">
                Remember your password? <a href="registration.html">Back to Login</a>
            </div>
        </div>
    </div>
    
    <div class="shape circle2"></div>
</body>
</html>