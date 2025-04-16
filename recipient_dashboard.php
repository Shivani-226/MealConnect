<?php
include 'db_connect.php'; // Include database connection

// Start the session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in and is a recipient
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'recipient') {
    // Redirect to login page if not logged in or not a recipient
    header("Location: login.php");
    exit;
}

// Get the logged-in recipient's username
$recipient_username = $_SESSION['user_username'];

// Fetch recipient information
$recipient_query = "SELECT username, email, phone, address, is_available FROM users WHERE username = ? AND role = 'recipient'";
$stmt = $conn->prepare($recipient_query);
$stmt->bind_param("s", $recipient_username);
$stmt->execute();
$recipient_result = $stmt->get_result();

if ($recipient_result->num_rows > 0) {
    $recipient_info = $recipient_result->fetch_assoc();
} else {
    // No recipient found or user is not a recipient
    header("Location: login.php");
    exit;
}

// Handle the availability toggle
if (isset($_POST['toggle_availability'])) {
    $new_status = ($recipient_info['is_available'] == 1) ? 0 : 1;
    $update_query = "UPDATE users SET is_available = ? WHERE username = ? AND role = 'recipient'";
    $update_stmt = $conn->prepare($update_query);
    $update_stmt->bind_param("is", $new_status, $recipient_username);
    $update_stmt->execute();
    
    // Update local variable for immediate UI feedback
    $recipient_info['is_available'] = $new_status;
}

