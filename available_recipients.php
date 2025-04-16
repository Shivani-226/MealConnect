<?php
include 'db_connect.php'; // Connect to DB

// Handle search functionality
$search_query = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$food_type_filter = isset($_GET['food_type']) ? $conn->real_escape_string($_GET['food_type']) : '';

// Modify SQL query to include food_type and handle search and filter
$sql = "SELECT username, email, phone, address, plates, food_type 
        FROM users 
        WHERE role='recipient' AND is_available = 1";

// Add search condition
if (!empty($search_query)) {
    $sql .= " AND (address LIKE '%$search_query%' OR username LIKE '%$search_query%')";
}

// Add food type filter condition
if (!empty($food_type_filter)) {
    $sql .= " AND food_type = '$food_type_filter'";
}

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Available Food Recipients - MealConnect</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #FFD100;
            --primary-light: #FFE44D;
            --primary-dark: #E6BC00;
            --text: #121212;
            --gray-light: #f5f5f5;
            --white: #FFFFFF;
            --black: #000000;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            color: var(--text);
            background: radial-gradient(circle,rgb(78, 54, 0) , rgb(46, 41, 0));
            line-height: 1.6;
            padding-top: 80px;
        }

        header {
            background-color: var(--black);
            padding: 1rem 5%;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
        }

        .logo {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--primary);
            display: flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
        }

        .logo-image {
            width: 40px;
            height: 40px;
        }

        .home-button {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--primary);
            text-decoration: none;
            font-size: 1rem;
            font-weight: 600;
        }

        .home-button i {
            color: var(--primary);
        }

        .recipients-list-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 5%;
        }

        .page-title {
            font-family: 'Playfair Display', serif;
            font-size: 2.5rem;
            color: var(--primary);
            margin-bottom: 2rem;
            text-align: center;
        }

        .recipients-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }

        .recipient-card {
            background: var(--white);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
            display: flex;
            flex-direction: column;
        }

        .recipient-card:hover {
            transform: translateY(-5px);
        }

        .recipient-info {
            padding: 1.5rem;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .recipient-name {
            font-size: 1.25rem;
            color: var(--primary);
            margin-bottom: 0.5rem;
        }

        .recipient-details {
            margin-bottom: 1rem;
            color: #475569;
        }

        .recipient-plates {
            font-size: 1rem;
            color: #333;
            margin-top: 0.5rem;
        }

        .request-btn {
            display: inline-block;
            width: 100%;
            padding: 0.75rem;
            background: var(--primary);
            color: white;
            text-decoration: none;
            border-radius: 4px;
            text-align: center;
            transition: background-color 0.3s ease;
        }

        .request-btn:hover {
            background: var(--primary-dark);
        }

        /* New styles for search and filter container */
        .search-filter-container {
            max-width: 1200px;
            margin: 0 auto 2rem;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 1rem;
        }

        .search-container {
            flex-grow: 1;
            display: flex;
            align-items: center;
            background: var(--white);
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            max-width: 600px;
        }

        .search-container input {
            flex-grow: 1;
            padding: 0.75rem 1rem;
            border: none;
            font-size: 1rem;
            color: var(--text);
        }

        .search-container button {
            padding: 0.75rem 1.5rem;
            background: var(--primary);
            color: var(--black);
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .search-container button:hover {
            background: var(--primary-dark);
        }

        .food-type-select {
            padding: 0.75rem 1rem;
            background: var(--white);
            border-radius: 8px;
            border: none;
            font-size: 1rem;
            color: var(--text);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .dashboard-btn {
            padding: 0.75rem 1.5rem;
            background: var(--primary);
            color: var(--black);
            border: none;
            border-radius: 8px;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: background-color 0.3s ease;
        }

        .dashboard-btn:hover {
            background: var(--primary-dark);
        }

        .food-type-badge {
            display: inline-block;
            color: var(--text);
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.875rem;
            margin-top: 0.5rem;
        }

        @media (max-width: 768px) {
            .search-filter-container {
                flex-direction: column;
                gap: 1rem;
            }

            .search-container, 
            .food-type-select, 
            .dashboard-btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <header>
        <nav>
            <a href="meal.html" class="logo">
                <img src="images/logo.png" alt="MealConnect Logo" class="logo-image">               
                MealConnect
            </a>
            <a href="meal.html" class="home-button">
                <i class="fas fa-home"></i> Home
            </a>
        </nav>
    </header>

    <main class="recipients-list-container">
        <h1 class="page-title">Available Food Recipients</h1>

        <!-- New Search and Filter Container -->
        <div class="search-filter-container">
            <form method="GET" action="" class="search-container">
                <input type="text" name="search" placeholder="Search by address or name" 
                       value="<?php echo htmlspecialchars($search_query); ?>">
                <button type="submit">
                    <i class="fas fa-search"></i>
                </button>
            </form>

            <form method="GET" action="" style="display: flex; align-items: center; gap: 1rem;">
                <select name="food_type" class="food-type-select">
                    <option value="">All Food Types</option>
                    <option value="Veg" <?php echo ($food_type_filter == 'Veg' ? 'selected' : ''); ?>>Vegetarian</option>
                    <option value="NonVeg" <?php echo ($food_type_filter == 'NonVeg' ? 'selected' : ''); ?>>Non-Vegetarian</option>
                </select>
                <input type="hidden" name="search" value="<?php echo htmlspecialchars($search_query); ?>">
                <button type="submit" class="food-type-select">Filter</button>
            </form>

            <a href="donor_dashboard.php" class="dashboard-btn">
                <i class="fas fa-chart-line"></i> Dashboard
            </a>
        </div>

        <div class="recipients-grid">
            <?php
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<div class='recipient-card'>
                            <div class='recipient-info'>
                                <h3 class='recipient-name'>".$row["username"]."</h3>
                                <p class='recipient-details'>
                                    <i class='fas fa-map-marker-alt'></i> ".$row["address"]."<br>
                                    <i class='fas fa-phone'></i> ".$row["phone"]."
                                </p>
                                <p class='recipient-plates'>
                                    <i class='fas fa-utensils'></i> Plates Needed: ".$row["plates"]."
                                </p>
                                <div class='food-type-badge'>
                                    <i class='fas fa-tag'></i> Food Type: ".$row["food_type"]."
                                </div>
                                <a href='#' class='request-btn' data-recipient='".$row["username"]."'>Provide help</a>
                            </div>
                          </div>";
                }
            } else {
                echo "<p style='color: white; text-align: center; width: 100%;'>No recipients found</p>";
            }
            ?>
        </div>
    </main>

    <script>
        // Select all buttons with the class "request-btn"
        const buttons = document.querySelectorAll('.request-btn');

        // Add click event listener to each button
        buttons.forEach(button => {
            button.addEventListener('click', (event) => {
                event.preventDefault();
                const recipientName = button.getAttribute('data-recipient');
                window.location.href = `donate.html?recipient=${encodeURIComponent(recipientName)}`;
            });
        });
    </script>
</body>
</html>