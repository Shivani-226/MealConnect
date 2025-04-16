<!-- filepath: c:\xampp\htdocs\mealconnect\submit_donation.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donation Confirmation - MealConnect</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #FFD100;
            --primary-light: #FFDF4D;
            --primary-dark: #E5BC00;
            --black: #121212;
            --gray-dark: #282828;
            --gray-light: #3A3A3A;
            --white: #F5F5F5;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--primary);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            color: var(--black);
            overflow-x: hidden;
            position: relative;
        }

        .container {
            width: 90%;
            max-width: 800px;
            background-color: var(--white);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            border-radius: 16px;
            overflow: hidden;
            padding: 2rem;
            text-align: center;
        }

        h1 {
            font-size: 2rem;
            margin-bottom: 1.5rem;
            color: var(--primary-dark);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 2rem;
        }

        table, th, td {
            border: 1px solid var(--gray-dark);
        }

        th, td {
            padding: 1rem;
            text-align: left;
        }

        th {
            background-color: var(--primary-light);
            color: var(--black);
        }

        td {
            background-color: var(--gray-light);
            color: var(--white);
        }

        .btn {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            background-color: var(--primary-dark);
            color: var(--black);
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            transition: background-color 0.3s;
        }

        .btn:hover {
            background-color: var(--primary);
        }
    </style>
</head>
<body>
    <div class="container">
    <?php
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect form data
    $donorName = $_POST['donor-name'];
    $donorPhone = $_POST['donor-phone'];
    $foodType = $_POST['food-type'];
    $quantity = $_POST['quantity'];
    $description = $_POST['food-description'];
    $deliveryTime = $_POST['delivery-time'];
    $transport = $_POST['transport'];
    $recipientName = $_POST['recipient-name'];

    // Prepare and bind
    $stmt = $conn->prepare("INSERT INTO donations (donor_name, donor_phone, food_type, quantity, food_description, delivery_time, transport, recipient_name) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssissss", $donorName, $donorPhone, $foodType, $quantity, $description, $deliveryTime, $transport, $recipientName);

    // Execute the statement
    if ($stmt->execute()) {
        echo "<h1>Thank you for your donation offer!</h1>";
        echo "<table>
                <tr><th>Donor Name</th><td>$donorName</td></tr>
                <tr><th>Phone Number</th><td>$donorPhone</td></tr>
                <tr><th>Food Type</th><td>$foodType</td></tr>
                <tr><th>Quantity</th><td>$quantity servings</td></tr>
                <tr><th>Description</th><td>$description</td></tr>
                <tr><th>Preferred Delivery Time</th><td>$deliveryTime</td></tr>
                <tr><th>Transport Method</th><td>$transport</td></tr>
                <tr><th>Recipient Name</th><td>$recipientName</td></tr>
              </table>";
        
        // Send WhatsApp notifications
        include 'send_sms.php';
        
        echo "<a href='meal.html' class='btn'>Back to Home</a>";
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
}
?>
    </div>
</body>
</html>