// Fetch donations received for this recipient
$donations_query = "SELECT donor_name, food_type, quantity, food_description, delivery_time, created_at
FROM donations
WHERE recipient_name = ? OR recipient_email = ?";
$stmt = $conn->prepare($donations_query);
$stmt->bind_param("ss", $recipient_info['username'], $recipient_info['email']);
$stmt->execute();
$donations_result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recipient Dashboard | MealConnect</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #FFDE59;
            --secondary-color: #FFD700;
            --dark-color: #222222;
            --light-dark: #333333;
            --text-light: #f9f9f9;
            --text-dark: #222222;
            --border-color: #444444;
            --green-color: #4bc05c;
            --red-color: #ff6384;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--dark-color);
            color: var(--text-light);
            margin: 0;
            padding: 0;
            line-height: 1.6;
        }

        .container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 20px;
        }

        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 2px solid var(--primary-color);
        }

        .logo {
            font-size: 24px;
            font-weight: 700;
            color: var(--primary-color);
        }

        .dashboard-btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: var(--primary-color);
            color: var(--text-dark);
            text-decoration: none;
            border-radius: 5px;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
        }

        .dashboard-btn:hover {
            background-color: var(--secondary-color);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(255, 222, 89, 0.3);
        }

        .card {
            background-color: var(--light-dark);
            border-radius: 10px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
            border-left: 4px solid var(--primary-color);
        }

        .card-title {
            font-size: 20px;
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid var(--border-color);
        }

        .profile-info {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
        }

        .profile-item {
            margin-bottom: 15px;
        }

        .profile-item strong {
            display: block;
            color: var(--primary-color);
            font-weight: 500;
            margin-bottom: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            color: var(--text-light);
        }

        th {
            background-color: rgba(255, 222, 89, 0.2);
            color: var(--primary-color);
            padding: 15px;
            text-align: left;
            font-weight: 600;
            border-bottom: 2px solid var(--primary-color);
        }

        td {
            padding: 15px;
            border-bottom: 1px solid var(--border-color);
        }

        tr:hover {
            background-color: rgba(255, 222, 89, 0.05);
        }

        .no-data {
            text-align: center;
            padding: 30px;
            color: #888;
            font-style: italic;
        }

        .badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .badge-veg {
            background-color: rgba(75, 192, 92, 0.2);
            color: #4bc05c;
        }

        .badge-nonveg {
            background-color: rgba(255, 99, 132, 0.2);
            color: #ff6384;
        }

        .badge-prepared-meals {
            background-color: rgba(255, 222, 89, 0.2);
            color: var(--primary-color);
        }

        .badge-groceries {
            background-color: rgba(75, 192, 192, 0.2);
            color: #4bc0c0;
        }

        .badge-produce {
            background-color: rgba(153, 102, 255, 0.2);
            color: #9966ff;
        }

        .welcome-message {
            margin-bottom: 30px;
            font-size: 18px;
            color: var(--text-light);
        }

        /* Availability toggle styles */
        .availability-section {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 30px;
        }

        .availability-status {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 15px;
            border-radius: 30px;
            font-weight: 600;
        }

        .status-available {
            background-color: rgba(75, 192, 92, 0.2);
            color: var(--green-color);
        }

        .status-unavailable {
            background-color: rgba(255, 99, 132, 0.2);
            color: var(--red-color);
        }

        .toggle-btn {
            padding: 10px 20px;
            border-radius: 5px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }

        .btn-make-available {
            background-color: var(--green-color);
            color: white;
        }

        .btn-make-unavailable {
            background-color: var(--red-color);
            color: white;
        }

        .toggle-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        @media (max-width: 768px) {
            .profile-info {
                grid-template-columns: 1fr;
            }

            table {
                display: block;
                overflow-x: auto;
            }
            
            .availability-section {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="dashboard-header">
            <div class="logo">MealConnect</div>
            <a href="meal.html" class="dashboard-btn">
                <i class="fas fa-sign-out-alt"></i> Log Out
            </a>
        </div>

        <div class="welcome-message">
            Welcome back, <strong><?php echo htmlspecialchars($recipient_info['username']); ?></strong>! Here's your dashboard.
        </div>
        
        <!-- Availability Toggle Section -->
        <div class="availability-section">
            <div class="availability-status <?php echo $recipient_info['is_available'] ? 'status-available' : 'status-unavailable'; ?>">
                <i class="fas <?php echo $recipient_info['is_available'] ? 'fa-check-circle' : 'fa-times-circle'; ?>"></i>
                <span>Currently <?php echo $recipient_info['is_available'] ? 'Available' : 'Unavailable'; ?> for Donations</span>
            </div>
            
            <form method="post">
                <button type="submit" name="toggle_availability" class="toggle-btn <?php echo $recipient_info['is_available'] ? 'btn-make-unavailable' : 'btn-make-available'; ?>">
                    <i class="fas <?php echo $recipient_info['is_available'] ? 'fa-toggle-off' : 'fa-toggle-on'; ?>"></i>
                    <?php echo $recipient_info['is_available'] ? 'Make Unavailable' : 'Make Available'; ?>
                </button>
            </form>
        </div>

        <!-- Recipient Information -->
        <div class="card">
            <h3 class="card-title"><i class="fas fa-user-circle"></i> Your Profile</h3>
            <div class="profile-info">
                <div class="profile-item">
                    <strong>Username</strong>
                    <?php echo htmlspecialchars($recipient_info['username']); ?>
                </div>
                <div class="profile-item">
                    <strong>Email</strong>
                    <?php echo htmlspecialchars($recipient_info['email']); ?>
                </div>
                <div class="profile-item">
                    <strong>Phone</strong>
                    <?php echo htmlspecialchars($recipient_info['phone']); ?>
                </div>
                <div class="profile-item">
                    <strong>Address</strong>
                    <?php echo htmlspecialchars($recipient_info['address']); ?>
                </div>
            </div>
            
        </div>

        <!-- Donations Received -->
        <div class="card">
            <h3 class="card-title"><i class="fas fa-gift"></i> Donations Received</h3>

            <?php if ($donations_result->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Donor</th>
                            <th>Food Type</th>
                            <th>Quantity</th>
                            <th>Description</th>
                            <th>Delivery Time</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($donation = $donations_result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($donation['donor_name']); ?></td>
                                <td>
                                    <span class="badge badge-<?php echo strtolower(htmlspecialchars($donation['food_type'])); ?>">
                                        <?php echo htmlspecialchars($donation['food_type']); ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars($donation['quantity']); ?></td>
                                <td><?php echo htmlspecialchars($donation['food_description']); ?></td>
                                <td><?php echo htmlspecialchars($donation['delivery_time']); ?></td>
                                <td><?php echo date('M d, Y', strtotime($donation['created_at'])); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="no-data">
                    <i class="fas fa-info-circle"></i> No donations received yet. Check back soon!
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>