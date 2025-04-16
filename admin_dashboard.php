<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.html");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - MealConnect</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
            display: flex;
            align-items: center;
            gap: 10px;
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

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid var(--border-color);
        }

        .search-container {
            position: relative;
        }

        .search-input {
            padding: 10px 15px 10px 40px;
            border: 1px solid var(--border-color);
            border-radius: 5px;
            background-color: rgba(255, 255, 255, 0.1);
            color: var(--text-light);
            font-size: 14px;
            width: 250px;
            transition: all 0.3s ease;
        }

        .search-input:focus {
            outline: none;
            border-color: var(--primary-color);
            background-color: rgba(255, 255, 255, 0.15);
            width: 300px;
        }

        .search-input::placeholder {
            color: rgba(255, 255, 255, 0.5);
        }

        .search-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(255, 255, 255, 0.5);
        }

        .table-responsive {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
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

        .welcome-message {
            margin-bottom: 30px;
            font-size: 18px;
            color: var(--text-light);
        }

        .hidden {
            display: none;
        }

        @media (max-width: 768px) {
            .card-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }

            .search-input {
                width: 100%;
            }

            .stats-container {
                grid-template-columns: 1fr;
            }

            .header-buttons {
                flex-direction: column;
            }

            table {
                display: block;
                overflow-x: auto;
            }
        }
    </style>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Set up search functionality for all tables
            setupSearch('donor-search', 'donor-table');
            setupSearch('recipient-search', 'recipient-table');
            setupSearch('donation-search', 'donation-table');
            
            // Function to set up search for a specific table
            function setupSearch(searchInputId, tableId) {
                const searchInput = document.getElementById(searchInputId);
                const table = document.getElementById(tableId);
                const rows = table.getElementsByTagName('tr');
                
                searchInput.addEventListener('keyup', function() {
                    const searchTerm = searchInput.value.toLowerCase();
                    
                    // Skip the header row (index 0)
                    for(let i = 1; i < rows.length; i++) {
                        let found = false;
                        const cells = rows[i].getElementsByTagName('td');
                        
                        // Search through each cell in the row
                        for(let j = 0; j < cells.length; j++) {
                            const cellContent = cells[j].textContent.toLowerCase();
                            if(cellContent.indexOf(searchTerm) > -1) {
                                found = true;
                                break;
                            }
                        }
                        
                        if(found) {
                            rows[i].classList.remove('hidden');
                        } else {
                            rows[i].classList.add('hidden');
                        }
                    }
                });
            }
        });
    </script>
</head>
<body>
    <div class="container">
        <div class="dashboard-header">
            <div class="logo">MealConnect</div>
            <div class="header-buttons">
                <a href="meal.html" class="dashboard-btn">
                    <i class="fas fa-sign-out-alt"></i> Log Out
                </a>
            </div>
        </div>

        <div class="welcome-message">
            Welcome back, <strong><?php echo $_SESSION['admin_username']; ?></strong>! Here's your admin dashboard.
        </div>

        <!-- Stats Summary -->
        <div class="stats-container">
            <?php
            include 'db_connect.php';
            $donor_count = $conn->query("SELECT COUNT(*) as count FROM users WHERE role = 'donor'")->fetch_assoc()['count'];
            $recipient_count = $conn->query("SELECT COUNT(*) as count FROM users WHERE role = 'recipient'")->fetch_assoc()['count'];
            $donation_count = $conn->query("SELECT COUNT(*) as count FROM donations")->fetch_assoc()['count'];
            ?>
            <div class="stat-card">
                <i class="fas fa-user-plus"></i>
                <div class="stat-number"><?php echo $donor_count; ?></div>
                <div class="stat-label">Total Donors</div>
            </div>
            <div class="stat-card">
                <i class="fas fa-hands-helping"></i>
                <div class="stat-number"><?php echo $recipient_count; ?></div>
                <div class="stat-label">Total Recipients</div>
            </div>
            <div class="stat-card">
                <i class="fas fa-box-open"></i>
                <div class="stat-number"><?php echo $donation_count; ?></div>
                <div class="stat-label">Total Donations</div>
            </div>
        </div>

        <!-- Donors Table -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title"><i class="fas fa-user-plus"></i> Donors</h2>
                <div class="search-container">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" id="donor-search" class="search-input" placeholder="Search donors...">
                </div>
            </div>
            <div class="table-responsive">
                <table id="donor-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Address</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $donors_result = $conn->query("SELECT * FROM users WHERE role = 'donor'");
                        while ($row = $donors_result->fetch_assoc()) {
                            echo "<tr>
                                    <td>{$row['id']}</td>
                                    <td>{$row['username']}</td>
                                    <td>{$row['email']}</td>
                                    <td>{$row['phone']}</td>
                                    <td>{$row['address']}</td>
                                  </tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recipients Table -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title"><i class="fas fa-hands-helping"></i> Recipients</h2>
                <div class="search-container">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" id="recipient-search" class="search-input" placeholder="Search recipients...">
                </div>
            </div>
            <div class="table-responsive">
                <table id="recipient-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Address</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $recipients_result = $conn->query("SELECT * FROM users WHERE role = 'recipient'");
                        while ($row = $recipients_result->fetch_assoc()) {
                            echo "<tr>
                                    <td>{$row['id']}</td>
                                    <td>{$row['username']}</td>
                                    <td>{$row['email']}</td>
                                    <td>{$row['phone']}</td>
                                    <td>{$row['address']}</td>
                                  </tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Donations Table -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title"><i class="fas fa-box-open"></i> Donations</h2>
                <div class="search-container">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" id="donation-search" class="search-input" placeholder="Search donations...">
                </div>
            </div>
            <div class="table-responsive">
                <table id="donation-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Donor Name</th>
                            <th>Food Type</th>
                            <th>Quantity</th>
                            <th>Description</th>
                            <th>Delivery Time</th>
                            <th>Transport</th>
                            <th>Recipient</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $donations_result = $conn->query("SELECT * FROM donations");
                        while ($row = $donations_result->fetch_assoc()) {
                            echo "<tr>
                                    <td>{$row['id']}</td>
                                    <td>{$row['donor_name']}</td>
                                    <td>{$row['food_type']}</td>
                                    <td>{$row['quantity']}</td>
                                    <td>{$row['food_description']}</td>
                                    <td>{$row['delivery_time']}</td>
                                    <td>{$row['transport']}</td>
                                    <td>{$row['recipient_name']}</td>
                                  </tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>