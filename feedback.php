<?php
// Database configuration
include 'db_connect.php';

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data and sanitize inputs
    $name = htmlspecialchars(trim($_POST['name']));
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $message = htmlspecialchars(trim($_POST['message']));
    
    // Validate inputs
    $errors = [];
    
    if (empty($name)) {
        $errors[] = "Name is required";
    }
    
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Valid email is required";
    }
    
    if (empty($message)) {
        $errors[] = "Message is required";
    }
    
    // If no errors, insert into database
    if (empty($errors)) {
        $sql = "INSERT INTO feedback (name, email, message) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $name, $email, $message);
        
        if ($stmt->execute()) {
            // Success message and redirect
            echo "
            <!DOCTYPE html>
            <html lang='en'>
            <head>
                <meta charset='UTF-8'>
                <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                <title>Feedback Submitted - MealConnect</title>
                <link href='https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap' rel='stylesheet'>
                <style>
                    body {
                        font-family: 'Inter', sans-serif;
                        background-color: #f7f7f7;
                        display: flex;
                        justify-content: center;
                        align-items: center;
                        height: 100vh;
                        margin: 0;
                    }
                    .success-container {
                        background-color: white;
                        padding: 2rem;
                        border-radius: 10px;
                        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
                        text-align: center;
                        max-width: 500px;
                    }
                    .success-icon {
                        font-size: 4rem;
                        color: #4CAF50;
                        margin-bottom: 1rem;
                    }
                    h1 {
                        color: #333;
                        margin-bottom: 1rem;
                    }
                    p {
                        color: #666;
                        margin-bottom: 2rem;
                    }
                    .redirect-msg {
                        font-size: 0.9rem;
                        color: #888;
                        margin-top: 2rem;
                    }
                </style>
            </head>
            <body>
                <div class='success-container'>
                    <div class='success-icon'>✓</div>
                    <h1>Thank You!</h1>
                    <p>Your feedback has been submitted successfully. We appreciate your input.</p>
                    <p class='redirect-msg'>You will be redirected to the homepage in a few seconds...</p>
                </div>
                
                <script>
                    setTimeout(function() {
                        window.location.href = 'meal.html';
                    }, 3000);
                </script>
            </body>
            </html>
            ";
        } else {
            echo "Error: " . $stmt->error;
        }
        
        $stmt->close();
    } else {
        // Display errors
        echo "Errors:<br>";
        foreach ($errors as $error) {
            echo "- $error<br>";
        }
        echo "<br><a href='javascript:history.back()'>Go back</a>";
    }
}

$conn->close();
?>