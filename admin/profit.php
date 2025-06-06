<?php
include 'Configure.php';


// Function to calculate profit
function calculate_profit($conn, $start_date, $end_date = null) {
    if ($end_date) {
        $query = "SELECT SUM((f.price - f.buying_price) * o.quantity) AS total_profit 
                  FROM orders o
                  INNER JOIN food_items f ON o.food_item_id = f.id
                  WHERE DATE(o.order_date) BETWEEN '$start_date' AND '$end_date'";
    } else {
        $query = "SELECT SUM((f.price - f.buying_price) * o.quantity) AS total_profit 
                  FROM orders o
                  INNER JOIN food_items f ON o.food_item_id = f.id
                  WHERE DATE(o.order_date) = '$start_date'";
    }
    $result = $conn->query($query);
    $row = $result->fetch_assoc();
    return $row['total_profit'] ?? 0; // Return 0 if no profit
}

// Calculate today's profit
$today = date('Y-m-d');
$todays_profit = calculate_profit($conn, $today);

// Calculate monthly profit (current month)
$current_month = date('F');
$month_start = date('Y-m-01');
$monthly_profit = calculate_profit($conn, $month_start, $today);

// Calculate yearly profit (current year)
$current_year = date('Y');
$year_start = date('Y-01-01');
$yearly_profit = calculate_profit($conn, $year_start, $today);

// Handle date range selection
$range_profit = null;
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['start_date']) && isset($_POST['end_date'])) {
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $range_profit = calculate_profit($conn, $start_date, $end_date);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restaurant Profit Analytics</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            color: #333;
            margin: 0;
            padding: 0;
        }
        header {
            background-color: #38b2ac;
            color: white;
            padding: 1rem;
            text-align: center;
        }
        .navbar {
    padding: 10px;
    display: flex;
    justify-content: center;
    align-items: center;
    flex-wrap: wrap;
    background-color: rgba(0, 0, 0, 0.5);
    border-radius: 10px;
}

.navbar a {
    color: #fff;
    text-decoration: none;
    margin: 0.5rem;
    font-weight: bold;
    transition: color 0.3s;
}

.navbar a:hover {
    color: gray;
}
        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background: white;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        .profit-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .profit-box {
            background: #f9fafb;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            text-align: center;
            flex: 1;
            margin: 0 10px;
        }
        .profit-box h3 {
            font-size: 18px;
            color: #38b2ac;
        }
        .profit-box span {
            font-size: 22px;
            font-weight: bold;
        }
        form {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        form input[type="date"],
        form button {
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 5px;
            width: 100%;
        }
        form button {
            background-color: #38b2ac;
            color: white;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        form button:hover {
            background-color: #319795;
        }
        .result {
            margin-top: 20px;
            font-size: 18px;
            color: #333;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background: #f9fafb;
        }
    </style>
</head>
<body>
    <header>
        <h1>Restaurant Profit Analytics</h1>
        <div class="navbar">
            <a href="home.php">Dashboard</a>
            <a href="Manage_food.php">Manage Food Items</a>
            <a href="Manage_Orders.php">Manage Orders</a>
            <a href="Manage_Users.php">Manage Users</a>
            <a href="manage_tables.php">Table Management</a>
            <a href="table_management.php">Reservation</a>
            <a href="admin_feedback.php">Feedback</a>
            <a href="admin_profile.php">Admin Profile</a>
            <a href="profit.php">Profit</a>
            <a href="alogin.php">Logout</a>
        </div>
    </header>
    <div class="container">
        <div class="profit-section">
            <div class="profit-box">
                <h3>Today's Profit</h3>
                <span>₹<?php echo number_format($todays_profit, 2); ?></span>
            </div>
            <div class="profit-box">
                <h3><?php echo $current_month; ?> Profit</h3>
                <span>₹<?php echo number_format($monthly_profit, 2); ?></span>
            </div>
            <div class="profit-box">
                <h3><?php echo $current_year; ?> Profit</h3>
                <span>₹<?php echo number_format($yearly_profit, 2); ?></span>
            </div>
        </div>

        <h2>Select Date Range</h2>
        <form method="POST">
            <input type="date" name="start_date" required>
            <input type="date" name="end_date" required>
            <button type="submit">Calculate Profit</button>
        </form>

        <?php if ($range_profit !== null) { ?>
            <div class="result">
                <strong>Profit for Selected Range:</strong> ₹<?php echo number_format($range_profit, 2); ?>
            </div>
        <?php } ?>
    </div>
</body>
</html>