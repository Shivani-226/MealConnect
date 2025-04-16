<!-- filepath: c:\xampp\htdocs\mealconnect\recipient_confirmation.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recipient Confirmation</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-color: #E6BC00;
            margin: 0;
            position: relative;
        }
        .confirmation {
            background-color: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        .confirmation h2 {
            color: rgb(0, 0, 0);
        }
        .home-button {
            position: absolute;
            top: 20px;
            left: 20px;
            background-color: #FFD100;
            color: #121212;
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: background-color 0.3s;
        }
        .home-button:hover {
            background-color: #FFDF4D;
        }
        .home-button i {
            color: #121212;
        }
    </style>
</head>
<body>
    <a href="meal.html" class="home-button">
        <i class="fas fa-home"></i> Home
    </a>
    <div class="confirmation">
        <h2>Registration Successful</h2>
        <p>Your data has been submitted. Please wait for a donor to contact you.</p>
    </div>
</body>
</html>