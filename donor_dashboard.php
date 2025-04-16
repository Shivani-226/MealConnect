<?php
include 'db_connect.php'; // Include database connection

// Start the session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in and is a donor
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'donor') {
    // Redirect to login page if not logged in or not a donor
    header("Location: login.php");
    exit;
}

// Get the logged-in donor's username
$donor_username = $_SESSION['user_username'];

// Fetch donor information
$donor_query = "SELECT username, email, phone, address FROM users WHERE username = ? AND role = 'donor'";
$stmt = $conn->prepare($donor_query);
$stmt->bind_param("s", $donor_username);
$stmt->execute();
$donor_result = $stmt->get_result();

if ($donor_result->num_rows > 0) {
    $donor_info = $donor_result->fetch_assoc();
} else {
    // No donor found or user is not a donor
    header("Location: login.php");
    exit;
}

// Fetch donations made by this donor
$donations_query = "SELECT recipient_name, food_type, quantity, food_description, delivery_time, created_at
                    FROM donations
                    WHERE donor_name = ?";
$stmt = $conn->prepare($donations_query);
$stmt->bind_param("s", $donor_info['username']); // Use only donor_name
$stmt->execute();
$donations_result = $stmt->get_result();

// Count total donations made
$total_donations = $donations_result->num_rows;

// Calculate total items donated
$total_items = 0;
$donation_data = [];
while ($row = $donations_result->fetch_assoc()) {
    $total_items += intval($row['quantity']);
    $donation_data[] = $row;
}
// Reset the result pointer
mysqli_data_seek($donations_result, 0);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donor Dashboard | MealConnect</title>
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

        .header-buttons {
            display: flex;
            gap: 15px;
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

        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background-color: var(--light-dark);
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            transition: transform 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-card i {
            font-size: 36px;
            color: var(--primary-color);
            margin-bottom: 15px;
        }

        .stat-number {
            font-size: 28px;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 5px;
        }

        .stat-label {
            font-size: 14px;
            color: var(--text-light);
            opacity: 0.8;
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

        @media (max-width: 768px) {
            .profile-info {
                grid-template-columns: 1fr;
            }

            table {
                display: block;
                overflow-x: auto;
            }
            
            .stats-container {
                grid-template-columns: 1fr;
            }
            
            .header-buttons {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="dashboard-header">
            <div class="logo">MealConnect</div>
            <div class="header-buttons">
                <a href="available_recipients.php" class="dashboard-btn">
                    <i class="fas fa-users"></i> Available Recipients
                </a>
                <a href="meal.html" class="dashboard-btn">
                    <i class="fas fa-sign-out-alt"></i> Log Out
                </a>
            </div>
        </div>

        <div class="welcome-message">
            Welcome back, <strong><?php echo htmlspecialchars($donor_info['username']); ?></strong>! Here's your donation dashboard.
        </div>
        
        <!-- Stats Overview -->
        <div class="stats-container">
            <div class="stat-card">
                <i class="fas fa-gift"></i>
                <div class="stat-number"><?php echo $total_donations; ?></div>
                <div class="stat-label">Total Donations</div>
            </div>
            
            <div class="stat-card">
                <i class="fas fa-utensils"></i>
                <div class="stat-number"><?php echo $total_items; ?></div>
                <div class="stat-label">Items Donated</div>
            </div>
            
            <div class="stat-card">
                <i class="fas fa-medal"></i>
                <div class="stat-number"><?php echo ($total_donations >= 5 ? 'Gold' : ($total_donations >= 3 ? 'Silver' : 'Bronze')); ?></div>
                <div class="stat-label">Donor Status</div>
            </div>
        </div>

        <!-- Donor Information -->
        <div class="card">
            <h3 class="card-title"><i class="fas fa-user-circle"></i> Your Profile</h3>
            <div class="profile-info">
                <div class="profile-item">
                    <strong>Username</strong>
                    <?php echo htmlspecialchars($donor_info['username']); ?>
                </div>
                <div class="profile-item">
                    <strong>Email</strong>
                    <?php echo htmlspecialchars($donor_info['email']); ?>
                </div>
                <div class="profile-item">
                    <strong>Phone</strong>
                    <?php echo htmlspecialchars($donor_info['phone']); ?>
                </div>
                <div class="profile-item">
                    <strong>Address</strong>
                    <?php echo htmlspecialchars($donor_info['address']); ?>
                </div>
            </div>
            
        </div>

        <!-- Donations Made -->
        <div class="card">
            <h3 class="card-title"><i class="fas fa-hand-holding-heart"></i> Your Donations</h3>

            <?php if ($donations_result->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Recipient</th>
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
                                <td><?php echo htmlspecialchars($donation['recipient_name']); ?></td>
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
                    <i class="fas fa-info-circle"></i> You haven't made any donations yet. Find recipients who need your help!
                </div>
                <a href="available_recipients.php" class="dashboard-btn" style="margin-top: 20px;">
                    <i class="fas fa-search"></i> Find Recipients
                </a>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>