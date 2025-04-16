<!-- filepath: c:\xampp\htdocs\mealconnect\wait_for_donors.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wait for Donors - MealConnect</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-color: #FFDF4D;
            margin: 0;
            padding: 0;
            text-align: center;
        }

        .container {
            max-width: 600px;
            width: 100%;
            background-color: #fff;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        }

        h1 {
            font-size: 2rem;
            margin-bottom: 1rem;
        }

        p {
            font-size: 1rem;
            color: #333;
        }

        .btn {
            display: inline-block;
            margin-top: 1.5rem;
            padding: 0.75rem 1.5rem;
            background-color: #FFDE59;
            color: #333;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            transition: background-color 0.3s;
        }

        .btn:hover {
            background-color: #FFD700;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Thank You for Registering!</h1>
        <p>Please wait for donors to provide help. We will notify you once donations are available.</p>
        <a href="meal.html" class="btn">Back to Home</a>
    </div>
</body>
</html>