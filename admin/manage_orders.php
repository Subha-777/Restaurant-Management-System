<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit();
}
include 'Configure.php';
// Fetch orders
$sql = "SELECT orders.id, orders.user_id, users.username, food_items.food_name, 
               orders.quantity, orders.total_price, orders.status, orders.order_date, 
               orders.payment_method 
        FROM orders
        INNER JOIN food_items ON orders.food_item_id = food_items.id
        INNER JOIN users ON orders.user_id = users.id";


$result = $conn->query($sql);
// Update order status
if (isset($_POST['update_status'])) {
    $order_id = $_POST['order_id'];
    $status = $_POST['status'];
    $conn->query("UPDATE orders SET status = '$status' WHERE id = $order_id");
    header('Location: manage_orders.php');
}
// Delete order
if (isset($_POST['delete'])) {
    $order_id = $_POST['order_id'];
    $conn->query("DELETE FROM orders WHERE id = $order_id");
    header('Location: manage_orders.php');
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Orders</title>
     <style>
   

  body {
    margin: 0;
    font-family: Arial, sans-serif;
    background: url('back5.webp') no-repeat center center fixed;
    background-size: cover;
    color: #fff;
}

header {
    color: #fff;
    padding: 20px;
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

.content {
    margin: 1.5rem;
    background-color: rgba(56, 56, 56, 0.7);
    border-radius: 10px;
    padding: 1.5rem;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
    overflow-x: auto; /* Ensures the table is scrollable on smaller devices */
}

h1 {
    text-align: center;
    color: white;
}
h2{
    text-align:center;
}

.table-responsive {
    width: 100%;
    overflow-x: auto; /* Add horizontal scrolling for smaller screens */
    margin-top: 1rem;
    border-radius: 10px;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin: 0;
    overflow-x: auto;
    min-width: 600px; /* Ensure the table doesn't shrink too much */
}

th, td {
    padding: 1rem;
    border: 1px solid rgba(255, 255, 255, 0.3);
    text-align: center;
    background-color: rgba(255, 255, 255, 0.1);
    font-size: 16px;
}

th {
    background-color: rgba(0, 0, 0, 0.6);
    color: #fff;
}

.btn {
    padding: 0.4rem 1rem;
    border: none;
    border-radius: 5px;
    color: #fff;
    font-weight: bold;
    cursor: pointer;
    transition: background-color 0.3s;
    font-size: 14px;
}

.btn-update {
    background-color: #28a745;
}

.btn-update:hover {
    background-color: #218838;
}

.btn-delete {
    background-color: #dc3545;
}

.btn-delete:hover {
    background-color: #c82333;
}

/* Responsive Design */
@media (max-width: 768px) {
    .navbar {
        flex-direction: column;
        text-align: center;
    }

    .navbar a {
        margin: 0.5rem 0;
    }

    table th, table td {
        font-size: 14px;
        padding: 0.8rem;
    }

    th, td {
        word-wrap: break-word;
    }

    .btn {
        padding: 0.3rem 0.8rem;
        font-size: 12px;
    }
}

@media (max-width: 480px) {
    .content {
        margin: 1rem;
        padding: 1rem;
    }

    table th, table td {
        font-size: 12px;
        padding: 0.6rem;
    }

    .btn {
        padding: 0.2rem 0.6rem;
        font-size: 10px;
    }
}

</style>

</>
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
            </div>
    </header>
    <div class="content">
    <h2>Manage Orders</h2>
    <div class="table-responsive">
    <table>
        <thead>
            <tr>
                <th>Order ID</th>
                <th>User ID</th>
                <th>Food Item</th>
                <th>Quantity</th>
                <th>Total Price</th>
                <th>Status</th>
                <th>Payment Option</th>
                <th>Order Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                // Output data of each row
                while($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <form method='POST'>
                                <td data-label>".$row["id"]."</td>
                                <td data-label>".$row["user_id"]."</td>
                                <td data-label>".$row["food_name"]."</td>
                                <td data-label>".$row["quantity"]."</td>
                                <td data-label>".$row["total_price"]."</td>
                                <td data-label>
                                    <select name='status'>
                                        <option value='Pending' ".($row["status"] == 'Pending' ? 'selected' : '').">Pending</option>
                                        <option value='In Progress' ".($row["status"] == 'In Progress' ? 'selected' : '').">In Progress</option>
                                        <option value='Ordered' ".($row["status"] == 'Ordered' ? 'selected' : '').">Ordered</option>
                                        <option value='Cancelled' ".($row["status"] == 'Cancelled' ? 'selected' : '').">Cancelled</option>
                                    </select>
                                </td>
                                <td data-label>".$row["payment_method"]."</td>
                                <td data-label>".$row["order_date"]."</td>
                                <td data-label>
                                    <input type='hidden' name='order_id' value='".$row["id"]."'>
                                    <button type='submit'  class='btn btn-update' name='update_status'>Update</button>
                                    <button type='submit' class='btn btn-delete' name='delete'>Delete</button>
                                   
                                </td>
                            </form>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='9'>No orders found</td></tr>";
            }
            ?>
        </tbody>
    </table>
    <?php $conn->close(); ?>
</body>
</html>

