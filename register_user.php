<?php
// Include the database connection file
include 'db_connect.php';

header('Content-Type: text/html');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if ($username === "Admin" && $password === "admin22") {
        session_start();
        $_SESSION['admin_id'] = $username;
        $_SESSION['admin_username'] = $username;
        header("Location: admin_dashboard.php");
        exit();
    }
 else {
        // Check if the user exists in the users table
        $stmt = $conn->prepare("SELECT password, role FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($hashedPassword, $role);
        $stmt->fetch();

        if ($stmt->num_rows > 0) {
            if (password_verify($password, $hashedPassword)) {
                session_start();
                $_SESSION['user_id'] = $username;
                $_SESSION['user_username'] = $username;
                $_SESSION['user_role'] = $role;
                
                if ($role == 'donor') {
                    // Donor redirects to available recipients page
                    header("Location: available_recipients.php");
                    exit();
                } else if ($role == 'recipient') {
                    // Recipient redirects to recipient confirmation page
                    header("Location: recipient_dashboard.php");
                    exit();
                } else {
                    // Fallback for any other role
                    $message = "Invalid user role. Please contact administrator.";
                }
            } else {
                $message = "Invalid password.";
            }
        } else {
            $message = "User does not exist. Please sign up.";
        }
    }
}
    $stmt->close();
    $conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In - Platform</title>
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

        .explore-btn {
            background-color: var(--primary);
            color: var(--dark);
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 50px;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s;
            cursor: pointer;
            width: fit-content;
        }

        .explore-btn:hover {
            background-color: var(--primary-hover);
            transform: translateY(-2px);
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

        .sign-in-btn {
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

        .sign-in-btn:hover {
            transform: translateY(-2px);
        }

        .new-user {
            text-align: center;
            margin-top: 1.5rem;
            color: var(--text-light);
            font-size: 0.9rem;
        }

        .new-user a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
        }

        .new-user a:hover {
            color: var (--primary-hover);
        }
        .forgot-password {
            text-align: center;
            margin-top: 1.5rem;
            color: var(--text-light);
            font-size: 0.9rem;
        }

        .forgot-password a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
        }

        .forgot-password a:hover {
            color: var(--primary-hover);
        }

        .shape {
            position: absolute;
            opacity: 0.15;
            z-index: -0.5;
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

        .message-box {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            text-align: center;
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
            <h1>Login to MealConnect</h1>
            <p>Join the mission to reduce food waste and fight hunger. Sign in to donate or request meals effortlessly!</p>
        </div>
        
        <div class="form-section">
            <?php if (isset($message)): ?>
                <div class="message-box">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>
            <h2 class="form-title">Sign in</h2>
            
            <form action="register_user.php" method="POST">
                <div class="form-group">
                    <label for="username">User Name</label>
                    <input type="text" id="username" name="username" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                
                <button type="submit" class="sign-in-btn">Sign in</button>
            </form>
            
            <div class="new-user">
                New to the platform? <a href="signin.html">Register here</a>
            </div>
            <div class="forgot-password">
                <a href="forgot_password.php">Forgot Password?</a>
            </div>
        </div>
    </div>
    
    <div class="shape circle2"></div>
</body>
</html>