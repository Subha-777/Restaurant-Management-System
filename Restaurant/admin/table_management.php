<?php
session_start();
error_reporting(E_ALL);
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit();
}
include 'Configure.php';

// Fetch orders
$sql = "SELECT reservation.*, users.username, users.email, users.phone
        FROM reservation
        INNER JOIN users ON reservation.user_id = users.id";
$result = $conn->query($sql);

// Update order status
if (isset($_POST['update_status']) && !empty($_POST['id'])) {
    $id = intval($_POST['id']);
    $status = $_POST['status'];
    $updateQuery = "UPDATE reservation SET status = '$status' WHERE id = $id";
    if ($conn->query($updateQuery)) {
        header('Location: table_management.php');
        exit();
    } else {
        echo "Error updating record: " . $conn->error;
    }
}

// Delete order
if (isset($_POST['delete']) && !empty($_POST['id'])) {
    $id = intval($_POST['id']);
    $deleteQuery = "DELETE FROM reservation WHERE id = $id";
    if ($conn->query($deleteQuery)) {
        header('Location: table_management.php');
        exit();
    } else {
        echo "Error deleting record: " . $conn->error;
    }
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
    <h2>Manage Orders</h2>
    <table border="1" class='content'>
        <thead>
            <tr>
                <th>Reservation ID</th>
                <th>User ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Special Requests</th>
                <th>Guests</th>
                <th>Date</th>
                <th>Time</th>
                <th>Status</th>
                <th>Price</th>
                <th>Payment Method</th>
                <th>Transaction ID</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>".$row["id"]."</td>
                            <td>".$row["user_id"]."</td>
                            <td>".$row["username"]."</td>
                            <td>".$row["email"]."</td>
                            <td>".$row["phone"]."</td>
                            <td>".$row["special_requests"]."</td>
                            <td>".$row["num_guests"]."</td>
                            <td>".$row["date"]."</td>
                            <td>".$row["time"]."</td>
                            <td>
                                <form method='POST' action=''>
                                    <input type='hidden' name='id' value='".$row["id"]."'>
                                    <select name='status'>
                                        <option value='Pending' ".($row["status"] == 'Pending' ? 'selected' : '').">Pending</option>
                                        <option value='In Progress' ".($row["status"] == 'In Progress' ? 'selected' : '').">In Progress</option>
                                        <option value='Reserved' ".($row["status"] == 'Reserved' ? 'selected' : '').">Reserved</option>
                                        <option value='Cancelled' ".($row["status"] == 'Cancelled' ? 'selected' : '').">Cancelled</option>
                                    </select>
                                    <button type='submit'class='btn-update' name='update_status'>Update</button>
                                </form>
                            </td>
                            <td>".$row["price"]."</td>
                            <td>".$row["payment_method"]."</td>
                            <td>".$row["transaction_id"]."</td>
                            <td>
                                <form method='POST'>
                                    <input type='hidden' name='id' value='".$row["id"]."'>
                                    <button type='submit' class='btn-delete' name='delete' onclick=\"return confirm('Are you sure?');\">Delete</button>
                                </form>
                            </td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='14'>No orders found</td></tr>";
            }
            ?>
        </tbody>
    </table>
    <?php $conn->close(); ?>
</body>
</html>
