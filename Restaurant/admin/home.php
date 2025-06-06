<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: alogin.php');
    exit();
}
include 'Configure.php';

// Fetch data for dashboard
$total_orders_query = "SELECT COUNT(*) AS count FROM orders";
$total_users_query = "SELECT COUNT(*) AS count FROM users";
$total_revenue_query = "SELECT SUM(total_price) AS revenue FROM orders";
$total_food_items_query = "SELECT COUNT(*) AS count FROM food_items";
$food_stock_query = "SELECT food_name, quantity FROM food_items";

$total_orders_result = $conn->query($total_orders_query);
$total_users_result = $conn->query($total_users_query);
$total_revenue_result = $conn->query($total_revenue_query);
$total_food_items_result = $conn->query($total_food_items_query);
$food_stock_result = $conn->query($food_stock_query);

if ($total_orders_result && $total_users_result && $total_revenue_result && $total_food_items_result && $food_stock_result) {
    $total_orders = $total_orders_result->fetch_assoc()['count'];
    $total_users = $total_users_result->fetch_assoc()['count'];
    $total_revenue = $total_revenue_result->fetch_assoc()['revenue'];
    $total_food_items = $total_food_items_result->fetch_assoc()['count'];
} else {
    die("Error fetching data: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: url('back3.webp') no-repeat center center fixed;
            background-size: cover;
            color: #fff;
        }

        .navbar {
            background-color: rgba(0, 0, 0, 0.8);
            padding: 20px;
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            align-items: center;
            border-radius: 10px;
        }

        .navbar a {
            color: #fff;
            text-decoration: none;
            margin: 0 1rem;
            font-weight: bold;
            transition: color 0.3s;
        }

        .navbar a:hover {
            color: gray;
        }

        header {
            text-align: center;
            padding: 2rem;
            color: #fff;
        }

        .dashboard-overview {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            margin: 2rem;
            gap: 1rem;
        }

        .overview-card {
            background-color: #f0f0f1;
            border: 1px solid gray;
            padding: 12px;
            border-radius: 10px;
            text-align: center;
            flex: 1 1 calc(15% - 15px);
            max-width: 1 1 calc(15% - 15px);
        }

        .overview-card h3 {
            margin: 0;
            font-size: 1.5rem;
            color: black;
        }

        .overview-card p {
            margin: 0.5rem 0;
            font-size: 1.2rem;
            color: black;
        }

        .food-stock {
            margin: 70px;
            background-color: rgba(0, 0, 0, 0.5);
            border-radius: 10px;
            padding: 1rem;
            color: #fff;
        }

        .food-stock table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            background-color: rgba(0, 0, 0, 0.7);
            color: white;
            font-weight: bold;
        }

        .food-stock th, .food-stock td {
            padding: 1rem;
            border: 1px solid rgba(255, 255, 255, 0.3);
            text-align: left;
        }

        .food-stock td {
            background-color: rgba(255, 255, 255, 0.1);
        }

        .footer {
            text-align: center;
            padding: 1rem;
            background-color: rgba(0, 0, 0, 0.8);
            margin-top: 2rem;
        }

        .footer p {
            margin: 0;
            color: #fff;
        }

        /* Media Queries for Responsiveness */
        @media (max-width: 768px) {
            .dashboard-overview {
                flex-direction: column;
                gap: 1rem;
            }

            .overview-card {
                flex: 1 1 100%;
                max-width: 100%;
            }

            .navbar {
                flex-direction: column;
                align-items: flex-start;
            }

            .navbar a {
                margin: 0.5rem 0;
            }

            .food-stock table {
                font-size: 0.9rem;
            }
        }

        @media (max-width: 480px) {
            .food-stock th, .food-stock td {
                padding: 0.5rem;
                font-size: 0.8rem;
            }

            .overview-card h3 {
                font-size: 1.2rem;
            }

            .overview-card p {
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>
    <header>
        <h1>Admin Dashboard</h1>
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
       

    </header>
       
            <div class="dashboard-overview">
                <div class="overview-card">
                    <h3>Total Orders</h3>
                    <p><?php echo $total_orders; ?></p>
                </div>
           
            <!-- <div class="grid-item">
                <div class="stat-box"> -->
                <div class="overview-card">
                    <h3>Total Users</h3>
                    <p><?php echo $total_users; ?></p>
                </div>
           
            <!-- <div class="grid-item">
                <div class="stat-box"> -->
                <div class="overview-card">
                    <h3>Total Revenue</h3>
                    <p><?php echo $total_revenue; ?> INR</p>
                </div>
           
            <!-- <div class="grid-item">
                <div class="stat-box"> -->
                <div class="overview-card">
                    <h3>Total Food Items</h3>
                    <p><?php echo $total_food_items; ?></p>
                </div>
           
            </div>
            <div class="food-stock">
        <h2>Food Stock</h2>
        <table>
            <thead>
            <tr>
                <th>Name</th>
                <th>Stock Quantity</th>
            </tr>
            <?php while ($row = $food_stock_result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['food_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['quantity']); ?></td>
                </tr>
            <?php } ?>
            </thead>
        </table>
        <div class="footer">
        <p>&copy; 2025 Food Dashboard</p>
    </div>
</body>
</html>

